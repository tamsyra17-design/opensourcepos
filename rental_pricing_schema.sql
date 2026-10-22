-- Rental pricing, discount, payment and Sales integration schema.
-- Matches the `ospos_` table prefix used by this installation.

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

-- Preserve the previous per-day pricing for existing catalog rows and rentals.
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

-- Lookup indexes (created only when missing).
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
