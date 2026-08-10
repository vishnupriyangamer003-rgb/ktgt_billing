<?php
$pageTitle = "Invoices";
$pageKey = "invoices";
include __DIR__ . '/components/header.php';
?>
      <div class="page-head fade-in-up">
        <div>
          <h1>Invoices</h1>
          <nav class="breadcrumb"><span class="breadcrumb-item"><a href="index.php">Dashboard</a></span><span class="breadcrumb-item active">Invoices</span></nav>
        </div>
        <div class="d-flex gap-2">
          <div class="btn-group">
            <button class="btn btn-soft-primary" id="viewTableBtn"><i class="bi bi-list-ul"></i></button>
            <button class="btn btn-light" id="viewGridBtn"><i class="bi bi-grid-3x3-gap"></i></button>
          </div>
          <a href="invoice-create.php" class="btn btn-primary"><i class="bi bi-plus-lg me-1"></i>New Invoice</a>
        </div>
      </div>

      <div class="row g-3 mb-3 stagger">
        <div class="col-6 col-md-3"><div class="stat-card accent-success"><div class="stat-icon bg-soft-success"><i class="bi bi-check-circle-fill"></i></div><div class="stat-label">Paid</div><div class="stat-value" id="statPaid">0</div></div></div>
        <div class="col-6 col-md-3"><div class="stat-card accent-warning"><div class="stat-icon bg-soft-warning"><i class="bi bi-hourglass-split"></i></div><div class="stat-label">Pending</div><div class="stat-value" id="statPending">0</div></div></div>
        <div class="col-6 col-md-3"><div class="stat-card accent-primary"><div class="stat-icon bg-soft-primary"><i class="bi bi-pie-chart-fill"></i></div><div class="stat-label">Partially Paid</div><div class="stat-value" id="statPartial">0</div></div></div>
        <div class="col-6 col-md-3"><div class="stat-card accent-danger"><div class="stat-icon bg-soft-danger"><i class="bi bi-exclamation-triangle-fill"></i></div><div class="stat-label">Overdue</div><div class="stat-value" id="statOverdue">0</div></div></div>
      </div>

      <ul class="nav nav-pills-soft gap-2 mb-3 fade-in-up" id="statusTabs">
        <li class="nav-item"><button class="nav-link active" data-status="">All</button></li>
        <li class="nav-item"><button class="nav-link" data-status="paid">Paid</button></li>
        <li class="nav-item"><button class="nav-link" data-status="pending">Pending</button></li>
        <li class="nav-item"><button class="nav-link" data-status="partial">Partially Paid</button></li>
        <li class="nav-item"><button class="nav-link" data-status="overdue">Overdue</button></li>
        <li class="nav-item"><button class="nav-link" data-status="cancelled">Cancelled</button></li>
      </ul>

      <div class="section-card mb-3 fade-in-up">
        <div class="section-body py-3">
          <div class="d-flex flex-wrap gap-2 align-items-center">
            <span class="bill-block-label mb-0 me-1"><i class="bi bi-calendar3-range me-1"></i>Filter by Date</span>
            <button type="button" class="quick-filter-chip" data-range="last_month"><i class="bi bi-calendar-minus"></i>Last Month</button>
            <button type="button" class="quick-filter-chip" data-range="this_month"><i class="bi bi-calendar-check"></i>This Month</button>
            <button type="button" class="quick-filter-chip" data-range="this_week"><i class="bi bi-calendar-week"></i>This Week</button>
            <button type="button" class="quick-filter-chip" data-range="today"><i class="bi bi-calendar-event"></i>Today</button>
            <button type="button" class="quick-filter-chip" data-range="custom"><i class="bi bi-sliders"></i>Custom Range</button>
            <input type="date" class="form-control d-none" id="invFromDate" style="max-width:160px;" title="From date">
            <input type="date" class="form-control d-none" id="invToDate" style="max-width:160px;" title="To date">
            <button type="button" class="btn btn-light" id="invClearFilterBtn" title="Clear date filter"><i class="bi bi-x-lg"></i></button>
            <button class="btn btn-light ms-auto"><i class="bi bi-filetype-xlsx me-1"></i>Excel</button>
            <button class="btn btn-light"><i class="bi bi-filetype-pdf me-1"></i>PDF</button>
            <button class="btn btn-light"><i class="bi bi-printer me-1"></i>Print</button>
          </div>
        </div>
      </div>

      <!-- Show All / GST / Non-GST split — sits directly above the datatable -->
      <ul class="nav nav-pills-soft gap-2 mb-3 fade-in-up" id="gstTabs">
        <li class="nav-item"><button class="nav-link active" data-gst="all"><i class="bi bi-collection me-1"></i>Show All</button></li>
        <li class="nav-item"><button class="nav-link" data-gst="gst"><i class="bi bi-receipt-cutoff me-1"></i>GST Invoices</button></li>
        <li class="nav-item"><button class="nav-link" data-gst="nongst"><i class="bi bi-receipt me-1"></i>Non-GST Invoices</button></li>
      </ul>

      <!-- Grid view -->
      <div id="gridView" class="row g-3 stagger fade-in-up d-none" style="margin-bottom:1rem;"></div>
      <div class="d-flex justify-content-between align-items-center d-none" id="gridFooter">
        <span class="fs-12 text-muted">Showing <b id="invCountGrid">0</b> invoices</span>
      </div>

      <!-- Table view (default) -->
      <div class="section-card fade-in-up" id="tableView">
        <div class="table-responsive">
          <table class="table table-modern mb-0">
            <thead><tr><th><input type="checkbox" class="form-check-input"></th><th>Invoice #</th><th>Customer</th><th>Date</th><th>Days</th><th>Amount</th><th>Paid</th><th>Balance</th><th>Status</th><th></th></tr></thead>
            <tbody id="invTableBody"></tbody>
          </table>
        </div>
        <div class="d-flex justify-content-between align-items-center p-3 border-top">
          <span class="fs-12 text-muted">Showing <b id="invCount">0</b> invoices</span>
          <nav><ul class="pagination mb-0">
            <li class="page-item"><a class="page-link" href="#">Prev</a></li>
            <li class="page-item active"><a class="page-link" href="#">1</a></li>
            <li class="page-item"><a class="page-link" href="#">2</a></li>
            <li class="page-item"><a class="page-link" href="#">Next</a></li>
          </ul></nav>
        </div>
      </div>

<?php
$pageModals = <<<'HTML'
<!-- Add Payment popup — embeds payments.php in an iframe, pre-scoped to the clicked invoice's customer -->
<div class="modal fade" id="paymentPopupModal" tabindex="-1">
  <div class="modal-dialog modal-xl modal-dialog-scrollable" style="max-width:1000px;">
    <div class="modal-content" style="height:88vh;">
      <div class="modal-header"><h5 class="modal-title"><i class="bi bi-cash-coin text-primary-KTG me-2"></i>Add Payment</h5><button class="btn-close" data-bs-dismiss="modal"></button></div>
      <div class="modal-body p-0">
        <iframe id="paymentPopupFrame" src="about:blank" style="width:100%; height:100%; border:0;" title="Add Payment"></iframe>
      </div>
    </div>
  </div>
</div>
HTML;
?>
<?php
$pageScript = <<<'JS'
document.addEventListener('DOMContentLoaded', () => setTimeout(init, 150));
let activeStatus = '';
let activeGst = 'all';

function openPaymentPopup(customer){
  document.getElementById('paymentPopupFrame').src = 'payments.php?embed=1&customer=' + customer;
  new bootstrap.Modal(document.getElementById('paymentPopupModal')).show();
}
document.getElementById('paymentPopupModal') && document.getElementById('paymentPopupModal').addEventListener('hidden.bs.modal', () => {
  document.getElementById('paymentPopupFrame').src = 'about:blank';
});
const STATUS_ICON = {paid:'bi-check-circle-fill', pending:'bi-hourglass-split', partial:'bi-pie-chart-fill', overdue:'bi-exclamation-triangle-fill', cancelled:'bi-x-circle-fill'};
const STATUS_COLOR = {paid:'success', pending:'warning', partial:'primary', overdue:'danger', cancelled:'secondary'};

function init(){
  applyFilters();
  document.querySelectorAll('#statusTabs .nav-link').forEach(btn => {
    btn.addEventListener('click', () => {
      document.querySelectorAll('#statusTabs .nav-link').forEach(b => b.classList.remove('active'));
      btn.classList.add('active');
      activeStatus = btn.dataset.status;
      applyFilters();
    });
  });
  document.querySelectorAll('#gstTabs .nav-link').forEach(btn => {
    btn.addEventListener('click', () => {
      document.querySelectorAll('#gstTabs .nav-link').forEach(b => b.classList.remove('active'));
      btn.classList.add('active');
      activeGst = btn.dataset.gst;
      applyFilters();
    });
  });
  document.querySelectorAll('.quick-filter-chip').forEach(chip => {
    chip.addEventListener('click', () => {
      document.querySelectorAll('.quick-filter-chip').forEach(c => c.classList.remove('active'));
      chip.classList.add('active');
      const isCustom = chip.dataset.range === 'custom';
      document.getElementById('invFromDate').classList.toggle('d-none', !isCustom);
      document.getElementById('invToDate').classList.toggle('d-none', !isCustom);
      if (!isCustom) {
        const { from, to } = KTG.dateRangeFor(chip.dataset.range);
        document.getElementById('invFromDate').value = from;
        document.getElementById('invToDate').value = to;
      }
      applyFilters();
    });
  });
  document.getElementById('invFromDate').addEventListener('change', applyFilters);
  document.getElementById('invToDate').addEventListener('change', applyFilters);
  document.getElementById('invClearFilterBtn').addEventListener('click', () => {
    document.querySelectorAll('.quick-filter-chip').forEach(c => c.classList.remove('active'));
    document.getElementById('invFromDate').value = '';
    document.getElementById('invToDate').value = '';
    document.getElementById('invFromDate').classList.add('d-none');
    document.getElementById('invToDate').classList.add('d-none');
    applyFilters();
  });
  document.getElementById('viewGridBtn').addEventListener('click', () => toggleView('grid'));
  document.getElementById('viewTableBtn').addEventListener('click', () => toggleView('table'));
}
function toggleView(mode){
  document.getElementById('gridView').classList.toggle('d-none', mode !== 'grid');
  document.getElementById('gridFooter').classList.toggle('d-none', mode !== 'grid');
  document.getElementById('tableView').classList.toggle('d-none', mode !== 'table');
  document.getElementById('viewGridBtn').className = 'btn ' + (mode === 'grid' ? 'btn-soft-primary' : 'btn-light');
  document.getElementById('viewTableBtn').className = 'btn ' + (mode === 'table' ? 'btn-soft-primary' : 'btn-light');
}
function dueDaysLabel(inv){
  if (inv.status === 'paid' || inv.status === 'cancelled') return '<span class="text-muted">—</span>';
  return `<span class="fw-600">${KTG.relativeDaysLabel(inv.due)}</span>`;
}
function applyFilters(){
  const from = document.getElementById('invFromDate').value;
  const to = document.getElementById('invToDate').value;
  const gstScoped = activeGst === 'all' ? KTG.DATA.invoices : KTG.DATA.invoices.filter(i => i.gstType === activeGst);

  updateStats(gstScoped);

  const filtered = gstScoped.filter(item => {
    const matchStatus = !activeStatus || item.status === activeStatus;
    const matchFrom = !from || item.date >= from;
    const matchTo = !to || item.date <= to;
    return matchStatus && matchFrom && matchTo;
  });
  render(filtered);
}
function updateStats(list){
  document.getElementById('statPaid').textContent = list.filter(i => i.status === 'paid').length;
  document.getElementById('statPending').textContent = list.filter(i => i.status === 'pending').length;
  document.getElementById('statPartial').textContent = list.filter(i => i.status === 'partial').length;
  document.getElementById('statOverdue').textContent = list.filter(i => i.status === 'overdue').length;
}
function render(list){
  document.getElementById('invCount').textContent = list.length;
  document.getElementById('invCountGrid').textContent = list.length;

  document.getElementById('gridView').innerHTML = list.map(inv => {
    const balance = inv.amount - inv.paid;
    const custArg = encodeURIComponent(inv.customer);
    return `
    <div class="col-sm-6 col-xl-4">
      <div class="doc-grid-card st-${inv.status}">
        <div class="d-flex justify-content-between align-items-start mb-2">
          <div class="avatar-sm bg-soft-${STATUS_COLOR[inv.status]}"><i class="bi ${STATUS_ICON[inv.status]}"></i></div>
          <span class="chip chip-${inv.status}">${inv.status}</span>
        </div>
        <a href="invoice-create.php" class="cell-title d-block fs-14 mb-1">${inv.id}</a>
        <div class="cell-sub mb-2">${inv.customer}</div>
        <div class="fs-12 text-secondary mb-1"><i class="bi bi-calendar3 me-2 text-muted"></i>${inv.date}</div>
        <div class="fs-12 mb-2">${dueDaysLabel(inv)}</div>
        <div class="d-flex justify-content-between fs-12 text-secondary mb-3">
          <span style="cursor:pointer;" onclick="openPaymentPopup('${custArg}')" title="Add Payment"><i class="bi bi-check2 me-1 text-success"></i>Paid ${KTG.moneyShort(inv.paid)}</span>
          ${balance > 0 ? `<span class="text-danger fw-600" style="cursor:pointer;" onclick="openPaymentPopup('${custArg}')" title="Add Payment">Bal ${KTG.moneyShort(balance)}</span>` : ''}
        </div>
        <div class="d-flex justify-content-between align-items-center">
          <span class="dg-amt" style="cursor:pointer;" onclick="openPaymentPopup('${custArg}')" title="Add Payment">${KTG.money(inv.amount)}</span>
          <div class="d-flex gap-1">
            <button type="button" class="btn btn-sm btn-soft-primary" onclick="openPaymentPopup('${custArg}')" title="Add Payment"><i class="bi bi-cash-coin"></i></button>
            <div class="dropdown">
              <button class="btn btn-sm btn-light" data-bs-toggle="dropdown"><i class="bi bi-three-dots-vertical"></i></button>
              <div class="dropdown-menu dropdown-menu-end">
                <a class="dropdown-item" href="invoice-create.php"><i class="bi bi-eye"></i>View</a>
                <a class="dropdown-item" href="#"><i class="bi bi-printer"></i>Print</a>
                <a class="dropdown-item" href="#"><i class="bi bi-share"></i>Share</a>
                <div class="dropdown-divider"></div>
                <a class="dropdown-item text-danger" href="#"><i class="bi bi-x-circle"></i>Cancel Invoice</a>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>`;
  }).join('') || `<div class="col-12"><div class="section-card"><div class="empty-state"><div class="es-icon"><i class="bi bi-receipt"></i></div><h6>No invoices found</h6><p>Try a different date range or status filter.</p></div></div></div>`;

  document.getElementById('invTableBody').innerHTML = list.map(inv => {
    const balance = inv.amount - inv.paid;
    const custArg = encodeURIComponent(inv.customer);
    return `
    <tr>
      <td><input type="checkbox" class="form-check-input"></td>
      <td><a href="invoice-create.php" class="cell-title">${inv.id}</a></td>
      <td>${inv.customer}</td>
      <td class="fs-13">${inv.date}</td>
      <td class="fs-13">${dueDaysLabel(inv)}</td>
      <td class="fw-600" style="cursor:pointer;" onclick="openPaymentPopup('${custArg}')" title="Add Payment">${KTG.money(inv.amount)}</td>
      <td class="text-success fw-600" style="cursor:pointer;" onclick="openPaymentPopup('${custArg}')" title="Add Payment">${KTG.money(inv.paid)}</td>
      <td class="fw-600 ${balance>0?'text-danger':''}" style="cursor:pointer;" onclick="openPaymentPopup('${custArg}')" title="Add Payment">${KTG.money(balance)}</td>
      <td><span class="chip chip-${inv.status}">${inv.status}</span></td>
      <td>
        <div class="d-flex gap-1">
          <button type="button" class="btn btn-sm btn-soft-primary" onclick="openPaymentPopup('${custArg}')" title="Add Payment"><i class="bi bi-cash-coin"></i></button>
          <div class="dropdown">
            <button class="btn btn-sm btn-light" data-bs-toggle="dropdown"><i class="bi bi-three-dots-vertical"></i></button>
            <div class="dropdown-menu dropdown-menu-end">
              <a class="dropdown-item" href="invoice-create.php"><i class="bi bi-eye"></i>View</a>
              <a class="dropdown-item" href="#"><i class="bi bi-printer"></i>Print</a>
              <a class="dropdown-item" href="#"><i class="bi bi-download"></i>Download PDF</a>
              <a class="dropdown-item" href="#"><i class="bi bi-share"></i>Share</a>
              <div class="dropdown-divider"></div>
              <a class="dropdown-item text-danger" href="#"><i class="bi bi-x-circle"></i>Cancel Invoice</a>
            </div>
          </div>
        </div>
      </td>
    </tr>`;
  }).join('') || `<tr><td colspan="10"><div class="empty-state"><div class="es-icon"><i class="bi bi-receipt"></i></div><h6>No invoices found</h6><p>Try a different date range or status filter.</p></div></td></tr>`;
  KTG.makeResponsiveTable(document.getElementById('invTableBody').closest('table'));
}
JS;
?>
<?php include __DIR__ . '/components/footer.php'; ?>
