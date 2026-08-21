<?php
$pageTitle = "Payments";
$pageKey = "payments";
$embed = (isset($_GET['embed']) && $_GET['embed'] === '1');
$extraHead = <<<'HTML'
<style>
.pay-summary{ display:flex; gap:1rem; flex-wrap:wrap; margin-bottom:1.25rem; }
.pay-summary .ps-box{ flex:1; min-width:160px; border-radius:14px; padding:1rem 1.2rem; }
.pay-summary .ps-box .lbl{ font-size:11px; text-transform:uppercase; letter-spacing:.05em; font-weight:700; opacity:.85; }
.pay-summary .ps-box .val{ font-size:1.35rem; font-weight:800; margin-top:.2rem; }
@media print{ .no-print{ display:none !important; } }
</style>
HTML;
include __DIR__ . '/components/header.php';
?>
      <div class="page-head fade-in-up no-print">
        <div>
          <h1>Payments</h1>
          <nav class="breadcrumb"><span class="breadcrumb-item"><a href="index.php">Dashboard</a></span><span class="breadcrumb-item active">Payments</span></nav>
        </div>
        <button class="btn btn-light" data-bs-toggle="modal" data-bs-target="#ledgerModal"><i class="bi bi-journal-text me-1"></i>Ledger Report</button>
      </div>

      <?php if (!$embed): ?>
      <div class="row g-3 mb-3 stagger no-print">
        <div class="col-6 col-md-3"><div class="stat-card accent-success"><div class="stat-icon bg-soft-success"><i class="bi bi-cash-coin"></i></div><div class="stat-label">Total Collected</div><div class="stat-value" id="statCollected">₹0</div></div></div>
        <div class="col-6 col-md-3"><div class="stat-card accent-danger"><div class="stat-icon bg-soft-danger"><i class="bi bi-exclamation-circle-fill"></i></div><div class="stat-label">Outstanding Balance</div><div class="stat-value" id="statOutstanding">₹0</div></div></div>
        <div class="col-6 col-md-3"><div class="stat-card accent-primary"><div class="stat-icon bg-soft-primary"><i class="bi bi-people-fill"></i></div><div class="stat-label">Customers with Dues</div><div class="stat-value" id="statCustDues">0</div></div></div>
        <div class="col-6 col-md-3"><div class="stat-card accent-warning"><div class="stat-icon bg-soft-warning"><i class="bi bi-receipt"></i></div><div class="stat-label">Total Invoices Billed</div><div class="stat-value" id="statInvoices">0</div></div></div>
      </div>
      <?php endif; ?>

      <div class="row g-3 no-print">
        <div class="col-xl-7">
          <div class="section-card mb-3 fade-in-up">
            <div class="section-head"><h5>New Payment Entry</h5><div class="subtitle">Select a customer to collect a bulk payment against all their pending invoices</div></div>
            <div class="section-body">
              <label class="form-label">Customer</label>
              <select class="form-select mb-3" id="paymentCustomer"><option value="">Select a customer...</option></select>

              <div id="pendingSection" class="d-none">
                <div class="pay-summary">
                  <div class="ps-box bg-soft-primary"><div class="lbl">Total Amount</div><div class="val" id="sumTotalBilled">₹0</div></div>
                  <div class="ps-box bg-soft-success"><div class="lbl">Collected</div><div class="val" id="sumCollected">₹0</div></div>
                  <div class="ps-box bg-soft-danger"><div class="lbl">Pending</div><div class="val" id="sumPending">₹0</div></div>
                </div>

                <div class="row g-3 mb-3">
                  <div class="col-md-4"><label class="form-label">Payment Date</label><input type="date" class="form-control" id="payDate"></div>
                  <div class="col-md-4"><label class="form-label">Payment Mode</label>
                    <select class="form-select" id="payMode"></select>
                  </div>
                  <div class="col-md-4"><label class="form-label">Reference Number</label><input class="form-control" id="payRef" placeholder="Transaction / Cheque #"></div>
                  <div class="col-6 col-md-3"><label class="form-label">Collected Amount <span class="req">*</span></label><input type="number" class="form-control fw-700" id="payAmount" placeholder="0.00"></div>
                  <div class="col-6 col-md-3"><label class="form-label">Balance After</label><input class="form-control fw-700 text-danger" id="payBalanceAfter" value="₹0" readonly></div>
                  <div class="col-6 col-md-3"><label class="form-label">Received By</label><input class="form-control" id="payBy" value="<?php echo htmlspecialchars(current_user()['name'] ?? ''); ?>"></div>
                  <div class="col-6 col-md-3"><label class="form-label">Remarks</label><input class="form-control" id="payRemarks" placeholder="Optional"></div>
                </div>

                <div class="bill-block-label">Bill-wise Tally</div>
                <div id="tallyList" class="mb-3"></div>

                <button class="btn btn-primary w-100" id="savePaymentBtn"><i class="bi bi-check-lg me-1"></i>Record Payment</button>
              </div>
            </div>
          </div>
        </div>

        <div class="col-xl-5">
          <div class="section-card fade-in-up">
            <div class="section-head"><h5 id="custHistoryTitle">Customer Payment History</h5></div>
            <div class="section-body p-0">
              <div class="list-group list-group-flush" id="custHistoryList">
                <div class="empty-state py-4">
                  <div class="es-icon"><i class="bi bi-person-lines-fill"></i></div>
                  <h6>No customer selected</h6>
                  <p>Select a customer on the left to see their past payments here.</p>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <?php if (!$embed): ?>
      <div class="section-card fade-in-up no-print">
        <div class="section-head">
          <h5>Payment History</h5>
          <select class="form-select form-select-sm" id="historyFilter" style="max-width:220px;"><option value="">All Customers</option></select>
        </div>
        <div class="table-responsive">
          <table class="table table-modern mb-0">
            <thead><tr><th>Payment ID</th><th>Customer</th><th>Date</th><th>Amount</th><th>Mode</th></tr></thead>
            <tbody id="payTableBody"></tbody>
          </table>
        </div>
      </div>
      <?php endif; ?>

<?php
$pageModals = <<<'HTML'
<!-- Receipt Modal -->
<div class="modal fade" id="receiptModal" tabindex="-1">
  <div class="modal-dialog modal-lg modal-dialog-scrollable">
    <div class="modal-content">
      <div class="modal-header no-print"><h5 class="modal-title">Payment Receipt</h5><button class="btn-close" data-bs-dismiss="modal"></button></div>
      <div class="modal-body" id="receiptBody"></div>
      <div class="modal-footer no-print">
        <button class="btn btn-light" data-bs-dismiss="modal">Close</button>
        <button class="btn btn-outline-primary" onclick="KTG.toast('Receipt has been emailed to the customer.','success','Email Sent');"><i class="bi bi-envelope me-1"></i>Send Mail</button>
        <button class="btn btn-primary" onclick="window.print()"><i class="bi bi-printer me-1"></i>Print</button>
      </div>
    </div>
  </div>
</div>

<!-- Ledger Modal -->
<div class="modal fade" id="ledgerModal" tabindex="-1">
  <div class="modal-dialog modal-xl modal-dialog-scrollable">
    <div class="modal-content">
      <div class="modal-header no-print">
        <h5 class="modal-title"><i class="bi bi-journal-text text-primary-KTG me-2"></i>Ledger Report</h5>
        <button class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <div class="d-flex gap-2 mb-3 no-print">
          <select class="form-select" id="ledgerCustomer" style="max-width:280px;"></select>
          <button class="btn btn-light ms-auto" id="ledgerExportBtn"><i class="bi bi-filetype-xlsx me-1"></i>Export</button>
          <button class="btn btn-light" onclick="window.print()"><i class="bi bi-printer me-1"></i>Print</button>
        </div>
        <div class="table-responsive-wrap">
          <table class="table table-modern mb-0">
            <thead><tr><th>Date</th><th>Description</th><th class="text-end">Debit</th><th class="text-end">Credit</th><th class="text-end">Balance</th></tr></thead>
            <tbody id="ledgerBody"></tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</div>
HTML;
?>
<?php
$pageScript = "const IS_EMBED = " . ($embed ? 'true' : 'false') . ";\n" . <<<'JS'
document.addEventListener('DOMContentLoaded', () => setTimeout(init, 150));

let customers = [];
let customerInvoices = [];

function customerOptionLabel(c){
  const company = (c.company_name || '').trim();
  if (!company || company.toLowerCase() === (c.name || '').trim().toLowerCase()) return c.name;
  return `${c.name} — ${company}`;
}

function init(){
  document.getElementById('payDate').value = new Date().toISOString().slice(0,10);

  $.get('ajax/payments.php', { action: 'payment_modes' }).done((res) => {
    document.getElementById('payMode').innerHTML = (res.data || []).map(m => `<option ${m.name === 'UPI' ? 'selected' : ''}>${m.name}</option>`).join('');
  });

  $.get('ajax/payments.php', { action: 'customer_options' }).done((res) => {
    customers = res.data || [];
    const optionsHtml = customers.map(c => `<option value="${c.id}">${KTG.escAttr(customerOptionLabel(c))}</option>`).join('');

    const paymentCustomer = document.getElementById('paymentCustomer');
    paymentCustomer.innerHTML = '<option value="">Select a customer...</option>' + optionsHtml;
    document.getElementById('ledgerCustomer').innerHTML = optionsHtml;

    paymentCustomer.addEventListener('change', () => onCustomerSelect(paymentCustomer.value));
    document.getElementById('payAmount').addEventListener('input', () => { renderTally(); updateBalanceAfter(); });
    document.getElementById('savePaymentBtn').addEventListener('click', savePayment);
    document.getElementById('ledgerCustomer').addEventListener('change', renderLedger);
    document.getElementById('ledgerExportBtn').addEventListener('click', exportLedger);

    if (!IS_EMBED) {
      document.getElementById('historyFilter').innerHTML = '<option value="">All Customers</option>' + optionsHtml;
      document.getElementById('historyFilter').addEventListener('change', renderHistory);
    }

    const preselect = KTG.qs('customer');
    if (preselect && customers.some(c => String(c.id) === String(preselect))) {
      paymentCustomer.value = preselect;
      onCustomerSelect(preselect);
    }

    if (customers.length) renderLedger();
    renderStats();
    renderHistory();
  });
}

function customerSummary(){
  const total = customerInvoices.reduce((s,i) => s + i.amount, 0);
  const paid = customerInvoices.reduce((s,i) => s + i.paid, 0);
  return { total, paid, balance: total - paid };
}

function renderStats(){
  if (IS_EMBED) return;
  $.get('ajax/payments.php', { action: 'stats' }).done((res) => {
    if (!res.success) return;
    document.getElementById('statCollected').textContent = KTG.moneyShort(+res.data.collected);
    document.getElementById('statOutstanding').textContent = KTG.moneyShort(+res.data.outstanding);
    document.getElementById('statCustDues').textContent = res.data.cust_dues;
    document.getElementById('statInvoices').textContent = res.data.total_invoices;
  });
}

function onCustomerSelect(customerId){
  const pendingSection = document.getElementById('pendingSection');
  if (!customerId) { pendingSection.classList.add('d-none'); customerInvoices = []; return; }
  pendingSection.classList.remove('d-none');
  document.getElementById('payAmount').value = '';

  $.get('ajax/payments.php', { action: 'invoices_for_customer', customer_id: customerId }).done((res) => {
    customerInvoices = (res.data || []).map(inv => ({
      id: inv.id, invoice_number: inv.invoice_number, invoice_date: inv.invoice_date,
      amount: +inv.grand_total, paid: +inv.advance_paid, balance: +inv.balance_due
    }));

    const summary = customerSummary();
    document.getElementById('sumTotalBilled').textContent = KTG.money(summary.total);
    document.getElementById('sumCollected').textContent = KTG.money(summary.paid);
    document.getElementById('sumPending').textContent = KTG.money(summary.balance);
    document.getElementById('payAmount').max = summary.balance;
    document.getElementById('payAmount').placeholder = 'e.g. ' + summary.balance;

    renderTally();
    updateBalanceAfter();
    renderCustHistory(customerId);
  });
}

function updateBalanceAfter(){
  const customerId = document.getElementById('paymentCustomer').value;
  const field = document.getElementById('payBalanceAfter');
  if (!customerId) { field.value = '₹0'; return; }
  const summary = customerSummary();
  const amount = +document.getElementById('payAmount').value || 0;
  const balanceAfter = Math.max(summary.balance - amount, 0);
  field.value = KTG.money(balanceAfter);
  field.classList.toggle('text-danger', balanceAfter > 0);
  field.classList.toggle('text-success', balanceAfter === 0);
}

function renderCustHistory(customerId){
  const c = customers.find(x => String(x.id) === String(customerId));
  const name = c ? c.name : '';
  document.getElementById('custHistoryTitle').textContent = name + ' — Payment History';
  $.get('ajax/payments.php', { action: 'history', customer_id: customerId }).done((res) => {
    const history = res.data || [];
    document.getElementById('custHistoryList').innerHTML = history.map(p => `
      <div class="list-group-item px-3 py-3">
        <div class="d-flex justify-content-between align-items-start">
          <div>
            <div class="cell-title fs-13">${p.payment_number}</div>
            <div class="cell-sub">${p.payment_date} &middot; ${p.mode || '—'} ${p.reference_number ? '('+p.reference_number+')' : ''}</div>
          </div>
          <span class="fw-700 text-success">${KTG.money(+p.amount)}</span>
        </div>
        <div class="fs-11 text-muted mt-1">${(p.allocations || []).map(a => a.invoice_number + ' ' + KTG.moneyShort(+a.amount)).join(', ')}</div>
      </div>`).join('') || `<div class="empty-state py-4"><div class="es-icon"><i class="bi bi-cash-coin"></i></div><h6>No payments yet</h6><p>Payments recorded for ${KTG.escAttr(name)} will appear here.</p></div>`;
  });
}

/* Oldest-invoice-first, no side effects — mirrors the algorithm ajax/payments.php's
   suggest_allocation() uses when a payment is actually saved, so the live tally preview
   matches what save will do without a network round trip on every keystroke. */
function suggestAllocation(amount){
  let remaining = Math.max(+amount || 0, 0);
  const breakdown = [];
  customerInvoices.forEach(inv => {
    if (inv.balance <= 0) { breakdown.push({invoice: inv, applied: 0, status: 'already-paid'}); return; }
    const applied = Math.min(remaining, inv.balance);
    remaining -= applied;
    const resultBalance = inv.balance - applied;
    breakdown.push({
      invoice: inv, applied, resultBalance,
      status: applied === 0 ? 'untouched' : resultBalance === 0 ? 'covered' : 'partial'
    });
  });
  return { breakdown, unallocated: remaining };
}

function renderTally(){
  const amount = +document.getElementById('payAmount').value || 0;
  const box = document.getElementById('tallyList');
  if (!customerInvoices.length) { box.innerHTML = `<div class="text-center text-muted fs-12 py-3">No invoices for this customer.</div>`; return; }

  if (amount <= 0) {
    box.innerHTML = customerInvoices.map(inv => `
      <div class="tally-row ${inv.balance<=0?'covered':'untouched'}">
        <div class="flex-grow-1"><span class="fw-600 fs-13">${inv.invoice_number}</span> <span class="fs-12 text-muted">— ${inv.invoice_date}</span></div>
        <span class="fs-12 text-muted">Bill ${KTG.money(inv.amount)} &middot; Balance ${KTG.money(inv.balance)}</span>
        <span class="t-amt ${inv.balance<=0?'text-success':'text-muted'}">${inv.balance<=0?'Paid':'—'}</span>
      </div>`).join('');
    return;
  }

  const { breakdown, unallocated } = suggestAllocation(amount);
  box.innerHTML = breakdown.map(b => {
    const cls = b.status === 'covered' ? 'covered' : b.status === 'partial' ? 'partial' : b.status === 'already-paid' ? 'covered' : 'untouched';
    const statusText = b.status === 'already-paid' ? 'Already Paid' : b.status === 'covered' ? 'Will be Fully Paid' : b.status === 'partial' ? 'Will be Partially Paid' : 'Not Reached';
    return `
    <div class="tally-row ${cls}">
      <div class="flex-grow-1"><span class="fw-600 fs-13">${b.invoice.invoice_number}</span> <span class="fs-12 text-muted">— Bill ${KTG.money(b.invoice.amount)} &middot; Balance ${KTG.money(b.invoice.balance)}</span></div>
      <span class="fs-12">${statusText}</span>
      <span class="t-amt ${b.applied>0?'text-success':'text-muted'}">${b.applied>0 ? '+'+KTG.money(b.applied) : '—'}</span>
    </div>`;
  }).join('') + (unallocated > 0 ? `<div class="alert alert-warning py-2 fs-12 mb-0" style="border-radius:10px;">${KTG.money(unallocated)} exceeds this customer's total outstanding and will be left unallocated (credit on account).</div>` : '');
}

function savePayment(){
  const customerId = document.getElementById('paymentCustomer').value;
  const amount = +document.getElementById('payAmount').value || 0;
  if (!customerId) { KTG.toast('Please select a customer first.', 'danger', 'Missing Customer'); return; }
  if (amount <= 0) { KTG.toast('Enter the collected amount.', 'danger', 'Missing Amount'); return; }

  const payload = {
    action: 'save',
    customer_id: customerId,
    payment_date: document.getElementById('payDate').value,
    amount,
    mode: document.getElementById('payMode').value,
    reference_number: document.getElementById('payRef').value,
    received_by: document.getElementById('payBy').value,
    remarks: document.getElementById('payRemarks').value
  };

  const $btn = $('#savePaymentBtn').prop('disabled', true);
  $.post('ajax/payments.php', payload).done((res) => {
    if (!res.success) { KTG.toast(res.message || 'Could not record the payment.', 'danger', 'Error'); return; }
    const c = customers.find(x => String(x.id) === String(customerId));
    KTG.toast(`Payment of ${KTG.money(amount)} recorded for ${c ? c.name : 'this customer'}.`, 'success', 'Payment Saved');
    document.getElementById('payRef').value = '';
    document.getElementById('payRemarks').value = '';
    onCustomerSelect(customerId);
    renderStats();
    renderHistory();
    renderLedger();
  }).fail((xhr) => {
    const msg = (xhr.responseJSON && xhr.responseJSON.message) || 'Could not record the payment.';
    KTG.toast(msg, 'danger', 'Error');
  }).always(() => $btn.prop('disabled', false));
}

function renderHistory(){
  if (IS_EMBED) return;
  const filter = document.getElementById('historyFilter').value;
  const params = { action: 'history' };
  if (filter) params.customer_id = filter;
  $.get('ajax/payments.php', params).done((res) => {
    const payments = res.data || [];
    document.getElementById('payTableBody').innerHTML = payments.map(p => `
      <tr>
        <td class="cell-title">${p.payment_number}</td>
        <td>${p.customer_name}</td>
        <td class="fs-13">${p.payment_date}</td>
        <td class="fw-700 text-success">${KTG.money(+p.amount)}</td>
        <td><span class="chip chip-inprogress">${p.mode || '—'}</span></td>
      </tr>`).join('') || `<tr><td colspan="5" class="text-center text-muted py-3">No payments recorded yet.</td></tr>`;
    KTG.makeResponsiveTable(document.getElementById('payTableBody').closest('table'));
  });
}

function showReceipt(paymentId){
  $.get('ajax/payments.php', { action: 'receipt', id: paymentId }).done((res) => {
    if (!res.success) { KTG.toast(res.message || 'Could not load the receipt.', 'danger', 'Error'); return; }
    const payment = res.data;

    document.getElementById('receiptBody').innerHTML = `
      <div class="receipt-sheet">
        <div class="rc-head">
          <div class="d-flex gap-3">
            <div class="bl-logo"><img src="assets/images/logo.png" alt="KTG"></div>
            <div><div class="bl-company">Knock The Globe Technologies</div><div class="bl-meta">Karur, Tamil Nadu 639002 &middot; 72001 23452</div></div>
          </div>
          <div class="text-end">
            <div class="bill-doctype" style="font-size:1.2rem;">PAYMENT RECEIPT</div>
            <div class="fs-12 text-muted"># ${payment.payment_number}</div>
          </div>
        </div>
        <div class="row g-3 mb-3">
          <div class="col-md-6">
            <div class="bill-block-label">Received From</div>
            <span class="bill-to-name">${KTG.escAttr(payment.customer_name)}</span>
          </div>
          <div class="col-md-6">
            <table class="bill-meta-table w-100">
              <tr><td>Date</td><td>${payment.payment_date}</td></tr>
              <tr><td>Mode</td><td>${payment.mode || '—'}</td></tr>
              <tr><td>Reference</td><td>${payment.reference_number || '—'}</td></tr>
              <tr><td>Received By</td><td>${payment.received_by || '—'}</td></tr>
            </table>
          </div>
        </div>
        <div class="bill-amount-due mb-3"><span class="lbl">Amount Collected</span><span class="val">${KTG.money(+payment.amount)}</span></div>

        <div class="bill-block-label">Applied Against Invoices</div>
        <table class="table table-modern mb-3"><thead><tr><th>Invoice #</th><th class="text-end">Amount Applied</th></tr></thead>
        <tbody>${(payment.allocations || []).map(a => `<tr><td>${a.invoice_number}</td><td class="text-end fw-600">${KTG.money(+a.amount)}</td></tr>`).join('')}</tbody></table>

        <div class="row g-3 mb-3">
          <div class="col-6"><div class="bill-totals"><div class="bt-row"><span class="text-muted">Balance Before</span><span class="fw-600">${KTG.money(+payment.balance_before)}</span></div></div></div>
          <div class="col-6"><div class="bill-totals"><div class="bt-row bt-grand"><span>Balance After</span><span>${KTG.money(+payment.balance_after)}</span></div></div></div>
        </div>

        <div class="bill-block-label">Recent Payment History — ${KTG.escAttr(payment.customer_name)}</div>
        <table class="table table-modern mb-0"><thead><tr><th>Payment ID</th><th>Date</th><th class="text-end">Amount</th><th>Mode</th></tr></thead>
        <tbody>${(payment.recent_history || []).map(h => `<tr><td>${h.payment_number}</td><td>${h.payment_date}</td><td class="text-end fw-600">${KTG.money(+h.amount)}</td><td>${h.mode || '—'}</td></tr>`).join('')}</tbody></table>

        <div class="bill-footer-note">This is a system generated payment receipt.</div>
      </div>`;
    new bootstrap.Modal(document.getElementById('receiptModal')).show();
  });
}

function renderLedger(){
  const customerId = document.getElementById('ledgerCustomer').value;
  if (!customerId) return;
  $.get('ajax/payments.php', { action: 'ledger', customer_id: customerId }).done((res) => {
    if (!res.success) return;
    const invoices = res.data.invoices || [];
    const payments = (res.data.payments || []).slice().sort((a,b) => a.payment_date.localeCompare(b.payment_date));

    const rows = invoices.map(inv => ({date: inv.invoice_date, desc: `Invoice ${inv.invoice_number} raised`, debit: +inv.grand_total, credit: 0}));
    payments.forEach(p => rows.push({date: p.payment_date, desc: `${p.payment_number} &middot; ${p.mode || '—'} ${p.reference_number ? '('+p.reference_number+')' : ''}`, debit: 0, credit: +p.amount}));
    rows.sort((a,b) => a.date.localeCompare(b.date));

    let balance = 0, html = '';
    rows.forEach(r => {
      balance += r.debit - r.credit;
      html += `<tr><td class="fs-13">${r.date}</td><td>${r.desc}</td><td class="text-end">${r.debit ? KTG.money(r.debit) : '—'}</td><td class="text-end text-success fw-600">${r.credit ? KTG.money(r.credit) : '—'}</td><td class="text-end fw-700">${KTG.money(balance)}</td></tr>`;
    });
    document.getElementById('ledgerBody').innerHTML = html || `<tr><td colspan="5" class="text-center text-muted py-3">No transactions.</td></tr>`;
    KTG.makeResponsiveTable(document.getElementById('ledgerBody').closest('table'));
  });
}

function exportLedger(){
  const customerId = document.getElementById('ledgerCustomer').value;
  const c = customers.find(x => String(x.id) === String(customerId));
  const rows = [['Date','Description','Debit','Credit','Balance']];
  document.querySelectorAll('#ledgerBody tr:not(.mobile-detail-row)').forEach(tr => {
    const cells = Array.from(tr.children).filter(td => !td.classList.contains('mobile-toggle-cell'));
    rows.push(cells.map(td => td.textContent.trim()));
  });
  KTG.exportCsv(`Ledger_${(c ? c.name : 'Customer').replace(/\s+/g,'_')}.csv`, rows);
  KTG.toast('Ledger exported as CSV (opens in Excel).', 'success', 'Export Ready');
}
JS;
?>
<?php include __DIR__ . '/components/footer.php'; ?>
