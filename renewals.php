<?php
$pageTitle = "Renewals &amp; Hosting";
$pageKey = "renewals";
$extraHead = <<<'HTML'
<style>
.expiry-badge{ font-size:10.5px; font-weight:700; padding:.15rem .5rem; border-radius: var(--radius-pill); white-space:nowrap; }
.link-cell{ max-width:220px; overflow:hidden; text-overflow:ellipsis; white-space:nowrap; display:inline-block; vertical-align:middle; }
.gst-toggle-btn{ flex:1; border:2px solid var(--border); border-radius:12px; padding:.6rem; text-align:center; cursor:pointer; transition: all .18s var(--ease); background:#fff; }
.gst-toggle-btn.active{ border-color: var(--primary); background: var(--primary-light); }
</style>
HTML;
include __DIR__ . '/components/header.php';
?>
      <div class="page-head fade-in-up">
        <div>
          <h1>Renewals &amp; Hosting</h1>
          <nav class="breadcrumb"><span class="breadcrumb-item"><a href="index.php">Dashboard</a></span><span class="breadcrumb-item active">Renewals</span></nav>
        </div>
        <div class="d-flex gap-2">
          <button class="btn btn-light" id="exportBtn"><i class="bi bi-filetype-xlsx me-1"></i>Export</button>
          <button class="btn btn-primary" id="newRenewalBtn"><i class="bi bi-plus-lg me-1"></i>New Renewal Entry</button>
        </div>
      </div>

      <div class="row g-3 mb-3 stagger">
        <div class="col-6 col-md-3"><div class="stat-card accent-primary"><div class="stat-icon bg-soft-primary"><i class="bi bi-hdd-network-fill"></i></div><div class="stat-label">Total Tracked</div><div class="stat-value" id="statTotal">0</div></div></div>
        <div class="col-6 col-md-3"><div class="stat-card accent-warning"><div class="stat-icon bg-soft-warning"><i class="bi bi-hourglass-split"></i></div><div class="stat-label">Expiring This Month</div><div class="stat-value" id="statThisMonth">0</div></div></div>
        <div class="col-6 col-md-3"><div class="stat-card accent-danger"><div class="stat-icon bg-soft-danger"><i class="bi bi-exclamation-triangle-fill"></i></div><div class="stat-label">Expired</div><div class="stat-value" id="statExpired">0</div></div></div>
        <div class="col-6 col-md-3"><div class="stat-card accent-success"><div class="stat-icon bg-soft-success"><i class="bi bi-check-circle-fill"></i></div><div class="stat-label">Paid</div><div class="stat-value" id="statPaid">0</div></div></div>
      </div>

      <!-- Filters — collapsed by default; expand to search/filter -->
      <div class="section-card mb-3 fade-in-up">
        <div class="section-body py-3">
          <button class="btn btn-light d-flex align-items-center justify-content-between w-100" type="button" data-bs-toggle="collapse" data-bs-target="#renewalFiltersBody" aria-expanded="false" aria-controls="renewalFiltersBody">
            <span><i class="bi bi-funnel me-2"></i>Filters</span>
            <i class="bi bi-chevron-down"></i>
          </button>
          <div class="collapse" id="renewalFiltersBody">
            <div class="d-flex flex-wrap align-items-center gap-2 mt-3">
              <button type="button" class="quick-filter-chip" data-range="all"><i class="bi bi-collection"></i>Show All</button>
              <button type="button" class="quick-filter-chip" data-range="last_month"><i class="bi bi-calendar-minus"></i>Last Month</button>
              <button type="button" class="quick-filter-chip active" data-range="this_month"><i class="bi bi-calendar-check"></i>This Month</button>
              <button type="button" class="quick-filter-chip" data-range="this_week"><i class="bi bi-calendar-week"></i>This Week</button>
              <button type="button" class="quick-filter-chip" data-range="next_month"><i class="bi bi-calendar-plus"></i>Next Month</button>
              <button type="button" class="quick-filter-chip" data-range="custom"><i class="bi bi-sliders"></i>Custom Range</button>
              <input type="date" class="form-control d-none" id="fromDate" style="max-width:160px;" title="From date">
              <input type="date" class="form-control d-none" id="toDate" style="max-width:160px;" title="To date">
              <button class="btn btn-light ms-auto" id="clearFiltersBtn" title="Clear all filters"><i class="bi bi-x-lg"></i></button>
            </div>
            <div class="row g-2 align-items-end mt-3">
              <div class="col-md-6"><label class="form-label mb-1">Customer</label><select class="form-select" id="customerFilter"><option value="">All Customers</option></select></div>
              <div class="col-md-6"><label class="form-label mb-1">Paid Status</label><select class="form-select" id="statusFilter"><option value="">All Status</option></select></div>
            </div>
          </div>
        </div>
      </div>

      <div class="section-card fade-in-up">
        <div class="section-head"><h5>Tracked Projects</h5><span class="chip chip-inprogress" id="resultsCount">0 entries</span></div>
        <div class="table-responsive">
          <table class="table table-modern mb-0">
            <thead>
              <tr><th style="width:46px;">S.No</th><th>Project / Customer</th><th>Type</th><th>Server</th><th>Live Link</th><th>Expiry</th><th>Paid Status</th><th></th></tr>
            </thead>
            <tbody id="resultsBody"></tbody>
          </table>
        </div>
      </div>

<?php
$pageModals = <<<'HTML'
<!-- Add / Edit Renewal Modal -->
<div class="modal fade" id="renewalModal" tabindex="-1">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header"><h5 class="modal-title" id="renewalModalTitle"><i class="bi bi-arrow-repeat text-primary-KTG me-2"></i>New Renewal Entry</h5><button class="btn-close" data-bs-dismiss="modal"></button></div>
      <div class="modal-body">
        <div class="row g-3">
          <div class="col-md-6">
            <label class="form-label">Customer <span class="req">*</span></label>
            <select class="form-select" id="rmCustomer"><option value="">Select customer...</option></select>
          </div>
          <div class="col-md-6">
            <label class="form-label">Project Name <span class="req">*</span></label>
            <input class="form-control" id="rmProjectName" placeholder="e.g. Corporate Website Revamp">
          </div>

          <div class="col-md-6"><label class="form-label">Project Type</label><select class="form-select" id="rmType"><option value="">Select Type</option></select></div>
          <div class="col-md-6"><label class="form-label">Server</label><select class="form-select" id="rmServer"></select></div>

          <div class="col-md-6"><label class="form-label">Renewal Type</label><select class="form-select" id="rmRenewalType"></select></div>
          <div class="col-md-6"><label class="form-label">Expiry Date <span class="req">*</span></label><input type="date" class="form-control" id="rmExpiry"></div>

          <div class="col-md-6"><label class="form-label">Domain Control</label><input class="form-control" id="rmDomainControl" placeholder="e.g. With us (KTG) / Client-side (GoDaddy)"></div>
          <div class="col-md-6"><label class="form-label">Paid Status</label><select class="form-select" id="rmPaidStatus"></select></div>

          <div class="col-12"><label class="form-label">Live Project Link <span class="fs-11 text-muted fw-400">(include admin login details if any)</span></label>
            <textarea class="form-control" rows="2" id="rmLiveLink" placeholder="https://example.com — admin: example.com/wp-admin, user: admin"></textarea>
          </div>

          <div class="col-12"><div id="renewalFormError" class="alert alert-danger py-2 fs-13 d-none mb-0" style="border-radius:11px;"></div></div>
        </div>
      </div>
      <div class="modal-footer"><button class="btn btn-light" data-bs-dismiss="modal">Cancel</button><button class="btn btn-primary" id="saveRenewalBtn">Save Entry</button></div>
    </div>
  </div>
</div>

<!-- View Details Modal -->
<div class="modal fade" id="viewRenewalModal" tabindex="-1">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header"><h5 class="modal-title"><i class="bi bi-hdd-network-fill text-primary-KTG me-2"></i>Domain &amp; Hosting Details</h5><button class="btn-close" data-bs-dismiss="modal"></button></div>
      <div class="modal-body" id="viewRenewalBody"></div>
      <div class="modal-footer">
        <button class="btn btn-light" data-bs-dismiss="modal">Close</button>
        <button class="btn btn-outline-primary" id="viewEditBtn"><i class="bi bi-pencil me-1"></i>Edit</button>
        <button class="btn btn-primary" id="viewInvoiceBtn"><i class="bi bi-receipt me-1"></i>Generate Invoice</button>
      </div>
    </div>
  </div>
</div>

<!-- Generate Invoice Modal -->
<div class="modal fade" id="invoiceModal" tabindex="-1">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header"><h5 class="modal-title"><i class="bi bi-receipt text-primary-KTG me-2"></i>Generate Renewal Invoice</h5><button class="btn-close" data-bs-dismiss="modal"></button></div>
      <div class="modal-body">
        <div class="bill-block-label mb-2" id="giProjectLabel">Project</div>
        <div class="d-flex gap-3 mb-3">
          <div class="gst-toggle-btn active" id="giWithGst"><i class="bi bi-receipt-cutoff d-block mb-1 text-primary-KTG"></i><div class="fw-700 fs-13">With GST</div></div>
          <div class="gst-toggle-btn" id="giWithoutGst"><i class="bi bi-receipt d-block mb-1 text-secondary"></i><div class="fw-700 fs-13">Without GST</div></div>
        </div>
        <div class="row g-3 mb-3" id="giTaxOptions">
          <div class="col-md-6"><label class="form-label">GST Type</label>
            <select class="form-select" id="giTaxType"><option value="sgst_cgst">SGST + CGST</option><option value="igst">IGST Only</option></select>
          </div>
          <div class="col-md-6"><label class="form-label">GST Rate</label><select class="form-select" id="giTaxRate"></select></div>
        </div>
        <div class="table-responsive mb-3">
          <table class="table table-modern mb-0">
            <thead><tr><th>Description</th><th class="col-qty">Qty</th><th style="width:130px;">Rate</th><th style="width:130px;">Amount</th></tr></thead>
            <tbody>
              <tr>
                <td id="giDesc" class="fw-600"></td>
                <td><input type="number" class="form-control" id="giQty" value="1" min="1"></td>
                <td><input type="number" class="form-control" id="giRate" value="5000"></td>
                <td class="fw-700" id="giAmount">₹0</td>
              </tr>
            </tbody>
          </table>
        </div>
        <div class="bill-totals">
          <div class="bt-row"><span class="text-muted">Subtotal</span><span class="fw-600" id="giSubtotal">₹0</span></div>
          <div class="bt-row" id="giSgstRow"><span class="text-muted" id="giSgstLabel">SGST 9%</span><span class="fw-600" id="giSgstAmt">₹0</span></div>
          <div class="bt-row" id="giCgstRow"><span class="text-muted" id="giCgstLabel">CGST 9%</span><span class="fw-600" id="giCgstAmt">₹0</span></div>
          <div class="bt-row" id="giIgstRow"><span class="text-muted" id="giIgstLabel">IGST 18%</span><span class="fw-600" id="giIgstAmt">₹0</span></div>
          <div class="bt-row bt-grand"><span>Grand Total</span><span id="giTotal">₹0</span></div>
        </div>
      </div>
      <div class="modal-footer">
        <button class="btn btn-light" data-bs-dismiss="modal">Close</button>
        <button class="btn btn-primary" id="giPreviewBtn"><i class="bi bi-eye me-1"></i>Preview &amp; Print Invoice</button>
      </div>
    </div>
  </div>
</div>
HTML;
?>
<?php
$pageScript = <<<'JS'
document.addEventListener('DOMContentLoaded', () => setTimeout(init, 150));

let items = [];
let editId = null;
let viewId = null;
let giGstEnabled = true;
let giContext = null;

function init(){
  loadCustomerOptions();

  document.getElementById('rmServer').innerHTML = '<option value="">Select Server</option>';
  $.get('ajax/masters.php', { master: 'servers', action: 'list' }).done((res) => {
    document.getElementById('rmServer').innerHTML = '<option value="">Select Server</option>' + (res.data || []).map(s => `<option value="${s.id}">${s.name}</option>`).join('');
  });
  $.get('ajax/masters.php', { master: 'renewal_types', action: 'list' }).done((res) => {
    document.getElementById('rmRenewalType').innerHTML = '<option value="">Select Renewal Type</option>' + (res.data || []).map(t => `<option value="${t.id}">${t.name}</option>`).join('');
  });
  $.get('ajax/masters.php', { master: 'paid_status_types', action: 'list' }).done((res) => {
    const opts = (res.data || []).map(t => `<option value="${t.id}">${t.name}</option>`).join('');
    document.getElementById('rmPaidStatus').innerHTML = '<option value="">Select Status</option>' + opts;
    document.getElementById('statusFilter').innerHTML = '<option value="">All Status</option>' + opts;
  });
  $.get('ajax/masters.php', { master: 'project_types', action: 'list' }).done((res) => {
    document.getElementById('rmType').innerHTML = '<option value="">Select Type</option>' + (res.data || []).map(t => `<option value="${t.id}">${t.name}</option>`).join('');
  });
  $.get('ajax/masters.php', { master: 'taxes', action: 'list' }).done((res) => {
    document.getElementById('giTaxRate').innerHTML = (res.data || []).filter(t => +t.percent > 0).map(t => `<option value="${t.percent}">${t.name}</option>`).join('');
  });

  document.getElementById('rmCustomer').addEventListener('change', onCustomerSelect);

  document.querySelectorAll('.quick-filter-chip').forEach(chip => {
    chip.addEventListener('click', () => {
      document.querySelectorAll('.quick-filter-chip').forEach(c => c.classList.remove('active'));
      chip.classList.add('active');
      applyQuickRange(chip.dataset.range);
    });
  });
  ['fromDate','toDate','customerFilter','statusFilter'].forEach(id => {
    document.getElementById(id).addEventListener('change', render);
  });
  document.getElementById('clearFiltersBtn').addEventListener('click', clearFilters);

  document.getElementById('newRenewalBtn').addEventListener('click', () => openModal(null));
  document.getElementById('saveRenewalBtn').addEventListener('click', saveRenewal);
  document.getElementById('exportBtn').addEventListener('click', exportRenewals);

  document.getElementById('giWithGst').addEventListener('click', () => setGiGst(true));
  document.getElementById('giWithoutGst').addEventListener('click', () => setGiGst(false));
  document.getElementById('giTaxType').addEventListener('change', calcGiTotals);
  document.getElementById('giTaxRate').addEventListener('change', calcGiTotals);
  document.getElementById('giQty').addEventListener('input', calcGiTotals);
  document.getElementById('giRate').addEventListener('input', calcGiTotals);
  document.getElementById('giPreviewBtn').addEventListener('click', openInvoicePreview);

  document.getElementById('viewEditBtn').addEventListener('click', () => {
    bootstrap.Modal.getInstance(document.getElementById('viewRenewalModal')).hide();
    openModal(viewId);
  });
  document.getElementById('viewInvoiceBtn').addEventListener('click', () => {
    bootstrap.Modal.getInstance(document.getElementById('viewRenewalModal')).hide();
    openInvoiceModal(viewId);
  });

  applyQuickRange('this_month');
}

let customerOptions = [];
function customerOptionLabel(c){
  const sameAsName = !c.company_name || c.company_name.trim().toLowerCase() === (c.name || '').trim().toLowerCase();
  return sameAsName ? c.name : `${c.name} - ${c.company_name}`;
}

function loadCustomerOptions(){
  $.get('ajax/renewals.php', { action: 'customer_options' }).done((res) => {
    customerOptions = res.data || [];
    const opts = customerOptions.map(c => `<option value="${c.id}">${KTG.escAttr(customerOptionLabel(c))}</option>`).join('');
    document.getElementById('rmCustomer').innerHTML = '<option value="">Select customer...</option>' + opts;
    document.getElementById('customerFilter').innerHTML = '<option value="">All Customers</option>' + opts;
  });
}

function onCustomerSelect(){
  const c = customerOptions.find(x => String(x.id) === document.getElementById('rmCustomer').value);
  if (c && c.project_type_id) document.getElementById('rmType').value = c.project_type_id;
}

function applyQuickRange(range){
  const isCustom = range === 'custom';
  document.getElementById('fromDate').classList.toggle('d-none', !isCustom);
  document.getElementById('toDate').classList.toggle('d-none', !isCustom);
  if (!isCustom) {
    const { from, to } = KTG.dateRangeFor(range);
    document.getElementById('fromDate').value = from;
    document.getElementById('toDate').value = to;
  }
  render();
}

function daysDiff(dateStr){
  const d = new Date(dateStr + 'T00:00:00');
  const today = new Date(); today.setHours(0,0,0,0);
  return Math.round((d - today) / 86400000);
}

function expiryBadge(dateStr){
  const days = daysDiff(dateStr);
  if (days < 0) return `<span class="expiry-badge bg-soft-danger">Expired ${Math.abs(days)}d ago</span>`;
  if (days <= 30) return `<span class="expiry-badge bg-soft-warning">${days}d left</span>`;
  return `<span class="expiry-badge bg-soft-success">${days}d left</span>`;
}
function paidChipClass(name){
  const n = (name || '').toLowerCase();
  return n === 'paid' ? 'active' : n === 'unpaid' ? 'overdue' : 'pending';
}

function render(){
  const params = {
    action: 'list',
    from: document.getElementById('fromDate').value,
    to: document.getElementById('toDate').value,
    customer_id: document.getElementById('customerFilter').value,
    paid_status_id: document.getElementById('statusFilter').value
  };
  $.get('ajax/renewals.php', params).done((res) => {
    if (!res.success) { KTG.toast(res.message || 'Could not load renewals.', 'danger', 'Error'); return; }
    items = res.data || [];
    renderList();
    renderStats();
  });
}

function renderList(){
  document.getElementById('resultsCount').textContent = items.length + ' entries';
  document.getElementById('resultsBody').innerHTML = items.map((r, i) => `
    <tr>
      <td class="fs-13 text-muted">${i+1}</td>
      <td>
        <div class="cell-title">${r.project_name}</div>
        <div class="cell-sub">${r.customer_name}</div>
      </td>
      <td>${r.project_type_name ? `<span class="chip chip-inprogress">${r.project_type_name}</span>` : '<span class="text-muted">—</span>'}</td>
      <td class="fs-13">${r.server_name || '—'}</td>
      <td><a href="#" class="link-cell fs-13" title="${(r.live_link||'').replace(/"/g,'&quot;')}" onclick="openViewModal(${r.id}); return false;">${r.live_link || '—'}</a></td>
      <td><div class="fs-13 fw-600">${r.expiry_date}</div>${expiryBadge(r.expiry_date)}</td>
      <td>${r.paid_status_name ? `<span class="chip chip-${paidChipClass(r.paid_status_name)}">${r.paid_status_name}</span>` : '<span class="text-muted">—</span>'}</td>
      <td>
        <div class="d-flex gap-1">
          <button class="btn btn-sm btn-light" title="View" onclick="openViewModal(${r.id})"><i class="bi bi-eye"></i></button>
          <div class="dropdown">
            <button class="btn btn-sm btn-light" data-bs-toggle="dropdown"><i class="bi bi-three-dots-vertical"></i></button>
            <div class="dropdown-menu dropdown-menu-end">
              <a class="dropdown-item" href="#" onclick="openInvoiceModal(${r.id}); return false;"><i class="bi bi-receipt"></i>Generate Invoice</a>
              <a class="dropdown-item" href="#" onclick="openModal(${r.id}); return false;"><i class="bi bi-pencil"></i>Edit</a>
              <div class="dropdown-divider"></div>
              <a class="dropdown-item text-danger" href="#" onclick="deleteRenewal(${r.id}); return false;"><i class="bi bi-trash"></i>Delete</a>
            </div>
          </div>
        </div>
      </td>
    </tr>`).join('') || `<tr><td colspan="8"><div class="empty-state"><div class="es-icon"><i class="bi bi-hdd-network"></i></div><h6>No matching entries</h6><p>Try widening the date range or clearing filters.</p></div></td></tr>`;
  KTG.makeResponsiveTable(document.getElementById('resultsBody').closest('table'));
}

function clearFilters(){
  document.getElementById('statusFilter').value = '';
  document.getElementById('customerFilter').value = '';
  document.querySelectorAll('.quick-filter-chip').forEach(c => c.classList.remove('active'));
  document.querySelector('.quick-filter-chip[data-range="all"]').classList.add('active');
  applyQuickRange('all');
}

function renderStats(){
  $.get('ajax/renewals.php', { action: 'stats' }).done((res) => {
    const s = (res && res.data) || {};
    document.getElementById('statTotal').textContent = s.total || 0;
    document.getElementById('statThisMonth').textContent = s.this_month || 0;
    document.getElementById('statExpired').textContent = s.expired || 0;
    document.getElementById('statPaid').textContent = s.paid || 0;
  });
}

function openModal(id){
  editId = id;
  const title = document.getElementById('renewalModalTitle');
  const btn = document.getElementById('saveRenewalBtn');
  document.getElementById('renewalFormError').classList.add('d-none');
  if (id === null) {
    title.innerHTML = '<i class="bi bi-arrow-repeat text-primary-KTG me-2"></i>New Renewal Entry';
    btn.textContent = 'Save Entry';
    document.getElementById('rmCustomer').value = '';
    document.getElementById('rmProjectName').value = '';
    document.getElementById('rmType').value = '';
    document.getElementById('rmServer').value = '';
    document.getElementById('rmRenewalType').value = '';
    document.getElementById('rmPaidStatus').value = '';
    document.getElementById('rmDomainControl').value = '';
    document.getElementById('rmLiveLink').value = '';
    document.getElementById('rmExpiry').value = '';
  } else {
    const r = items.find(x => x.id === id);
    if (!r) return;
    title.innerHTML = '<i class="bi bi-pencil-square text-primary-KTG me-2"></i>Edit Renewal Entry';
    btn.textContent = 'Save Changes';
    document.getElementById('rmCustomer').value = r.customer_id;
    document.getElementById('rmProjectName').value = r.project_name;
    document.getElementById('rmType').value = r.project_type_id || '';
    document.getElementById('rmServer').value = r.server_id || '';
    document.getElementById('rmRenewalType').value = r.renewal_type_id || '';
    document.getElementById('rmPaidStatus').value = r.paid_status_id || '';
    document.getElementById('rmDomainControl').value = r.domain_control || '';
    document.getElementById('rmLiveLink').value = r.live_link || '';
    document.getElementById('rmExpiry').value = r.expiry_date;
  }
  new bootstrap.Modal(document.getElementById('renewalModal')).show();
}

function saveRenewal(){
  const payload = {
    action: editId === null ? 'add' : 'edit',
    id: editId,
    customer_id: document.getElementById('rmCustomer').value,
    project_name: document.getElementById('rmProjectName').value.trim(),
    project_type_id: document.getElementById('rmType').value,
    server_id: document.getElementById('rmServer').value,
    renewal_type_id: document.getElementById('rmRenewalType').value,
    paid_status_id: document.getElementById('rmPaidStatus').value,
    domain_control: document.getElementById('rmDomainControl').value.trim(),
    live_link: document.getElementById('rmLiveLink').value.trim(),
    expiry_date: document.getElementById('rmExpiry').value
  };
  const $err = $('#renewalFormError').addClass('d-none');
  const $btn = $('#saveRenewalBtn').prop('disabled', true);

  $.post('ajax/renewals.php', payload).done((res) => {
    if (res.success) {
      bootstrap.Modal.getInstance(document.getElementById('renewalModal')).hide();
      KTG.toast(editId === null ? 'Renewal entry has been added.' : 'Renewal entry has been updated.', 'success', editId === null ? 'Entry Added' : 'Entry Updated');
      render();
    } else {
      $err.text(res.message || 'Could not save renewal entry.').removeClass('d-none');
    }
  }).fail(() => {
    $err.text('Something went wrong. Please try again.').removeClass('d-none');
  }).always(() => $btn.prop('disabled', false));
}

function deleteRenewal(id){
  const r = items.find(x => x.id === id);
  if (!r) return;
  KTG.confirmDialog({
    title: 'Delete Renewal Entry?',
    body: `This will remove tracking for "${r.project_name}".`,
    confirmText: 'Delete', confirmClass: 'btn-danger',
    onConfirm: () => {
      $.post('ajax/renewals.php', { action: 'delete', id }).done((res) => {
        if (res.success) { KTG.toast('Renewal entry deleted.', 'danger', 'Deleted'); render(); }
        else KTG.toast(res.message || 'Could not delete.', 'danger', 'Error');
      });
    }
  });
}

function exportRenewals(){
  const rows = [['S.No','Project','Customer','Type','Server','Domain Control','Live Link','Expiry Date','Renewal Type','Paid Status']];
  items.forEach((r,i) => rows.push([i+1, r.project_name, r.customer_name, r.project_type_name||'', r.server_name||'', r.domain_control||'', r.live_link||'', r.expiry_date, r.renewal_type_name||'', r.paid_status_name||'']));
  KTG.exportCsv('Renewals.csv', rows);
  KTG.toast('Renewals exported as CSV (opens in Excel).', 'success', 'Export Ready');
}

/* ---------------- View Details ---------------- */
function openViewModal(id){
  viewId = id;
  const r = items.find(x => x.id === id);
  if (!r) return;
  const days = daysDiff(r.expiry_date);
  document.getElementById('viewRenewalBody').innerHTML = `
    <div class="d-flex align-items-start justify-content-between mb-3">
      <div>
        <h5 class="mb-1">${r.project_name}</h5>
        <div class="cell-sub">${r.customer_name} &middot; <span class="chip chip-inprogress">${r.project_type_name || '—'}</span></div>
      </div>
      <span class="chip chip-${paidChipClass(r.paid_status_name)}">${r.paid_status_name || '—'}</span>
    </div>
    <div class="row g-3">
      <div class="col-md-6">
        <div class="bill-block-label">Server</div>
        <div class="fs-14 fw-600 mb-3"><i class="bi bi-hdd-network me-2 text-primary-KTG"></i>${r.server_name || '—'}</div>
        <div class="bill-block-label">Domain Control</div>
        <div class="fs-14 mb-3">${r.domain_control || '—'}</div>
        <div class="bill-block-label">Renewal Type</div>
        <div class="fs-14 mb-3"><span class="chip chip-inprogress">${r.renewal_type_name || '—'}</span></div>
      </div>
      <div class="col-md-6">
        <div class="bill-block-label">Expiry Date</div>
        <div class="fs-14 fw-600 mb-1">${r.expiry_date}</div>
        <div class="mb-3">${expiryBadge(r.expiry_date)} <span class="fs-12 text-muted">(${days<0?'expired':'expires in'} ${Math.abs(days)} day${Math.abs(days)===1?'':'s'})</span></div>
        <div class="bill-block-label">Paid Status</div>
        <div class="fs-14 mb-3">${r.paid_status_name || '—'}</div>
      </div>
      <div class="col-12">
        <div class="bill-block-label">Live Project Link &amp; Login Details</div>
        <div class="section-card p-3 fs-13" style="background:var(--surface-alt); white-space:pre-wrap;">${r.live_link || '—'}</div>
      </div>
    </div>`;
  new bootstrap.Modal(document.getElementById('viewRenewalModal')).show();
}

/* ---------------- Generate Invoice (preview only — Invoices module is a later phase) ---------------- */
function openInvoiceModal(id){
  const r = items.find(x => x.id === id);
  if (!r) return;
  giContext = r;
  document.getElementById('giProjectLabel').textContent = `${r.project_name} — ${r.customer_name}`;
  document.getElementById('giDesc').textContent = `${r.renewal_type_name || 'Renewal'} — ${r.project_name} (${r.server_name || 'hosting'})`;
  document.getElementById('giQty').value = 1;
  document.getElementById('giRate').value = 5000;
  setGiGst(true);
  new bootstrap.Modal(document.getElementById('invoiceModal')).show();
}

function setGiGst(state){
  giGstEnabled = state;
  document.getElementById('giWithGst').classList.toggle('active', state);
  document.getElementById('giWithoutGst').classList.toggle('active', !state);
  document.getElementById('giTaxOptions').style.display = state ? 'flex' : 'none';
  calcGiTotals();
}

function calcGiTotals(){
  const qty = +document.getElementById('giQty').value || 0;
  const rate = +document.getElementById('giRate').value || 0;
  const amount = qty * rate;
  document.getElementById('giAmount').textContent = KTG.money(amount);

  const percent = giGstEnabled ? (+document.getElementById('giTaxRate').value || 0) : 0;
  const taxType = giGstEnabled ? document.getElementById('giTaxType').value : 'none';
  const taxAmount = amount * percent / 100;
  const half = percent / 2;

  document.getElementById('giSubtotal').textContent = KTG.money(amount);
  document.getElementById('giSgstLabel').textContent = `SGST ${half}%`;
  document.getElementById('giCgstLabel').textContent = `CGST ${half}%`;
  document.getElementById('giIgstLabel').textContent = `IGST ${percent}%`;
  document.getElementById('giSgstAmt').textContent = KTG.money(Math.round(taxAmount/2));
  document.getElementById('giCgstAmt').textContent = KTG.money(Math.round(taxAmount/2));
  document.getElementById('giIgstAmt').textContent = KTG.money(Math.round(taxAmount));
  document.getElementById('giSgstRow').classList.toggle('bt-hidden', taxType !== 'sgst_cgst');
  document.getElementById('giCgstRow').classList.toggle('bt-hidden', taxType !== 'sgst_cgst');
  document.getElementById('giIgstRow').classList.toggle('bt-hidden', taxType !== 'igst');
  document.getElementById('giTotal').textContent = KTG.money(Math.round(amount + taxAmount));
}

function openInvoicePreview(){
  const r = giContext;
  const qty = +document.getElementById('giQty').value || 0;
  const rate = +document.getElementById('giRate').value || 0;
  const subtotal = qty * rate;
  const percent = giGstEnabled ? (+document.getElementById('giTaxRate').value || 0) : 0;
  const taxType = giGstEnabled ? document.getElementById('giTaxType').value : 'none';
  const taxAmount = Math.round(subtotal * percent / 100);
  const grandTotal = subtotal + taxAmount;

  const previewWin = window.open('', '_blank');
  $.get('ajax/customers.php', { action: 'view', id: r.customer_id }).always((res) => {
    const c = res && res.data;
    const data = {
      docType: giGstEnabled ? 'TAX INVOICE' : 'BILL OF SUPPLY',
      invNumber: 'INV-RENEWAL-' + r.id,
      invDate: new Date().toISOString().slice(0,10),
      dueDate: r.expiry_date,
      customer: c ? {name:c.name, gst:c.gst_number||'—', address:c.address||'—', mobile:c.mobile} : {name:r.customer_name, gst:'—', address:'—', mobile:'—'},
      items: [{service: document.getElementById('giDesc').textContent, desc: r.live_link || '', qty, rate}],
      subtotal, taxType, percent, halfPercent: percent/2, taxAmount,
      grandTotal, advancePaid: 0, balanceDue: grandTotal,
      terms: `Renewal invoice for ${(r.renewal_type_name||'').toLowerCase()} hosting/domain renewal — ${r.project_name}. Please settle before the service expiry date to avoid downtime.`,
      bank: {name:'Knock The Globe Technologies', account:'0012299171', bank:'Kotak Bank', ifsc:'KKBK0008775'}
    };
    sessionStorage.setItem('ktg_invoice_preview', JSON.stringify(data));
    if (previewWin) previewWin.location = 'invoice-preview.php';
  });
}
JS;
?>
<?php include __DIR__ . '/components/footer.php'; ?>
