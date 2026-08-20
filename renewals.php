<?php
$pageTitle = "Renewals &amp; Hosting";
$pageKey = "renewals";
$extraHead = <<<'HTML'
<style>
.expiry-badge{ font-size:10.5px; font-weight:700; padding:.15rem .5rem; border-radius: var(--radius-pill); white-space:nowrap; }
.link-cell{ max-width:220px; overflow:hidden; text-overflow:ellipsis; white-space:nowrap; display:inline-block; vertical-align:middle; }
.gst-toggle-btn{ flex:1; border:2px solid var(--border); border-radius:12px; padding:.6rem; text-align:center; cursor:pointer; transition: all .18s var(--ease); background:#fff; }
.gst-toggle-btn.active{ border-color: var(--primary); background: var(--primary-light); }

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
              <tr><th style="width:46px;">S.No</th><th>Date</th><th>Project / Customer</th><th>Type</th><th>Total</th><th>Expiry</th><th>Paid Status</th><th></th></tr>
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
    calcGiTotals();
  });
  $.get('ajax/masters.php', { master: 'services', action: 'list' }).done((res) => {
    giServices = res.data || [];
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
      <td class="fs-13">${(r.created_at || '').slice(0,10) || '—'}</td>
      <td>
        <div class="cell-title">${r.project_name}</div>
        <div class="cell-sub">${r.customer_name}</div>
      </td>
      <td>${r.project_type_name ? `<span class="chip chip-inprogress">${r.project_type_name}</span>` : '<span class="text-muted">—</span>'}</td>
      <td class="fs-13 fw-600">${r.invoice_grand_total ? KTG.money(Math.round(+r.invoice_grand_total)) : '<span class="text-muted">—</span>'}</td>
      <td><div class="fs-13 fw-600">${r.expiry_date}</div>${expiryBadge(r.expiry_date)}</td>
      <td>${r.paid_status_name ? `<span class="chip chip-${paidChipClass(r.paid_status_name)}">${r.paid_status_name}</span>` : '<span class="text-muted">—</span>'}</td>
      <td>
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
  document.getElementById('viewInvoiceBtn').innerHTML = r.invoice_id
    ? '<i class="bi bi-receipt me-1"></i>View Invoice'
    : '<i class="bi bi-receipt me-1"></i>Generate Invoice';
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
  meta.innerHTML = `<span class="bill-to-name">${KTG.escAttr(c.name)}</span><br>${gstLine}${KTG.escAttr(c.address || '—')}<br>${KTG.escAttr(c.mobile || '—')}`;
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
    customer: c ? {name:c.name, gst: giGstEnabled ? (c.gst_number||'') : '', address:c.address||'—', mobile:c.mobile||'—'} : {name: r ? r.customer_name : '—', gst:'', address:'—', mobile:'—'},
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
