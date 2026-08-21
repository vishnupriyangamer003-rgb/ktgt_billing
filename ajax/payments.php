<?php
/**
 * AJAX handler for the Payments module: customer_options (customers that
 * have at least one invoice), payment_modes, invoices_for_customer (bill-wise
 * tally source), summary, stats, history, receipt, ledger, and save (records
 * a payment, applying it oldest-invoice-first and updating each invoice's
 * advance_paid/balance_due/status in place).
 */
require_once __DIR__ . '/../includes/auth.php';
require_login();
header('Content-Type: application/json');

$action = $_REQUEST['action'] ?? '';
$userId = current_user()['id'] ?? null;

/** A customer's non-cancelled, non-deleted invoices, oldest first — the same set
 * used both for the bill-wise tally preview and for actually applying a payment. */
function customer_invoices(int $customerId): array {
    $stmt = db()->prepare("SELECT id, invoice_number, invoice_date, grand_total, advance_paid, balance_due, status
        FROM invoices WHERE customer_id = ? AND is_deleted = 0 AND status != 'cancelled' ORDER BY invoice_date ASC, id ASC");
    $stmt->execute([$customerId]);
    return $stmt->fetchAll();
}

/** Oldest-invoice-first allocation of a lump sum across a customer's outstanding
 * invoices (no side effects — used both to preview the tally and, for real, by save). */
function suggest_allocation(array $invoices, float $amount): array {
    $remaining = max($amount, 0);
    $breakdown = [];
    foreach ($invoices as $inv) {
        $balance = (float)$inv['balance_due'];
        if ($balance <= 0.004) {
            $breakdown[] = ['invoice' => $inv, 'applied' => 0, 'status' => 'already-paid'];
            continue;
        }
        $applied = min($remaining, $balance);
        $remaining -= $applied;
        $resultBalance = $balance - $applied;
        $status = $applied <= 0 ? 'untouched' : ($resultBalance <= 0.004 ? 'covered' : 'partial');
        $breakdown[] = ['invoice' => $inv, 'applied' => $applied, 'status' => $status];
    }
    return ['breakdown' => $breakdown, 'unallocated' => $remaining];
}

function compute_next_payment_number(): string {
    $stmt = db()->query("SELECT payment_number FROM payments WHERE payment_number LIKE 'PAY-%'");
    $max = 1000;
    foreach ($stmt->fetchAll(PDO::FETCH_COLUMN) as $num) {
        if (preg_match('/^PAY-(\d+)$/', $num, $m)) {
            $max = max($max, (int)$m[1]);
        }
    }
    return 'PAY-' . ($max + 1);
}

if ($action === 'customer_options') {
    $rows = db()->query("SELECT DISTINCT c.id, c.name, c.company_name FROM customers c
        JOIN invoices i ON i.customer_id = c.id AND i.is_deleted = 0
        WHERE c.is_deleted = 0 ORDER BY c.name ASC")->fetchAll();
    json_success($rows);
}

if ($action === 'payment_modes') {
    $rows = db()->query("SELECT id, name, icon FROM payment_modes WHERE is_active = 1 ORDER BY sort_order ASC")->fetchAll();
    json_success($rows);
}

if ($action === 'invoices_for_customer') {
    $customerId = (int)($_GET['customer_id'] ?? 0);
    if ($customerId <= 0) json_error('Invalid customer.');
    json_success(customer_invoices($customerId));
}

if ($action === 'tally_preview') {
    $customerId = (int)($_GET['customer_id'] ?? 0);
    $amount = (float)($_GET['amount'] ?? 0);
    if ($customerId <= 0) json_error('Invalid customer.');
    $invoices = customer_invoices($customerId);
    $result = suggest_allocation($invoices, $amount);
    json_success($result);
}

if ($action === 'summary') {
    $customerId = (int)($_GET['customer_id'] ?? 0);
    if ($customerId <= 0) json_error('Invalid customer.');
    $invoices = customer_invoices($customerId);
    $total = array_sum(array_column($invoices, 'grand_total'));
    $paid = array_sum(array_column($invoices, 'advance_paid'));
    $balance = array_sum(array_column($invoices, 'balance_due'));
    json_success(['total' => $total, 'paid' => $paid, 'balance' => $balance]);
}

if ($action === 'stats') {
    $collected = (float)db()->query("SELECT COALESCE(SUM(advance_paid),0) FROM invoices WHERE is_deleted = 0 AND status != 'cancelled'")->fetchColumn();
    $outstanding = (float)db()->query("SELECT COALESCE(SUM(balance_due),0) FROM invoices WHERE is_deleted = 0 AND status != 'cancelled'")->fetchColumn();
    $custDues = (int)db()->query("SELECT COUNT(DISTINCT customer_id) FROM invoices WHERE is_deleted = 0 AND status != 'cancelled' AND balance_due > 0")->fetchColumn();
    $totalInvoices = (int)db()->query("SELECT COUNT(*) FROM invoices WHERE is_deleted = 0")->fetchColumn();
    json_success(['collected' => $collected, 'outstanding' => $outstanding, 'cust_dues' => $custDues, 'total_invoices' => $totalInvoices]);
}

if ($action === 'history') {
    $customerId = (int)($_GET['customer_id'] ?? 0);
    $where = '1=1';
    $params = [];
    if ($customerId > 0) { $where = 'customer_id = ?'; $params[] = $customerId; }
    $stmt = db()->prepare("SELECT id, payment_number, customer_id, customer_name, payment_date, amount, mode, reference_number
        FROM payments WHERE {$where} ORDER BY payment_date DESC, id DESC");
    $stmt->execute($params);
    json_success($stmt->fetchAll());
}

if ($action === 'receipt') {
    $id = (int)($_GET['id'] ?? 0);
    if ($id <= 0) json_error('Invalid payment.');
    $stmt = db()->prepare('SELECT * FROM payments WHERE id = ?');
    $stmt->execute([$id]);
    $payment = $stmt->fetch();
    if (!$payment) json_error('Payment not found.', 404);

    $allocStmt = db()->prepare('SELECT pa.amount, i.invoice_number FROM payment_allocations pa
        JOIN invoices i ON i.id = pa.invoice_id WHERE pa.payment_id = ?');
    $allocStmt->execute([$id]);
    $payment['allocations'] = $allocStmt->fetchAll();

    $invoices = customer_invoices((int)$payment['customer_id']);
    $currentBalance = array_sum(array_column($invoices, 'balance_due'));
    $payment['balance_before'] = $currentBalance + (float)$payment['amount'];
    $payment['balance_after'] = $currentBalance;

    $histStmt = db()->prepare('SELECT id, payment_number, payment_date, amount, mode FROM payments
        WHERE customer_id = ? ORDER BY payment_date DESC, id DESC LIMIT 5');
    $histStmt->execute([$payment['customer_id']]);
    $payment['recent_history'] = $histStmt->fetchAll();

    json_success($payment);
}

if ($action === 'ledger') {
    $customerId = (int)($_GET['customer_id'] ?? 0);
    if ($customerId <= 0) json_error('Invalid customer.');
    $invStmt = db()->prepare("SELECT invoice_number, invoice_date, grand_total FROM invoices
        WHERE customer_id = ? AND is_deleted = 0 AND status != 'cancelled' ORDER BY invoice_date ASC");
    $invStmt->execute([$customerId]);
    $payStmt = db()->prepare('SELECT payment_number, payment_date, amount, mode, reference_number FROM payments
        WHERE customer_id = ? ORDER BY payment_date ASC');
    $payStmt->execute([$customerId]);
    json_success(['invoices' => $invStmt->fetchAll(), 'payments' => $payStmt->fetchAll()]);
}

if ($action === 'save') {
    verify_csrf();
    $customerId = (int)($_POST['customer_id'] ?? 0);
    $amount = (float)($_POST['amount'] ?? 0);
    if ($customerId <= 0) json_error('Please select a customer.');
    if ($amount <= 0) json_error('Enter the collected amount.');

    $custChk = db()->prepare('SELECT id, name FROM customers WHERE id = ? AND is_deleted = 0');
    $custChk->execute([$customerId]);
    $customer = $custChk->fetch();
    if (!$customer) json_error('Please select a valid customer.');

    $paymentDate = clean($_POST['payment_date'] ?? '');
    if ($paymentDate === '' || !DateTime::createFromFormat('Y-m-d', $paymentDate)) {
        json_error('Please choose a valid payment date.');
    }

    $db = db();
    $db->beginTransaction();
    try {
        $invoices = customer_invoices($customerId);
        $result = suggest_allocation($invoices, $amount);

        $paymentNumber = compute_next_payment_number();
        $stmt = $db->prepare('INSERT INTO payments
            (payment_number, customer_id, customer_name, payment_date, amount, mode, reference_number, received_by, remarks, created_by)
            VALUES (?,?,?,?,?,?,?,?,?,?)');
        $stmt->execute([
            $paymentNumber, $customerId, $customer['name'], $paymentDate, $amount,
            clean($_POST['mode'] ?? ''), clean($_POST['reference_number'] ?? ''),
            clean($_POST['received_by'] ?? ''), clean($_POST['remarks'] ?? ''), $userId,
        ]);
        $paymentId = (int)$db->lastInsertId();

        $allocStmt = $db->prepare('INSERT INTO payment_allocations (payment_id, invoice_id, amount) VALUES (?, ?, ?)');
        $updateStmt = $db->prepare("UPDATE invoices SET advance_paid = ?, balance_due = ?,
            status = CASE WHEN ? <= 0.004 THEN 'paid' WHEN ? > 0 THEN 'partial' ELSE status END
            WHERE id = ?");

        foreach ($result['breakdown'] as $b) {
            if ($b['applied'] <= 0) continue;
            $inv = $b['invoice'];
            $allocStmt->execute([$paymentId, $inv['id'], $b['applied']]);

            $newPaid = (float)$inv['advance_paid'] + $b['applied'];
            $newBalance = max((float)$inv['balance_due'] - $b['applied'], 0);
            $updateStmt->execute([$newPaid, $newBalance, $newBalance, $newPaid, $inv['id']]);
        }

        $db->commit();
    } catch (Throwable $e) {
        $db->rollBack();
        json_error('Could not record the payment. Please try again.');
    }

    json_success(['id' => $paymentId, 'payment_number' => $paymentNumber, 'unallocated' => $result['unallocated']], 'Payment recorded successfully.');
}

json_error('Unknown action.', 400);
