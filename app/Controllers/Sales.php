<?php
/**
 * @var string $controller_name
 * @var array $modes
 * @var array $mode
 * @var array $empty_tables
 * @var array $selected_table
 * @var array $stock_locations
 * @var array $stock_location
 * @var array $cart
 * @var bool $items_module_allowed
 * @var bool $change_price
 * @var int $customer_id
 * @var int $customer_discount_type
 * @var float $customer_discount
 * @var float $customer_total
 * @var string $customer_required
 * @var float|int $item_count
 * @var float|int $total_units
 * @var float $subtotal
 * @var array $taxes
 * @var float $total
 * @var float $payments_total
 * @var float $amount_due
 * @var bool $payments_cover_total
 * @var array $payment_options
 * @var array $selected_payment_type
 * @var bool $pos_mode
 * @var array $payments
 * @var string $mode_label
 * @var string $comment
 * @var bool $print_after_sale
 * @var bool $email_receipt
 * @var bool $price_work_orders
 * @var string $invoice_number
 * @var int $cash_mode
 * @var float $non_cash_total
 * @var float $cash_amount_due
 * @var array $config
 */

use App\Models\Employee;

helper('url');
?>

<?= view('partial/header') ?>

<!-- ============================================
     MODERN POS 2025 - Fully Auto-Adjustable
     ============================================ -->
<style>
:root {
    --pos-bg: #f4f6fa;
    --pos-surface: #ffffff;
    --pos-surface-2: #f8fafc;
    --pos-border: #e2e8f0;
    --pos-text: #1e293b;
    --pos-text-muted: #64748b;
    --pos-primary: #4f46e5;
    --pos-primary-hover: #4338ca;
    --pos-success: #10b981;
    --pos-success-hover: #059669;
    --pos-danger: #ef4444;
    --pos-danger-hover: #dc2626;
    --pos-warning: #f59e0b;
    --pos-info: #0ea5e9;
    --pos-radius: 12px;
    --pos-radius-sm: 8px;
    --pos-shadow: 0 1px 3px rgba(0,0,0,0.06), 0 1px 2px rgba(0,0,0,0.04);
    --pos-shadow-md: 0 4px 6px -1px rgba(0,0,0,0.07), 0 2px 4px -1px rgba(0,0,0,0.04);
    --pos-font: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
}

[data-theme="dark"] {
    --pos-bg: #0f172a;
    --pos-surface: #1e293b;
    --pos-surface-2: #334155;
    --pos-border: #334155;
    --pos-text: #f1f5f9;
    --pos-text-muted: #94a3b8;
}

* { box-sizing: border-box; }

body {
    background: var(--pos-bg);
    color: var(--pos-text);
    font-family: var(--pos-font);
    font-size: 14px;
    margin: 0;
    padding: 0;
}

/* ===== Layout Grid ===== */
#register_wrapper {
    display: grid;
    grid-template-columns: minmax(0, 1fr) 400px;
    gap: 16px;
    padding: 16px;
    width: 100%;
    max-width: 100%;
    margin: 0 auto;
    box-sizing: border-box;
}

#register_wrapper > div { min-width: 0; }

@media (max-width: 1400px) {
    #register_wrapper { grid-template-columns: minmax(0, 1fr) 360px; }
}

@media (max-width: 1100px) {
    #register_wrapper { grid-template-columns: 1fr; padding: 12px; }
}

/* ===== Panels ===== */
.panel {
    background: var(--pos-surface);
    border: 1px solid var(--pos-border);
    border-radius: var(--pos-radius);
    box-shadow: var(--pos-shadow);
    margin-bottom: 16px;
    overflow: visible;
}

.panel-body { padding: 14px 18px; }

/* ===== Top Bar Controls ===== */
#mode_form .panel-body ul {
    list-style: none;
    margin: 0;
    padding: 0;
    display: flex;
    align-items: center;
    flex-wrap: wrap;
    gap: 10px 14px;
    width: 100%;
}

#mode_form .panel-body ul li {
    display: flex;
    align-items: center;
    gap: 6px;
    flex-shrink: 0;
}

#mode_form .panel-body ul li.pull-left,
#mode_form .panel-body ul li.pull-right,
#mode_form .panel-body ul li.first_li {
    float: none !important;
    margin: 0;
}

#mode_form .panel-body ul li.pull-right { margin-left: auto; }
#mode_form .panel-body ul li.pull-right + li.pull-right { margin-left: 0; }

#mode_form label {
    font-weight: 600;
    color: var(--pos-text-muted);
    font-size: 11px;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    margin: 0;
    white-space: nowrap;
}

#mode_form .bootstrap-select,
#mode_form .selectpicker { min-width: 130px; }

#mode_form .bootstrap-select > .dropdown-toggle {
    border-radius: var(--pos-radius-sm) !important;
    border: 1px solid var(--pos-border) !important;
    background: var(--pos-surface-2) !important;
    color: var(--pos-text) !important;
    font-weight: 600;
    font-size: 12px;
    padding: 7px 12px;
    height: auto;
}

#mode_form .bootstrap-select > .dropdown-toggle:hover {
    background: var(--pos-border) !important;
}

/* ===== Item Search Bar ===== */
#add_item_form .panel-body ul {
    list-style: none;
    margin: 0;
    padding: 0;
    display: flex;
    align-items: center;
    gap: 10px 12px;
    flex-wrap: wrap;
    width: 100%;
}

#add_item_form .panel-body ul li {
    display: flex;
    align-items: center;
    gap: 8px;
    flex-shrink: 0;
}

#add_item_form .panel-body ul li.pull-left,
#add_item_form .panel-body ul li.pull-right,
#add_item_form .panel-body ul li.first_li {
    float: none !important;
    margin: 0;
}

#add_item_form .panel-body ul li.pull-left:nth-child(2) {
    flex: 1;
    min-width: 200px;
    flex-shrink: 1;
}

#add_item_form .panel-body ul li.pull-right { margin-left: auto; }

#add_item_form label {
    font-weight: 600;
    color: var(--pos-text-muted);
    font-size: 11px;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    margin: 0;
    white-space: nowrap;
}

#item {
    min-width: 0;
    width: 100%;
    font-size: 15px;
    padding: 10px 14px;
    border: 2px solid var(--pos-border);
    border-radius: var(--pos-radius-sm);
    transition: all 0.2s;
    background: var(--pos-surface-2);
    color: var(--pos-text);
}

#item:focus {
    outline: none;
    border-color: var(--pos-primary);
    background: var(--pos-surface);
    box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.1);
}

/* ===== Buttons ===== */
.btn {
    border-radius: var(--pos-radius-sm);
    font-weight: 600;
    font-size: 12px;
    padding: 8px 14px;
    border: none;
    cursor: pointer;
    transition: all 0.15s;
    display: inline-flex;
    align-items: center;
    gap: 6px;
    text-decoration: none;
    line-height: 1.4;
    white-space: nowrap;
}

.btn-sm { padding: 7px 12px; font-size: 12px; }

.btn-primary { background: var(--pos-primary); color: white; }
.btn-primary:hover { background: var(--pos-primary-hover); color: white; }
.btn-success { background: var(--pos-success); color: white; }
.btn-success:hover { background: var(--pos-success-hover); color: white; }
.btn-danger { background: var(--pos-danger); color: white; }
.btn-danger:hover { background: var(--pos-danger-hover); color: white; }
.btn-info { background: var(--pos-info); color: white; }
.btn-info:hover { background: #0284c7; color: white; }
.btn-default {
    background: var(--pos-surface-2);
    color: var(--pos-text);
    border: 1px solid var(--pos-border);
}
.btn-default:hover { background: var(--pos-border); color: var(--pos-text); }

/* ==========================================
   SALES TABLE — FULLY AUTO-ADJUSTABLE
   ========================================== */
.sales_table_100 {
    width: 100%;
    border-collapse: collapse;
    border-spacing: 0;
}

#register {
    font-size: 13px;
    table-layout: auto;              /* ← KEY: browser auto-calculates widths */
    width: 100%;
    border-collapse: collapse;
}

#register thead th,
#register tbody td {
    padding: 10px 8px;
    border-bottom: 1px solid var(--pos-border);
    vertical-align: middle;
    color: var(--pos-text);
    word-wrap: break-word;
    overflow-wrap: break-word;
}

/* Header */
#register thead th {
    background: var(--pos-surface-2);
    color: var(--pos-text-muted);
    font-weight: 600;
    font-size: 11px;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    border-bottom: 2px solid var(--pos-border);
    position: relative;
    user-select: none;
    white-space: nowrap;
}

/* ---- PER-COLUMN AUTO BEHAVIOR ---- */

/* Col 1: Delete icon — shrink to content */
#register td:nth-child(1),
#register th:nth-child(1) {
    width: 1%;
    white-space: nowrap;
    text-align: center;
    padding: 8px 6px;
}

/* Col 2: Item Number — shrink to content, max 200px */
#register td:nth-child(2),
#register th:nth-child(2) {
    width: 1%;
    white-space: nowrap;
    max-width: 200px;
    overflow: hidden;
    text-overflow: ellipsis;
}

/* Col 3: Item Name — FLEX, takes ALL remaining space */
#register td:nth-child(3),
#register th:nth-child(3) {
    width: auto;
    text-align: left;
    line-height: 1.4;
    min-width: 150px;
}

/* Col 4: Price — shrink to content */
#register td:nth-child(4),
#register th:nth-child(4) {
    width: 1%;
    white-space: nowrap;
    text-align: right;
}

/* Col 5: Quantity — shrink to content */
#register td:nth-child(5),
#register th:nth-child(5) {
    width: 1%;
    white-space: nowrap;
    text-align: center;
}

/* Col 6: Discount — needs input space */
#register td:nth-child(6),
#register th:nth-child(6) {
    width: 1%;
    min-width: 130px;
    white-space: nowrap;
}

/* Col 7: Total — shrink to content */
#register td:nth-child(7),
#register th:nth-child(7) {
    width: 1%;
    white-space: nowrap;
    text-align: right;
    font-weight: 600;
}

/* Col 8: Update icon — shrink to content */
#register td:nth-child(8),
#register th:nth-child(8) {
    width: 1%;
    white-space: nowrap;
    text-align: center;
    padding: 8px 6px;
}

/* Row states */
#register tbody tr { transition: background 0.15s; }
#register tbody tr:hover { background: var(--pos-surface-2); }
#register tbody tr:last-child td { border-bottom: none; }

/* ---- Inputs inside cells ---- */
#register td .form-control,
#register td input[type="text"],
#register td input[type="number"] {
    width: 100%;
    min-width: 0;
    max-width: 100%;
    box-sizing: border-box;
    padding: 6px 8px;
    font-size: 12px;
}

/* Price input */
#register td:nth-child(4) .form-control {
    text-align: right;
    max-width: 100px;
    margin-left: auto;
}

/* Quantity input */
#register td:nth-child(5) .form-control {
    text-align: center;
    max-width: 80px;
    margin: 0 auto;
}

/* Discount input group */
#register td:nth-child(6) .input-group {
    display: flex;
    align-items: stretch;
    width: 100%;
    min-width: 0;
}

#register td:nth-child(6) .input-group .form-control {
    border-top-right-radius: 0;
    border-bottom-right-radius: 0;
    border-right: none;
    min-width: 0;
    flex: 1;
    padding: 6px;
}

#register td:nth-child(6) .input-group-btn {
    display: flex;
    flex-shrink: 0;
}

#register td:nth-child(6) .input-group-btn .btn {
    border-top-left-radius: 0;
    border-bottom-left-radius: 0;
    padding: 6px 8px;
    font-size: 10px;
    font-weight: 800;
    white-space: nowrap;
}

/* Total input (amount entry mode) */
#register td:nth-child(7) .form-control {
    text-align: right;
    max-width: 110px;
    margin-left: auto;
}

/* Item name input (TEMP items) */
#register td:nth-child(3) input { font-size: 12px; }

/* Second row (description/serial) */
#register tbody tr + tr td {
    padding-top: 4px;
    padding-bottom: 4px;
}

/* Empty cart state */
#register tbody tr td[colspan="8"] {
    padding: 0;
    border: none;
    width: 100%;
}

/* Column resizer handle (optional, user can drag) */
.col-resizer {
    position: absolute;
    right: 0;
    top: 0;
    bottom: 0;
    width: 6px;
    cursor: col-resize;
    user-select: none;
    background: transparent;
    z-index: 20;
    transition: background 0.15s;
}

.col-resizer:hover {
    background: var(--pos-primary);
    opacity: 0.4;
}

/* ===== General Form Controls ===== */
.form-control {
    width: 100%;
    padding: 8px 10px;
    border: 1px solid var(--pos-border);
    border-radius: var(--pos-radius-sm);
    font-size: 13px;
    background: var(--pos-surface);
    color: var(--pos-text);
    transition: all 0.15s;
    box-sizing: border-box;
}

.form-control:focus {
    outline: none;
    border-color: var(--pos-primary);
    box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.1);
}

.form-control.input-sm { padding: 6px 8px; font-size: 12px; height: auto; }

/* ===== Overall Sale Panel ===== */
#overall_sale {
    position: sticky;
    top: 16px;
    align-self: start;
    max-height: calc(100vh - 32px);
    overflow-y: auto;
    overflow-x: hidden;
    border: none;
    background: transparent;
    box-shadow: none;
    min-width: 0;
    width: 100%;
}

#overall_sale > .panel-body {
    background: var(--pos-surface);
    border: 1px solid var(--pos-border);
    border-radius: var(--pos-radius);
    box-shadow: var(--pos-shadow-md);
    padding: 20px;
}

#overall_sale::-webkit-scrollbar { width: 6px; }
#overall_sale::-webkit-scrollbar-track { background: transparent; }
#overall_sale::-webkit-scrollbar-thumb { background: var(--pos-border); border-radius: 3px; }

/* ===== Totals Section ===== */
#sale_totals, #payment_totals {
    margin-top: 16px;
    padding-top: 16px;
    border-top: 1px dashed var(--pos-border);
    width: 100%;
}

#sale_totals tr th, #payment_totals tr th {
    padding: 6px 0;
    font-weight: 500;
    color: var(--pos-text-muted);
    font-size: 13px;
    text-align: left;
}

#sale_totals tr th:last-child, #payment_totals tr th:last-child {
    color: var(--pos-text);
    font-weight: 600;
    text-align: right;
}

#sale_totals tr:last-child th {
    font-size: 22px !important;
    color: var(--pos-text) !important;
    font-weight: 800 !important;
    padding-top: 12px;
    border-top: 2px solid var(--pos-border);
}

#sale_amount_due { color: var(--pos-danger); font-weight: 700; }

/* ===== Payment Section ===== */
#payment_details {
    margin-top: 16px;
    padding-top: 16px;
    border-top: 1px dashed var(--pos-border);
}

#payment_details .sales_table_100 td {
    padding: 6px 0;
    font-size: 13px;
    color: var(--pos-text-muted);
}

#payment_details .sales_table_100 td:last-child { text-align: right; }

/* ===== Action Buttons ===== */
#buttons_sale {
    display: flex;
    gap: 10px;
    margin-top: 20px;
    flex-wrap: wrap;
}

#buttons_sale > div {
    flex: 1;
    justify-content: center;
    min-width: 100px;
    padding: 12px;
    font-size: 13px;
}

#finish_sale_button, #finish_invoice_quote_button, #add_payment_button {
    background: linear-gradient(135deg, var(--pos-success) 0%, #059669 100%);
    color: white;
    box-shadow: 0 4px 12px rgba(16, 185, 129, 0.3);
    font-weight: 700;
    padding: 14px 20px !important;
    font-size: 14px !important;
    cursor: pointer;
}

#finish_sale_button:hover, #finish_invoice_quote_button:hover, #add_payment_button:hover {
    transform: translateY(-1px);
    box-shadow: 0 6px 16px rgba(16, 185, 129, 0.4);
}

#cancel_sale_button { background: var(--pos-danger); color: white; cursor: pointer; }
#suspend_sale_button { background: var(--pos-warning); color: white; cursor: pointer; }

/* ===== Customer Section ===== */
#select_customer {
    background: var(--pos-surface-2);
    border-radius: var(--pos-radius-sm);
    padding: 16px;
    margin-bottom: 16px;
    border: 1px dashed var(--pos-border);
}

#select_customer label {
    display: block;
    margin-bottom: 8px !important;
    font-weight: 600;
    color: var(--pos-text-muted);
    font-size: 11px;
    text-transform: uppercase;
}

#customer { margin-bottom: 10px; font-size: 14px; padding: 10px 12px; }

/* ===== Alerts ===== */
.alert {
    border-radius: var(--pos-radius-sm);
    padding: 12px 16px;
    margin-bottom: 16px;
    border: none;
    font-size: 13px;
    font-weight: 500;
}

.alert-info {
    background: #e0f2fe;
    color: #075985;
    text-align: center;
    padding: 40px 20px;
    font-size: 15px;
    border-radius: var(--pos-radius);
}
.alert-danger { background: #fee2e2; color: #991b1b; }
.alert-warning { background: #fef3c7; color: #92400e; }
.alert-success { background: #d1fae5; color: #065f46; }

/* ===== Checkboxes ===== */
.checkbox {
    display: flex;
    align-items: center;
    gap: 8px;
    font-size: 13px;
    color: var(--pos-text);
    cursor: pointer;
    font-weight: 500;
    margin: 4px 0;
}

.checkbox input[type="checkbox"] {
    width: 16px;
    height: 16px;
    accent-color: var(--pos-primary);
    cursor: pointer;
}

/* ===== Reference Code Row ===== */
.reference-code-input-hidden { display: none; }

/* ===== Theme Toggle Button ===== */
.theme-toggle-btn {
    background: var(--pos-surface-2);
    border: 1px solid var(--pos-border);
    color: var(--pos-text);
    border-radius: 50%;
    width: 34px;
    height: 34px;
    padding: 0;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    transition: all 0.2s;
}

.theme-toggle-btn:hover { transform: rotate(180deg); background: var(--pos-border); }

/* ===== Responsive ===== */
@media (max-width: 1400px) {
    #register td:nth-child(3),
    #register th:nth-child(3) { min-width: 120px; }
    #register td:nth-child(6),
    #register th:nth-child(6) { min-width: 110px; }
}

@media (max-width: 1100px) {
    #register_wrapper > div:first-child { overflow-x: auto; }
    #register { min-width: 650px; }
}

@media (max-width: 768px) {
    #register_wrapper { padding: 8px; gap: 12px; }
    #overall_sale { position: static; max-height: none; }
    #register { min-width: 0; }
    #register thead { display: none; }
    #register tbody tr {
        display: block;
        background: var(--pos-surface);
        border: 1px solid var(--pos-border);
        border-radius: var(--pos-radius-sm);
        margin-bottom: 8px;
        padding: 10px;
    }
    #register tbody td {
        display: flex !important;
        justify-content: space-between;
        align-items: center;
        padding: 4px 0 !important;
        border: none;
        text-align: left !important;
        width: auto !important;
        min-width: 0 !important;
        max-width: none !important;
        white-space: normal !important;
    }
    #register tbody td:before {
        content: attr(data-label);
        font-weight: 600;
        color: var(--pos-text-muted);
        font-size: 10px;
        text-transform: uppercase;
        margin-right: 8px;
        flex-shrink: 0;
    }
    #register td:nth-child(4) .form-control,
    #register td:nth-child(5) .form-control,
    #register td:nth-child(7) .form-control {
        max-width: 120px;
    }
    #buttons_sale > div { min-width: 100%; }
    #mode_form .panel-body ul li.pull-right { margin-left: 0; }
}

/* ===== Scrollbar ===== */
::-webkit-scrollbar { width: 8px; height: 8px; }
::-webkit-scrollbar-track { background: var(--pos-bg); }
::-webkit-scrollbar-thumb { background: var(--pos-border); border-radius: 4px; }
::-webkit-scrollbar-thumb:hover { background: var(--pos-text-muted); }

/* ===== Animation ===== */
@keyframes slideIn {
    from { opacity: 0; transform: translateY(-4px); }
    to { opacity: 1; transform: translateY(0); }
}
#register tbody tr { animation: slideIn 0.2s ease-out; }
</style>

<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">

<!-- ============================================
     Alerts
     ============================================ -->
<?php if (isset($error)): ?>
    <div class="alert alert-danger" style="margin: 20px auto; max-width: 1600px;">
        <strong>Error:</strong> <?= esc($error) ?>
    </div>
<?php endif; ?>

<?php if (!empty($warning)): ?>
    <div class="alert alert-warning" style="margin: 20px auto; max-width: 1600px;">
        <strong>Warning:</strong> <?= esc($warning) ?>
    </div>
<?php endif; ?>

<?php if (isset($success)): ?>
    <div class="alert alert-success" style="margin: 20px auto; max-width: 1600px;">
        <strong>Success:</strong> <?= esc($success) ?>
    </div>
<?php endif; ?>

<!-- ============================================
     MAIN REGISTER WRAPPER
     ============================================ -->
<div id="register_wrapper">

    <!-- ========== LEFT SIDE: Items ========== -->
    <div>

        <!-- Top register controls -->
        <?= form_open("$controller_name/changeMode", ['id' => 'mode_form', 'class' => 'form-horizontal panel']) ?>
            <div class="panel-body form-group">
                <ul>
                    <li class="pull-left first_li">
                        <label class="control-label"><?= lang(ucfirst($controller_name) . '.mode') ?></label>
                    </li>
                    <li class="pull-left">
                        <?= form_dropdown('mode', $modes, $mode, ['onchange' => "$('#mode_form').submit();", 'class' => 'selectpicker show-menu-arrow', 'data-style' => 'btn-default btn-sm', 'data-width' => 'fit']) ?>
                    </li>

                    <?php if ($config['dinner_table_enable']) { ?>
                        <li class="pull-left first_li">
                            <label class="control-label"><?= lang(ucfirst($controller_name) . '.table') ?></label>
                        </li>
                        <li class="pull-left">
                            <?= form_dropdown('dinner_table', $empty_tables, $selected_table, ['onchange' => "$('#mode_form').submit();", 'class' => 'selectpicker show-menu-arrow', 'data-style' => 'btn-default btn-sm', 'data-width' => 'fit']) ?>
                        </li>
                    <?php } ?>

                    <?php if (count($stock_locations) > 1) { ?>
                        <li class="pull-left first_li">
                            <label class="control-label"><?= lang(ucfirst($controller_name) . '.stock_location') ?></label>
                        </li>
                        <li class="pull-left">
                            <?= form_dropdown('stock_location', $stock_locations, $stock_location, ['onchange' => "$('#mode_form').submit();", 'class' => 'selectpicker show-menu-arrow', 'data-style' => 'btn-default btn-sm', 'data-width' => 'fit']) ?>
                        </li>
                    <?php } ?>

                    <li class="pull-right">
                        <button class="btn btn-default btn-sm modal-dlg" id="show_suspended_sales_button" data-href="<?= esc("$controller_name/suspended") ?>"
                            title="<?= lang(ucfirst($controller_name) . '.suspended_sales') ?>">
                            <span class="glyphicon glyphicon-align-justify"></span>
                            <?= lang(ucfirst($controller_name) . '.suspended_sales') ?>
                        </button>
                    </li>

                    <?php
                    $employee = model(Employee::class);
                    if ($employee->has_grant('reports_sales', session('person_id'))) {
                    ?>
                        <li class="pull-right">
                            <?= anchor(
                                "$controller_name/manage",
                                '<span class="glyphicon glyphicon-list-alt"></span>' . lang(ucfirst($controller_name) . '.takings'),
                                array('class' => 'btn btn-primary btn-sm', 'id' => 'sales_takings_button', 'title' => lang(ucfirst($controller_name) . '.takings'))
                            ) ?>
                        </li>
                    <?php } ?>

                    <li class="pull-right">
                        <button type="button" class="theme-toggle-btn" onclick="toggleTheme()" title="Toggle Dark Mode">
                            <span class="glyphicon glyphicon-adjust"></span>
                        </button>
                    </li>
                </ul>
            </div>
        <?= form_close() ?>

        <?php $tabindex = 0; ?>

        <!-- Item Search -->
        <?= form_open("$controller_name/add", ['id' => 'add_item_form', 'class' => 'form-horizontal panel']) ?>
            <div class="panel-body form-group">
                <ul>
                    <li class="pull-left first_li">
                        <label for="item" class="control-label"><?= lang(ucfirst($controller_name) . '.find_or_scan_item_or_receipt') ?></label>
                    </li>
                    <li class="pull-left">
                        <?= form_input([
                            'name' => 'item',
                            'id' => 'item',
                            'class' => 'form-control input-sm',
                            'tabindex' => ++$tabindex,
                            'placeholder' => '🔍 Scan barcode or type item name...'
                        ]) ?>
                        <span class="ui-helper-hidden-accessible" role="status"></span>
                    </li>
                    <li class="pull-right">
                        <button id="new_item_button" class="btn btn-info btn-sm pull-right modal-dlg"
                            data-btn-new="<?= lang('Common.new') ?>"
                            data-btn-submit="<?= lang('Common.submit') ?>"
                            data-href="<?= "items/view" ?>"
                            title="<?= lang(ucfirst($controller_name) . ".new_item") ?>">
                            <span class="glyphicon glyphicon-tag"></span>
                            <?= lang(ucfirst($controller_name) . ".new_item") ?>
                        </button>
                    </li>
                </ul>
            </div>
        <?= form_close() ?>

        <!-- ========== Sale Items List ========== -->
        <div class="panel">
            <table class="sales_table_100" id="register">
                <thead>
                    <tr>
                        <th><?= lang('Common.delete') ?></th>
                        <th><?= lang(ucfirst($controller_name) . '.item_number') ?></th>
                        <th><?= lang(ucfirst($controller_name) . '.item_name') ?></th>
                        <th><?= lang(ucfirst($controller_name) . '.price') ?></th>
                        <th><?= lang(ucfirst($controller_name) . '.quantity') ?></th>
                        <th><?= lang(ucfirst($controller_name) . '.discount') ?></th>
                        <th><?= lang(ucfirst($controller_name) . '.total') ?></th>
                        <th><?= lang(ucfirst($controller_name) . '.update') ?></th>
                    </tr>
                </thead>

                <tbody id="cart_contents">
                    <?php if (count($cart) == 0) { ?>
                        <tr>
                            <td colspan="8">
                                <div class="alert alert-info">
                                    🛒 <?= lang(ucfirst($controller_name) . '.no_items_in_cart') ?>
                                </div>
                            </td>
                        </tr>
                    <?php
                    } else {
                        foreach (array_reverse($cart, true) as $line => $item) {
                    ?>
                            <?= form_open("$controller_name/editItem/$line", ['class' => 'form-horizontal', 'id' => "cart_$line"]) ?>
                                <tr>
                                    <td data-label="<?= lang('Common.delete') ?>">
                                        <?php
                                        echo anchor("$controller_name/deleteItem/$line", '<span class="glyphicon glyphicon-trash"></span>');
                                        echo form_hidden('location', (string)$item['item_location']);
                                        echo form_input(['type' => 'hidden', 'name' => 'item_id', 'value' => $item['item_id']]);
                                        ?>
                                    </td>
                                    <?php if ($item['item_type'] == ITEM_TEMP) { ?>
                                        <td data-label="<?= lang(ucfirst($controller_name) . '.item_number') ?>"><?= form_input(['name' => 'item_number', 'id' => 'item_number', 'class' => 'form-control input-sm', 'value' => $item['item_number'], 'tabindex' => ++$tabindex]) ?></td>
                                        <td data-label="<?= lang(ucfirst($controller_name) . '.item_name') ?>" style="text-align: center;">
                                            <?= form_input(['name' => 'name', 'id' => 'name', 'class' => 'form-control input-sm', 'value' => $item['name'], 'tabindex' => ++$tabindex]) ?>
                                        </td>
                                    <?php } else { ?>
                                        <td data-label="<?= lang(ucfirst($controller_name) . '.item_number') ?>"><?= esc($item['item_number']) ?></td>
                                        <td data-label="<?= lang(ucfirst($controller_name) . '.item_name') ?>" style="text-align: left;">
                                            <?= esc($item['name']) . ' ' . esc(implode(' ', [$item['attribute_values'], $item['attribute_dtvalues']])) ?>
                                            <br>
                                            <?php if ($item['stock_type'] == '0'): echo '[' . to_quantity_decimals($item['in_stock']) . ' in ' . esc($item['stock_name']) . ']';
                                            endif; ?>
                                        </td>
                                    <?php } ?>

                                    <td data-label="<?= lang(ucfirst($controller_name) . '.price') ?>">
                                        <?php
                                        if ($items_module_allowed && $change_price) {
                                            echo form_input(['name' => 'price', 'class' => 'form-control input-sm', 'value' => to_currency_no_money($item['price']), 'tabindex' => ++$tabindex, 'onClick' => 'this.select();']);
                                        } else {
                                            echo to_currency($item['price']);
                                            echo form_hidden('price', to_currency_no_money($item['price']));
                                        }
                                        ?>
                                    </td>

                                    <td data-label="<?= lang(ucfirst($controller_name) . '.quantity') ?>">
                                        <?php
                                        if ($item['is_serialized']) {
                                            echo to_quantity_decimals($item['quantity']);
                                            echo form_hidden('quantity', $item['quantity']);
                                        } else {
                                            echo form_input(['name' => 'quantity', 'class' => 'form-control input-sm', 'value' => to_quantity_decimals($item['quantity']), 'tabindex' => ++$tabindex, 'onClick' => 'this.select();']);
                                        }
                                        ?>
                                    </td>

                                    <td data-label="<?= lang(ucfirst($controller_name) . '.discount') ?>">
                                        <div class="input-group">
                                            <?= form_input(['name' => 'discount', 'class' => 'form-control input-sm', 'value' => $item['discount_type'] ? to_currency_no_money($item['discount']) : to_decimals($item['discount']), 'tabindex' => ++$tabindex, 'onClick' => 'this.select();']) ?>
                                            <span class="input-group-btn">
                                                <?= form_checkbox(['id' => 'discount_toggle', 'name' => 'discount_toggle', 'value' => 1, 'data-toggle' => "toggle", 'data-size' => 'small', 'data-onstyle' => 'success', 'data-on' => '<b>' . $config['currency_symbol'] . '</b>', 'data-off' => '<b>%</b>', 'data-line' => $line, 'checked' => $item['discount_type'] == 1]) ?>
                                            </span>
                                        </div>
                                    </td>

                                    <td data-label="<?= lang(ucfirst($controller_name) . '.total') ?>">
                                        <?php
                                        if ($item['item_type'] == ITEM_AMOUNT_ENTRY) {
                                            echo form_input(['name' => 'discounted_total', 'class' => 'form-control input-sm', 'value' => to_currency_no_money($item['discounted_total']), 'tabindex' => ++$tabindex, 'onClick' => 'this.select();']);
                                        } else {
                                            echo to_currency($item['discounted_total']);
                                        }
                                        ?>
                                    </td>

                                    <td data-label="<?= lang(ucfirst($controller_name) . '.update') ?>">
                                        <a href="javascript:document.getElementById('<?= "cart_$line" ?>').submit();" title="<?= lang(ucfirst($controller_name) . '.update') ?>">
                                            <span class="glyphicon glyphicon-refresh"></span>
                                        </a>
                                    </td>
                                </tr>
                                <tr>
                                    <?php if ($item['item_type'] == ITEM_TEMP) { ?>
                                        <td><?= form_input(['type' => 'hidden', 'name' => 'item_id', 'value' => $item['item_id']]) ?></td>
                                        <td style="text-align: center;" colspan="6">
                                            <?= form_input(['name' => 'item_description', 'id' => 'item_description', 'class' => 'form-control input-sm', 'value' => $item['description'], 'tabindex' => ++$tabindex]) ?>
                                        </td>
                                        <td> </td>
                                    <?php } else { ?>
                                        <td>&nbsp;</td>
                                        <?php if ($item['allow_alt_description']) { ?>
                                            <td style="color: var(--pos-text-muted);"><?= lang(ucfirst($controller_name) . '.description_abbrv') ?></td>
                                        <?php } ?>

                                        <td colspan="2" style="text-align: left;">
                                            <?php
                                            if ($item['allow_alt_description']) {
                                                echo form_input(['name' => 'description', 'class' => 'form-control input-sm', 'value' => $item['description'], 'onClick' => 'this.select();']);
                                            } else {
                                                if ($item['description'] != '') {
                                                    echo esc($item['description']);
                                                    echo form_hidden('description', $item['description']);
                                                } else {
                                                    echo lang(ucfirst($controller_name) . '.no_description');
                                                    echo form_hidden('description', '');
                                                }
                                            }
                                            ?>
                                        </td>
                                        <td>&nbsp;</td>
                                        <td style="color: var(--pos-text-muted);">
                                            <?php
                                            if ($item['is_serialized']) {
                                                echo lang(ucfirst($controller_name) . '.serial');
                                            }
                                            ?>
                                        </td>
                                        <td colspan="4" style="text-align: left;">
                                            <?php
                                            if ($item['is_serialized']) {
                                                echo form_input(['name' => 'serialnumber', 'class' => 'form-control input-sm', 'value' => $item['serialnumber'], 'onClick' => 'this.select();']);
                                            } else {
                                                echo form_hidden('serialnumber', '');
                                            }
                                            ?>
                                        </td>
                                    <?php } ?>
                                </tr>
                            <?= form_close() ?>
                    <?php
                        }
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- ========== RIGHT SIDE: Overall Sale ========== -->
    <div id="overall_sale" class="panel">
        <div class="panel-body">
            <?= form_open("$controller_name/selectCustomer", ['id' => 'select_customer_form', 'class' => 'form-horizontal']) ?>
                <?php if (isset($customer)) { ?>
                    <table class="sales_table_100">
                        <tr>
                            <th style="width: 55%;"><?= lang(ucfirst($controller_name) . '.customer') ?></th>
                            <th style="width: 45%; text-align: right;"><?= anchor("customers/view/$customer_id", esc($customer), ['class' => 'modal-dlg', 'data-btn-submit' => lang('Common.submit'), 'title' => lang('Customers.update')]) ?></th>
                        </tr>
                        <?php if (!empty($customer_email)) { ?>
                            <tr>
                                <th style="width: 55%;"><?= lang(ucfirst($controller_name) . '.customer_email') ?></th>
                                <th style="width: 45%; text-align: right;"><?= esc($customer_email) ?></th>
                            </tr>
                        <?php } ?>
                        <?php if (!empty($customer_address)) { ?>
                            <tr>
                                <th style="width: 55%;"><?= lang(ucfirst($controller_name) . '.customer_address') ?></th>
                                <th style="width: 45%; text-align: right;"><?= esc($customer_address) ?></th>
                            </tr>
                        <?php } ?>
                        <?php if (!empty($customer_location)) { ?>
                            <tr>
                                <th style="width: 55%;"><?= lang(ucfirst($controller_name) . '.customer_location') ?></th>
                                <th style="width: 45%; text-align: right;"><?= esc($customer_location) ?></th>
                            </tr>
                        <?php } ?>
                        <tr>
                            <th style="width: 55%;"><?= lang(ucfirst($controller_name) . '.customer_discount') ?></th>
                            <th style="width: 45%; text-align: right;"><?= ($customer_discount_type == FIXED) ? to_currency($customer_discount) : $customer_discount . '%' ?></th>
                        </tr>
                        <?php if ($config['customer_reward_enable']): ?>
                            <?php if (!empty($customer_rewards)) { ?>
                                <tr>
                                    <th style="width: 55%;"><?= lang(ucfirst($controller_name) . '.rewards_package') ?></th>
                                    <th style="width: 45%; text-align: right;"><?= esc($customer_rewards['package_name']) ?></th>
                                </tr>
                                <tr>
                                    <th style="width: 55%;"><?= lang('Customers.available_points') ?></th>
                                    <th style="width: 45%; text-align: right;"><?= esc($customer_rewards['points']) ?></th>
                                </tr>
                            <?php } ?>
                        <?php endif; ?>
                        <tr>
                            <th style="width: 55%;"><?= lang(ucfirst($controller_name) . '.customer_total') ?></th>
                            <th style="width: 45%; text-align: right;"><?= to_currency($customer_total) ?></th>
                        </tr>
                        <?php if (!empty($mailchimp_info)) { ?>
                            <tr>
                                <th style="width: 55%;"><?= lang(ucfirst($controller_name) . '.customer_mailchimp_status') ?></th>
                                <th style="width: 45%; text-align: right;"><?= esc($mailchimp_info['status']) ?></th>
                            </tr>
                        <?php } ?>
                    </table>

                    <?= anchor(
                        "$controller_name/removeCustomer",
                        '<span class="glyphicon glyphicon-remove"></span>' . lang('Common.remove') . ' ' . lang('Customers.customer'),
                        ['class' => 'btn btn-danger btn-sm', 'id' => 'remove_customer_button', 'title' => lang('Common.remove') . ' ' . lang('Customers.customer')]
                    )
                    ?>
                <?php } else { ?>
                    <div class="form-group" id="select_customer">
                        <label id="customer_label" for="customer" class="control-label">
                            <?= lang(ucfirst($controller_name) . '.select_customer') . esc(" $customer_required") ?>
                        </label>
                        <?= form_input(['name' => 'customer', 'id' => 'customer', 'class' => 'form-control input-sm', 'value' => lang(ucfirst($controller_name) . '.start_typing_customer_name'), 'placeholder' => '👤 Customer name or phone...']) ?>

                        <div style="display:flex; gap:8px; margin-top:8px;">
                            <button class="btn btn-info btn-sm modal-dlg" data-btn-submit="<?= lang('Common.submit') ?>" data-href="<?= "customers/view" ?>" title="<?= lang(ucfirst($controller_name) . ".new_customer") ?>" style="flex:1;">
                                <span class="glyphicon glyphicon-user"></span>
                                <?= lang(ucfirst($controller_name) . ".new_customer") ?>
                            </button>
                            <button class="btn btn-default btn-sm modal-dlg" id="show_keyboard_help" data-href="<?= esc("$controller_name/salesKeyboardHelp") ?>" title="<?= lang(ucfirst($controller_name) . '.key_title') ?>">
                                <span class="glyphicon glyphicon-share-alt"></span>
                            </button>
                        </div>
                    </div>
                <?php } ?>
            <?= form_close() ?>

            <!-- Sale Totals -->
            <table class="sales_table_100" id="sale_totals">
                <tr>
                    <th style="width: 55%;"><?= lang(ucfirst($controller_name) . '.quantity_of_items', [$item_count]) ?></th>
                    <th style="width: 45%; text-align: right;"><?= $total_units ?></th>
                </tr>
                <tr>
                    <th style="width: 55%;"><?= lang(ucfirst($controller_name) . '.sub_total') ?></th>
                    <th style="width: 45%; text-align: right;"><?= to_currency($subtotal) ?></th>
                </tr>
                <?php foreach ($taxes as $tax_group_index => $tax) { ?>
                    <tr>
                        <th style="width: 55%;"><?= (float)$tax['tax_rate'] . '% ' . esc($tax['tax_group']) ?></th>
                        <th style="width: 45%; text-align: right;"><?= to_currency_tax($tax['sale_tax_amount']) ?></th>
                    </tr>
                <?php } ?>
                <tr>
                    <th style="width: 55%; font-size: 150%"><?= lang(ucfirst($controller_name) . '.total') ?></th>
                    <th style="width: 45%; font-size: 150%; text-align: right;"><span id="sale_total"><?= to_currency($total) ?></span></th>
                </tr>
            </table>

            <?php if (count($cart) > 0) { ?>
                <!-- Payment Totals -->
                <table class="sales_table_100" id="payment_totals">
                    <tr>
                        <th style="width: 55%;"><?= lang(ucfirst($controller_name) . '.payments_total') ?></th>
                        <th style="width: 45%; text-align: right;"><?= to_currency($payments_total) ?></th>
                    </tr>
                    <tr>
                        <th style="width: 55%; font-size: 120%"><?= lang(ucfirst($controller_name) . '.amount_due') ?></th>
                        <th style="width: 45%; font-size: 120%; text-align: right;"><span id="sale_amount_due"><?= to_currency($amount_due) ?></span></th>
                    </tr>
                </table>

                <div id="payment_details">
                    <?php if ($payments_cover_total) { ?>
                        <?= form_open("$controller_name/addPayment", ['id' => 'add_payment_form', 'class' => 'form-horizontal']) ?>
                            <input type="hidden" name="complete_after_payment" value="0">
                            <table class="sales_table_100">
                                <tr>
                                    <td><?= lang(ucfirst($controller_name) . '.payment') ?></td>
                                    <td>
                                        <?= form_dropdown('payment_type', $payment_options, $selected_payment_type, ['id' => 'payment_types', 'class' => 'selectpicker show-menu-arrow', 'data-style' => 'btn-default btn-sm', 'data-width' => '100%', 'disabled' => 'disabled']) ?>
                                    </td>
                                </tr>
                                <tr>
                                    <td><span id="amount_tendered_label"><?= lang(ucfirst($controller_name) . '.amount_tendered') ?></span></td>
                                    <td>
                                        <?= form_input([
                                            'name' => 'amount_tendered',
                                            'id' => 'amount_tendered',
                                            'class' => 'form-control input-sm disabled',
                                            'disabled' => 'disabled',
                                            'value' => '0',
                                            'size' => '5',
                                            'tabindex' => ++$tabindex,
                                            'onClick' => 'this.select();'
                                        ]) ?>
                                    </td>
                                </tr>
                                <tr class="reference-code-input reference-code-input-hidden">
                                    <td style="padding-top: 8px;"><span id='reference_code_label'><?= lang('Sales.reference_code') ?></span></td>
                                    <td style="padding-top: 8px;">
                                        <?php echo form_input(array(
                                            'name'      => 'reference_code',
                                            'id'        => 'reference_code',
                                            'class'     => 'form-control input-sm non-giftcard-input',
                                            'disabled'  => true,
                                            'value'     => '',
                                            'size'      => 5,
                                            'tabindex'  => ++$tabindex,
                                            'onClick'   => 'this.select();'
                                        ));
                                        ?>
                                    </td>
                                </tr>
                            </table>
                        <?= form_close() ?>

                        <?php
                        if ($pos_mode) {
                            $due_payment = false;

                            if (count($payments) > 0) {
                                foreach ($payments as $payment_id => $payment) {
                                    if ($payment['payment_type'] == lang(ucfirst($controller_name) . '.due')) {
                                        $due_payment = true;
                                    }
                                }
                            }

                            if (!$due_payment || ($due_payment && isset($customer))) {
                        ?>
                                <div class="btn btn-sm btn-success pull-right" id="finish_sale_button" tabindex="<?= ++$tabindex ?>">
                                    <span class="glyphicon glyphicon-ok-circle"></span>
                                    <?= lang(ucfirst($controller_name) . '.complete_sale') ?>
                                </div>
                        <?php
                            }
                        }
                        ?>
                    <?php } else { ?>
                        <?= form_open("$controller_name/addPayment", ['id' => 'add_payment_form', 'class' => 'form-horizontal']) ?>
                            <input type="hidden" name="complete_after_payment" value="0">
                            <table class="sales_table_100">
                                <tr>
                                    <td><?= lang(ucfirst($controller_name) . '.payment') ?></td>
                                    <td>
                                        <?= form_dropdown('payment_type', $payment_options, $selected_payment_type, ['id' => 'payment_types', 'class' => 'selectpicker show-menu-arrow', 'data-style' => 'btn-default btn-sm', 'data-width' => '100%']) ?>
                                    </td>
                                </tr>
                                <tr>
                                    <td><span id="amount_tendered_label"><?= lang(ucfirst($controller_name) . '.amount_tendered') ?></span></td>
                                    <td>
                                        <?= form_input(['name' => 'amount_tendered', 'id' => 'amount_tendered', 'class' => 'form-control input-sm non-giftcard-input', 'value' => to_currency_no_money($amount_due), 'size' => '5', 'tabindex' => ++$tabindex, 'onClick' => 'this.select();']) ?>
                                        <?= form_input(['name' => 'amount_tendered', 'id' => 'amount_tendered', 'class' => 'form-control input-sm giftcard-input', 'disabled' => true, 'value' => to_currency_no_money($amount_due), 'size' => '5', 'tabindex' => ++$tabindex]) ?>
                                    </td>
                                </tr>
                                <tr class="reference-code-input reference-code-input-hidden">
                                    <td style="padding-top: 8px;"><span id='reference_code_label'><?= lang('Sales.reference_code') ?></span></td>
                                    <td style="padding-top: 8px;">
                                        <?php echo form_input(array(
                                            'name'      => 'reference_code',
                                            'id'        => 'reference_code',
                                            'class'     => 'form-control input-sm non-giftcard-input',
                                            'disabled'  => true,
                                            'value'     => '',
                                            'size'      => 5,
                                            'tabindex'  => ++$tabindex,
                                            'onClick'   => 'this.select();'
                                        ));
                                        ?>
                                    </td>
                                </tr>
                            </table>
                        <?= form_close() ?>

                        <div class="btn btn-sm btn-success pull-right" id="add_payment_button" tabindex="<?= ++$tabindex ?>">
                            <span class="glyphicon glyphicon-credit-card"></span>
                            <?= lang(ucfirst($controller_name) . '.add_payment') ?>
                        </div>
                    <?php } ?>

                    <?php if (count($payments) > 0) { ?>
                        <table class="sales_table_100" id="register_payments" style="margin-top:16px;">
                            <thead>
                                <tr>
                                    <th style="width: 10%;"><?= lang('Common.delete') ?></th>
                                    <th style="width: 60%;"><?= lang(ucfirst($controller_name) . '.payment_type') ?></th>
                                    <th style="width: 20%;"><?= lang(ucfirst($controller_name) . '.payment_amount') ?></th>
                                </tr>
                            </thead>

                            <tbody id="payment_contents">
                                <?php foreach ($payments as $payment_id => $payment) { ?>
                                    <tr>
                                        <td><?= anchor("$controller_name/deletePayment/" . esc(base64url_encode($payment_id), 'url'), '<span class="glyphicon glyphicon-trash"></span>') ?></td>
                                        <td><?= $payment['payment_type'] ?></td>
                                        <td style="text-align: right;"><?= to_currency($payment['payment_amount']) ?></td>
                                    </tr>
                                <?php } ?>
                            </tbody>
                        </table>
                    <?php } ?>
                </div>

                <!-- Action Buttons -->
                <?= form_open("$controller_name/cancel", ['id' => 'buttons_form']) ?>
                <div class="form-group" id="buttons_sale">
                    <div class="btn btn-sm btn-default" id="suspend_sale_button">
                        <span class="glyphicon glyphicon-align-justify"></span>
                        <?= lang(ucfirst($controller_name) . '.suspend_sale') ?>
                    </div>
                    <?php if (!$pos_mode && isset($customer)) { ?>
                        <div class="btn btn-sm btn-success" id="finish_invoice_quote_button">
                            <span class="glyphicon glyphicon-ok"></span>
                            <?= esc($mode_label) ?>
                        </div>
                    <?php } ?>

                    <div class="btn btn-sm btn-danger" id="cancel_sale_button">
                        <span class="glyphicon glyphicon-remove"></span>
                        <?= lang(ucfirst($controller_name) . '.cancel_sale') ?>
                    </div>
                </div>
                <?= form_close() ?>

                <?php if ($payments_cover_total || !$pos_mode) { ?>
                    <div class="container-fluid" style="padding:0; margin-top:16px;">
                        <div class="no-gutter row">
                            <div class="form-group form-group-sm">
                                <div class="col-xs-12">
                                    <?= form_label(lang('Common.comments'), 'comments', ['class' => 'control-label', 'id' => 'comment_label', 'for' => 'comment']) ?>
                                    <?= form_textarea(['name' => 'comment', 'id' => 'comment', 'class' => 'form-control input-sm', 'value' => $comment, 'rows' => '2', 'placeholder' => 'Optional note...']) ?>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-group form-group-sm">
                                <div class="col-xs-6">
                                    <label for="sales_print_after_sale" class="control-label checkbox">
                                        <?= form_checkbox(['name' => 'sales_print_after_sale', 'id' => 'sales_print_after_sale', 'value' => 1, 'checked' => $print_after_sale]) ?>
                                        <?= lang(ucfirst($controller_name) . '.print_after_sale') ?>
                                    </label>
                                </div>

                                <?php if (!empty($customer_email)) { ?>
                                    <div class="col-xs-6">
                                        <label for="email_receipt" class="control-label checkbox">
                                            <?= form_checkbox(['name' => 'email_receipt', 'id' => 'email_receipt', 'value' => 1, 'checked' => $email_receipt]) ?>
                                            <?= lang(ucfirst($controller_name) . '.email_receipt') ?>
                                        </label>
                                    </div>
                                <?php } ?>
                                <?php if ($mode == 'sale_work_order') { ?>
                                    <div class="col-xs-6">
                                        <label for="price_work_orders" class="control-label checkbox">
                                            <?= form_checkbox(['name' => 'price_work_orders', 'id' => 'price_work_orders', 'value' => 1, 'checked' => $price_work_orders]) ?>
                                            <?= lang(ucfirst($controller_name) . '.include_prices') ?>
                                        </label>
                                    </div>
                                <?php } ?>
                            </div>
                        </div>
                        <?php if (($mode == 'sale_invoice') && $config['invoice_enable']) { ?>
                            <div class="row">
                                <div class="form-group form-group-sm">
                                    <div class="col-xs-6">
                                        <label for="sales_invoice_number" class="control-label checkbox">
                                            <?= lang(ucfirst($controller_name) . '.invoice_enable') ?>
                                        </label>
                                    </div>

                                    <div class="col-xs-6">
                                        <div class="input-group input-group-sm">
                                            <span class="input-group-addon input-sm">#</span>
                                            <?= form_input(['name' => 'sales_invoice_number', 'id' => 'sales_invoice_number', 'class' => 'form-control input-sm', 'value' => $invoice_number]) ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php } ?>
                    </div>
                <?php } ?>
            <?php } ?>
        </div>
    </div>
</div>

<!-- ============================================
     JavaScript
     ============================================ -->
<script type="text/javascript">
    // ========== Theme Toggle ==========
    function initTheme() {
        const saved = localStorage.getItem('pos-theme') || 'light';
        document.documentElement.setAttribute('data-theme', saved);
    }

    function toggleTheme() {
        const current = document.documentElement.getAttribute('data-theme');
        const next = current === 'dark' ? 'light' : 'dark';
        document.documentElement.setAttribute('data-theme', next);
        localStorage.setItem('pos-theme', next);
    }

    initTheme();

    // ========== Keyboard Shortcuts ==========
    const keyboardShortcuts = <?= json_encode($keyboardShortcuts ?? []) ?>;
    const paymentsCoverTotal = <?= json_encode((bool) $payments_cover_total) ?>;
    const referenceCodePaymentTypes = <?= json_encode($reference_code_payment_types ?? []) ?>;
    const shortcutCodes = {
        items: keyboardShortcuts?.items?.code ?? null,
        customers: keyboardShortcuts?.customers?.code ?? null,
        suspend: keyboardShortcuts?.suspend?.code ?? null,
        suspended: keyboardShortcuts?.suspended?.code ?? null,
        amount: keyboardShortcuts?.amount?.code ?? null,
        payment: keyboardShortcuts?.payment?.code ?? null,
        complete: keyboardShortcuts?.complete?.code ?? null,
        finish: keyboardShortcuts?.finish?.code ?? null,
        help: keyboardShortcuts?.help?.code ?? null,
        cancel: keyboardShortcuts?.cancel?.code ?? null
    };

    $(document).ready(function() {
        const redirect = function() {
            window.location.href = "<?= site_url('sales'); ?>";
        };

        $("#remove_customer_button").click(function() {
            $.post("<?= site_url('sales/removeCustomer'); ?>", redirect);
        });

        $(".delete_item_button").click(function() {
            const item_id = $(this).data('item-id');
            $.post("<?= site_url('sales/deleteItem/'); ?>" + item_id, redirect);
        });

        $(".delete_payment_button").click(function() {
            const item_id = $(this).data('payment-id');
            $.post("<?= site_url('sales/deletePayment/'); ?>" + item_id, redirect);
        });

        $("input[name='item_number']").change(function() {
            const item_id = $(this).parents('tr').find("input[name='item_id']").val();
            const item_number = $(this).val();
            $.ajax({
                url: "<?= site_url('sales/changeItemNumber') ?>",
                method: 'post',
                data: { 'item_id': item_id, 'item_number': item_number },
                dataType: 'json'
            });
        });

        $("input[name='name']").change(function() {
            const item_id = $(this).parents('tr').find("input[name='item_id']").val();
            const item_name = $(this).val();
            $.ajax({
                url: "<?= site_url('sales/changeItemName') ?>",
                method: 'post',
                data: { 'item_id': item_id, 'item_name': item_name },
                dataType: 'json'
            });
        });

        $("input[name='item_description']").change(function() {
            const item_id = $(this).parents('tr').find("input[name='item_id']").val();
            const item_description = $(this).val();
            $.ajax({
                url: "<?= site_url('sales/changeItemDescription') ?>",
                method: 'post',
                data: { 'item_id': item_id, 'item_description': item_description },
                dataType: 'json'
            });
        });

        $('#item').focus();

        $('#item').blur(function() {
            $(this).val("<?= lang(ucfirst($controller_name) . '.start_typing_item_name') ?>");
        });

        $('#item').autocomplete({
            source: "<?= esc("$controller_name/itemSearch") ?>",
            minChars: 0,
            autoFocus: false,
            delay: 500,
            select: function(a, ui) {
                $(this).val(ui.item.value);
                $('#add_item_form').submit();
                return false;
            }
        });

        $('#item').keypress(function(e) {
            if (e.which == 13) {
                $('#add_item_form').submit();
                return false;
            }
        });

        const clear_fields = function() {
            if ($(this).val().match("<?= lang(ucfirst($controller_name) . '.start_typing_item_name') . '|' . lang(ucfirst($controller_name) . '.start_typing_customer_name') ?>")) {
                $(this).val('');
            }
        };

        $('#item, #customer').click(clear_fields).dblclick(function(event) {
            $(this).autocomplete('search');
        });

        $('#customer').blur(function() {
            $(this).val("<?= lang(ucfirst($controller_name) . '.start_typing_customer_name') ?>");
        });

        $('#customer').autocomplete({
            source: "<?= site_url('customers/suggest') ?>",
            minChars: 0,
            delay: 10,
            select: function(a, ui) {
                $(this).val(ui.item.value);
                $('#select_customer_form').submit();
                return false;
            }
        });

        $('#customer').keypress(function(e) {
            if (e.which == 13) {
                $('#select_customer_form').submit();
                return false;
            }
        });

        $('.giftcard-input').autocomplete({
            source: "<?= site_url('giftcards/suggest') ?>",
            minChars: 0,
            delay: 10,
            select: function(a, ui) {
                $(this).val(ui.item.value);
                $('#add_payment_form').submit();
                return false;
            }
        });

        $('#comment').keyup(function() {
            $.post("<?= esc(site_url("$controller_name/setComment")) ?>", {
                comment: $('#comment').val()
            });
        });

        <?php if ($config['invoice_enable']) { ?>
            $('#sales_invoice_number').keyup(function() {
                $.post("<?= esc(site_url("$controller_name/setInvoiceNumber")) ?>", {
                    sales_invoice_number: $('#sales_invoice_number').val()
                });
            });
        <?php } ?>

        $('#sales_print_after_sale').change(function() {
            $.post("<?= esc(site_url("$controller_name/setPrintAfterSale")) ?>", {
                sales_print_after_sale: $(this).is(':checked')
            });
        });

        $('#price_work_orders').change(function() {
            $.post("<?= esc(site_url("$controller_name/setPriceWorkOrders")) ?>", {
                price_work_orders: $(this).is(':checked')
            });
        });

        $('#email_receipt').change(function() {
            $.post("<?= esc(site_url("$controller_name/setEmailReceipt")) ?>", {
                email_receipt: $(this).is(':checked')
            });
        });

        $('#finish_sale_button').click(function() {
            $('#buttons_form').attr('action', "<?= "$controller_name/complete" ?>");
            $('#buttons_form').submit();
        });

        $('#finish_invoice_quote_button').click(function() {
            $('#buttons_form').attr('action', "<?= "$controller_name/complete" ?>");
            $('#buttons_form').submit();
        });

        $('#suspend_sale_button').click(function() {
            $('#buttons_form').attr('action', "<?= site_url("$controller_name/suspend") ?>");
            $('#buttons_form').submit();
        });

        $('#cancel_sale_button').click(function() {
            if (confirm("<?= lang(ucfirst($controller_name) . '.confirm_cancel_sale') ?>")) {
                $('#buttons_form').attr('action', "<?= site_url("$controller_name/cancel") ?>");
                $('#buttons_form').submit();
            }
        });

        $('#add_payment_button').click(function() {
            $('#add_payment_form').find('input[name="complete_after_payment"]').val('0');
            $('#add_payment_form').submit();
        });

        $('#payment_types').change(check_payment_type).ready(check_payment_type);

        $('#cart_contents input').keypress(function(event) {
            if (event.which == 13) {
                $(this).parents('tr').prevAll('form:first').submit();
            }
        });

        $('#amount_tendered').keypress(function(event) {
            if (event.which == 13) {
                $('#add_payment_form').submit();
            }
        });

        $('#finish_sale_button').keypress(function(event) {
            if (event.which == 13) {
                $('#finish_sale_form').submit();
            }
        });

        dialog_support.init('a.modal-dlg, button.modal-dlg');

        table_support.handle_submit = function(resource, response, stay_open) {
            $.notify({
                message: response.message
            }, {
                type: response.success ? 'success' : 'danger'
            })

            if (response.success) {
                if (resource.match(/customers$/)) {
                    $('#customer').val(response.id);
                    $('#select_customer_form').submit();
                } else {
                    const $stock_location = $("select[name='stock_location']").val();
                    $('#item_location').val($stock_location);
                    $('#item').val(response.id);
                    if (stay_open) {
                        $('#add_item_form').ajaxSubmit();
                    } else {
                        $('#add_item_form').submit();
                    }
                }
            }
        }

        $('[name="price"],[name="quantity"],[name="discount"],[name="description"],[name="serialnumber"],[name="discounted_total"]').change(function() {
            $(this).parents('tr').prevAll('form:first').submit()
        });

        $('[name="discount_toggle"]').change(function() {
            const input = $('<input>').attr('type', 'hidden').attr('name', 'discount_type').val(($(this).prop('checked')) ? 1 : 0);
            $('#cart_' + $(this).attr('data-line')).append($(input));
            $('#cart_' + $(this).attr('data-line')).submit();
        });
    });

    function check_payment_type() {
        const cash_mode = <?= json_encode($cash_mode) ?>;
        const paymentType = $("#payment_types").val();
        const isGiftCard = paymentType == "<?= lang(ucfirst($controller_name) . '.giftcard') ?>";
        const isCash = paymentType == "<?= lang(ucfirst($controller_name) . '.cash') ?>";
        const needsReferenceCode = referenceCodePaymentTypes.indexOf(paymentType) !== -1;

        if (isGiftCard) {
            $("#sale_total").html("<?= to_currency($total) ?>");
            $("#sale_amount_due").html("<?= to_currency($amount_due) ?>");
            $("#amount_tendered_label").html("<?= lang(ucfirst($controller_name) . '.giftcard_number') ?>");
            $("#amount_tendered:enabled").val('').focus();
            $(".giftcard-input").attr('disabled', false);
            $(".non-giftcard-input").attr('disabled', true);
            $(".giftcard-input:enabled").val('').focus();
            $(".reference-code-input").hide();
        } else if (isCash && cash_mode == '1') {
            $("#sale_total").html("<?= to_currency($non_cash_total) ?>");
            $("#sale_amount_due").html("<?= to_currency($cash_amount_due) ?>");
            $("#amount_tendered_label").html("<?= lang(ucfirst($controller_name) . '.amount_tendered') ?>");
            $("#amount_tendered:enabled").val("<?= to_currency_no_money($cash_amount_due) ?>");
            $(".giftcard-input").attr('disabled', true);
            $(".non-giftcard-input").attr('disabled', false);
            $(".reference-code-input").hide();
        } else {
            $("#sale_total").html("<?= to_currency($non_cash_total) ?>");
            $("#sale_amount_due").html("<?= to_currency($amount_due) ?>");
            $("#amount_tendered_label").html("<?= lang(ucfirst($controller_name) . '.amount_tendered') ?>");
            $("#amount_tendered:enabled").val("<?= to_currency_no_money($amount_due) ?>");
            $(".giftcard-input").attr('disabled', true);
            $(".non-giftcard-input").attr('disabled', false);
            if (needsReferenceCode) {
                $("#reference_code_label").html("<?= lang(ucfirst($controller_name) . '.reference_code') ?>");
                $(".reference-code-input").show();
                $(".reference-code-input input").attr('disabled', false);
            } else {
                $(".reference-code-input").hide();
                $(".reference-code-input input").attr('disabled', true);
            }
        }
    }

    // ========== Keyboard Shortcuts Handler ==========
    document.body.onkeyup = function(event) {
        if ($(event.target).closest('.modal').length || $('.modal.in').length) {
            return;
        }
        if (event.altKey) {
            switch (event.keyCode) {
                case shortcutCodes.items:
                    $("#item").focus();
                    $("#item").select();
                    break;
                case shortcutCodes.customers:
                    $("#customer").focus();
                    $("#customer").select();
                    break;
                case shortcutCodes.suspend:
                    $("#suspend_sale_button").click();
                    break;
                case shortcutCodes.suspended:
                    $("#show_suspended_sales_button").click();
                    break;
                case shortcutCodes.amount:
                    $("#amount_tendered").focus();
                    $("#amount_tendered").select();
                    break;
                case shortcutCodes.payment:
                    $("#add_payment_button").click();
                    break;
                case shortcutCodes.complete:
                    if (paymentsCoverTotal && $("#finish_sale_button").length) {
                        $("#finish_sale_button").click();
                    } else {
                        $("#add_payment_button").click();
                    }
                    break;
                case shortcutCodes.finish:
                    $("#finish_invoice_quote_button").click();
                    break;
                case shortcutCodes.help:
                    $("#show_keyboard_help").click();
                    break;
            }
        }

        switch (event.keyCode) {
            case shortcutCodes.cancel:
                $("#cancel_sale_button").click();
                break;
        }
    }
</script>

<?= view('partial/footer') ?>
