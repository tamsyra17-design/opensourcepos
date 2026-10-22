-- ---------------------------------------------------------------------------
-- Supplier Ledger / Payables
-- Adds a manual supplier-payment ledger on top of OSPOS Receivings.
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

-- ---------------------------------------------------------------------------
-- Module registration
-- ---------------------------------------------------------------------------

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
