<?php
$pageTitle = "Renewals &amp; Hosting";
$pageKey = "renewals";
$extraHead = <<<'HTML'
<style>
.quick-filter-chip{ border:1.5px solid var(--border); border-radius: var(--radius-pill); padding:.4rem 1rem; font-size:12.5px; font-weight:700; background:#fff; color:var(--text-secondary); cursor:pointer; transition: all .15s var(--ease); }
.quick-filter-chip:hover{ border-color: var(--primary-100); background: var(--primary-light); }
.quick-filter-chip.active{ background: var(--primary); border-color: var(--primary); color:#fff; }
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

      <!-- Expiry quick filters -->
      <div class="section-card mb-3 fade-in-up">
        <div class="section-body py-3">
          <div class="d-flex flex-wrap align-items-center gap-2">
            <span class="bill-block-label mb-0 me-1">Domain / Renewal Expiry</span>
            <button class="quick-filter-chip" data-range="last">Last Month</button>
            <button class="quick-filter-chip active" data-range="this">This Month</button>
            <button class="quick-filter-chip" data-range="next">Next Month</button>
            <button class="quick-filter-chip" data-range="all">Any Time</button>
          </div>
        </div>
      </div>

      <!-- Filters — collapsed by default; expand to search/filter -->
      <div class="section-card mb-3 fade-in-up">
        <div class="section-body py-3">
          <button class="btn btn-light d-flex align-items-center justify-content-between w-100" type="button" data-bs-toggle="collapse" data-bs-target="#renewalFiltersBody" aria-expanded="false" aria-controls="renewalFiltersBody">
            <span><i class="bi bi-funnel me-2"></i>Filters</span>
            <i class="bi bi-chevron-down"></i>
          </button>
          <div class="collapse" id="renewalFiltersBody">
            <div class="row g-2 align-items-end mt-2">
              <div class="col-md-5">
                <label class="form-label mb-1">Search</label>
                <div class="position-relative">
                  <i class="bi bi-search position-absolute" style="left:14px; top:50%; transform:translateY(-50%); color:var(--text-muted);"></i>
                  <input type="text" class="form-control ps-5" id="searchInput" placeholder="Search project, customer or live link...">
                </div>
              </div>
              <div class="col-md-3"><label class="form-label mb-1">Hosted On (Server)</label><select class="form-select" id="serverFilter"><option value="">All Servers</option></select></div>
              <div class="col-md-3"><label class="form-label mb-1">Paid Status</label><select class="form-select" id="statusFilter"><option value="">All Status</option></select></div>
              <div class="col-md-1"><button class="btn btn-light w-100" id="clearFiltersBtn" title="Clear all filters"><i class="bi bi-x-lg"></i></button></div>
            </div>
            <div class="row g-2 align-items-end mt-2">
              <div class="col-md-2"><label class="form-label mb-1">From</label><input type="date" class="form-control" id="fromDate"></div>
              <div class="col-md-2"><label class="form-label mb-1">To</label><input type="date" class="form-control" id="toDate"></div>
              <div class="col-md-4"><label class="form-label mb-1">Customer</label><select class="form-select" id="customerFilter"><option value="">All Customers</option></select></div>
              <div class="col-md-4"><label class="form-label mb-1">Project Type</label><select class="form-select" id="typeFilter"><option value="">All Types</option></select></div>
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
            <label class="form-label">Project Name <span class="req">*</span></label>
            <select class="form-select" id="rmProject"></select>
          </div>
          <div class="col-md-3"><label class="form-label">Customer</label><input class="form-control" id="rmCustomer" readonly></div>
          <div class="col-md-3"><label class="form-label">Project Type</label><input class="form-control" id="rmType" readonly></div>

          <div class="col-md-6"><label class="form-label">Server</label><select class="form-select" id="rmServer"></select></div>
          <div class="col-md-6"><label class="form-label">Renewal Type</label><select class="form-select" id="rmRenewalType"></select></div>

          <div class="col-md-6"><label class="form-label">Domain Control</label><input class="form-control" id="rmDomainControl" placeholder="e.g. With us (KTG) / Client-side (GoDaddy)"></div>
          <div class="col-md-6"><label class="form-label">Expiry Date</label><input type="date" class="form-control" id="rmExpiry"></div>

          <div class="col-12"><label class="form-label">Live Project Link <span class="fs-11 text-muted fw-400">(include admin login details if any)</span></label>
            <textarea class="form-control" rows="2" id="rmLiveLink" placeholder="https://example.com — admin: example.com/wp-admin, user: admin"></textarea>
          </div>

          <div class="col-md-6"><label class="form-label">Paid Status</label><select class="form-select" id="rmPaidStatus"></select></div>
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

const TODAY = new Date('2026-08-06T00:00:00');
let renewalsList = [];
let editIndex = null;
let giGstEnabled = true;
let giContext = null;

function init(){
  renewalsList = KTG.DATA.renewals.map(r => ({...r}));

  const projects = KTG.DATA.projects;
  document.getElementById('rmProject').innerHTML = projects.map(p => `<option value="${p.name}">${p.name}</option>`).join('');
  document.getElementById('rmProject').addEventListener('change', onProjectSelect);
  document.getElementById('rmServer').innerHTML = KTG.Masters.servers().map(s => `<option>${s}</option>`).join('');
  document.getElementById('rmRenewalType').innerHTML = KTG.Masters.renewalTypes().map(t => `<option>${t}</option>`).join('');
  document.getElementById('rmPaidStatus').innerHTML = KTG.Masters.paidStatusTypes().map(t => `<option>${t}</option>`).join('');

  const custNames = [...new Set(KTG.DATA.projects.map(p => p.customer))];
  document.getElementById('customerFilter').innerHTML = '<option value="">All Customers</option>' + custNames.map(n => `<option>${n}</option>`).join('');
  document.getElementById('typeFilter').innerHTML = '<option value="">All Types</option>' + KTG.Masters.projectTypes().map(t => `<option>${t}</option>`).join('');
  document.getElementById('statusFilter').innerHTML = '<option value="">All Status</option>' + KTG.Masters.paidStatusTypes().map(t => `<option>${t}</option>`).join('');
  document.getElementById('serverFilter').innerHTML = '<option value="">All Servers</option>' + KTG.Masters.servers().map(s => `<option>${s}</option>`).join('');

  document.getElementById('giTaxRate').innerHTML = KTG.Masters.taxes().filter(t => t.percent > 0).map(t => `<option value="${t.percent}">${t.name}</option>`).join('');

  document.querySelectorAll('.quick-filter-chip').forEach(chip => {
    chip.addEventListener('click', () => {
      document.querySelectorAll('.quick-filter-chip').forEach(c => c.classList.remove('active'));
      chip.classList.add('active');
      applyQuickRange(chip.dataset.range);
    });
  });
  ['fromDate','toDate','customerFilter','typeFilter','statusFilter','serverFilter'].forEach(id => {
    document.getElementById(id).addEventListener('change', render);
  });
  document.getElementById('searchInput').addEventListener('input', render);
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
    openModal(viewIndex);
  });
  document.getElementById('viewInvoiceBtn').addEventListener('click', () => {
    bootstrap.Modal.getInstance(document.getElementById('viewRenewalModal')).hide();
    openInvoiceModal(viewIndex);
  });

  applyQuickRange('this');
}

function monthRange(offset){
  const d = new Date(TODAY.getFullYear(), TODAY.getMonth() + offset, 1);
  const from = new Date(d.getFullYear(), d.getMonth(), 1);
  const to = new Date(d.getFullYear(), d.getMonth() + 1, 0);
  const fmt = (dt) => dt.toISOString().slice(0,10);
  return { from: fmt(from), to: fmt(to) };
}

function applyQuickRange(range){
  if (range === 'all') { document.getElementById('fromDate').value = ''; document.getElementById('toDate').value = ''; }
  else {
    const offset = range === 'last' ? -1 : range === 'next' ? 1 : 0;
    const { from, to } = monthRange(offset);
    document.getElementById('fromDate').value = from;
    document.getElementById('toDate').value = to;
  }
  render();
}

function daysDiff(dateStr){
  const d = new Date(dateStr + 'T00:00:00');
  return Math.round((d - TODAY) / 86400000);
}

function expiryBadge(dateStr){
  const days = daysDiff(dateStr);
  if (days < 0) return `<span class="expiry-badge bg-soft-danger">Expired ${Math.abs(days)}d ago</span>`;
  if (days <= 30) return `<span class="expiry-badge bg-soft-warning">${days}d left</span>`;
  return `<span class="expiry-badge bg-soft-success">${days}d left</span>`;
}

function render(){
  const from = document.getElementById('fromDate').value;
  const to = document.getElementById('toDate').value;
  const customer = document.getElementById('customerFilter').value;
  const type = document.getElementById('typeFilter').value;
  const status = document.getElementById('statusFilter').value;
  const server = document.getElementById('serverFilter').value;
  const q = document.getElementById('searchInput').value.trim().toLowerCase();

  const filtered = renewalsList.filter(r =>
    (!from || r.expiryDate >= from) && (!to || r.expiryDate <= to) &&
    (!customer || r.customer === customer) &&
    (!type || r.type === type) && (!status || r.paidStatus === status) &&
    (!server || r.server === server) &&
    (!q || r.project.toLowerCase().includes(q) || r.customer.toLowerCase().includes(q) || r.liveLink.toLowerCase().includes(q) || r.server.toLowerCase().includes(q))
  );

  document.getElementById('resultsCount').textContent = filtered.length + ' entries';
  document.getElementById('resultsBody').innerHTML = filtered.map((r) => {
    const i = renewalsList.indexOf(r);
    return `
    <tr>
      <td class="fs-13 text-muted">${i+1}</td>
      <td>
        <div class="cell-title">${r.project}</div>
        <div class="cell-sub">${r.customer}</div>
      </td>
      <td><span class="chip chip-inprogress">${r.type}</span></td>
      <td class="fs-13">${r.server}</td>
      <td><a href="#" class="link-cell fs-13" title="${r.liveLink}" onclick="openViewModal(${i}); return false;">${r.liveLink}</a></td>
      <td><div class="fs-13 fw-600">${r.expiryDate}</div>${expiryBadge(r.expiryDate)}</td>
      <td><span class="chip chip-${r.paidStatus.toLowerCase()==='paid'?'active':r.paidStatus.toLowerCase()==='unpaid'?'overdue':'pending'}">${r.paidStatus}</span></td>
      <td>
        <div class="d-flex gap-1">
          <button class="btn btn-sm btn-light" title="View" onclick="openViewModal(${i})"><i class="bi bi-eye"></i></button>
          <div class="dropdown">
            <button class="btn btn-sm btn-light" data-bs-toggle="dropdown"><i class="bi bi-three-dots-vertical"></i></button>
            <div class="dropdown-menu dropdown-menu-end">
              <a class="dropdown-item" href="#" onclick="openInvoiceModal(${i}); return false;"><i class="bi bi-receipt"></i>Generate Invoice</a>
              <a class="dropdown-item" href="#" onclick="openModal(${i}); return false;"><i class="bi bi-pencil"></i>Edit</a>
              <div class="dropdown-divider"></div>
              <a class="dropdown-item text-danger" href="#" onclick="deleteRenewal(${i}); return false;"><i class="bi bi-trash"></i>Delete</a>
            </div>
          </div>
        </div>
      </td>
    </tr>`;
  }).join('') || `<tr><td colspan="8"><div class="empty-state"><div class="es-icon"><i class="bi bi-hdd-network"></i></div><h6>No matching entries</h6><p>Try widening the date range or clearing filters.</p></div></td></tr>`;
  KTG.makeResponsiveTable(document.getElementById('resultsBody').closest('table'));

  renderStats();
}

function clearFilters(){
  document.getElementById('searchInput').value = '';
  document.getElementById('serverFilter').value = '';
  document.getElementById('statusFilter').value = '';
  document.getElementById('customerFilter').value = '';
  document.getElementById('typeFilter').value = '';
  document.querySelectorAll('.quick-filter-chip').forEach(c => c.classList.remove('active'));
  document.querySelector('.quick-filter-chip[data-range="all"]').classList.add('active');
  document.getElementById('fromDate').value = '';
  document.getElementById('toDate').value = '';
  render();
}

function renderStats(){
  document.getElementById('statTotal').textContent = renewalsList.length;
  document.getElementById('statThisMonth').textContent = renewalsList.filter(r => { const d = daysDiff(r.expiryDate); return d >= 0 && d <= monthDaysLeft(); }).length;
  document.getElementById('statExpired').textContent = renewalsList.filter(r => daysDiff(r.expiryDate) < 0).length;
  document.getElementById('statPaid').textContent = renewalsList.filter(r => r.paidStatus.toLowerCase() === 'paid').length;
}
function monthDaysLeft(){
  const lastDay = new Date(TODAY.getFullYear(), TODAY.getMonth()+1, 0).getDate();
  return lastDay - TODAY.getDate();
}

function openModal(index){
  editIndex = index;
  const title = document.getElementById('renewalModalTitle');
  const btn = document.getElementById('saveRenewalBtn');
  if (index === null) {
    title.innerHTML = '<i class="bi bi-arrow-repeat text-primary-KTG me-2"></i>New Renewal Entry';
    btn.textContent = 'Save Entry';
    document.getElementById('rmProject').selectedIndex = 0;
    onProjectSelect();
    document.getElementById('rmServer').selectedIndex = 0;
    document.getElementById('rmRenewalType').selectedIndex = 0;
    document.getElementById('rmPaidStatus').selectedIndex = 0;
    document.getElementById('rmDomainControl').value = '';
    document.getElementById('rmLiveLink').value = '';
    document.getElementById('rmExpiry').value = '';
  } else {
    const r = renewalsList[index];
    title.innerHTML = '<i class="bi bi-pencil-square text-primary-KTG me-2"></i>Edit Renewal Entry';
    btn.textContent = 'Save Changes';
    document.getElementById('rmProject').value = r.project;
    onProjectSelect();
    document.getElementById('rmServer').value = r.server;
    document.getElementById('rmRenewalType').value = r.renewalType;
    document.getElementById('rmPaidStatus').value = r.paidStatus;
    document.getElementById('rmDomainControl').value = r.domainControl;
    document.getElementById('rmLiveLink').value = r.liveLink;
    document.getElementById('rmExpiry').value = r.expiryDate;
  }
  new bootstrap.Modal(document.getElementById('renewalModal')).show();
}

function onProjectSelect(){
  const name = document.getElementById('rmProject').value;
  const p = KTG.DATA.projects.find(x => x.name === name);
  document.getElementById('rmCustomer').value = p ? p.customer : '';
  document.getElementById('rmType').value = p ? p.type : '';
}

function saveRenewal(){
  const entry = {
    project: document.getElementById('rmProject').value,
    customer: document.getElementById('rmCustomer').value,
    type: document.getElementById('rmType').value,
    server: document.getElementById('rmServer').value,
    domainControl: document.getElementById('rmDomainControl').value || '—',
    liveLink: document.getElementById('rmLiveLink').value || '—',
    expiryDate: document.getElementById('rmExpiry').value,
    renewalType: document.getElementById('rmRenewalType').value,
    paidStatus: document.getElementById('rmPaidStatus').value
  };
  if (!entry.expiryDate) { KTG.toast('Please set an expiry date.', 'danger', 'Missing Date'); return; }

  if (editIndex === null) {
    renewalsList.unshift({id: Date.now(), ...entry});
    KTG.toast('Renewal entry has been added.', 'success', 'Entry Added');
  } else {
    Object.assign(renewalsList[editIndex], entry);
    KTG.toast('Renewal entry has been updated.', 'success', 'Entry Updated');
  }
  bootstrap.Modal.getInstance(document.getElementById('renewalModal')).hide();
  render();
}

function deleteRenewal(index){
  const r = renewalsList[index];
  KTG.confirmDialog({
    title: 'Delete Renewal Entry?',
    body: `This will remove tracking for "${r.project}".`,
    confirmText: 'Delete', confirmClass: 'btn-danger',
    onConfirm: () => { renewalsList.splice(index, 1); render(); KTG.toast('Renewal entry deleted.', 'danger', 'Deleted'); }
  });
}

function exportRenewals(){
  const rows = [['S.No','Project','Customer','Type','Server','Domain Control','Live Link','Expiry Date','Renewal Type','Paid Status']];
  renewalsList.forEach((r,i) => rows.push([i+1, r.project, r.customer, r.type, r.server, r.domainControl, r.liveLink, r.expiryDate, r.renewalType, r.paidStatus]));
  KTG.exportCsv('Renewals.csv', rows);
  KTG.toast('Renewals exported as CSV (opens in Excel).', 'success', 'Export Ready');
}

/* ---------------- View Details ---------------- */
let viewIndex = null;

function openViewModal(index){
  viewIndex = index;
  const r = renewalsList[index];
  const days = daysDiff(r.expiryDate);
  document.getElementById('viewRenewalBody').innerHTML = `
    <div class="d-flex align-items-start justify-content-between mb-3">
      <div>
        <h5 class="mb-1">${r.project}</h5>
        <div class="cell-sub">${r.customer} &middot; <span class="chip chip-inprogress">${r.type}</span></div>
      </div>
      <span class="chip chip-${r.paidStatus.toLowerCase()==='paid'?'active':r.paidStatus.toLowerCase()==='unpaid'?'overdue':'pending'}">${r.paidStatus}</span>
    </div>
    <div class="row g-3">
      <div class="col-md-6">
        <div class="bill-block-label">Server</div>
        <div class="fs-14 fw-600 mb-3"><i class="bi bi-hdd-network me-2 text-primary-KTG"></i>${r.server}</div>
        <div class="bill-block-label">Domain Control</div>
        <div class="fs-14 mb-3">${r.domainControl}</div>
        <div class="bill-block-label">Renewal Type</div>
        <div class="fs-14 mb-3"><span class="chip chip-inprogress">${r.renewalType}</span></div>
      </div>
      <div class="col-md-6">
        <div class="bill-block-label">Expiry Date</div>
        <div class="fs-14 fw-600 mb-1">${r.expiryDate}</div>
        <div class="mb-3">${expiryBadge(r.expiryDate)} <span class="fs-12 text-muted">(${days<0?'expired':'expires in'} ${Math.abs(days)} day${Math.abs(days)===1?'':'s'})</span></div>
        <div class="bill-block-label">Paid Status</div>
        <div class="fs-14 mb-3">${r.paidStatus}</div>
      </div>
      <div class="col-12">
        <div class="bill-block-label">Live Project Link &amp; Login Details</div>
        <div class="section-card p-3 fs-13" style="background:var(--surface-alt); white-space:pre-wrap;">${r.liveLink}</div>
      </div>
    </div>`;
  new bootstrap.Modal(document.getElementById('viewRenewalModal')).show();
}

/* ---------------- Generate Invoice ---------------- */
function openInvoiceModal(index){
  const r = renewalsList[index];
  const svc = KTG.Masters.services().find(s => /renewal/i.test(s.name)) || {rate: 5000};
  giContext = r;
  document.getElementById('giProjectLabel').textContent = `${r.project} — ${r.customer}`;
  document.getElementById('giDesc').textContent = `${r.renewalType} Renewal — ${r.project} (${r.server})`;
  document.getElementById('giQty').value = 1;
  document.getElementById('giRate').value = svc.rate;
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
  document.getElementById('giSgstRow').style.display = taxType === 'sgst_cgst' ? 'flex' : 'none';
  document.getElementById('giCgstRow').style.display = taxType === 'sgst_cgst' ? 'flex' : 'none';
  document.getElementById('giIgstRow').style.display = taxType === 'igst' ? 'flex' : 'none';
  document.getElementById('giTotal').textContent = KTG.money(Math.round(amount + taxAmount));
}

function openInvoicePreview(){
  const r = giContext;
  const c = KTG.DATA.customers.find(x => x.name === r.customer);
  const qty = +document.getElementById('giQty').value || 0;
  const rate = +document.getElementById('giRate').value || 0;
  const subtotal = qty * rate;
  const percent = giGstEnabled ? (+document.getElementById('giTaxRate').value || 0) : 0;
  const taxType = giGstEnabled ? document.getElementById('giTaxType').value : 'none';
  const taxAmount = Math.round(subtotal * percent / 100);
  const grandTotal = subtotal + taxAmount;

  const data = {
    docType: giGstEnabled ? 'TAX INVOICE' : 'BILL OF SUPPLY',
    invNumber: 'INV-RENEWAL-' + r.id,
    invDate: TODAY.toISOString().slice(0,10),
    dueDate: r.expiryDate,
    customer: c ? {name:c.name, gst:c.gst, address:c.address, mobile:c.mobile} : {name:r.customer, gst:'—', address:'—', mobile:'—'},
    items: [{service: document.getElementById('giDesc').textContent, desc: r.liveLink, qty, rate}],
    subtotal, taxType, percent, halfPercent: percent/2, taxAmount,
    grandTotal, advancePaid: 0, balanceDue: grandTotal,
    terms: `Renewal invoice for ${r.renewalType.toLowerCase()} hosting/domain renewal — ${r.project}. Please settle before the service expiry date to avoid downtime.`,
    bank: {name:'Knock The Globe Technologies', account:'0012299171', bank:'Kotak Bank', ifsc:'KKBK0008775'}
  };
  sessionStorage.setItem('ktg_invoice_preview', JSON.stringify(data));
  window.open('invoice-preview.php', '_blank');
}
JS;
?>
<?php include __DIR__ . '/components/footer.php'; ?>
