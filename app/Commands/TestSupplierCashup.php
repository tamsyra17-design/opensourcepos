<?php

namespace App\Commands;

use App\Models\Cashup;
use App\Models\SupplierledgerModel;
use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;

class TestSupplierCashup extends BaseCommand
{
    protected $group = 'Testing';
    protected $name = 'test-supplier-cashup';
    protected $description = 'Temporary supplier return and cashup calculation check.';

    public function run(array $params)
    {
        $ledger = model(SupplierledgerModel::class);
        $cashup = model(Cashup::class);
        $db = db_connect();

        foreach ($ledger->get_supplier_balances() as $supplier) {
            CLI::write(sprintf(
                'SUPPLIER %d %s | purchases %0.2f | returns %0.2f (credit %0.2f, cash %0.2f) | paid %0.2f+%0.2f | payable %0.2f',
                $supplier->supplier_id,
                $supplier->company_name ?: $supplier->first_name,
                $supplier->total_purchased,
                $supplier->total_returns,
                $supplier->credit_returns,
                $supplier->cash_returns,
                $supplier->paid_at_purchase,
                $supplier->manual_paid,
                $supplier->balance
            ));
        }

        $summary = $cashup->get_summary([
            'open_date' => '2026-10-20 22:48:18',
            'close_date' => '2026-10-22 23:59:59',
            'open_amount_cash' => 0,
            'transfer_amount_cash' => 0,
            'closed_amount_cash' => 0,
        ]);

        CLI::write(sprintf(
            'CASHUP | cash sales %0.2f | cash refunds %0.2f | supplier purchases %0.2f | supplier refunds %0.2f | cash expenses %0.2f | expected %0.2f',
            $summary['cash_sales'],
            $summary['cash_refunds'],
            $summary['supplier_cash_purchases'],
            $summary['supplier_cash_refunds'],
            $summary['cash_expenses'],
            $summary['expected_amount_cash']
        ));

        $this->test_return($ledger, $db, 'Due', 'credit');
        $this->test_return($ledger, $db, 'Cash', 'cash');
        $this->render_pages();
    }

    private function render_pages(): void
    {
        session()->set('person_id', 1);

        $pages = [
            'supplier ledger' => static fn() => (new \App\Controllers\Supplierledger())->getIndex(),
            'supplier details' => static fn() => (new \App\Controllers\Supplierledger())->getDetails(4),
            'supplier pay' => static fn() => (new \App\Controllers\Supplierledger())->getPay(4),
            'cashup form' => static fn() => (new \App\Controllers\Cashups())->getView(0),
        ];

        foreach ($pages as $label => $render) {
            try {
                $html = $render();
                CLI::write('RENDER ' . $label . ' | bytes ' . strlen($html));
            } catch (\Throwable $exception) {
                CLI::error('RENDER ' . $label . ' FAILED: ' . $exception->getMessage());
            }
        }
    }

    private function test_return(SupplierledgerModel $ledger, $db, string $paymentType, string $label): void
    {
        $db->transBegin();

        try {
            $db->table('receivings')->insert([
                'receiving_time' => '2026-10-22 18:00:00',
                'supplier_id' => 4,
                'employee_id' => 1,
                'comment' => '',
                'payment_type' => $paymentType,
                'reference' => null,
            ]);
            $receivingId = (int) $db->insertID();

            $db->table('receivings_items')->insert([
                'receiving_id' => $receivingId,
                'item_id' => 1,
                'description' => '',
                'serialnumber' => null,
                'line' => 1,
                'quantity_purchased' => -1,
                'item_cost_price' => 0,
                'item_unit_price' => 5,
                'discount' => 0,
                'discount_type' => 0,
                'item_location' => 1,
                'receiving_quantity' => 1,
            ]);

            $totals = $ledger->get_supplier_totals(4);
            $purchases = $ledger->get_supplier_purchases(4);
            $testRow = null;
            foreach ($purchases as $purchase) {
                if ((int) $purchase->receiving_id === $receivingId) {
                    $testRow = $purchase;
                }
            }

            $cashup = model(Cashup::class)->get_summary([
                'open_date' => '2026-10-20 22:48:18',
                'close_date' => '2026-10-22 23:59:59',
                'open_amount_cash' => 0,
                'transfer_amount_cash' => 0,
                'closed_amount_cash' => 0,
            ]);

            CLI::write(sprintf(
                'TEST %s return #%d | purchases %0.2f | returns %0.2f (credit %0.2f, cash %0.2f) | payable %0.2f | flags return=%d credit=%d cash=%d | cashup supplier refunds %0.2f',
                $label,
                $receivingId,
                $totals['total_purchased'],
                $totals['total_returns'],
                $totals['credit_returns'],
                $totals['cash_returns'],
                $totals['balance'],
                (int) ($testRow->is_return ?? 0),
                (int) ($testRow->is_credit_return ?? 0),
                (int) ($testRow->is_cash_return ?? 0),
                $cashup['supplier_cash_refunds']
            ));
        } finally {
            $db->transRollback();
        }
    }
}
