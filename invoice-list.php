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
            <button type="button" class="quick-filter-chip" data-range="today"><i class="bi bi-calendar-event"></i>Today</button>
            <button type="button" class="quick-filter-chip" data-range="custom"><i class="bi bi-sliders"></i>Custom Range</button>
            <input type="date" class="form-control d-none" id="invFromDate" style="max-width:160px;" title="From date">
            <input type="date" class="form-control d-none" id="invToDate" style="max-width:160px;" title="To date">
            <button type="button" class="btn btn-light" id="invClearFilterBtn" title="Clear date filter"><i class="bi bi-x-lg"></i></button>
            <button class="btn btn-light ms-auto" id="invExportBtn"><i class="bi bi-filetype-xlsx me-1"></i>Excel</button>
            <button class="btn btn-light" id="invPrintBtn"><i class="bi bi-printer me-1"></i>Print</button>
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
            <thead><tr><th>Invoice #</th><th>Customer</th><th>Date</th><th>Days</th><th>Amount</th><th>Paid</th><th>Balance</th><th>Status</th><th></th></tr></thead>
            <tbody id="invTableBody"></tbody>
          </table>
        </div>
        <div class="d-flex justify-content-between align-items-center p-3 border-top">
          <span class="fs-12 text-muted">Showing <b id="invCount">0</b> invoices</span>
        </div>
      </div>

<?php
$pageScript = <<<'JS'
document.addEventListener('DOMContentLoaded', () => setTimeout(init, 150));
let activeStatus = '';
let activeGst = 'all';
let items = [];

const STATUS_ICON = {draft:'bi-file-earmark', paid:'bi-check-circle-fill', pending:'bi-hourglass-split', partial:'bi-pie-chart-fill', overdue:'bi-exclamation-triangle-fill', cancelled:'bi-x-circle-fill'};
const STATUS_COLOR = {draft:'secondary', paid:'success', pending:'warning', partial:'primary', overdue:'danger', cancelled:'secondary'};
const STATUS_LABEL = {draft:'draft', paid:'paid', pending:'pending', partial:'partial', overdue:'overdue', cancelled:'cancelled'};

function consumeFlash(){
  const raw = sessionStorage.getItem('ktg_invoice_flash');
  if (!raw) return;
  sessionStorage.removeItem('ktg_invoice_flash');
  try {
    const flash = JSON.parse(raw);
    KTG.toast(flash.message, 'success', flash.title || 'Success');
  } catch (e) { /* ignore malformed flash data */ }
}

function init(){
  consumeFlash();
  document.querySelectorAll('#statusTabs .nav-link').forEach(btn => {
    btn.addEventListener('click', () => {
      document.querySelectorAll('#statusTabs .nav-link').forEach(b => b.classList.remove('active'));
      btn.classList.add('active');
      activeStatus = btn.dataset.status;
      render();
    });
  });
  document.querySelectorAll('#gstTabs .nav-link').forEach(btn => {
    btn.addEventListener('click', () => {
      document.querySelectorAll('#gstTabs .nav-link').forEach(b => b.classList.remove('active'));
      btn.classList.add('active');
      activeGst = btn.dataset.gst;
      render();
      renderStats();
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
      render();
    });
  });
  document.getElementById('invFromDate').addEventListener('change', render);
  document.getElementById('invToDate').addEventListener('change', render);
  document.getElementById('invClearFilterBtn').addEventListener('click', () => {
    document.querySelectorAll('.quick-filter-chip').forEach(c => c.classList.remove('active'));
    document.getElementById('invFromDate').value = '';
    document.getElementById('invToDate').value = '';
    document.getElementById('invFromDate').classList.add('d-none');
    document.getElementById('invToDate').classList.add('d-none');
    render();
  });
  document.getElementById('viewGridBtn').addEventListener('click', () => toggleView('grid'));
  document.getElementById('viewTableBtn').addEventListener('click', () => toggleView('table'));
  document.getElementById('invExportBtn').addEventListener('click', exportInvoices);
  document.getElementById('invPrintBtn').addEventListener('click', () => window.print());

  render();
  renderStats();
}

function toggleView(mode){
  document.getElementById('gridView').classList.toggle('d-none', mode !== 'grid');
  document.getElementById('gridFooter').classList.toggle('d-none', mode !== 'grid');
  document.getElementById('tableView').classList.toggle('d-none', mode !== 'table');
  document.getElementById('viewGridBtn').className = 'btn ' + (mode === 'grid' ? 'btn-soft-primary' : 'btn-light');
  document.getElementById('viewTableBtn').className = 'btn ' + (mode === 'table' ? 'btn-soft-primary' : 'btn-light');
}

function dueDaysLabel(inv){
  if (inv.status === 'paid' || inv.status === 'cancelled' || !inv.due_date) return '<span class="text-muted">—</span>';
  const days = KTG.dueDays(inv.due_date);
  const cls = days < 0 ? 'text-danger' : days <= 3 ? 'text-warning' : 'text-info';
  return `<span class="fw-700 ${cls}">${KTG.relativeDaysLabel(inv.due_date)}</span>`;
}

function renderStats(){
  const params = { action: 'stats' };
  if (activeGst !== 'all') params.gst_type = activeGst;
  $.get('ajax/invoices.php', params).done((res) => {
    if (!res.success) return;
    document.getElementById('statPaid').textContent = res.data.paid;
    document.getElementById('statPending').textContent = res.data.pending;
    document.getElementById('statPartial').textContent = res.data.partial;
    document.getElementById('statOverdue').textContent = res.data.overdue;
  });
}

function render(){
  const params = { action: 'list' };
  if (activeStatus) params.status = activeStatus;
  if (activeGst !== 'all') params.gst_type = activeGst;
  const from = document.getElementById('invFromDate').value;
  const to = document.getElementById('invToDate').value;
  if (from) params.from = from;
  if (to) params.to = to;

  $.get('ajax/invoices.php', params).done((res) => {
    if (!res.success) { KTG.toast(res.message || 'Could not load invoices.', 'danger', 'Error'); return; }
    items = res.data || [];
    renderList();
  });
}

function renderList(){
  document.getElementById('invCount').textContent = items.length;
  document.getElementById('invCountGrid').textContent = items.length;

  const gridView = document.getElementById('gridView');
  gridView.innerHTML = items.map(inv => {
    const balance = +inv.balance_due;
    return `
    <div class="col-sm-6 col-xl-4">
      <div class="doc-grid-card st-${inv.status}">
        <div class="d-flex justify-content-between align-items-start mb-2">
          <div class="avatar-sm bg-soft-${STATUS_COLOR[inv.status]}"><i class="bi ${STATUS_ICON[inv.status]}"></i></div>
          <span class="chip chip-${inv.status}">${inv.status}</span>
        </div>
        <a href="invoice-create.php?id=${inv.id}" class="cell-title d-block fs-14 mb-1">${inv.invoice_number}</a>
        <div class="fs-13 fw-600 text-info mb-2">${inv.customer_name}</div>
        <div class="fs-12 text-secondary mb-1"><i class="bi bi-calendar3 me-2 text-muted"></i>${inv.invoice_date}</div>
        <div class="fs-12 mb-2">${dueDaysLabel(inv)}</div>
        <div class="d-flex justify-content-between fs-12 text-secondary mb-3">
          <span><i class="bi bi-check2 me-1 text-success"></i>Paid ${KTG.moneyShort(+inv.advance_paid)}</span>
          ${balance > 0 ? `<span class="text-danger fw-600">Bal ${KTG.moneyShort(balance)}</span>` : ''}
        </div>
        <div class="d-flex justify-content-between align-items-center">
          <span class="dg-amt text-primary-KTG">${KTG.money(+inv.grand_total)}</span>
          <div class="dropdown">
            <button class="btn btn-sm btn-light" data-bs-toggle="dropdown"><i class="bi bi-three-dots-vertical"></i></button>
            <div class="dropdown-menu dropdown-menu-end">
              <a class="dropdown-item" href="invoice-create.php?id=${inv.id}"><i class="bi bi-eye"></i>View</a>
              <a class="dropdown-item" href="#" onclick="printInvoice(${inv.id}); return false;"><i class="bi bi-printer"></i>Print</a>
              <a class="dropdown-item" href="#" onclick="printInvoice(${inv.id}); return false;"><i class="bi bi-download"></i>Download PDF</a>
              <a class="dropdown-item" href="#" onclick="shareInvoice(); return false;"><i class="bi bi-share"></i>Share</a>
              <div class="dropdown-divider"></div>
              <a class="dropdown-item text-danger" href="#" onclick="deleteInvoice(${inv.id}); return false;"><i class="bi bi-trash"></i>Delete Invoice</a>
            </div>
          </div>
        </div>
      </div>
    </div>`;
  }).join('') || `<div class="col-12"><div class="section-card"><div class="empty-state"><div class="es-icon"><i class="bi bi-receipt"></i></div><h6>No invoices found</h6><p>Try a different date range or status filter.</p></div></div></div>`;
  KTG.fixDropdownOverflow(gridView);

  const tableBody = document.getElementById('invTableBody');
  tableBody.innerHTML = items.map(inv => {
    const balance = +inv.balance_due;
    return `
    <tr>
      <td><a href="invoice-create.php?id=${inv.id}" class="cell-title">${inv.invoice_number}</a></td>
      <td class="fw-600 text-info">${inv.customer_name}</td>
      <td class="fs-13">${inv.invoice_date}</td>
      <td class="fs-13">${dueDaysLabel(inv)}</td>
      <td class="fw-700 text-primary-KTG">${KTG.money(+inv.grand_total)}</td>
      <td class="text-success fw-600">${KTG.money(+inv.advance_paid)}</td>
      <td class="fw-600 ${balance>0?'text-danger':''}">${KTG.money(balance)}</td>
      <td><span class="chip chip-${inv.status}">${inv.status}</span></td>
      <td>
        <div class="dropdown">
          <button class="btn btn-sm btn-light" data-bs-toggle="dropdown"><i class="bi bi-three-dots-vertical"></i></button>
          <div class="dropdown-menu dropdown-menu-end">
            <a class="dropdown-item" href="invoice-create.php?id=${inv.id}"><i class="bi bi-eye"></i>View</a>
            <a class="dropdown-item" href="#" onclick="printInvoice(${inv.id}); return false;"><i class="bi bi-printer"></i>Print</a>
            <a class="dropdown-item" href="#" onclick="printInvoice(${inv.id}); return false;"><i class="bi bi-download"></i>Download PDF</a>
            <a class="dropdown-item" href="#" onclick="shareInvoice(); return false;"><i class="bi bi-share"></i>Share</a>
            <div class="dropdown-divider"></div>
            <a class="dropdown-item text-danger" href="#" onclick="deleteInvoice(${inv.id}); return false;"><i class="bi bi-trash"></i>Delete Invoice</a>
          </div>
        </div>
      </td>
    </tr>`;
  }).join('') || `<tr><td colspan="9"><div class="empty-state"><div class="es-icon"><i class="bi bi-receipt"></i></div><h6>No invoices found</h6><p>Try a different date range or status filter.</p></div></td></tr>`;
  KTG.makeResponsiveTable(tableBody.closest('table'));
  KTG.fixDropdownOverflow(tableBody);
}

function printInvoice(id){
  window.open('invoice-preview.php?id=' + id, '_blank');
}

function shareInvoice(){
  KTG.toast('Sharing is not available yet.', 'primary', 'Coming Soon');
}

function deleteInvoice(id){
  const inv = items.find(i => i.id === id);
  if (!inv) return;
  KTG.confirmDialog({
    title: 'Delete Invoice?',
    body: `This will permanently remove invoice ${inv.invoice_number} from your invoice list.`,
    confirmText: 'Delete Invoice',
    confirmClass: 'btn-danger',
    onConfirm: () => {
      $.post('ajax/invoices.php', { action: 'delete', id }).done((res) => {
        if (!res.success) { KTG.toast(res.message || 'Could not delete the invoice.', 'danger', 'Error'); return; }
        KTG.toast('Invoice has been deleted.', 'success', 'Deleted');
        render();
        renderStats();
      }).fail((xhr) => {
        const msg = (xhr.responseJSON && xhr.responseJSON.message) || 'Could not delete the invoice.';
        KTG.toast(msg, 'danger', 'Error');
      });
    }
  });
}

function exportInvoices(){
  const rows = [['Invoice #','Customer','Date','Amount','Paid','Balance','Status']];
  items.forEach(inv => rows.push([inv.invoice_number, inv.customer_name, inv.invoice_date, inv.grand_total, inv.advance_paid, inv.balance_due, inv.status]));
  KTG.exportCsv('Invoices.csv', rows);
  KTG.toast('Invoices exported as CSV (opens in Excel).', 'success', 'Export Ready');
}
JS;
?>
<?php include __DIR__ . '/components/footer.php'; ?>
