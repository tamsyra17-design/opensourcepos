-- OSPOS performance indexes
-- Safe to run multiple times: every index is created only when missing.
-- The table prefix below matches `database.default.DBPrefix = 'ospos_'`.
-- If you use a different prefix, replace `ospos_` accordingly.

-- Dashboard / report queries filter completed sales by date.
SET @exists := (
    SELECT COUNT(*) FROM information_schema.STATISTICS
    WHERE TABLE_SCHEMA = DATABASE()
      AND TABLE_NAME = 'ospos_sales'
      AND INDEX_NAME = 'idx_sales_status_time'
);
SET @sql := IF(@exists = 0,
    'CREATE INDEX idx_sales_status_time ON ospos_sales (sale_status, sale_time)',
    'SELECT ''idx_sales_status_time already exists'' AS note');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- Item sales aggregations (top items, reports).
SET @exists := (
    SELECT COUNT(*) FROM information_schema.STATISTICS
    WHERE TABLE_SCHEMA = DATABASE()
      AND TABLE_NAME = 'ospos_sales_items'
      AND INDEX_NAME = 'idx_sales_items_sale_item'
);
SET @sql := IF(@exists = 0,
    'CREATE INDEX idx_sales_items_sale_item ON ospos_sales_items (sale_id, item_id)',
    'SELECT ''idx_sales_items_sale_item already exists'' AS note');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- Payment type breakdown.
SET @exists := (
    SELECT COUNT(*) FROM information_schema.STATISTICS
    WHERE TABLE_SCHEMA = DATABASE()
      AND TABLE_NAME = 'ospos_sales_payments'
      AND INDEX_NAME = 'idx_sales_payments_type'
);
SET @sql := IF(@exists = 0,
    'CREATE INDEX idx_sales_payments_type ON ospos_sales_payments (payment_type, sale_id)',
    'SELECT ''idx_sales_payments_type already exists'' AS note');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- Customer lookups by company name during checkout.
SET @exists := (
    SELECT COUNT(*) FROM information_schema.STATISTICS
    WHERE TABLE_SCHEMA = DATABASE()
      AND TABLE_NAME = 'ospos_customers'
      AND INDEX_NAME = 'idx_customers_person_company'
);
SET @sql := IF(@exists = 0,
    'CREATE INDEX idx_customers_person_company ON ospos_customers (person_id, company_name)',
    'SELECT ''idx_customers_person_company already exists'' AS note');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- ---------------------------------------------------------------------------
-- Due collection ledger (Receivables module)
-- Collections are stored separately from sales_payments so revenue, reports and
-- cashup sales totals are not double counted.
-- ---------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `ospos_receivable_payments` (
  `collection_id` int(11) NOT NULL AUTO_INCREMENT,
  `sale_id` int(10) NOT NULL,
  `customer_id` int(10) NOT NULL,
  `payment_type` varchar(40) NOT NULL,
  `payment_amount` decimal(15,2) NOT NULL,
  `payment_time` datetime NOT NULL DEFAULT current_timestamp(),
  `employee_id` int(11) DEFAULT NULL,
  `reference_code` varchar(40) NOT NULL DEFAULT '',
  `note` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`collection_id`),
  KEY `collection_sale` (`sale_id`),
  KEY `collection_customer` (`customer_id`),
  KEY `collection_time` (`payment_time`),
  KEY `collection_type` (`payment_type`),
  KEY `collection_sale_time` (`sale_id`,`payment_time`),
  CONSTRAINT `ospos_receivable_payments_sale` FOREIGN KEY (`sale_id`) REFERENCES `ospos_sales` (`sale_id`) ON DELETE CASCADE,
  CONSTRAINT `ospos_receivable_payments_customer` FOREIGN KEY (`customer_id`) REFERENCES `ospos_customers` (`person_id`) ON DELETE CASCADE,
  CONSTRAINT `ospos_receivable_payments_employee` FOREIGN KEY (`employee_id`) REFERENCES `ospos_employees` (`person_id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

-- ---------------------------------------------------------------------------
-- Rental pricing, discount, payment and Sales integration
-- See rental_pricing_schema.sql for the full, repeat-safe migration.
-- ---------------------------------------------------------------------------
ALTER TABLE `ospos_rental_items`
  ADD COLUMN IF NOT EXISTS `base_days` int(10) NOT NULL DEFAULT 1 AFTER `daily_rate`,
  ADD COLUMN IF NOT EXISTS `base_fee` decimal(15,2) NOT NULL DEFAULT 0.00 AFTER `base_days`,
  ADD COLUMN IF NOT EXISTS `extra_daily_rate` decimal(15,2) NOT NULL DEFAULT 0.00 AFTER `base_fee`;

ALTER TABLE `ospos_rental_lines`
  ADD COLUMN IF NOT EXISTS `base_days` int(10) NOT NULL DEFAULT 1 AFTER `days`,
  ADD COLUMN IF NOT EXISTS `base_fee` decimal(15,2) NOT NULL DEFAULT 0.00 AFTER `base_days`,
  ADD COLUMN IF NOT EXISTS `extra_days` int(10) NOT NULL DEFAULT 0 AFTER `base_fee`,
  ADD COLUMN IF NOT EXISTS `extra_daily_rate` decimal(15,2) NOT NULL DEFAULT 0.00 AFTER `extra_days`,
  ADD COLUMN IF NOT EXISTS `extra_fee` decimal(15,2) NOT NULL DEFAULT 0.00 AFTER `extra_daily_rate`;

ALTER TABLE `ospos_rentals`
  ADD COLUMN IF NOT EXISTS `rent_fee` decimal(15,2) NOT NULL DEFAULT 0.00 AFTER `rental_total`,
  ADD COLUMN IF NOT EXISTS `discount_type` varchar(10) NOT NULL DEFAULT 'none' AFTER `rent_fee`,
  ADD COLUMN IF NOT EXISTS `discount_value` decimal(15,2) NOT NULL DEFAULT 0.00 AFTER `discount_type`,
  ADD COLUMN IF NOT EXISTS `discount_amount` decimal(15,2) NOT NULL DEFAULT 0.00 AFTER `discount_value`,
  ADD COLUMN IF NOT EXISTS `payment_type` varchar(40) NOT NULL DEFAULT '' AFTER `discount_amount`,
  ADD COLUMN IF NOT EXISTS `payment_reference` varchar(40) NOT NULL DEFAULT '' AFTER `payment_type`,
  ADD COLUMN IF NOT EXISTS `deposit_payment_type` varchar(40) NOT NULL DEFAULT '' AFTER `payment_reference`,
  ADD COLUMN IF NOT EXISTS `sale_id` int(10) DEFAULT NULL AFTER `deposit_payment_type`,
  ADD COLUMN IF NOT EXISTS `late_fee_sale_id` int(10) DEFAULT NULL AFTER `sale_id`;

UPDATE `ospos_rental_items`
SET `base_fee` = `daily_rate`
WHERE `base_fee` = 0 AND `daily_rate` > 0;

UPDATE `ospos_rental_items`
SET `extra_daily_rate` = `daily_rate`
WHERE `extra_daily_rate` = 0 AND `daily_rate` > 0;

UPDATE `ospos_rental_lines`
SET `base_fee` = `daily_rate`,
    `extra_days` = GREATEST(`days` - 1, 0),
    `extra_daily_rate` = `daily_rate`,
    `extra_fee` = ROUND(`quantity` * GREATEST(`days` - 1, 0) * `daily_rate`, 2)
WHERE `base_fee` = 0 AND `daily_rate` > 0;

UPDATE `ospos_rentals`
SET `rent_fee` = `rental_total`
WHERE `rent_fee` = 0 AND `rental_total` > 0;

-- Rental sale lookup indexes (created only when missing).
SET @exists := (
    SELECT COUNT(*) FROM information_schema.STATISTICS
    WHERE TABLE_SCHEMA = DATABASE()
      AND TABLE_NAME = 'ospos_rentals'
      AND INDEX_NAME = 'idx_rentals_sale'
);
SET @sql := IF(@exists = 0,
    'CREATE INDEX idx_rentals_sale ON ospos_rentals (sale_id)',
    'SELECT ''idx_rentals_sale already exists'' AS note');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @exists := (
    SELECT COUNT(*) FROM information_schema.STATISTICS
    WHERE TABLE_SCHEMA = DATABASE()
      AND TABLE_NAME = 'ospos_rentals'
      AND INDEX_NAME = 'idx_rentals_late_fee_sale'
);
SET @sql := IF(@exists = 0,
    'CREATE INDEX idx_rentals_late_fee_sale ON ospos_rentals (late_fee_sale_id)',
    'SELECT ''idx_rentals_late_fee_sale already exists'' AS note');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- ---------------------------------------------------------------------------
-- Supplier Ledger / Payables
-- See supplier_ledger_schema.sql for the full, repeat-safe migration.
-- Purchases remain in ospos_receivings / ospos_receivings_items.
-- ---------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `ospos_supplier_payments` (
  `payment_id` int(11) NOT NULL AUTO_INCREMENT,
  `supplier_id` int(10) NOT NULL,
  `receiving_id` int(10) DEFAULT NULL,
  `payment_type` varchar(40) NOT NULL,
  `payment_amount` decimal(15,2) NOT NULL,
  `payment_time` datetime NOT NULL DEFAULT current_timestamp(),
  `employee_id` int(11) DEFAULT NULL,
  `reference_code` varchar(40) NOT NULL DEFAULT '',
  `note` varchar(255) NOT NULL DEFAULT '',
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`payment_id`),
  KEY `supplier_payment_supplier` (`supplier_id`),
  KEY `supplier_payment_receiving` (`receiving_id`),
  KEY `supplier_payment_time` (`payment_time`),
  KEY `supplier_payment_type` (`payment_type`),
  KEY `supplier_payment_supplier_time` (`supplier_id`, `payment_time`),
  KEY `ospos_supplier_payments_employee` (`employee_id`),
  CONSTRAINT `ospos_supplier_payments_supplier`
    FOREIGN KEY (`supplier_id`) REFERENCES `ospos_suppliers` (`person_id`) ON DELETE CASCADE,
  CONSTRAINT `ospos_supplier_payments_receiving`
    FOREIGN KEY (`receiving_id`) REFERENCES `ospos_receivings` (`receiving_id`) ON DELETE SET NULL,
  CONSTRAINT `ospos_supplier_payments_employee`
    FOREIGN KEY (`employee_id`) REFERENCES `ospos_employees` (`person_id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

INSERT INTO `ospos_modules` (`module_id`, `name_lang_key`, `desc_lang_key`, `sort`)
VALUES ('supplierledger', 'module_supplierledger', 'module_supplierledger_desc', 113)
ON DUPLICATE KEY UPDATE
  `name_lang_key` = VALUES(`name_lang_key`),
  `desc_lang_key` = VALUES(`desc_lang_key`),
  `sort` = VALUES(`sort`);

INSERT INTO `ospos_permissions` (`permission_id`, `module_id`)
VALUES ('supplierledger', 'supplierledger')
ON DUPLICATE KEY UPDATE
  `module_id` = VALUES(`module_id`);

INSERT INTO `ospos_grants` (`permission_id`, `person_id`, `menu_group`)
VALUES
  ('supplierledger', 1, 'home'),
  ('supplierledger', 3, 'home')
ON DUPLICATE KEY UPDATE
  `menu_group` = VALUES(`menu_group`);
