<?php
$pageTitle = "Renewals &amp; Hosting";
$pageKey = "renewals";
$extraHead = <<<'HTML'
<style>
.expiry-badge{ font-size:10.5px; font-weight:700; padding:.15rem .5rem; border-radius: var(--radius-pill); white-space:nowrap; }
.link-cell{ max-width:220px; overflow:hidden; text-overflow:ellipsis; white-space:nowrap; display:inline-block; vertical-align:middle; }
.gst-toggle-btn{ flex:1; border:2px solid var(--border); border-radius:12px; padding:.6rem; text-align:center; cursor:pointer; transition: all .18s var(--ease); background:#fff; }
.gst-toggle-btn.active{ border-color: var(--primary); background: var(--primary-light); }

/* ---- Renewals list: colourful cards-in-a-table look ---- */
.rn-stats .stat-card{ border:none; border-top:none; color:#fff; position:relative; overflow:hidden; }
.rn-stats .stat-card::after{ content:''; position:absolute; right:-18px; top:-18px; width:84px; height:84px; border-radius:50%; background:rgba(255,255,255,.14); }
.rn-stats .stat-card .stat-label{ color:rgba(255,255,255,.85); }
.rn-stats .stat-card .stat-value{ color:#fff; }
.rn-stats .stat-card .stat-icon{ background:rgba(255,255,255,.22); color:#fff; }
.rn-stats .accent-primary{ background:linear-gradient(135deg,#2563eb,#4f46e5); }
.rn-stats .accent-warning{ background:linear-gradient(135deg,#f59e0b,#ea580c); }
.rn-stats .accent-danger{ background:linear-gradient(135deg,#ef4444,#be123c); }
.rn-stats .accent-success{ background:linear-gradient(135deg,#10b981,#0d9488); }

.rn-list-head{ background:linear-gradient(90deg,var(--primary-light),#f5f3ff 60%,#fff); border-bottom:1px solid var(--border-soft); }
.rn-list-head h5{ display:flex; align-items:center; gap:.5rem; }
.rn-list-head h5 i{ width:30px; height:30px; border-radius:9px; display:inline-flex; align-items:center; justify-content:center; color:#fff; background:linear-gradient(135deg,#2563eb,#7c3aed); font-size:14px; }

.rn-table{ border-collapse:separate; border-spacing:0 6px; padding:0 .75rem; }
.rn-table thead th{ border-bottom:none; }
.rn-table tbody tr.rn-row{ background:#fff; box-shadow:0 1px 3px rgba(15,23,42,.06); transition:transform .15s var(--ease), box-shadow .15s var(--ease), background .15s var(--ease); }
.rn-table tbody tr.rn-row:hover{ background:linear-gradient(90deg,#f8faff,#fff); transform:translateY(-1px); box-shadow:0 6px 18px rgba(37,99,235,.12); }
.rn-table tbody tr.rn-row td{ border-bottom:none; border-top:1px solid var(--border-soft); border-bottom:1px solid var(--border-soft); }
.rn-table tbody tr.rn-row td:first-child{ border-left:4px solid var(--rn-accent,#94a3b8); border-radius:12px 0 0 12px; }
.rn-table tbody tr.rn-row td:last-child{ border-right:1px solid var(--border-soft); border-radius:0 12px 12px 0; }
.rn-table tbody tr.rn-row.rn-expired{ --rn-accent:#dc2626; background:linear-gradient(90deg,#fff5f5,#fff 40%); }
.rn-table tbody tr.rn-row.rn-soon{ --rn-accent:#f59e0b; background:linear-gradient(90deg,#fffbeb,#fff 40%); }
.rn-table tbody tr.rn-row.rn-ok{ --rn-accent:#2563eb; }
.rn-table tbody tr.rn-row.rn-paid{ --rn-accent:#16a34a; background:linear-gradient(90deg,#f0fdf4,#fff 40%); }

.rn-no{ display:inline-flex; width:26px; height:26px; align-items:center; justify-content:center; border-radius:8px; background:var(--surface-alt); color:var(--text-muted); font-weight:700; font-size:11.5px; }
.rn-avatar{ width:38px; height:38px; border-radius:12px; display:flex; align-items:center; justify-content:center; color:#fff; font-weight:800; font-size:13px; flex-shrink:0; box-shadow:0 4px 10px rgba(15,23,42,.18); }
.rn-sub i{ font-size:10.5px; margin-right:.2rem; }
.rn-type{ display:inline-flex; align-items:center; gap:.3rem; font-size:10.8px; font-weight:700; padding:.14rem .55rem; border-radius:var(--radius-pill); margin-top:.2rem; }
.rn-server{ display:inline-flex; align-items:center; gap:.4rem; font-size:12.3px; font-weight:600; padding:.3rem .65rem; border-radius:9px; background:var(--info-light); color:#0e7490; white-space:nowrap; }
.rn-expiry-date{ display:flex; align-items:center; gap:.4rem; font-size:13px; font-weight:700; margin-bottom:.2rem; }
.rn-expiry-date i{ color:var(--primary); }
.rn-row .chip{ box-shadow:0 1px 2px rgba(15,23,42,.06); transition:transform .12s var(--ease); }
.rn-row .chip:hover{ transform:scale(1.06); }
.rn-row .btn-light{ background:var(--surface-alt); border:1px solid var(--border-soft); }
.rn-row .btn-light:hover{ background:var(--primary); color:#fff; border-color:var(--primary); }
@media print{
  .rn-table{ border-spacing:0; padding:0; }
  .rn-table tbody tr.rn-row{ box-shadow:none; transform:none; background:#fff !important; }
}

/* Generate Invoice modal — mirrors invoice-create.php's bill-sheet design */
.bill-accordion-head{ display:flex; align-items:center; justify-content:space-between; gap:1rem; padding-bottom:1rem; margin-bottom:1rem; border-bottom:2px solid var(--secondary); cursor:pointer; }
.bill-accordion-head .bah-title{ display:flex; align-items:center; gap:.6rem; flex-wrap:wrap; }
.bill-accordion-head .bill-doctype{ margin:0; text-align:left; }
.bill-accordion-head .bah-toggle i{ transition: transform .2s var(--ease); }
.bill-accordion-head[aria-expanded="true"] .bah-toggle i{ transform: rotate(180deg); }
#giLetterheadBody .bill-letterhead{ border-bottom:none; padding-bottom:0; margin-bottom:1rem; }
.item-table input, .item-table select{ font-size:12.8px; padding:.4rem .6rem; }
.item-table th{ font-size:10.8px; }
.ie2-item{ border:1px solid var(--border-soft); border-radius:var(--radius-lg); padding:1.1rem 1.2rem; margin-bottom:1rem; background:var(--surface-alt); position:relative; }
.ie2-item .ie2-remove{ position:absolute; top:.7rem; right:.7rem; }
.ie2-field{ margin-bottom:.75rem; }
.ie2-field label{ font-size:11px; font-weight:700; color:var(--text-secondary); text-transform:uppercase; letter-spacing:.03em; margin-bottom:.3rem; display:block; }
.ie2-2col{ display:grid; grid-template-columns:1fr 1fr; gap:.75rem; }
.ie2-amount-bar{ display:flex; justify-content:space-between; align-items:center; background:#fff; border:1px dashed var(--border); border-radius:10px; padding:.6rem .9rem; margin-top:.5rem; }
.ie2-amount-bar .val{ font-weight:800; font-size:15px; color:var(--primary); }
.gi-itc-mobile{ display:none; }
@media (max-width: 767.98px){
  .gi-itc-desktop{ display:none !important; }
  .gi-itc-mobile{ display:block; }
}

/* Flatpickr — themed to match the app's own card/brand styling */
.flatpickr-calendar{ border-radius: 14px; box-shadow: 0 16px 40px rgba(15,23,42,.18); border: 1px solid var(--border); font-family: inherit; }
.flatpickr-calendar.arrowTop:before, .flatpickr-calendar.arrowTop:after{ display:none; }
.flatpickr-months .flatpickr-month{ color: var(--secondary); }
.flatpickr-current-month .flatpickr-monthDropdown-months, .flatpickr-current-month input.cur-year{ font-weight:700; }
.flatpickr-day.selected, .flatpickr-day.selected:hover, .flatpickr-day.startRange, .flatpickr-day.endRange{
  background: var(--primary); border-color: var(--primary);
}
.flatpickr-day.inRange{ background: var(--primary-light); border-color: var(--primary-light); box-shadow: -5px 0 0 var(--primary-light), 5px 0 0 var(--primary-light); }
.flatpickr-day.today{ border-color: var(--primary); }
.flatpickr-day:hover{ background: var(--surface-alt); }
</style>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
HTML;
include __DIR__ . '/components/header.php';
?>
      <div class="page-head fade-in-up no-print">
        <div>
          <h1>Renewals &amp; Hosting</h1>
          <nav class="breadcrumb"><span class="breadcrumb-item"><a href="index.php">Dashboard</a></span><span class="breadcrumb-item active">Renewals</span></nav>
        </div>
        <div class="d-flex gap-2">
          <button class="btn btn-primary" id="newRenewalBtn"><i class="bi bi-plus-lg me-1"></i>New Renewal Entry</button>
        </div>
      </div>

      <div class="row g-3 mb-3 stagger no-print rn-stats">
        <div class="col-6 col-md-3"><div class="stat-card accent-primary"><div class="stat-icon bg-soft-primary"><i class="bi bi-hdd-network-fill"></i></div><div class="stat-label">Total Tracked</div><div class="stat-value" id="statTotal">0</div></div></div>
        <div class="col-6 col-md-3"><div class="stat-card accent-warning"><div class="stat-icon bg-soft-warning"><i class="bi bi-hourglass-split"></i></div><div class="stat-label">Expiring This Month</div><div class="stat-value" id="statThisMonth">0</div></div></div>
        <div class="col-6 col-md-3"><div class="stat-card accent-danger"><div class="stat-icon bg-soft-danger"><i class="bi bi-exclamation-triangle-fill"></i></div><div class="stat-label">Pending Renewal</div><div class="stat-value" id="statPendingRenewal">0</div></div></div>
        <div class="col-6 col-md-3"><div class="stat-card accent-success"><div class="stat-icon bg-soft-success"><i class="bi bi-check-circle-fill"></i></div><div class="stat-label">Renewal Done</div><div class="stat-value" id="statRenewalDone">0</div></div></div>
      </div>

      <!-- Filters — always visible, all in one responsive row -->
      <div class="section-card mb-3 fade-in-up no-print">
        <div class="section-body py-3">
          <div class="row g-2 row-cols-2 row-cols-md-3 row-cols-lg-6 align-items-end">
            <div>
              <label class="form-label mb-1">Date Range</label>
              <select class="form-select" id="dateRangeFilter">
                <option value="all">Show All</option>
                <option value="last_month">Last Month</option>
                <option value="this_month" selected>This Month</option>
                <option value="next_month">Next Month</option>
                <option value="custom">Custom Range</option>
              </select>
            </div>
            <div><label class="form-label mb-1">Customer</label><select class="form-select" id="customerFilter"><option value="">All Customers</option></select></div>
            <div><label class="form-label mb-1">Server</label><select class="form-select" id="serverFilter"><option value="">All Servers</option></select></div>
            <div><label class="form-label mb-1">Paid Status</label>
              <select class="form-select" id="statusFilter">
                <option value="">All Status</option>
              </select>
            </div>
            <div><label class="form-label mb-1">Status</label>
              <select class="form-select" id="activeStatusFilter">
                <option value="">All Status</option>
                <option value="active">Active</option>
                <option value="inactive">Inactive</option>
              </select>
            </div>
            <div><button class="btn btn-light w-100" id="clearFiltersBtn" title="Clear all filters"><i class="bi bi-x-lg me-1"></i>Clear Filter</button></div>
          </div>
          <div class="d-flex flex-wrap align-items-center gap-2 mt-3 d-none" id="customRangeRow">
            <div id="fromDateWrap" style="max-width:170px;"><input type="text" class="form-control" id="fromDate" placeholder="From date" title="From date"></div>
            <div id="toDateWrap" style="max-width:170px;"><input type="text" class="form-control" id="toDate" placeholder="To date" title="To date"></div>
          </div>
        </div>
      </div>

      <div class="section-card fade-in-up">
        <div class="section-head rn-list-head"><h5><i class="bi bi-hdd-network-fill"></i>Tracked Projects</h5><span class="chip chip-inprogress no-print" id="resultsCount">0 entries</span></div>
        <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 px-3 pb-3 no-print">
          <div class="d-flex gap-2">
            <button class="btn btn-light btn-sm" id="exportExcelBtn" style="background: green;color: #fff;"><i class="bi bi-filetype-xlsx me-1"></i>Excel</button>
            <button class="btn btn-light btn-sm" id="exportPrintBtn" style="background: #2563eb;color: #fff;"><i class="bi bi-printer me-1"></i>Print</button>
          </div>
          <div class="d-flex align-items-center gap-2 fs-13 text-muted">
            <span>Show</span>
            <select class="form-select form-select-sm" id="pageSizeSelect" style="width:auto;">
              <option value="50" selected>50</option>
              <option value="100">100</option>
              <option value="500">500</option>
            </select>
            <span>entries</span>
          </div>
        </div>
        <div class="table-responsive">
          <table class="table table-modern rn-table mb-0">
            <thead>
              <tr><th style="width:46px;">S.No</th><th>Company / Customer</th><th>Type</th><th>Server</th><th>Expiry</th><th>Paid Status</th><th>Renewed</th><th>Status</th><th class="no-print"></th></tr>
            </thead>
            <tbody id="resultsBody"></tbody>
          </table>
        </div>
        <div class="d-flex justify-content-between align-items-center p-3 border-top no-print">
          <span class="fs-12 text-muted">Showing <b id="resultsShowingCount">0</b> of <b id="resultsTotalCount">0</b> entries</span>
          <nav><ul class="pagination mb-0" id="resultsPagination"></ul></nav>
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
            <div class="d-flex gap-2">
              <select class="form-select" id="rmCustomer"><option value="">Select customer...</option></select>
              <button class="btn btn-light flex-shrink-0" type="button" id="quickAddCustomerBtn" title="Add new customer"><i class="bi bi-plus-lg"></i></button>
            </div>
          </div>
          <div class="col-md-6">
            <label class="form-label">Project Name <span class="req">*</span></label>
            <input class="form-control" id="rmProjectName" placeholder="e.g. Corporate Website Revamp">
          </div>

          <div class="col-md-6"><label class="form-label">Project Type <button type="button" class="btn btn-link btn-sm p-0 ms-1 align-baseline" id="quickAddTypeBtn" title="Add new project type"><i class="bi bi-plus-circle-fill"></i></button></label><select class="form-select" id="rmType"><option value="">Select Type</option></select></div>
          <div class="col-md-6"><label class="form-label">Server <button type="button" class="btn btn-link btn-sm p-0 ms-1 align-baseline" id="quickAddServerBtn" title="Add new server"><i class="bi bi-plus-circle-fill"></i></button></label><select class="form-select" id="rmServer"></select></div>

          <div class="col-md-6"><label class="form-label">Renewal Type</label><select class="form-select" id="rmRenewalType"></select></div>
          <div class="col-md-6"><label class="form-label">Expiry Date <span class="req">*</span></label><input type="text" class="form-control" id="rmExpiry" placeholder="dd-mm-yyyy"></div>

          <div class="col-md-6"><label class="form-label">Domain Control <button type="button" class="btn btn-link btn-sm p-0 ms-1 align-baseline" id="quickAddDomainControlBtn" title="Add new domain control"><i class="bi bi-plus-circle-fill"></i></button></label><select class="form-select" id="rmDomainControl"><option value="">Select Domain Control</option></select></div>
          <div class="col-md-6"><label class="form-label">Paid Status</label><select class="form-select" id="rmPaidStatus"></select></div>

          <div class="col-md-6">
            <label class="form-label">Status</label>
            <select class="form-select" id="rmStatus">
              <option value="active" selected>Active</option>
              <option value="inactive">Inactive</option>
            </select>
          </div>

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

<!-- Status Modal (Active / Inactive) — opened by clicking the Status chip in the list -->
<div class="modal fade" id="statusModal" tabindex="-1">
  <div class="modal-dialog modal-sm">
    <div class="modal-content">
      <div class="modal-header"><h5 class="modal-title"><i class="bi bi-toggle-on text-primary-KTG me-2"></i>Renewal Status</h5><button class="btn-close" data-bs-dismiss="modal"></button></div>
      <div class="modal-body">
        <div class="fs-13 text-muted mb-3" id="statusModalContext"></div>
        <label class="form-label">Status</label>
        <select class="form-select" id="statusModalSelect">
          <option value="active">Active</option>
          <option value="inactive">Inactive</option>
        </select>
      </div>
      <div class="modal-footer">
        <button class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
        <button class="btn btn-primary" id="statusModalSaveBtn">Save</button>
      </div>
    </div>
  </div>
</div>

<!-- Renewed Status Modal — a manually-set flag, separate from the computed Renewal Status
     chip. Opened by clicking the Renewed chip in the list. -->
<div class="modal fade" id="renewedStatusModal" tabindex="-1">
  <div class="modal-dialog modal-sm">
    <div class="modal-content">
      <div class="modal-header"><h5 class="modal-title"><i class="bi bi-arrow-repeat text-primary-KTG me-2"></i>Renewed Status</h5><button class="btn-close" data-bs-dismiss="modal"></button></div>
      <div class="modal-body">
        <div class="fs-13 text-muted mb-3" id="renewedStatusContext"></div>
        <label class="form-label">Status</label>
        <select class="form-select" id="renewedStatusSelect">
          <option value="1">Renewed</option>
          <option value="0">Not Renewed</option>
        </select>
      </div>
      <div class="modal-footer">
        <button class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
        <button class="btn btn-primary" id="renewedStatusSaveBtn">Save</button>
      </div>
    </div>
  </div>
</div>

<!-- Paid Status Modal — opened by clicking the Paid Status chip in the list or View modal. -->
<div class="modal fade" id="paidStatusModal" tabindex="-1">
  <div class="modal-dialog modal-sm">
    <div class="modal-content">
      <div class="modal-header"><h5 class="modal-title"><i class="bi bi-cash-coin text-primary-KTG me-2"></i>Paid Status</h5><button class="btn-close" data-bs-dismiss="modal"></button></div>
      <div class="modal-body">
        <div class="fs-13 text-muted mb-3" id="paidStatusContext"></div>
        <label class="form-label">Status</label>
        <select class="form-select" id="paidStatusSelect"></select>
      </div>
      <div class="modal-footer">
        <button class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
        <button class="btn btn-primary" id="paidStatusSaveBtn">Save</button>
      </div>
    </div>
  </div>
</div>

<!-- Quick Add Customer Modal — lets you add a customer inline from the renewal form
     without leaving the page or losing what's already filled in. Same fields as the
     full Add New Customer form on customers.php. -->
<div class="modal fade" id="quickAddCustomerModal" tabindex="-1">
  <div class="modal-dialog modal-lg modal-dialog-scrollable">
    <div class="modal-content">
      <div class="modal-header"><h5 class="modal-title"><i class="bi bi-person-plus text-primary-KTG me-2"></i>Add New Customer</h5><button class="btn-close" data-bs-dismiss="modal"></button></div>
      <div class="modal-body">
        <div class="divider-label">Basic Details</div>
        <div class="row g-3">
          <div class="col-md-6"><label class="form-label">Customer Name <span class="req">*</span></label><input class="form-control" id="qcName" placeholder="e.g. Arun Kumar"></div>
          <div class="col-md-6"><label class="form-label">Mobile Number <span class="req">*</span></label><input class="form-control" id="qcMobile" placeholder="+91 98765 43210"></div>
          <div class="col-md-6"><label class="form-label">WhatsApp Number</label><input class="form-control" id="qcWhatsapp" placeholder="+91 98765 43210"></div>
          <div class="col-md-6"><label class="form-label">Email Address</label><input type="email" class="form-control" id="qcEmail" placeholder="name@company.com"></div>
          <div class="col-md-6"><label class="form-label">GST Number</label><input class="form-control" id="qcGst" placeholder="33ABCDE1234F1Z5"></div>
          <div class="col-md-6"><label class="form-label">City</label><input class="form-control" id="qcCity" placeholder="e.g. Karur"></div>
          <div class="col-12"><label class="form-label">Address</label><textarea class="form-control" id="qcAddress" rows="2" placeholder="Full billing address"></textarea></div>
        </div>

        <div class="divider-label">Companies</div>
        <div class="fs-12 text-muted mb-2">A customer can own more than one company. Each company becomes its own entry in the customer dropdown.</div>
        <div id="qcCompanyList" class="d-flex flex-column gap-2"></div>
        <button type="button" class="btn btn-sm btn-soft-primary mt-2" id="qcAddCompanyBtn"><i class="bi bi-plus-lg me-1"></i>Add another company</button>

        <div class="divider-label">Project Type</div>
        <div class="row g-3">
          <div class="col-md-6">
            <label class="form-label">Select Type</label>
            <select class="form-select" id="qcProjectType"><option value="">Select Type</option></select>
          </div>
          <div class="col-md-6"><label class="form-label">Status</label>
            <select class="form-select" id="qcStatus">
              <option value="active">Active</option><option value="pending" selected>Pending</option><option value="inactive">Inactive</option>
            </select>
          </div>
        </div>

        <div class="divider-label">Notes</div>
        <textarea class="form-control" id="qcNotes" rows="2" placeholder="Internal notes about this customer"></textarea>

        <div id="quickAddCustomerError" class="alert alert-danger py-2 fs-13 mt-3 d-none" style="border-radius:11px;"></div>
      </div>
      <div class="modal-footer"><button class="btn btn-light" data-bs-dismiss="modal">Cancel</button><button class="btn btn-primary" id="quickAddCustomerSaveBtn">Add Customer</button></div>
    </div>
  </div>
</div>

<!-- Quick Add Master Value Modal — one shared popup for Project Type / Server / Domain Control,
     so a missing dropdown value can be added without leaving the renewal form. Writes through
     the same ajax/masters.php endpoint the Masters pages use. -->
<div class="modal fade" id="quickMasterModal" tabindex="-1">
  <div class="modal-dialog modal-sm">
    <div class="modal-content">
      <div class="modal-header"><h5 class="modal-title" id="quickMasterTitle"><i class="bi bi-plus-circle text-primary-KTG me-2"></i>Add New</h5><button class="btn-close" data-bs-dismiss="modal"></button></div>
      <div class="modal-body">
        <label class="form-label" id="quickMasterLabel">Name <span class="req">*</span></label>
        <input class="form-control" id="quickMasterName" maxlength="150" autocomplete="off">
        <div id="quickMasterError" class="alert alert-danger py-2 fs-13 mt-3 mb-0 d-none" style="border-radius:11px;"></div>
      </div>
      <div class="modal-footer"><button class="btn btn-light" data-bs-dismiss="modal">Cancel</button><button class="btn btn-primary" id="quickMasterSaveBtn">Add</button></div>
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

<!-- Generate Invoice Modal — mirrors invoice-create.php's full bill-sheet layout -->
<div class="modal fade" id="invoiceModal" tabindex="-1">
  <div class="modal-dialog modal-xl modal-dialog-scrollable">
    <div class="modal-content">
      <div class="modal-header"><h5 class="modal-title" id="giModalTitle"><i class="bi bi-receipt text-primary-KTG me-2"></i>Generate Renewal Invoice</h5><button class="btn-close" data-bs-dismiss="modal"></button></div>
      <div class="modal-body" style="background:var(--surface-alt);">

        <!-- Tax mode -->
        <div class="section-card mb-3">
          <div class="section-body py-3">
            <div class="row g-3 align-items-end">
              <div class="col-md-7">
                <div class="bill-block-label mb-2">Tax Mode</div>
                <div class="d-flex gap-3">
                  <div class="gst-toggle-btn active" id="giWithGst"><i class="bi bi-receipt-cutoff d-block mb-1 text-primary-KTG"></i><div class="fw-700 fs-14">With GST</div><div class="fs-11 text-muted">Tax Invoice</div></div>
                  <div class="gst-toggle-btn" id="giWithoutGst"><i class="bi bi-receipt d-block mb-1 text-secondary"></i><div class="fw-700 fs-14">Without GST</div><div class="fs-11 text-muted">Bill of Supply</div></div>
                </div>
              </div>
              <div class="col-md-5" id="giTaxOptions">
                <label class="form-label">GST Type</label>
                <select class="form-select mb-2" id="giTaxType"><option value="sgst_cgst">SGST + CGST (Intra-state)</option><option value="igst">IGST Only (Inter-state)</option></select>
                <label class="form-label">GST Rate</label>
                <select class="form-select" id="giTaxRate"></select>
              </div>
            </div>
          </div>
        </div>

        <!-- Bill sheet -->
        <div class="bill-sheet mb-3">
          <div class="bill-accordion-head" data-bs-toggle="collapse" data-bs-target="#giLetterheadBody" role="button" aria-expanded="false" aria-controls="giLetterheadBody">
            <div class="bah-title">
              <div class="bill-doctype" id="giDocTypeLabel">TAX INVOICE</div>
              <span class="fs-12 text-muted" id="giLetterheadSummary">Knock The Globe Technologies</span>
            </div>
            <button class="btn btn-sm btn-light bah-toggle" type="button" tabindex="-1"><i class="bi bi-chevron-down me-1"></i>Company Details</button>
          </div>
          <div class="collapse" id="giLetterheadBody">
            <div class="bill-letterhead">
              <div class="text-center">
                <div class="bl-logo mx-auto"><img src="assets/images/logo.png" alt="KTG"></div>
                <div class="bl-tagline">Give Us Your Hands …!</div>
              </div>
              <div class="bl-right" id="giLetterheadCompanyBlock"></div>
            </div>
          </div>

          <div class="row g-4 mb-4">
            <div class="col-md-6">
              <div class="bill-block-label">Bill To</div>
              <div id="giCustomerBlock" class="bill-to-meta">Loading customer…</div>
            </div>
            <div class="col-md-6">
              <table class="bill-meta-table w-100">
                <tr><td>Invoice Number</td><td><span id="giInvNumberDisplay">—</span></td></tr>
                <tr><td>Invoice Date</td><td><input type="date" class="form-control form-control-sm d-inline-block w-auto" id="giInvDate"></td></tr>
                <tr><td>Due Date</td><td><input type="date" class="form-control form-control-sm d-inline-block w-auto" id="giDueDate"></td></tr>
              </table>
            </div>
          </div>

          <div class="d-flex align-items-center justify-content-between mb-2">
            <div class="bill-block-label mb-0">Items &amp; Services</div>
            <button class="btn btn-sm btn-soft-primary" type="button" id="giAddItemBtn"><i class="bi bi-plus-lg me-1"></i>Add Item</button>
          </div>

          <div class="table-responsive mb-4 gi-itc-desktop">
            <table class="table table-modern item-table mb-0">
              <thead><tr><th style="min-width:240px;">Product</th><th class="col-qty">Qty</th><th style="width:120px;">Price</th><th style="width:130px;">Amount</th><th style="width:40px;"></th></tr></thead>
              <tbody id="giItemsBodyDesktop"></tbody>
            </table>
          </div>
          <div class="gi-itc-mobile mb-4" id="giItemsBodyMobile"></div>

          <div class="row g-3 mb-2">
            <div class="col-md-7">
              <div class="bill-block-label mb-1">Notes / Terms</div>
              <textarea class="form-control mb-3" rows="3" id="giTermsText">Renewal invoice — please settle before the service expiry date to avoid downtime.</textarea>
            </div>
            <div class="col-md-5">
              <div class="bill-totals">
                <div class="bt-row"><span class="text-muted">Subtotal</span><span class="fw-600" id="giSubtotal">₹0</span></div>
                <div class="bt-row"><span class="text-muted">Discount (₹)</span>
                  <span class="fw-600"><input type="number" class="form-control form-control-sm text-end" style="width:120px; display:inline-block;" id="giDiscountAmount" placeholder="0" min="0"></span>
                </div>
                <div class="bt-row" id="giSgstRow"><span class="text-muted" id="giSgstLabel">SGST 9%</span><span class="fw-600" id="giSgstAmt">₹0</span></div>
                <div class="bt-row" id="giCgstRow"><span class="text-muted" id="giCgstLabel">CGST 9%</span><span class="fw-600" id="giCgstAmt">₹0</span></div>
                <div class="bt-row" id="giIgstRow"><span class="text-muted" id="giIgstLabel">IGST 18%</span><span class="fw-600" id="giIgstAmt">₹0</span></div>
                <div class="bt-row" id="giRoundoffRow"><span class="text-muted">Round Off</span><span class="fw-600" id="giRoundoff">₹0</span></div>
                <div class="bt-row bt-grand"><span>Total</span>
                  <span class="fw-800"><input type="number" class="form-control form-control-sm text-end fw-800" style="width:130px; display:inline-block; font-size:1.05rem;" id="giGrandTotalInput" value="0"></span>
                </div>
                <div class="bt-row"><span class="text-muted">Advance Paid</span>
                  <span class="fw-600 text-success"><input type="number" class="form-control form-control-sm text-end" style="width:120px; display:inline-block;" id="giAdvancePaid" placeholder="0"></span>
                </div>
                <div class="bt-row"><span class="fw-700">Amount Due</span><span class="fw-700 text-danger" id="giSumBalance">₹0</span></div>
              </div>
              <div class="fs-11 text-muted mt-1"><i class="bi bi-info-circle me-1"></i>Edit Total to auto-adjust the discount, or edit Discount to recompute the Total.</div>
            </div>
          </div>
        </div>

      </div>
      <div class="modal-footer">
        <button class="btn btn-light" data-bs-dismiss="modal">Close</button>
        <button class="btn btn-outline-primary" id="giPreviewBtn"><i class="bi bi-eye me-1"></i>Preview &amp; Print</button>
        <button class="btn btn-primary" id="giSaveBtn"><i class="bi bi-save me-1"></i>Save Invoice</button>
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
let giItems = [];
let giServices = [];
let giCustomer = null;
let fpFrom = null;
let fpTo = null;
let fpExpiry = null;
let pageSize = 50;
let paidStatusOptions = [];
let renewalTypesOptions = [];
let defaultPaidStatusId = null;
let currentPage = 1;

function init(){
  loadCustomerOptions();

  loadServerOptions();
  loadProjectTypeOptions();
  loadDomainControlOptions();
  $.get('ajax/masters.php', { master: 'renewal_types', action: 'list' }).done((res) => {
    renewalTypesOptions = res.data || [];
    document.getElementById('rmRenewalType').innerHTML = '<option value="">Select Renewal Type</option>' + renewalTypesOptions.map(t => `<option value="${t.id}">${t.name}</option>`).join('');
  });
  $.get('ajax/masters.php', { master: 'paid_status_types', action: 'list' }).done((res) => {
    paidStatusOptions = res.data || [];
    const opts = paidStatusOptions.map(t => `<option value="${t.id}">${t.name}</option>`).join('');
    document.getElementById('rmPaidStatus').innerHTML = '<option value="">Select Status</option>' + opts;
    document.getElementById('statusFilter').innerHTML = '<option value="">All Status</option>' + opts;
    document.getElementById('paidStatusSelect').innerHTML = opts;
    const unpaid = paidStatusOptions.find(t => (t.name || '').toLowerCase() === 'unpaid');
    defaultPaidStatusId = unpaid ? unpaid.id : null;
    if (editId === null) document.getElementById('rmPaidStatus').value = defaultPaidStatusId || '';
  });
  $.get('ajax/masters.php', { master: 'taxes', action: 'list' }).done((res) => {
    document.getElementById('giTaxRate').innerHTML = (res.data || []).filter(t => +t.percent > 0).map(t => `<option value="${t.percent}">${t.name}</option>`).join('');
    calcGiTotals();
  });
  $.get('ajax/masters.php', { master: 'services', action: 'list' }).done((res) => {
    giServices = res.data || [];
  });

  document.getElementById('rmCustomer').addEventListener('change', onCustomerSelect);
  document.getElementById('rmRenewalType').addEventListener('change', onRenewalTypeChange);

  initDatePickers();

  document.getElementById('dateRangeFilter').addEventListener('change', () => applyQuickRange(document.getElementById('dateRangeFilter').value));
  ['customerFilter','serverFilter','statusFilter','activeStatusFilter'].forEach(id => {
    document.getElementById(id).addEventListener('change', render);
  });
  document.getElementById('pageSizeSelect').addEventListener('change', () => {
    pageSize = +document.getElementById('pageSizeSelect').value;
    currentPage = 1;
    renderList();
  });
  document.getElementById('clearFiltersBtn').addEventListener('click', clearFilters);

  document.getElementById('newRenewalBtn').addEventListener('click', () => openModal(null));
  document.getElementById('saveRenewalBtn').addEventListener('click', saveRenewal);
  document.getElementById('exportExcelBtn').addEventListener('click', exportRenewals);
  document.getElementById('exportPrintBtn').addEventListener('click', () => window.print());

  document.getElementById('quickAddCustomerBtn').addEventListener('click', openQuickAddCustomer);
  document.getElementById('quickAddTypeBtn').addEventListener('click', () => openQuickMaster('project_types'));
  document.getElementById('quickAddServerBtn').addEventListener('click', () => openQuickMaster('servers'));
  document.getElementById('quickAddDomainControlBtn').addEventListener('click', () => openQuickMaster('domain_controls'));
  document.getElementById('quickMasterSaveBtn').addEventListener('click', saveQuickMaster);
  document.getElementById('quickMasterName').addEventListener('keydown', (e) => { if (e.key === 'Enter') { e.preventDefault(); saveQuickMaster(); } });
  document.getElementById('quickMasterModal').addEventListener('shown.bs.modal', () => document.getElementById('quickMasterName').focus());
  document.getElementById('quickAddCustomerSaveBtn').addEventListener('click', saveQuickAddCustomer);
  document.getElementById('qcAddCompanyBtn').addEventListener('click', () => addQcCompanyRow('', true));
  document.getElementById('statusModalSaveBtn').addEventListener('click', saveStatusModal);
  document.getElementById('renewedStatusSaveBtn').addEventListener('click', saveRenewedStatus);
  document.getElementById('paidStatusSaveBtn').addEventListener('click', savePaidStatus);

  document.getElementById('giWithGst').addEventListener('click', () => setGiGst(true));
  document.getElementById('giWithoutGst').addEventListener('click', () => setGiGst(false));
  document.getElementById('giTaxType').addEventListener('change', calcGiTotals);
  document.getElementById('giTaxRate').addEventListener('change', calcGiTotals);
  document.getElementById('giAddItemBtn').addEventListener('click', () => { giItems.push(giEmptyItem()); renderGiItems(); });
  document.getElementById('giDiscountAmount').addEventListener('input', calcGiTotals);
  document.getElementById('giAdvancePaid').addEventListener('input', calcGiTotals);
  document.getElementById('giGrandTotalInput').addEventListener('input', giOnGrandTotalChange);
  document.getElementById('giPreviewBtn').addEventListener('click', openInvoicePreview);
  document.getElementById('giSaveBtn').addEventListener('click', giSaveInvoice);

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
/* Name - Company - Mobile. The searchable dropdown filters on this label text, so including
   the mobile number is what makes customers findable by number. */
function customerOptionLabel(c){
  const sameAsName = !c.company_name || c.company_name.trim().toLowerCase() === (c.name || '').trim().toLowerCase();
  const label = sameAsName ? c.name : `${c.name} - ${c.company_name}`;
  return c.mobile ? `${label} - ${c.mobile}` : label;
}

/* ---------------- Master dropdowns (Project Type / Server / Domain Control) ----------------
   selectValue (optional): value to select once repopulated — used after adding a value
   inline, so the newly added option is picked straight away. */
function loadProjectTypeOptions(selectValue){
  $.get('ajax/masters.php', { master: 'project_types', action: 'list' }).done((res) => {
    const opts = '<option value="">Select Type</option>' + (res.data || []).map(t => `<option value="${t.id}">${KTG.escAttr(t.name)}</option>`).join('');
    const cur = document.getElementById('rmType').value;
    document.getElementById('rmType').innerHTML = opts;
    document.getElementById('qcProjectType').innerHTML = opts;
    document.getElementById('rmType').value = selectValue || cur;
  });
}

function loadServerOptions(selectValue){
  $.get('ajax/masters.php', { master: 'servers', action: 'list' }).done((res) => {
    const opts = (res.data || []).map(s => `<option value="${s.id}">${KTG.escAttr(s.name)}</option>`).join('');
    const cur = document.getElementById('rmServer').value;
    document.getElementById('rmServer').innerHTML = '<option value="">Select Server</option>' + opts;
    document.getElementById('serverFilter').innerHTML = '<option value="">All Servers</option>' + opts;
    document.getElementById('rmServer').value = selectValue || cur;
  });
}

/* Domain Control is stored on the renewal as its name (text), not an id. */
function loadDomainControlOptions(selectValue){
  $.get('ajax/masters.php', { master: 'domain_controls', action: 'list' }).done((res) => {
    const opts = (res.data || []).map(d => `<option value="${KTG.escAttr(d.name)}">${KTG.escAttr(d.name)}</option>`).join('');
    const cur = document.getElementById('rmDomainControl').value;
    document.getElementById('rmDomainControl').innerHTML = '<option value="">Select Domain Control</option>' + opts;
    setDomainControlValue(selectValue || cur);
  });
}

/* A renewal saved before this became a dropdown may hold a value that isn't in the master —
   keep it selectable so editing that entry doesn't silently blank it. */
function setDomainControlValue(val){
  const sel = document.getElementById('rmDomainControl');
  val = (val || '').trim();
  if (val && !Array.from(sel.options).some(o => o.value === val)) {
    sel.insertAdjacentHTML('beforeend', `<option value="${KTG.escAttr(val)}">${KTG.escAttr(val)}</option>`);
  }
  sel.value = val;
}

const QUICK_MASTERS = {
  project_types:   { title: 'Add Project Type',   label: 'Project Type Name', placeholder: 'e.g. Mobile Application',    done: 'Project type added.' },
  servers:         { title: 'Add Server',         label: 'Server Name',       placeholder: 'e.g. DigitalOcean',          done: 'Server added.' },
  domain_controls: { title: 'Add Domain Control', label: 'Domain Control',    placeholder: 'e.g. Client-side (GoDaddy)', done: 'Domain control added.' }
};
let quickMasterKey = null;

function openQuickMaster(key){
  quickMasterKey = key;
  const q = QUICK_MASTERS[key];
  document.getElementById('quickMasterTitle').innerHTML = `<i class="bi bi-plus-circle text-primary-KTG me-2"></i>${q.title}`;
  document.getElementById('quickMasterLabel').innerHTML = `${q.label} <span class="req">*</span>`;
  const input = document.getElementById('quickMasterName');
  input.value = '';
  input.placeholder = q.placeholder;
  document.getElementById('quickMasterError').classList.add('d-none');
  new bootstrap.Modal(document.getElementById('quickMasterModal')).show();
}

function saveQuickMaster(){
  const key = quickMasterKey;
  const name = document.getElementById('quickMasterName').value.trim();
  const $err = $('#quickMasterError').addClass('d-none');
  if (!key) return;
  if (!name) { $err.text('Please enter a name.').removeClass('d-none'); return; }
  const $btn = $('#quickMasterSaveBtn').prop('disabled', true);

  $.post('ajax/masters.php', { master: key, action: 'add', name }).done((res) => {
    if (!res.success) { $err.text(res.message || 'Could not add.').removeClass('d-none'); return; }
    bootstrap.Modal.getInstance(document.getElementById('quickMasterModal')).hide();
    KTG.toast(QUICK_MASTERS[key].done, 'success', 'Added');
    if (key === 'project_types') loadProjectTypeOptions(String(res.data.id));
    else if (key === 'servers') loadServerOptions(String(res.data.id));
    else loadDomainControlOptions(name);
  }).fail((xhr) => {
    const msg = (xhr.responseJSON && xhr.responseJSON.message) || 'Something went wrong. Please try again.';
    $err.text(msg).removeClass('d-none');
  }).always(() => $btn.prop('disabled', false));
}

/* selectId (optional): re-select this customer in rmCustomer once the dropdown is
   repopulated — used after adding a customer inline via the Quick Add popup, so the
   new customer is picked without the user having to find it in the list themselves. */
function loadCustomerOptions(selectId){
  $.get('ajax/renewals.php', { action: 'customer_options' }).done((res) => {
    customerOptions = res.data || [];
    const opts = customerOptions.map(c => `<option value="${KTG.escAttr(KTG.customerEntryValue(c))}">${KTG.escAttr(customerOptionLabel(c))}</option>`).join('');
    document.getElementById('rmCustomer').innerHTML = '<option value="">Select customer...</option>' + opts;
    document.getElementById('customerFilter').innerHTML = '<option value="">All Customers</option>' + opts;
    if (selectId) {
      document.getElementById('rmCustomer').value = KTG.customerEntryFor(customerOptions, selectId, '');
      onCustomerSelect();
    }
  });
}

function onCustomerSelect(){
  const c = KTG.findCustomerEntry(customerOptions, document.getElementById('rmCustomer').value);
  if (c && c.project_type_id) document.getElementById('rmType').value = c.project_type_id;
}

function onRenewalTypeChange(){
  const typeId = document.getElementById('rmRenewalType').value;
  if (!typeId) return;
  const rt = renewalTypesOptions.find(t => String(t.id) === String(typeId));
  const typeName = (rt ? rt.name : '').toLowerCase();

  let baseDate = (fpExpiry && fpExpiry.selectedDates.length) ? new Date(fpExpiry.selectedDates[0]) : new Date();
  const nextDate = new Date(baseDate);
  if (typeName.includes('month')) {
    nextDate.setMonth(nextDate.getMonth() + 1);
  } else if (typeName.includes('quarter')) {
    nextDate.setMonth(nextDate.getMonth() + 3);
  } else if (typeName.includes('half')) {
    nextDate.setMonth(nextDate.getMonth() + 6);
  } else if (typeName.includes('year')) {
    nextDate.setFullYear(nextDate.getFullYear() + 1);
  }

  if (editId === null || !fpExpiry || !fpExpiry.selectedDates.length) {
    if (fpExpiry) fpExpiry.setDate(nextDate, true);
    else document.getElementById('rmExpiry').value = nextDate.toISOString().slice(0, 10);
  }
}

/* ---------------- Quick Add Customer (inline, from the renewal form) ---------------- */
/* Companies repeater — same behaviour as the Add/Edit Customer form on customers.php. */
function addQcCompanyRow(value, focus){
  const list = document.getElementById('qcCompanyList');
  const row = document.createElement('div');
  row.className = 'd-flex gap-2';
  row.innerHTML = `<input class="form-control qc-company-input" placeholder="${list.children.length ? 'Another company name' : 'e.g. Zen Foods Pvt Ltd'}" maxlength="150" value="${KTG.escAttr(value || '')}">
    <button type="button" class="btn btn-light flex-shrink-0" title="Remove company"><i class="bi bi-trash text-danger"></i></button>`;
  row.querySelector('button').addEventListener('click', () => {
    row.remove();
    if (!list.children.length) addQcCompanyRow('');
  });
  list.appendChild(row);
  if (focus) row.querySelector('input').focus();
}

function openQuickAddCustomer(){
  document.getElementById('qcName').value = '';
  document.getElementById('qcCompanyList').innerHTML = '';
  addQcCompanyRow('');
  document.getElementById('qcMobile').value = '';
  document.getElementById('qcWhatsapp').value = '';
  document.getElementById('qcEmail').value = '';
  document.getElementById('qcGst').value = '';
  document.getElementById('qcCity').value = '';
  document.getElementById('qcAddress').value = '';
  document.getElementById('qcProjectType').value = '';
  document.getElementById('qcStatus').value = 'pending';
  document.getElementById('qcNotes').value = '';
  document.getElementById('quickAddCustomerError').classList.add('d-none');
  new bootstrap.Modal(document.getElementById('quickAddCustomerModal')).show();
}

function saveQuickAddCustomer(){
  const name = document.getElementById('qcName').value.trim();
  const mobile = document.getElementById('qcMobile').value.trim();
  const $err = $('#quickAddCustomerError').addClass('d-none');
  if (!name) { $err.text('Customer name is required.').removeClass('d-none'); return; }
  if (!mobile) { $err.text('Mobile number is required.').removeClass('d-none'); return; }

  const payload = {
    action: 'add',
    name,
    mobile,
    companies: JSON.stringify(Array.from(document.querySelectorAll('#qcCompanyList .qc-company-input')).map(i => i.value.trim()).filter(Boolean)),
    whatsapp: document.getElementById('qcWhatsapp').value.trim(),
    email: document.getElementById('qcEmail').value.trim(),
    gst_number: document.getElementById('qcGst').value.trim(),
    city: document.getElementById('qcCity').value.trim(),
    address: document.getElementById('qcAddress').value.trim(),
    project_type_id: document.getElementById('qcProjectType').value,
    status: document.getElementById('qcStatus').value,
    notes: document.getElementById('qcNotes').value.trim()
  };
  const $btn = $('#quickAddCustomerSaveBtn').prop('disabled', true);

  $.post('ajax/customers.php', payload).done((res) => {
    if (!res.success) { $err.text(res.message || 'Could not add customer.').removeClass('d-none'); return; }
    KTG.toast(`${name} has been added.`, 'success', 'Customer Added');
    bootstrap.Modal.getInstance(document.getElementById('quickAddCustomerModal')).hide();
    loadCustomerOptions(res.data.id);
  }).fail((xhr) => {
    const msg = (xhr.responseJSON && xhr.responseJSON.message) || 'Something went wrong. Please try again.';
    $err.text(msg).removeClass('d-none');
  }).always(() => $btn.prop('disabled', false));
}

/* Flatpickr replaces native date inputs with a friendly themed calendar picker */
function initDatePickers(){
  fpFrom = flatpickr('#fromDate', {
    dateFormat: 'Y-m-d', altInput: true, altFormat: 'd/m/Y', allowInput: true,
    onChange: (dates) => { if (fpTo) fpTo.set('minDate', dates[0] || null); render(); }
  });
  fpTo = flatpickr('#toDate', {
    dateFormat: 'Y-m-d', altInput: true, altFormat: 'd/m/Y', allowInput: true,
    onChange: (dates) => { if (fpFrom) fpFrom.set('maxDate', dates[0] || null); render(); }
  });
  fpExpiry = flatpickr('#rmExpiry', {
    dateFormat: 'Y-m-d', altInput: true, altFormat: 'd-m-Y', allowInput: true,
    placeholder: 'dd-mm-yyyy'
  });
}

function applyQuickRange(range){
  const isCustom = range === 'custom';
  document.getElementById('customRangeRow').classList.toggle('d-none', !isCustom);
  if (!isCustom) {
    const { from, to } = KTG.dateRangeFor(range);
    if (fpFrom) fpFrom.setDate(from || null, false);
    if (fpTo) fpTo.setDate(to || null, false);
  }
  render();
}

function daysDiff(dateStr){
  const d = new Date(dateStr + 'T00:00:00');
  const today = new Date(); today.setHours(0,0,0,0);
  return Math.round((d - today) / 86400000);
}

/* paidStatusName is optional — once a renewal is Paid (freshly extended),
   showing a "365d left" countdown isn't useful, so the badge is suppressed entirely. */
function expiryBadge(dateStr, paidStatusName){
  if ((paidStatusName || '').toLowerCase() === 'paid') return '';
  const days = daysDiff(dateStr);
  if (days < 0) return `<span class="expiry-badge bg-soft-danger">Expired ${Math.abs(days)}d ago</span>`;
  if (days <= 30) return `<span class="expiry-badge bg-soft-warning">${days}d left</span>`;
  return `<span class="expiry-badge bg-soft-success">${days}d left</span>`;
}

/* DD/MM/YYYY display, from a Y-M-D (or Y-M-D H:i:s) value straight out of the DB. */
function formatDMY(dateStr){
  if (!dateStr) return '—';
  const [y, m, d] = dateStr.slice(0, 10).split('-');
  if (!y || !m || !d) return dateStr;
  return `${d}/${m}/${y}`;
}

/* Renewal status is never stored — a renewal "paid" today only stays covered through its
   expiry_date. Once that date passes it's Pending Renewal again, whatever paid_status_id
   last said, so the paid period can't linger as "Paid" forever. */
function renewalStatusInfo(expiryDate){
  const days = daysDiff(expiryDate);
  return days < 0
    ? { label: 'Pending Renewal', cls: 'overdue' }
    : { label: 'Renewal Done', cls: 'active' };
}

/* The list's Status column shows the actual Paid Status (Paid/Unpaid/Hold/Overdue —
   the master_paid_status_types value set in the Add/Edit form), not the computed
   expiry-based renewalStatusInfo() above (that still only backs the dashboard cards). */
function paidChipClass(name){
  const n = (name || '').toLowerCase();
  return n === 'paid' ? 'active' : n === 'unpaid' ? 'overdue' : 'pending';
}

/* Company Name (title) + Customer Name (sub). In the list's two-line cell both are always
   shown (forceBoth) even when they're the same text — in the View modal's single-line
   breadcrumb, showing the same text twice back-to-back would just look redundant, so that
   caller still dedupes down to one when there's no distinct company name on file. */
function companyCustomerLabel(r, forceBoth){
  const company = (r.company_name || '').trim();
  const customer = (r.customer_name || '').trim();
  if (!forceBoth && (!company || company.toLowerCase() === customer.toLowerCase())) {
    return { title: customer || '—', sub: '' };
  }
  return { title: company || customer || '—', sub: customer };
}

/* Left-edge accent + soft tint per row: green once Paid, otherwise by how close the expiry is. */
function rowToneClass(r){
  if ((r.paid_status_name || '').toLowerCase() === 'paid') return 'rn-paid';
  const days = daysDiff(r.expiry_date);
  return days < 0 ? 'rn-expired' : days <= 30 ? 'rn-soon' : 'rn-ok';
}

function hashStr(str){
  let h = 0;
  for (let i = 0; i < str.length; i++) h = (h * 31 + str.charCodeAt(i)) >>> 0;
  return h;
}

const RN_AVATAR_GRADIENTS = [
  ['#2563eb', '#4f46e5'], ['#7c3aed', '#c026d3'], ['#0891b2', '#0d9488'], ['#ea580c', '#dc2626'],
  ['#16a34a', '#0d9488'], ['#db2777', '#e11d48'], ['#d97706', '#ea580c'], ['#4f46e5', '#0891b2']
];
const RN_TYPE_COLORS = [['#dbeafe', '#1d4ed8'], ['#ede9fe', '#6d28d9'], ['#fce7f3', '#be185d'], ['#ccfbf1', '#0f766e'], ['#fef3c7', '#b45309'], ['#e0e7ff', '#4338ca']];

function avatarGradient(name){
  const g = RN_AVATAR_GRADIENTS[hashStr(name || '') % RN_AVATAR_GRADIENTS.length];
  return `linear-gradient(135deg, ${g[0]}, ${g[1]})`;
}

function initialsOf(name){
  const parts = (name || '').trim().split(/\s+/).filter(Boolean);
  if (!parts.length) return '?';
  return (parts.length === 1 ? parts[0].slice(0, 2) : parts[0][0] + parts[1][0]).toUpperCase();
}

function render(){
  const custFilter = KTG.parseCustomerEntry(document.getElementById('customerFilter').value);
  const params = {
    action: 'list',
    from: document.getElementById('fromDate').value,
    to: document.getElementById('toDate').value,
    customer_id: custFilter.id,
    company: custFilter.company,
    server_id: document.getElementById('serverFilter').value,
    paid_status_id: document.getElementById('statusFilter').value,
    status: document.getElementById('activeStatusFilter').value
  };
  $.get('ajax/renewals.php', params).done((res) => {
    if (!res.success) { KTG.toast(res.message || 'Could not load renewals.', 'danger', 'Error'); return; }
    items = res.data || [];
    currentPage = 1;
    renderList();
    renderStats();
  });
}

/* Pagination is client-side — the whole filtered set is already in `items` from the AJAX
   call above, so slicing it per page avoids a second round trip for something this small. */
function renderList(){
  document.getElementById('resultsCount').textContent = items.length + ' entries';
  const totalPages = Math.max(1, Math.ceil(items.length / pageSize));
  if (currentPage > totalPages) currentPage = totalPages;
  const start = (currentPage - 1) * pageSize;
  const pageItems = items.slice(start, start + pageSize);

  document.getElementById('resultsBody').innerHTML = pageItems.map((r, i) => {
    const cc = companyCustomerLabel(r, true);
    const typeColor = RN_TYPE_COLORS[hashStr(r.project_type_name || '') % RN_TYPE_COLORS.length];
    return `
    <tr class="rn-row ${rowToneClass(r)}">
      <td><span class="rn-no">${start + i + 1}</span></td>
      <td>
        <div class="d-flex align-items-center gap-2">
          <div class="rn-avatar" style="background:${avatarGradient(cc.title)};">${initialsOf(cc.title)}</div>
          <div>
            <div class="cell-title">${cc.title}</div>
            ${cc.sub ? `<div class="cell-sub rn-sub"><i class="bi bi-person"></i>${cc.sub}</div>` : ''}
          </div>
        </div>
      </td>
      <td>
        <div class="cell-title fs-13">${r.project_name}</div>
        ${r.project_type_name ? `<span class="rn-type" style="background:${typeColor[0]};color:${typeColor[1]};"><i class="bi bi-tag-fill"></i>${r.project_type_name}</span>` : '<div class="cell-sub">—</div>'}
      </td>
      <td>${r.server_name ? `<span class="rn-server"><i class="bi bi-hdd-network"></i>${r.server_name}</span>` : '<span class="text-muted">—</span>'}</td>
      <td><div class="rn-expiry-date"><i class="bi bi-calendar-event"></i>${formatDMY(r.expiry_date)}</div>${expiryBadge(r.expiry_date, r.paid_status_name)}</td>
      <td><span class="chip ${r.paid_status_name ? 'chip-' + paidChipClass(r.paid_status_name) : 'chip-pending'}" style="cursor:pointer;" onclick="openPaidStatusModal(${r.id})" title="Click to change">${r.paid_status_name || 'Set Status'}</span></td>
      <td><span class="chip chip-${+r.is_renewed ? 'active' : 'overdue'}" style="cursor:pointer;" onclick="openRenewedModal(${r.id})" title="Click to change">${+r.is_renewed ? 'Renewed' : 'Not Renewed'}</span></td>
      <td><span class="chip chip-${(r.status || 'active') === 'active' ? 'active' : 'cancelled'}" style="cursor:pointer;" onclick="openStatusModal(${r.id})" title="Click to change">${(r.status || 'active') === 'active' ? 'Active' : 'Inactive'}</span></td>
      <td class="no-print">
        <div class="d-flex gap-1">
          <button class="btn btn-sm btn-light" title="View" onclick="openViewModal(${r.id})"><i class="bi bi-eye"></i></button>
          <div class="dropdown">
            <button class="btn btn-sm btn-light" data-bs-toggle="dropdown"><i class="bi bi-three-dots-vertical"></i></button>
            <div class="dropdown-menu dropdown-menu-end">
              ${r.invoice_id
                ? `<a class="dropdown-item" href="#" onclick="openInvoiceModal(${r.id}); return false;"><i class="bi bi-receipt"></i>View Invoice</a>`
                : `<a class="dropdown-item" href="#" onclick="openInvoiceModal(${r.id}); return false;"><i class="bi bi-receipt"></i>Generate Invoice</a>`}
              <a class="dropdown-item" href="#" onclick="openModal(${r.id}); return false;"><i class="bi bi-pencil"></i>Edit</a>
              <div class="dropdown-divider"></div>
              <a class="dropdown-item text-danger" href="#" onclick="deleteRenewal(${r.id}); return false;"><i class="bi bi-trash"></i>Delete</a>
            </div>
          </div>
        </div>
      </td>
    </tr>`;
  }).join('') || `<tr><td colspan="9"><div class="empty-state"><div class="es-icon"><i class="bi bi-hdd-network"></i></div><h6>No matching entries</h6><p>${emptyFilterHint()}</p></div></td></tr>`;

  document.getElementById('resultsShowingCount').textContent = pageItems.length;
  document.getElementById('resultsTotalCount').textContent = items.length;
  renderResultsPagination(totalPages);
  KTG.makeResponsiveTable(document.getElementById('resultsBody').closest('table'));
}

/* Filters combine (AND), so e.g. Customer + Server only shows renewals matching both. When nothing
   matches, name the active filters so it's obvious which one to loosen. */
function emptyFilterHint(){
  const label = (id) => { const s = document.getElementById(id); return s.value ? s.options[s.selectedIndex].textContent.trim() : ''; };
  const active = [
    ['Customer', label('customerFilter')], ['Server', label('serverFilter')],
    ['Paid Status', label('statusFilter')], ['Status', label('activeStatusFilter')]
  ].filter(f => f[1]);
  if (!active.length) return 'Try widening the date range.';
  return 'Nothing matches all of these filters together: ' + active.map(f => `<b>${KTG.escAttr(f[0])}</b> = ${KTG.escAttr(f[1])}`).join(', ') + '. Clear one of them or widen the date range.';
}

function renderResultsPagination(totalPages){
  const nav = document.getElementById('resultsPagination');
  if (totalPages <= 1) { nav.innerHTML = ''; return; }
  let html = `<li class="page-item ${currentPage <= 1 ? 'disabled' : ''}"><a class="page-link" href="#" onclick="gotoResultsPage(${currentPage - 1});return false;">Prev</a></li>`;
  for (let p = 1; p <= totalPages; p++) {
    html += `<li class="page-item ${p === currentPage ? 'active' : ''}"><a class="page-link" href="#" onclick="gotoResultsPage(${p});return false;">${p}</a></li>`;
  }
  html += `<li class="page-item ${currentPage >= totalPages ? 'disabled' : ''}"><a class="page-link" href="#" onclick="gotoResultsPage(${currentPage + 1});return false;">Next</a></li>`;
  nav.innerHTML = html;
}

function gotoResultsPage(p){
  currentPage = p;
  renderList();
}

function clearFilters(){
  document.getElementById('statusFilter').value = '';
  document.getElementById('activeStatusFilter').value = '';
  document.getElementById('customerFilter').value = '';
  document.getElementById('serverFilter').value = '';
  document.getElementById('dateRangeFilter').value = 'all';
  applyQuickRange('all');
}

/* Computed straight from the already-filtered `items` (not a separate all-renewals AJAX
   call) so all 4 cards track whatever date range / customer / status filters are currently
   applied, and can never drift out of sync with what the table below is actually showing. */
function renderStats(){
  const today = new Date(); today.setHours(0,0,0,0);
  const monthEnd = new Date(today.getFullYear(), today.getMonth() + 1, 0);

  let thisMonth = 0, pendingRenewal = 0, renewalDone = 0;
  items.forEach(r => {
    const days = daysDiff(r.expiry_date);
    if (days < 0) pendingRenewal++; else renewalDone++;
    const d = new Date(r.expiry_date + 'T00:00:00');
    if (d >= today && d <= monthEnd) thisMonth++;
  });

  document.getElementById('statTotal').textContent = items.length;
  document.getElementById('statThisMonth').textContent = thisMonth;
  document.getElementById('statPendingRenewal').textContent = pendingRenewal;
  document.getElementById('statRenewalDone').textContent = renewalDone;
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
    document.getElementById('rmPaidStatus').value = defaultPaidStatusId || '';
    document.getElementById('rmStatus').value = 'active';
    setDomainControlValue('');
    document.getElementById('rmLiveLink').value = '';
    if (fpExpiry) fpExpiry.clear();
    else document.getElementById('rmExpiry').value = '';
  } else {
    const r = items.find(x => x.id === id);
    if (!r) return;
    title.innerHTML = '<i class="bi bi-pencil-square text-primary-KTG me-2"></i>Edit Renewal Entry';
    btn.textContent = 'Save Changes';
    document.getElementById('rmCustomer').value = KTG.customerEntryFor(customerOptions, r.customer_id, r.company_name);
    document.getElementById('rmProjectName').value = r.project_name;
    document.getElementById('rmType').value = r.project_type_id || '';
    document.getElementById('rmServer').value = r.server_id || '';
    document.getElementById('rmRenewalType').value = r.renewal_type_id || '';
    document.getElementById('rmPaidStatus').value = r.paid_status_id || '';
    document.getElementById('rmStatus').value = r.status || 'active';
    setDomainControlValue(r.domain_control);
    document.getElementById('rmLiveLink').value = r.live_link || '';
    if (fpExpiry) fpExpiry.setDate(r.expiry_date || '', true);
    else document.getElementById('rmExpiry').value = r.expiry_date || '';
  }
  new bootstrap.Modal(document.getElementById('renewalModal')).show();
}

function saveRenewal(){
  const custEntry = KTG.parseCustomerEntry(document.getElementById('rmCustomer').value);
  const payload = {
    action: editId === null ? 'add' : 'edit',
    id: editId,
    customer_id: custEntry.id,
    company_name: custEntry.company,
    project_name: document.getElementById('rmProjectName').value.trim(),
    project_type_id: document.getElementById('rmType').value,
    server_id: document.getElementById('rmServer').value,
    renewal_type_id: document.getElementById('rmRenewalType').value,
    paid_status_id: document.getElementById('rmPaidStatus').value,
    status: document.getElementById('rmStatus').value,
    domain_control: document.getElementById('rmDomainControl').value,
    live_link: document.getElementById('rmLiveLink').value.trim(),
    expiry_date: document.getElementById('rmExpiry').value
  };
  const $err = $('#renewalFormError').addClass('d-none');
  const $btn = $('#saveRenewalBtn').prop('disabled', true);

  $.post('ajax/renewals.php', payload).done((res) => {
    if (res.success) {
      bootstrap.Modal.getInstance(document.getElementById('renewalModal')).hide();
      if (res.data && res.data.expiry_extended) {
        KTG.toast(`Renewal entry has been updated. Expiry extended to ${formatDMY(res.data.expiry_date)}.`, 'success', 'Renewal Extended');
      } else {
        KTG.toast(editId === null ? 'Renewal entry has been added.' : 'Renewal entry has been updated.', 'success', editId === null ? 'Entry Added' : 'Entry Updated');
      }
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

/* ---------------- Active / Inactive Status ---------------- */
let statusModalId = null;

function openStatusModal(id){
  const r = items.find(x => x.id === id);
  if (!r) return;
  statusModalId = id;
  document.getElementById('statusModalContext').textContent = `${r.project_name} — ${r.customer_name}`;
  document.getElementById('statusModalSelect').value = (r.status || 'active') === 'inactive' ? 'inactive' : 'active';
  new bootstrap.Modal(document.getElementById('statusModal')).show();
}

function saveStatusModal(){
  const newStatus = document.getElementById('statusModalSelect').value;
  const $btn = $('#statusModalSaveBtn').prop('disabled', true);
  $.post('ajax/renewals.php', { action: 'set_status', id: statusModalId, status: newStatus }).done((res) => {
    if (!res.success) { KTG.toast(res.message || 'Could not update status.', 'danger', 'Error'); return; }
    const r = items.find(x => x.id === statusModalId);
    if (r) r.status = newStatus;
    bootstrap.Modal.getInstance(document.getElementById('statusModal')).hide();
    KTG.toast('Status updated successfully.', 'success', 'Updated');
    renderList();
    if (viewId === statusModalId) openViewModal(viewId);
  }).fail((xhr) => {
    const msg = (xhr.responseJSON && xhr.responseJSON.message) || 'Could not update status.';
    KTG.toast(msg, 'danger', 'Error');
  }).always(() => $btn.prop('disabled', false));
}

/* ---------------- Renewed Status (manual, separate from the computed Status chip) ---------------- */
let renewedStatusId = null;

function openRenewedModal(id){
  const r = items.find(x => x.id === id);
  if (!r) return;
  renewedStatusId = id;
  document.getElementById('renewedStatusContext').textContent = `${r.project_name} — ${r.customer_name}`;
  document.getElementById('renewedStatusSelect').value = +r.is_renewed ? '1' : '0';
  new bootstrap.Modal(document.getElementById('renewedStatusModal')).show();
}

function saveRenewedStatus(){
  const isRenewed = document.getElementById('renewedStatusSelect').value;
  const $btn = $('#renewedStatusSaveBtn').prop('disabled', true);
  $.post('ajax/renewals.php', { action: 'set_renewed', id: renewedStatusId, is_renewed: isRenewed }).done((res) => {
    if (!res.success) { KTG.toast(res.message || 'Could not update.', 'danger', 'Error'); return; }
    const r = items.find(x => x.id === renewedStatusId);
    if (r) r.is_renewed = +isRenewed;
    bootstrap.Modal.getInstance(document.getElementById('renewedStatusModal')).hide();
    KTG.toast('Renewed status updated.', 'success', 'Updated');
    renderList();
    if (viewId === renewedStatusId) openViewModal(viewId);
  }).fail((xhr) => {
    const msg = (xhr.responseJSON && xhr.responseJSON.message) || 'Could not update.';
    KTG.toast(msg, 'danger', 'Error');
  }).always(() => $btn.prop('disabled', false));
}

/* ---------------- Paid Status (quick popup, same pattern as Renewed) ---------------- */
let paidStatusId = null;

function openPaidStatusModal(id){
  const r = items.find(x => x.id === id);
  if (!r) return;
  paidStatusId = id;
  document.getElementById('paidStatusContext').textContent = `${r.project_name} — ${r.customer_name}`;
  document.getElementById('paidStatusSelect').value = r.paid_status_id || defaultPaidStatusId || '';
  new bootstrap.Modal(document.getElementById('paidStatusModal')).show();
}

function savePaidStatus(){
  const newStatusId = document.getElementById('paidStatusSelect').value;
  const $btn = $('#paidStatusSaveBtn').prop('disabled', true);
  $.post('ajax/renewals.php', { action: 'set_paid_status', id: paidStatusId, paid_status_id: newStatusId }).done((res) => {
    if (!res.success) { KTG.toast(res.message || 'Could not update.', 'danger', 'Error'); return; }
    const r = items.find(x => x.id === paidStatusId);
    if (r) {
      r.paid_status_id = res.data.paid_status_id;
      const opt = paidStatusOptions.find(t => String(t.id) === String(res.data.paid_status_id));
      r.paid_status_name = opt ? opt.name : null;
      if (res.data.expiry_date) r.expiry_date = res.data.expiry_date;
    }
    bootstrap.Modal.getInstance(document.getElementById('paidStatusModal')).hide();
    if (res.data.expiry_extended) {
      KTG.toast(`Paid status updated. Expiry extended to ${formatDMY(res.data.expiry_date)}.`, 'success', 'Renewal Extended');
    } else {
      KTG.toast('Paid status updated.', 'success', 'Updated');
    }
    renderList();
    if (viewId === paidStatusId) openViewModal(viewId);
  }).fail((xhr) => {
    const msg = (xhr.responseJSON && xhr.responseJSON.message) || 'Could not update.';
    KTG.toast(msg, 'danger', 'Error');
  }).always(() => $btn.prop('disabled', false));
}

/* Mirrors exactly what the list shows (Company/Customer, Type = Project + Project Type,
   Server, Expiry, Paid Status, Renewed, Status), plus extra fields useful in export. */
function exportRenewals(){
  const rows = [['S.No','Company','Customer','Project','Project Type','Server','Domain Control','Live Link','Expiry Date','Renewal Type','Paid Status','Renewed','Status']];
  items.forEach((r,i) => rows.push([i+1, r.company_name||'', r.customer_name, r.project_name, r.project_type_name||'', r.server_name||'', r.domain_control||'', r.live_link||'', formatDMY(r.expiry_date), r.renewal_type_name||'', r.paid_status_name||'', +r.is_renewed ? 'Renewed' : 'Not Renewed', (r.status || 'active') === 'active' ? 'Active' : 'Inactive']));
  KTG.exportCsv('Renewals.csv', rows);
  KTG.toast('Renewals exported as CSV (opens in Excel).', 'success', 'Export Ready');
}

/* ---------------- View Details ---------------- */
function openViewModal(id){
  viewId = id;
  const r = items.find(x => x.id === id);
  if (!r) return;
  document.getElementById('viewInvoiceBtn').innerHTML = r.invoice_id
    ? '<i class="bi bi-receipt me-1"></i>View Invoice'
    : '<i class="bi bi-receipt me-1"></i>Generate Invoice';
  const days = daysDiff(r.expiry_date);
  const cc = companyCustomerLabel(r);
  document.getElementById('viewRenewalBody').innerHTML = `
    <div class="d-flex align-items-start justify-content-between mb-3">
      <div>
        <h5 class="mb-1">${r.project_name}</h5>
        <div class="cell-sub">${cc.title}${cc.sub ? ' &middot; ' + cc.sub : ''} &middot; <span class="chip chip-inprogress">${r.project_type_name || '—'}</span></div>
      </div>
      <span class="chip ${r.paid_status_name ? 'chip-' + paidChipClass(r.paid_status_name) : 'chip-pending'}" style="cursor:pointer;" onclick="openPaidStatusModal(${r.id})" title="Click to change">${r.paid_status_name || 'Set Status'}</span>
    </div>
    <div class="row g-3">
      <div class="col-md-6">
        <div class="bill-block-label">Server</div>
        <div class="fs-14 fw-600 mb-3"><i class="bi bi-hdd-network me-2 text-primary-KTG"></i>${r.server_name || '—'}</div>
        <div class="bill-block-label">Domain Control</div>
        <div class="fs-14 mb-3">${r.domain_control || '—'}</div>
        <div class="bill-block-label">Renewal Type</div>
        <div class="fs-14 mb-3"><span class="chip chip-inprogress">${r.renewal_type_name || '—'}</span></div>
        <div class="bill-block-label">Status</div>
        <div class="fs-14 mb-3"><span class="chip chip-${(r.status || 'active') === 'active' ? 'active' : 'cancelled'}" style="cursor:pointer;" onclick="openStatusModal(${r.id})" title="Click to change">${(r.status || 'active') === 'active' ? 'Active' : 'Inactive'}</span></div>
      </div>
      <div class="col-md-6">
        <div class="bill-block-label">Expiry Date</div>
        <div class="fs-14 fw-600 mb-1">${formatDMY(r.expiry_date)}</div>
        <div class="mb-3">${expiryBadge(r.expiry_date, r.paid_status_name)} ${(r.paid_status_name || '').toLowerCase() === 'paid' ? '' : `<span class="fs-12 text-muted">(${days<0?'expired':'expires in'} ${Math.abs(days)} day${Math.abs(days)===1?'':'s'})</span>`}</div>
        <div class="bill-block-label">Paid Status</div>
        <div class="fs-14 mb-3"><span class="chip ${r.paid_status_name ? 'chip-' + paidChipClass(r.paid_status_name) : 'chip-pending'}" style="cursor:pointer;" onclick="openPaidStatusModal(${r.id})" title="Click to change">${r.paid_status_name || 'Set Status'}</span></div>
        <div class="bill-block-label">Renewed</div>
        <div class="fs-14 mb-3"><span class="chip chip-${+r.is_renewed ? 'active' : 'overdue'}" style="cursor:pointer;" onclick="openRenewedModal(${r.id})" title="Click to change">${+r.is_renewed ? 'Renewed' : 'Not Renewed'}</span></div>
      </div>
      <div class="col-12">
        <div class="bill-block-label">Live Project Link &amp; Login Details</div>
        <div class="section-card p-3 fs-13" style="background:var(--surface-alt); white-space:pre-wrap;">${r.live_link || '—'}</div>
      </div>
    </div>`;
  new bootstrap.Modal(document.getElementById('viewRenewalModal')).show();
}

/* ---------------- Generate Invoice — mirrors invoice-create.php, wired to real backend data ---------------- */
function giEmptyItem(){ return {service:'', desc:'', qty:'', rate:''}; }

function openInvoiceModal(id){
  const r = items.find(x => x.id === id);
  if (!r) return;
  giContext = r;
  giCustomer = null;

  document.getElementById('giModalTitle').innerHTML = r.invoice_id
    ? `<i class="bi bi-receipt text-primary-KTG me-2"></i>Renewal Invoice — ${KTG.escAttr(r.invoice_number || '')}`
    : '<i class="bi bi-receipt text-primary-KTG me-2"></i>Generate Renewal Invoice';
  document.getElementById('giSaveBtn').innerHTML = r.invoice_id
    ? '<i class="bi bi-save me-1"></i>Update Invoice'
    : '<i class="bi bi-save me-1"></i>Save Invoice';

  document.getElementById('giCustomerBlock').textContent = 'Loading customer…';
  $.get('ajax/customers.php', { action: 'view', id: r.customer_id }).always((res) => {
    giCustomer = (res && res.data) || null;
    giRenderCustomer();
  });

  if (r.invoice_id) {
    $.get('ajax/renewals.php', { action: 'invoice_view', renewal_id: id }).done((res) => {
      giLoadSavedInvoice(res.data);
    }).fail(() => {
      giBuildFreshInvoice(r);
    });
  } else {
    giBuildFreshInvoice(r);
  }

  new bootstrap.Modal(document.getElementById('invoiceModal')).show();
}

function giBuildFreshInvoice(r){
  giItems = [giEmptyItem()];
  const preset = giServices.find(s => (r.renewal_type_name || '').toLowerCase().includes((s.name || '').toLowerCase()));
  if (preset) {
    giItems[0] = {service: preset.name, desc: `${r.project_name} (${r.server_name || 'hosting'})`, qty: 1, rate: +preset.rate || 0};
    giItems.push(giEmptyItem());
  }
  renderGiItems();

  document.getElementById('giInvNumberDisplay').textContent = 'INV-RENEWAL-' + r.id;
  document.getElementById('giInvDate').value = new Date().toISOString().slice(0,10);
  document.getElementById('giDueDate').value = r.expiry_date || '';
  document.getElementById('giTermsText').value = `Renewal invoice for ${(r.renewal_type_name||'').toLowerCase()} hosting/domain renewal — ${r.project_name}. Please settle before the service expiry date to avoid downtime.`;
  document.getElementById('giDiscountAmount').value = '';
  document.getElementById('giAdvancePaid').value = '';
  setGiGst(true);
}

function giLoadSavedInvoice(inv){
  giItems = (inv.items || []).map(it => ({service: it.service, desc: it.desc, qty: it.qty, rate: it.rate}));
  if (!giItems.length) giItems.push(giEmptyItem());
  giItems.push(giEmptyItem());
  renderGiItems();

  document.getElementById('giInvNumberDisplay').textContent = inv.invoice_number || '';
  document.getElementById('giInvDate').value = inv.invoice_date || '';
  document.getElementById('giDueDate').value = inv.due_date || '';
  document.getElementById('giTermsText').value = inv.terms || '';
  document.getElementById('giDiscountAmount').value = +inv.discount || '';
  document.getElementById('giAdvancePaid').value = +inv.advance_paid || '';
  document.getElementById('giTaxType').value = inv.tax_type === 'igst' ? 'igst' : 'sgst_cgst';
  if (+inv.tax_percent) document.getElementById('giTaxRate').value = inv.tax_percent;

  setGiGst(inv.gst_type !== 'nongst');
}

function giRenderCustomer(){
  const meta = document.getElementById('giCustomerBlock');
  const r = giContext;
  const c = giCustomer;
  if (!c) { meta.innerHTML = `<span class="bill-to-name">${r ? KTG.escAttr(r.customer_name) : '—'}</span>`; return; }
  const gstLine = giGstEnabled ? `GST : ${c.gst_number || '—'}<br>` : '';
  const company = (r && r.company_name || '').trim();
  const nameLine = company && company.toLowerCase() !== (c.name || '').trim().toLowerCase()
    ? `<span class="bill-to-name">${KTG.escAttr(company)}</span><br>Attn: ${KTG.escAttr(c.name)}<br>`
    : `<span class="bill-to-name">${KTG.escAttr(c.name)}</span><br>`;
  meta.innerHTML = `${nameLine}${gstLine}${KTG.escAttr(c.address || '—')}<br>${KTG.escAttr(c.mobile || '—')}`;
}

function giRefreshLetterhead(){
  const docType = giGstEnabled ? 'TAX INVOICE' : 'BILL OF SUPPLY';
  const gstType = giGstEnabled ? 'gst' : 'nongst';
  document.getElementById('giDocTypeLabel').textContent = docType;
  document.getElementById('giLetterheadCompanyBlock').innerHTML = KTG.companyLetterheadHtml(gstType, docType);
  const c = KTG.CompanyProfile.get(gstType);
  document.getElementById('giLetterheadSummary').textContent = c.name || '';
}

function renderGiItems(){
  const serviceOptions = (it) => `<option value="" ${it.service ? '' : 'selected'} disabled>Select Product</option>` +
    giServices.map(s => `<option value="${s.name}" ${s.name === it.service ? 'selected' : ''}>${s.name}</option>`).join('');

  document.getElementById('giItemsBodyDesktop').innerHTML = giItems.map((it, i) => `
    <tr>
      <td>
        <select class="form-select mb-1 no-search" onchange="giOnServiceChange(${i}, this.value)">
          ${serviceOptions(it)}
        </select>
        <input class="form-control form-control-sm" placeholder="Description / link (optional)" value="${KTG.escAttr(it.desc)}" onchange="giItems[${i}].desc=this.value">
      </td>
      <td><input type="number" class="form-control" placeholder="0" value="${it.qty}" onchange="giItems[${i}].qty=+this.value; calcGiTotals();"></td>
      <td><input type="number" class="form-control" placeholder="0" value="${it.rate}" onchange="giItems[${i}].rate=+this.value; calcGiTotals();"></td>
      <td class="fw-600 gi-item-amount" data-i="${i}">₹0</td>
      <td><button class="btn btn-sm btn-light" type="button" onclick="removeGiItem(${i})"><i class="bi bi-trash text-danger"></i></button></td>
    </tr>`).join('');

  document.getElementById('giItemsBodyMobile').innerHTML = giItems.map((it, i) => `
    <div class="ie2-item">
      <button class="btn btn-sm btn-light ie2-remove" type="button" onclick="removeGiItem(${i})"><i class="bi bi-trash text-danger"></i></button>
      <div class="ie2-field">
        <label>Product</label>
        <select class="form-select no-search" onchange="giOnServiceChange(${i}, this.value)">
          ${serviceOptions(it)}
        </select>
      </div>
      <div class="ie2-field">
        <label>Description</label>
        <textarea class="form-control" rows="2" onchange="giItems[${i}].desc=this.value">${KTG.escAttr(it.desc)}</textarea>
      </div>
      <div class="ie2-2col">
        <div class="ie2-field mb-0"><label>Qty</label><input type="number" class="form-control" placeholder="0" value="${it.qty}" onchange="giItems[${i}].qty=+this.value; calcGiTotals();"></div>
        <div class="ie2-field mb-0"><label>Rate (₹)</label><input type="number" class="form-control" placeholder="0" value="${it.rate}" onchange="giItems[${i}].rate=+this.value; calcGiTotals();"></div>
      </div>
      <div class="ie2-amount-bar"><span class="text-muted fs-13">Line Amount</span><span class="val gi-item-amount" data-i="${i}">₹0</span></div>
    </div>`).join('');

  calcGiTotals();
}

function removeGiItem(i){
  giItems.splice(i, 1);
  if (!giItems.length) giItems.push(giEmptyItem());
  renderGiItems();
}

function giOnServiceChange(i, name){
  const svc = giServices.find(s => s.name === name);
  if (!svc) return;
  giItems[i].service = svc.name;
  giItems[i].rate = +svc.rate || 0;
  if (!giItems[i].qty) giItems[i].qty = 1;
  if (i === giItems.length - 1) giItems.push(giEmptyItem());
  renderGiItems();
}

function setGiGst(state){
  giGstEnabled = state;
  document.getElementById('giWithGst').classList.toggle('active', state);
  document.getElementById('giWithoutGst').classList.toggle('active', !state);
  document.getElementById('giTaxOptions').style.display = state ? 'block' : 'none';
  giRefreshLetterhead();
  giRenderCustomer();
  calcGiTotals();
}

function giItemsSubtotal(list){
  return list.reduce((s, it) => s + (+it.qty || 0) * (+it.rate || 0), 0);
}

function giCurrentTax(taxable){
  const percent = giGstEnabled ? (+document.getElementById('giTaxRate').value || 0) : 0;
  const taxType = giGstEnabled ? document.getElementById('giTaxType').value : 'none';
  const taxAmount = taxable * percent / 100;
  return { percent, taxType, taxAmount };
}

/* Subtotal → Discount → taxable amount → tax → raw total → rounded Total (Round Off = the gap).
   The Total field is editable: typing a target total back-solves the Discount needed to reach it. */
function calcGiTotals(){
  let subtotal = 0;
  giItems.forEach((it, i) => {
    const amount = (+it.qty || 0) * (+it.rate || 0);
    subtotal += amount;
    document.querySelectorAll(`.gi-item-amount[data-i="${i}"]`).forEach(cell => cell.textContent = KTG.money(Math.round(amount)));
  });

  const discount = Math.min(Math.max(+document.getElementById('giDiscountAmount').value || 0, 0), subtotal);
  const taxable = subtotal - discount;
  const { percent, taxType, taxAmount } = giCurrentTax(taxable);
  const half = percent / 2;
  const rawTotal = taxable + taxAmount;
  const roundedTotal = Math.round(rawTotal);
  const roundOff = roundedTotal - rawTotal;
  const advance = +document.getElementById('giAdvancePaid').value || 0;
  const due = Math.max(roundedTotal - advance, 0);

  document.getElementById('giSubtotal').textContent = KTG.money(Math.round(subtotal));
  document.getElementById('giSgstLabel').textContent = `SGST ${half}%`;
  document.getElementById('giCgstLabel').textContent = `CGST ${half}%`;
  document.getElementById('giIgstLabel').textContent = `IGST ${percent}%`;
  document.getElementById('giSgstAmt').textContent = KTG.money(Math.round(taxAmount/2));
  document.getElementById('giCgstAmt').textContent = KTG.money(Math.round(taxAmount/2));
  document.getElementById('giIgstAmt').textContent = KTG.money(Math.round(taxAmount));
  document.getElementById('giSgstRow').classList.toggle('bt-hidden', taxType !== 'sgst_cgst');
  document.getElementById('giCgstRow').classList.toggle('bt-hidden', taxType !== 'sgst_cgst');
  document.getElementById('giIgstRow').classList.toggle('bt-hidden', taxType !== 'igst');
  document.getElementById('giRoundoffRow').classList.toggle('bt-hidden', Math.abs(roundOff) <= 0.001);
  document.getElementById('giRoundoff').textContent = (roundOff >= 0 ? '+' : '−') + KTG.money(Math.abs(roundOff));
  document.getElementById('giGrandTotalInput').value = roundedTotal;
  document.getElementById('giSumBalance').textContent = KTG.money(due);
}

function giOnGrandTotalChange(){
  const target = +document.getElementById('giGrandTotalInput').value || 0;
  const subtotal = giItemsSubtotal(giItems);
  const percent = giGstEnabled ? (+document.getElementById('giTaxRate').value || 0) : 0;
  const taxable = percent > 0 ? target / (1 + percent / 100) : target;
  const discount = Math.min(Math.max(subtotal - taxable, 0), subtotal);
  document.getElementById('giDiscountAmount').value = Math.round(discount);
  calcGiTotals();
}

function openInvoicePreview(){
  const r = giContext;
  const c = giCustomer;
  const realItems = giItems.filter(it => it.service);
  const subtotal = giItemsSubtotal(realItems);
  const discount = Math.min(Math.max(+document.getElementById('giDiscountAmount').value || 0, 0), subtotal);
  const taxable = subtotal - discount;
  const { percent, taxType, taxAmount } = giCurrentTax(taxable);
  const rawTotal = taxable + taxAmount;
  const grandTotal = Math.round(rawTotal);
  const roundOff = grandTotal - rawTotal;
  const advance = +document.getElementById('giAdvancePaid').value || 0;
  const balanceDue = Math.max(grandTotal - advance, 0);

  const data = {
    docType: document.getElementById('giDocTypeLabel').textContent,
    gstType: giGstEnabled ? 'gst' : 'nongst',
    invNumber: document.getElementById('giInvNumberDisplay').textContent,
    invDate: document.getElementById('giInvDate').value,
    dueDate: document.getElementById('giDueDate').value,
    customer: c ? {name:c.name, company: (r && r.company_name) || '', gst: giGstEnabled ? (c.gst_number||'') : '', address:c.address||'—', mobile:c.mobile||'—'} : {name: r ? r.customer_name : '—', gst:'', address:'—', mobile:'—'},
    items: realItems,
    subtotal: Math.round(subtotal), discount: Math.round(discount),
    taxType, percent, halfPercent: percent/2, taxAmount: Math.round(taxAmount), roundOff,
    grandTotal, advancePaid: advance, balanceDue,
    terms: document.getElementById('giTermsText').value,
    bank: {name:'Knock The Globe Technologies', account:'0012299171', bank:'Kotak Bank', ifsc:'KKBK0008775'}
  };
  sessionStorage.setItem('ktg_invoice_preview', JSON.stringify(data));
  window.open('invoice-preview.php', '_blank');
}

function giSaveInvoice(){
  const r = giContext;
  if (!r) return;
  const realItems = giItems.filter(it => it.service);
  if (!realItems.length) { KTG.toast('Please add at least one item before saving.', 'danger', 'Error'); return; }

  const c = giCustomer;
  const subtotal = giItemsSubtotal(realItems);
  const discount = Math.min(Math.max(+document.getElementById('giDiscountAmount').value || 0, 0), subtotal);
  const taxable = subtotal - discount;
  const { percent, taxType, taxAmount } = giCurrentTax(taxable);
  const rawTotal = taxable + taxAmount;
  const grandTotal = Math.round(rawTotal);
  const roundOff = grandTotal - rawTotal;
  const advance = +document.getElementById('giAdvancePaid').value || 0;
  const balanceDue = Math.max(grandTotal - advance, 0);

  const payload = {
    action: 'invoice_save',
    renewal_id: r.id,
    invoice_number: document.getElementById('giInvNumberDisplay').textContent,
    gst_type: giGstEnabled ? 'gst' : 'nongst',
    doc_type: document.getElementById('giDocTypeLabel').textContent,
    invoice_date: document.getElementById('giInvDate').value,
    due_date: document.getElementById('giDueDate').value,
    customer_name: c ? c.name : (r.customer_name || ''),
    company_name: r.company_name || '',
    customer_gst: c ? (c.gst_number || '') : '',
    customer_address: c ? (c.address || '') : '',
    customer_mobile: c ? (c.mobile || '') : '',
    items: JSON.stringify(realItems),
    subtotal: Math.round(subtotal), discount: Math.round(discount),
    tax_type: taxType, tax_percent: percent, tax_amount: Math.round(taxAmount),
    round_off: roundOff, grand_total: grandTotal, advance_paid: advance, balance_due: balanceDue,
    terms: document.getElementById('giTermsText').value
  };

  const $btn = $('#giSaveBtn').prop('disabled', true);
  $.post('ajax/renewals.php', payload).done((res) => {
    KTG.toast(res.message || 'Invoice saved.', 'success', 'Saved');
    r.invoice_id = res.data.invoice_id;
    r.invoice_number = res.data.invoice_number;
    renderList();
    document.getElementById('giModalTitle').innerHTML = `<i class="bi bi-receipt text-primary-KTG me-2"></i>Renewal Invoice — ${KTG.escAttr(r.invoice_number || '')}`;
    document.getElementById('giSaveBtn').innerHTML = '<i class="bi bi-save me-1"></i>Update Invoice';
  }).fail((xhr) => {
    const msg = (xhr.responseJSON && xhr.responseJSON.message) || 'Could not save the invoice. Please try again.';
    KTG.toast(msg, 'danger', 'Error');
  }).always(() => $btn.prop('disabled', false));
}
JS;
?>
<?php include __DIR__ . '/components/footer.php'; ?>
