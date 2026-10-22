-- Add deleted_at column to ospos_cash_up table
ALTER TABLE `ospos_cash_up` ADD COLUMN `deleted_at` datetime NULL AFTER `deleted`;
