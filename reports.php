<?php
$pageTitle = "Reports";
$pageKey = "reports";
include __DIR__ . '/components/header.php';
?>
      <div class="page-head fade-in-up">
        <div>
          <h1 id="pageTitle">Invoice Report</h1>
          <nav class="breadcrumb"><span class="breadcrumb-item"><a href="index.php">Dashboard</a></span><span class="breadcrumb-item active" id="breadcrumbType">Invoice Report</span></nav>
        </div>
        <div class="d-flex gap-2">
          <button class="btn btn-light" id="exportExcelBtn"><i class="bi bi-filetype-xlsx me-1"></i>Excel</button>
          <button class="btn btn-light" id="exportPdfBtn"><i class="bi bi-filetype-pdf me-1"></i>PDF</button>
          <button class="btn btn-primary" onclick="window.print()"><i class="bi bi-printer me-1"></i>Print</button>
        </div>
      </div>

      <!-- Filters -->
      <div class="section-card mb-3 fade-in-up">
        <div class="section-body py-3">
          <div class="row g-2 align-items-end">
            <div class="col-md-2"><label class="form-label mb-1">From Date</label><input type="date" class="form-control" id="fromDate" value="2026-04-01"></div>
            <div class="col-md-2"><label class="form-label mb-1">To Date</label><input type="date" class="form-control" id="toDate" value="2026-08-04"></div>
            <div class="col-md-3"><label class="form-label mb-1">Customer</label><select class="form-select" id="custFilter"><option value="">All Customers</option></select></div>
            <div class="col-md-3"><label class="form-label mb-1" id="extraFilterLabel">Status</label><select class="form-select" id="extraFilter"><option value="">All</option></select></div>
            <div class="col-md-2"><button class="btn btn-primary w-100" id="applyFiltersBtn"><i class="bi bi-funnel me-1"></i>Apply</button></div>
          </div>
        </div>
      </div>

      <!-- Results table (driven by filters) -->
      <div class="section-card fade-in-up">
        <div class="section-head"><h5 id="resultsTitle">Invoice Report Results</h5><span class="chip chip-inprogress" id="resultsCount">0 entries</span></div>
        <div class="table-responsive">
          <table class="table table-modern mb-0" id="resultsTable">
            <thead><tr id="resultsHead"></tr></thead>
            <tbody id="resultsBody"></tbody>
            <tfoot><tr id="resultsFoot"></tr></tfoot>
          </table>
        </div>
      </div>

<?php
$pageScript = <<<'JS'
document.addEventListener('DOMContentLoaded', () => setTimeout(init, 150));

const TITLES = {quotation:'Quotation Report', customer:'Customer Report', invoice:'Invoice Report', payment:'Payment Report', ledger:'Ledger Report', gst:'GST Report', nongst:'Non-GST Report', followup:'Follow Up Report'};
const EXTRA_FILTERS = {
  quotation: {label:'Status', options:['draft','sent','viewed','approved','rejected','expired']},
  invoice:   {label:'Status', options:['draft','pending','partial','paid','overdue','cancelled']},
  gst:       {label:'Status', options:['draft','pending','partial','paid','overdue','cancelled']},
  nongst:    {label:'Status', options:['draft','pending','partial','paid','overdue','cancelled']},
  payment:   {label:'Mode', options:['Cash','UPI','Bank Transfer','Cheque','Card']},
  customer:  {label:'Status', options:['active','pending','inactive']},
  followup:  {label:'Priority', options:['high','medium','low']},
  ledger:    null
};
let currentType = 'invoice';

function init(){
  const names = [...new Set(KTG.DATA.customers.map(c => c.name))];
  document.getElementById('custFilter').innerHTML = '<option value="">All Customers</option>' + names.map(n => `<option>${n}</option>`).join('');

  const typeParam = KTG.qs('type');
  if (typeParam && TITLES[typeParam]) currentType = typeParam;

  document.getElementById('pageTitle').textContent = TITLES[currentType];
  document.getElementById('breadcrumbType').textContent = TITLES[currentType];

  setupExtraFilter();
  document.getElementById('applyFiltersBtn').addEventListener('click', renderResults);
  document.getElementById('exportExcelBtn').addEventListener('click', exportResults);
  document.getElementById('exportPdfBtn').addEventListener('click', () => { KTG.toast('Use the Print dialog and choose "Save as PDF" as the destination.', 'primary', 'Generating PDF'); window.print(); });

  renderResults();
}

function setupExtraFilter(){
  const cfg = EXTRA_FILTERS[currentType];
  const wrap = document.getElementById('extraFilter');
  const label = document.getElementById('extraFilterLabel');
  if (!cfg) {
    wrap.closest('.col-md-3').classList.add('d-none');
    return;
  }
  wrap.closest('.col-md-3').classList.remove('d-none');
  label.textContent = cfg.label;
  wrap.innerHTML = '<option value="">All</option>' + cfg.options.map(o => `<option value="${o}">${o.charAt(0).toUpperCase()+o.slice(1)}</option>`).join('');
}

function inRange(dateStr, from, to){
  return (!from || dateStr >= from) && (!to || dateStr <= to);
}

function renderResults(){
  const from = document.getElementById('fromDate').value;
  const to = document.getElementById('toDate').value;
  const cust = document.getElementById('custFilter').value;
  const extra = document.getElementById('extraFilter').value;
  document.getElementById('resultsTitle').textContent = TITLES[currentType] + ' Results';

  let head = '', rows = [], foot = '';

  if (currentType === 'invoice' || currentType === 'gst' || currentType === 'nongst') {
    head = '<th>Invoice #</th><th>Customer</th><th>Date</th><th>Due</th><th>Amount</th><th>Paid</th><th>Balance</th><th>Status</th>';
    const filtered = KTG.DATA.invoices.filter(i => inRange(i.date, from, to) && (!cust || i.customer === cust) && (!extra || i.status === extra)
      && (currentType === 'invoice' || i.gstType === currentType));
    rows = filtered.map(i => `<tr><td class="cell-title">${i.id}</td><td>${i.customer}</td><td class="fs-13">${i.date}</td><td class="fs-13">${i.due}</td><td class="fw-600">${KTG.money(i.amount)}</td><td class="text-success fw-600">${KTG.money(i.paid)}</td><td class="fw-600 ${i.amount-i.paid>0?'text-danger':''}">${KTG.money(i.amount-i.paid)}</td><td><span class="chip chip-${i.status}">${i.status}</span></td></tr>`);
    document.getElementById('resultsCount').textContent = filtered.length + ' entries';
    const totalAmt = filtered.reduce((s,i) => s+i.amount, 0), totalPaid = filtered.reduce((s,i) => s+i.paid, 0);
    foot = `<td colspan="4" class="text-end">Total</td><td>${KTG.money(totalAmt)}</td><td>${KTG.money(totalPaid)}</td><td>${KTG.money(totalAmt-totalPaid)}</td><td></td>`;
  } else if (currentType === 'quotation') {
    head = '<th>Quotation #</th><th>Customer</th><th>Date</th><th>Expiry</th><th>Amount</th><th>Status</th>';
    const filtered = KTG.DATA.quotations.filter(q => inRange(q.date, from, to) && (!cust || q.customer === cust) && (!extra || q.status === extra));
    rows = filtered.map(q => `<tr><td class="cell-title">${q.id}</td><td>${q.customer}</td><td class="fs-13">${q.date}</td><td class="fs-13">${q.expiry}</td><td class="fw-600">${KTG.money(q.amount)}</td><td><span class="chip chip-${q.status}">${q.status}</span></td></tr>`);
    document.getElementById('resultsCount').textContent = filtered.length + ' entries';
    const totalAmt = filtered.reduce((s,q) => s+q.amount, 0);
    foot = `<td colspan="4" class="text-end">Total</td><td>${KTG.money(totalAmt)}</td><td></td>`;
  } else if (currentType === 'payment') {
    head = '<th>Payment ID</th><th>Customer</th><th>Date</th><th>Amount</th><th>Mode</th><th>Reference</th><th>Received By</th>';
    const filtered = KTG.Ledger.allPayments().filter(p => inRange(p.date, from, to) && (!cust || p.customer === cust) && (!extra || p.mode === extra));
    rows = filtered.map(p => `<tr><td class="cell-title">${p.id}</td><td>${p.customer}</td><td class="fs-13">${p.date}</td><td class="fw-700 text-success">${KTG.money(p.amount)}</td><td><span class="chip chip-inprogress">${p.mode}</span></td><td class="fs-13">${p.ref}</td><td class="fs-13">${p.by}</td></tr>`);
    document.getElementById('resultsCount').textContent = filtered.length + ' entries';
    const totalAmt = filtered.reduce((s,p) => s+p.amount, 0);
    foot = `<td colspan="3" class="text-end">Total</td><td>${KTG.money(totalAmt)}</td><td colspan="3"></td>`;
  } else if (currentType === 'customer') {
    head = '<th>Customer</th><th>Type</th><th>Status</th><th>Mobile</th><th>Email</th><th>Created</th><th>City</th>';
    const filtered = KTG.DATA.customers.filter(c => inRange(c.created, from, to) && (!cust || c.name === cust) && (!extra || c.status === extra));
    rows = filtered.map(c => `<tr><td class="cell-title">${c.name}</td><td><span class="chip chip-inprogress">${c.type}</span></td><td><span class="chip chip-${c.status==='active'?'active':c.status==='pending'?'pending':'cancelled'}">${c.status}</span></td><td class="fs-13">${c.mobile}</td><td class="fs-13">${c.email}</td><td class="fs-13">${c.created}</td><td>${c.city}</td></tr>`);
    document.getElementById('resultsCount').textContent = filtered.length + ' entries';
    foot = `<td colspan="7" class="text-end">${filtered.length} customer(s)</td>`;
  } else if (currentType === 'followup') {
    head = '<th>Customer</th><th>Type</th><th>Subject</th><th>Next Follow Up</th><th>Priority</th><th>Owner</th><th>Status</th>';
    const filtered = KTG.DATA.followups.filter(f => inRange(f.next, from, to) && (!cust || f.customer === cust) && (!extra || f.priority === extra));
    rows = filtered.map(f => `<tr><td class="cell-title">${f.customer}</td><td><span class="chip chip-inprogress">${f.type}</span></td><td class="fs-13">${f.subject}</td><td class="fs-13">${f.next}</td><td><span class="priority-dot priority-${f.priority}"></span>${f.priority}</td><td class="fs-13">${f.owner}</td><td><span class="chip chip-${f.status==='completed'?'completed':'pending'}">${f.status}</span></td></tr>`);
    document.getElementById('resultsCount').textContent = filtered.length + ' entries';
    foot = `<td colspan="7" class="text-end">${filtered.length} follow-up(s)</td>`;
  } else if (currentType === 'ledger') {
    head = '<th>Date</th><th>Description</th><th>Debit</th><th>Credit</th><th>Balance</th>';
    if (!cust) {
      rows = [];
      document.getElementById('resultsCount').textContent = 'Select a customer';
      foot = '';
    } else {
      const invoices = KTG.Ledger.invoicesForCustomer(cust).filter(i => inRange(i.date, from, to));
      const payments = KTG.Ledger.paymentsForCustomer(cust).filter(p => inRange(p.date, from, to));
      const entries = [
        ...invoices.map(i => ({date: i.date, desc: `Invoice ${i.id} raised`, debit: i.amount, credit: 0})),
        ...payments.map(p => ({date: p.date, desc: `${p.id} · ${p.mode}`, debit: 0, credit: p.amount}))
      ].sort((a,b) => a.date.localeCompare(b.date));
      let balance = 0;
      rows = entries.map(e => { balance += e.debit - e.credit; return `<tr><td class="fs-13">${e.date}</td><td>${e.desc}</td><td>${e.debit?KTG.money(e.debit):'—'}</td><td class="text-success">${e.credit?KTG.money(e.credit):'—'}</td><td class="fw-700">${KTG.money(balance)}</td></tr>`; });
      document.getElementById('resultsCount').textContent = entries.length + ' entries';
      foot = `<td colspan="4" class="text-end">Closing Balance</td><td>${KTG.money(balance)}</td>`;
    }
  }

  document.getElementById('resultsHead').innerHTML = head;
  document.getElementById('resultsFoot').innerHTML = rows.length ? foot : '';
  document.getElementById('resultsBody').innerHTML = rows.join('') || `<tr><td colspan="8"><div class="empty-state"><div class="es-icon"><i class="bi bi-inbox"></i></div><h6>${currentType==='ledger' && !cust ? 'Choose a customer to view their ledger' : 'No entries in this range'}</h6><p>${currentType==='ledger' && !cust ? 'Use the Customer filter above.' : 'Try widening the date range or clearing the filters.'}</p></div></td></tr>`;
  KTG.makeResponsiveTable(document.getElementById('resultsTable'));
}

function exportResults(){
  const table = document.getElementById('resultsTable');
  const rows = [];
  table.querySelectorAll('thead tr, tbody tr:not(.mobile-detail-row)').forEach(tr => {
    const cells = Array.from(tr.children).filter(td => !td.classList.contains('mobile-toggle-cell')).map(td => td.textContent.trim());
    if (cells.length) rows.push(cells);
  });
  KTG.exportCsv(`${TITLES[currentType].replace(/\s+/g,'_')}.csv`, rows);
  KTG.toast('Report exported as CSV (opens in Excel).', 'success', 'Export Ready');
}
JS;
?>
<?php include __DIR__ . '/components/footer.php'; ?>
