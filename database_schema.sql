-- =====================================================
-- OSPOS Database Schema Enhancement (FINAL)
-- Run this SQL to create missing tables and indexes
-- =====================================================

-- Create cash_up table if not exists
CREATE TABLE IF NOT EXISTS `cash_up` (
  `cashup_id` int(11) NOT NULL AUTO_INCREMENT,
  `open_date` datetime DEFAULT NULL,
  `close_date` datetime DEFAULT NULL,
  `open_amount_cash` decimal(12,2) DEFAULT '0.00',
  `transfer_amount_cash` decimal(12,2) DEFAULT '0.00',
  `closed_amount_cash` decimal(12,2) DEFAULT '0.00',
  `closed_amount_due` decimal(12,2) DEFAULT '0.00',
  `closed_amount_card` decimal(12,2) DEFAULT '0.00',
  `closed_amount_check` decimal(12,2) DEFAULT '0.00',
  `closed_amount_total` decimal(12,2) DEFAULT '0.00',
  `description` varchar(255) DEFAULT NULL,
  `note` tinyint(1) DEFAULT '0',
  `open_employee_id` int(11) DEFAULT NULL,
  `close_employee_id` int(11) DEFAULT NULL,
  `deleted` tinyint(1) DEFAULT '0',
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`cashup_id`),
  KEY `idx_cashup_open_date` (`open_date`),
  KEY `idx_cashup_employee` (`open_employee_id`, `close_employee_id`),
  KEY `idx_cashup_deleted` (`deleted_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Add soft delete support to cash_up if column doesn't exist
ALTER TABLE `cash_up` ADD COLUMN IF NOT EXISTS `deleted_at` datetime NULL AFTER `deleted`;

-- Check which columns exist and add indexes only if they don't
-- These are conditional - will only run if column exists

-- Items table indexes (only if column exists)
SET @dbname = DATABASE();
SET @tablename = 'ospos_items';

-- Check for name column and add index
SET @sql = NULL;
SELECT GROUP_CONCAT(column_name) INTO @columns
FROM INFORMATION_SCHEMA.COLUMNS
WHERE table_schema = @dbname AND table_name = @tablename AND column_name IN ('name', 'item_number', 'category');

IF @columns IS NOT NULL THEN
  SET @sql = CONCAT('ALTER TABLE ', @tablename, ' ADD INDEX IF NOT EXISTS idx_items_search (', @columns, ')');
  PREPARE stmt FROM @sql;
  EXECUTE stmt;
  DEALLOCATE PREPARE stmt;
END IF;

-- Sales table indexes
SET @tablename = 'ospos_sales';
SELECT GROUP_CONCAT(column_name) INTO @columns
FROM INFORMATION_SCHEMA.COLUMNS
WHERE table_schema = @dbname AND table_name = @tablename AND column_name IN ('sale_date', 'employee_id', 'customer_id');

IF @columns IS NOT NULL THEN
  SET @sql = CONCAT('ALTER TABLE ', @tablename, ' ADD INDEX IF NOT EXISTS idx_sales_search (', @columns, ')');
  PREPARE stmt FROM @sql;
  EXECUTE stmt;
  DEALLOCATE PREPARE stmt;
END IF;

-- Employees table indexes
SET @tablename = 'ospos_employees';
SELECT GROUP_CONCAT(column_name) INTO @columns
FROM INFORMATION_SCHEMA.COLUMNS
WHERE table_schema = @dbname AND table_name = @tablename AND column_name IN ('username', 'email', 'phone');

IF @columns IS NOT NULL THEN
  SET @sql = CONCAT('ALTER TABLE ', @tablename, ' ADD INDEX IF NOT EXISTS idx_employees_search (', @columns, ')');
  PREPARE stmt FROM @sql;
  EXECUTE stmt;
  DEALLOCATE PREPARE stmt;
END IF;

-- Customers table indexes
SET @tablename = 'ospos_customers';
SELECT GROUP_CONCAT(column_name) INTO @columns
FROM INFORMATION_SCHEMA.COLUMNS
WHERE table_schema = @dbname AND table_name = @tablename AND column_name IN ('first_name', 'last_name', 'email');

IF @columns IS NOT NULL THEN
  SET @sql = CONCAT('ALTER TABLE ', @tablename, ' ADD INDEX IF NOT EXISTS idx_customers_search (', @columns, ')');
  PREPARE stmt FROM @sql;
  EXECUTE stmt;
  DEALLOCATE PREPARE stmt;
END IF;

-- Verify table creation
SELECT 'Cash_up table status:' AS info;
SHOW TABLE STATUS LIKE 'cash_up';

SELECT 'Indexes created successfully!' AS status;
