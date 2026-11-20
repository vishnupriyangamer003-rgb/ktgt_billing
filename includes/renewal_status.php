<?php
/**
 * Shared Paid Status <-> Expiry Date automation, used by both ajax/renewals.php
 * (runs on every list/view load) and cron/sync_renewal_status.php (optional daily
 * job for exact-day precision even when nobody opens the Renewals page that day).
 */

function renewal_unpaid_status_id(): ?int {
    $id = db()->query("SELECT id FROM master_paid_status_types WHERE LOWER(name) = 'unpaid' AND status = 'active' LIMIT 1")->fetchColumn();
    return $id ? (int)$id : null;
}

function renewal_is_paid_status_id(?int $id): bool {
    if (!$id) return false;
    $stmt = db()->prepare('SELECT name FROM master_paid_status_types WHERE id = ?');
    $stmt->execute([$id]);
    $name = $stmt->fetchColumn();
    return $name && strtolower($name) === 'paid';
}

/** Returns the new expiry_date (Y-m-d) if this represents an Unpaid/unset -> Paid
 * transition (expiry extends by exactly one year from its current value), or null if
 * no extension applies (was already Paid, or isn't becoming Paid). Only meaningful for
 * edits to an existing renewal — new renewals keep whatever expiry date was entered. */
function renewal_extend_expiry_if_newly_paid(?int $oldPaidStatusId, string $currentExpiry, ?int $newPaidStatusId): ?string {
    if (renewal_is_paid_status_id($oldPaidStatusId) || !renewal_is_paid_status_id($newPaidStatusId)) return null;
    $d = DateTime::createFromFormat('Y-m-d', $currentExpiry);
    if (!$d) return null;
    $d->modify('+1 year');
    return $d->format('Y-m-d');
}

/** Auto-flips Paid -> Unpaid for any renewal whose expiry date has come within 2 months.
 * Returns how many rows were changed. */
function renewal_sync_paid_status_for_expiry(): int {
    $unpaidId = renewal_unpaid_status_id();
    if (!$unpaidId) return 0;
    $stmt = db()->prepare("UPDATE renewals r
        JOIN master_paid_status_types ps ON ps.id = r.paid_status_id AND LOWER(ps.name) = 'paid'
        SET r.paid_status_id = ?
        WHERE r.is_deleted = 0 AND r.expiry_date <= DATE_ADD(CURDATE(), INTERVAL 2 MONTH)");
    $stmt->execute([$unpaidId]);
    return $stmt->rowCount();
}
