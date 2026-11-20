<?php
$pageTitle = "Customers";
$pageKey = "customers";
include __DIR__ . '/components/header.php';
?>
      <div class="page-head fade-in-up">
        <div>
          <h1>Customers</h1>
          <nav class="breadcrumb"><span class="breadcrumb-item"><a href="index.php">Dashboard</a></span><span class="breadcrumb-item active">Customers</span></nav>
        </div>
        <div class="d-flex gap-2">
          <button class="btn btn-primary" id="addCustomerBtn"><i class="bi bi-plus-lg me-1"></i>Add Customer</button>
        </div>
      </div>

      <!-- Mini stats -->
      <div class="row g-3 mb-3 stagger">
        <div class="col-6 col-md-3">
          <div class="stat-card"><div class="stat-icon bg-soft-primary"><i class="bi bi-people-fill"></i></div><div class="stat-label">Total Customers</div><div class="stat-value" id="statTotal">0</div></div>
        </div>
        <div class="col-6 col-md-3">
          <div class="stat-card"><div class="stat-icon bg-soft-success"><i class="bi bi-person-check-fill"></i></div><div class="stat-label">Active</div><div class="stat-value" id="statActive">0</div></div>
        </div>
        <div class="col-6 col-md-3">
          <div class="stat-card"><div class="stat-icon bg-soft-warning"><i class="bi bi-person-exclamation"></i></div><div class="stat-label">Pending Onboard</div><div class="stat-value" id="statPending">0</div></div>
        </div>
        <div class="col-6 col-md-3">
          <div class="stat-card"><div class="stat-icon bg-soft-danger"><i class="bi bi-person-dash-fill"></i></div><div class="stat-label">Inactive</div><div class="stat-value" id="statInactive">0</div></div>
        </div>
      </div>

      <!-- Toolbar -->
      <div class="section-card mb-3 fade-in-up">
        <div class="section-body py-3">
          <div class="d-flex flex-wrap gap-2 align-items-center">
            <div class="position-relative flex-grow-1" style="min-width:220px; max-width:340px;">
              <i class="bi bi-search position-absolute" style="left:14px; top:50%; transform:translateY(-50%); color:var(--text-muted);"></i>
              <input type="text" id="custSearch" class="form-control ps-5" placeholder="Search by name, company, mobile, email...">
            </div>
            <select class="form-select" id="filterStatus" style="max-width:160px;">
              <option value="">All Status</option>
              <option value="active">Active</option>
              <option value="pending">Pending</option>
              <option value="inactive">Inactive</option>
            </select>
            <select class="form-select" id="filterType" style="max-width:180px;">
              <option value="">All Project Types</option>
            </select>
            <div class="ms-auto btn-group" role="group">
              <button class="btn btn-soft-primary" id="viewTableBtn"><i class="bi bi-list-ul"></i></button>
              <button class="btn btn-light" id="viewGridBtn"><i class="bi bi-grid-3x3-gap"></i></button>
            </div>
          </div>
        </div>
      </div>

      <!-- Table View -->
      <div class="section-card fade-in-up" id="tableView">
        <div class="d-flex align-items-center gap-2 p-3 border-bottom fs-13 text-muted">
          <span>Show</span>
          <select class="form-select form-select-sm" id="pageSizeSelect" style="width:auto;">
            <option value="50" selected>50</option>
            <option value="100">100</option>
            <option value="500">500</option>
          </select>
          <span>entries</span>
        </div>
        <div class="table-responsive">
          <table class="table table-modern mb-0">
            <thead>
              <tr>
                <th><input type="checkbox" class="form-check-input"></th>
                <th>Customer</th><th>Contact</th><th>Project Type</th><th>GST Number</th><th>Status</th><th></th>
              </tr>
            </thead>
            <tbody id="custTableBody"></tbody>
          </table>
        </div>
        <div class="d-flex justify-content-between align-items-center p-3 border-top">
          <span class="fs-12 text-muted">Showing <b id="custCount">0</b> of <b id="custTotal">0</b> entries</span>
          <nav><ul class="pagination mb-0" id="custPagination"></ul></nav>
        </div>
      </div>

      <!-- Grid View -->
      <div class="row g-3 d-none" id="gridView"></div>

<?php
$pageModals = <<<'HTML'
<!-- Add / Edit Customer Modal -->
<div class="modal fade" id="addCustomerModal" tabindex="-1">
  <div class="modal-dialog modal-lg modal-dialog-scrollable">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="customerModalTitle"><i class="bi bi-person-plus-fill text-primary-KTG me-2"></i>Add New Customer</h5>
        <button class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <form id="addCustomerForm">
          <input type="hidden" id="cmId" value="">
          <div class="divider-label">Basic Details</div>
          <div class="row g-3">
            <div class="col-md-6"><label class="form-label">Customer Name <span class="req">*</span></label><input class="form-control" id="cmName" placeholder="e.g. Arun Kumar" required></div>
                        <div class="col-md-6"><label class="form-label">Mobile Number <span class="req">*</span></label><input class="form-control" id="cmMobile" placeholder="+91 98765 43210" required></div>
            <div class="col-md-6"><label class="form-label">WhatsApp Number</label><input class="form-control" id="cmWhatsapp" placeholder="+91 98765 43210"></div>
            <div class="col-md-6"><label class="form-label">Email Address</label><input type="email" class="form-control" id="cmEmail" placeholder="name@company.com"></div>
            <div class="col-md-6"><label class="form-label">GST Number</label><input class="form-control" id="cmGst" placeholder="33ABCDE1234F1Z5"></div>
            <div class="col-md-6"><label class="form-label">City</label><input class="form-control" id="cmCity" placeholder="e.g. Karur"></div>
            <div class="col-12"><label class="form-label">Address</label><textarea class="form-control" id="cmAddress" rows="2" placeholder="Full billing address"></textarea></div>
          </div>

          <div class="divider-label">Companies</div>
          <div class="fs-12 text-muted mb-2">A customer can own more than one company. The first company is the primary one shown across the app.</div>
          <div id="cmCompanyList" class="d-flex flex-column gap-2"></div>
          <button type="button" class="btn btn-sm btn-soft-primary mt-2" id="cmAddCompanyBtn"><i class="bi bi-plus-lg me-1"></i>Add another company</button>

          <div class="divider-label">Project Type</div>
          <div class="row g-3">
            <div class="col-md-6">
              <label class="form-label">Select Type</label>
              <select class="form-select" id="modalProjectType"><option value="">Select Type</option></select>
            </div>
            <div class="col-md-6"><label class="form-label">Status</label>
              <select class="form-select" id="cmStatus">
                <option value="active">Active</option><option value="pending" selected>Pending</option><option value="inactive">Inactive</option>
              </select>
            </div>
          </div>

          <div class="divider-label">Notes</div>
          <textarea class="form-control" id="cmNotes" rows="2" placeholder="Internal notes about this customer"></textarea>

          <div id="customerFormError" class="alert alert-danger py-2 fs-13 mt-3 d-none" style="border-radius:11px;"></div>
        </form>
      </div>
      <div class="modal-footer">
        <button class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
        <button class="btn btn-primary" id="saveCustomerBtn"><i class="bi bi-check-lg me-1"></i>Save Customer</button>
      </div>
    </div>
  </div>
</div>
HTML;
?>
<?php
$pageScript = <<<'JS'
document.addEventListener('DOMContentLoaded', () => setTimeout(init, 150));

let currentPage = 1;
let pageSize = 50;
let searchTimer = null;

function init(){
  loadProjectTypes();
  loadStats();
  loadCustomers();

  document.getElementById('custSearch').addEventListener('input', () => {
    clearTimeout(searchTimer);
    searchTimer = setTimeout(() => { currentPage = 1; loadCustomers(); }, 300);
  });
  document.getElementById('filterStatus').addEventListener('change', () => { currentPage = 1; loadCustomers(); });
  document.getElementById('filterType').addEventListener('change', () => { currentPage = 1; loadCustomers(); });
  document.getElementById('pageSizeSelect').addEventListener('change', () => {
    pageSize = +document.getElementById('pageSizeSelect').value;
    currentPage = 1;
    loadCustomers();
  });

  document.getElementById('viewTableBtn').addEventListener('click', () => toggleView('table'));
  document.getElementById('viewGridBtn').addEventListener('click', () => toggleView('grid'));

  document.getElementById('addCustomerBtn').addEventListener('click', () => openCustomerModal(null));
  document.getElementById('saveCustomerBtn').addEventListener('click', saveCustomer);
  document.getElementById('addCustomerModal').addEventListener('hidden.bs.modal', resetCustomerForm);
  document.getElementById('cmAddCompanyBtn').addEventListener('click', () => addCompanyRow('', true));

  const editId = KTG.qs('edit');
  if (editId) openCustomerModal(+editId);
}

function loadProjectTypes(){
  $.get('ajax/masters.php', { master: 'project_types', action: 'list' }).done((res) => {
    const list = (res && res.data) || [];
    const options = list.map(t => `<option value="${t.id}">${t.name}</option>`).join('');
    document.getElementById('modalProjectType').innerHTML = '<option value="">Select Type</option>' + options;
    document.getElementById('filterType').innerHTML = '<option value="">All Project Types</option>' + options;
  });
}

function loadStats(){
  $.get('ajax/customers.php', { action: 'stats' }).done((res) => {
    const s = (res && res.data) || {};
    document.getElementById('statTotal').textContent = s.total || 0;
    document.getElementById('statActive').textContent = s.active || 0;
    document.getElementById('statPending').textContent = s.pending || 0;
    document.getElementById('statInactive').textContent = s.inactive || 0;
  });
}

function toggleView(mode){
  const tableBtn = document.getElementById('viewTableBtn'), gridBtn = document.getElementById('viewGridBtn');
  document.getElementById('tableView').classList.toggle('d-none', mode !== 'table');
  document.getElementById('gridView').classList.toggle('d-none', mode !== 'grid');
  tableBtn.className = 'btn ' + (mode === 'table' ? 'btn-soft-primary' : 'btn-light');
  gridBtn.className = 'btn ' + (mode === 'grid' ? 'btn-soft-primary' : 'btn-light');
}

function loadCustomers(){
  const params = {
    action: 'list',
    q: document.getElementById('custSearch').value.trim(),
    status: document.getElementById('filterStatus').value,
    project_type_id: document.getElementById('filterType').value,
    page: currentPage,
    per_page: pageSize
  };
  $.get('ajax/customers.php', params).done((res) => {
    if (!res.success) { KTG.toast(res.message || 'Could not load customers.', 'danger', 'Error'); return; }
    renderTable(res.data.rows);
    renderGrid(res.data.rows);
    renderPagination(res.data);
    document.getElementById('custCount').textContent = res.data.rows.length;
    document.getElementById('custTotal').textContent = res.data.total;
  });
}

function renderPagination(data){
  const nav = document.getElementById('custPagination');
  if (data.total_pages <= 1) { nav.innerHTML = ''; return; }
  let html = `<li class="page-item ${data.page <= 1 ? 'disabled' : ''}"><a class="page-link" href="#" onclick="gotoPage(${data.page - 1});return false;">Prev</a></li>`;
  for (let p = 1; p <= data.total_pages; p++) {
    html += `<li class="page-item ${p === data.page ? 'active' : ''}"><a class="page-link" href="#" onclick="gotoPage(${p});return false;">${p}</a></li>`;
  }
  html += `<li class="page-item ${data.page >= data.total_pages ? 'disabled' : ''}"><a class="page-link" href="#" onclick="gotoPage(${data.page + 1});return false;">Next</a></li>`;
  nav.innerHTML = html;
}

function gotoPage(p){
  currentPage = p;
  loadCustomers();
}

function avatarColor(name){
  const colors = ['#2563eb','#7c3aed','#0891b2','#16a34a','#d97706','#dc2626'];
  return colors[(name || '?').charCodeAt(0) % colors.length];
}
function initials(name){ return (name || '?').split(' ').map(w=>w[0]).slice(0,2).join('').toUpperCase(); }
function statusChip(status){ return status === 'active' ? 'active' : status === 'pending' ? 'pending' : 'cancelled'; }

/* The list has one row per company (see ajax/customers.php), so c.company_name here is that
   row's own company. Company Name (title) + Customer Name (sub) — falls back to just the customer name
   when no distinct company name is on file. Same pattern used in renewals.php. */
function companyCustomerLabel(c){
  const company = (c.company_name || '').trim();
  const name = (c.name || '').trim();
  if (!company || company.toLowerCase() === name.toLowerCase()) {
    return { title: name || '—', sub: '' };
  }
  return { title: company, sub: name };
}

/* ---- Companies repeater inside the Add/Edit modal ---- */
function addCompanyRow(value, focus){
  const list = document.getElementById('cmCompanyList');
  const row = document.createElement('div');
  row.className = 'd-flex gap-2 cm-company-row';
  row.innerHTML = `<input class="form-control cm-company-input" placeholder="${list.children.length ? 'Another company name' : 'e.g. Zen Foods Pvt Ltd'}" maxlength="150" value="${KTG.escAttr(value || '')}">
    <button type="button" class="btn btn-light flex-shrink-0" title="Remove company"><i class="bi bi-trash text-danger"></i></button>`;
  row.querySelector('button').addEventListener('click', () => {
    row.remove();
    if (!list.children.length) addCompanyRow('');
  });
  list.appendChild(row);
  if (focus) row.querySelector('input').focus();
}

function setCompanyRows(companies){
  document.getElementById('cmCompanyList').innerHTML = '';
  (companies && companies.length ? companies : ['']).forEach(c => addCompanyRow(c));
}

function getCompanyValues(){
  return Array.from(document.querySelectorAll('#cmCompanyList .cm-company-input')).map(i => i.value.trim()).filter(Boolean);
}

function renderTable(list){
  document.getElementById('custTableBody').innerHTML = list.map(c => {
    const cc = companyCustomerLabel(c);
    return `
    <tr>
      <td><input type="checkbox" class="form-check-input"></td>
      <td>
        <div class="d-flex align-items-center gap-3">
          <div class="avatar-sm" style="background:${avatarColor(c.name)}">${initials(c.name)}</div>
          <div>
            <a href="customer-details.php?id=${c.id}" class="cell-title d-block">${cc.title}</a>
            ${cc.sub ? `<div class="cell-sub">${cc.sub}</div>` : ''}
          </div>
        </div>
      </td>
      <td><div>${c.mobile}</div><div class="cell-sub">${c.email || ''}</div></td>
      <td>${c.project_type_name ? `<span class="chip chip-inprogress">${c.project_type_name}</span>` : '<span class="text-muted">—</span>'}</td>
      <td class="fs-13">${c.gst_number || '—'}</td>
      <td><span class="chip chip-${statusChip(c.status)}">${c.status}</span></td>
      <td>
        <div class="dropdown">
          <button class="btn btn-sm btn-light" data-bs-toggle="dropdown"><i class="bi bi-three-dots-vertical"></i></button>
          <div class="dropdown-menu dropdown-menu-end">
            <a class="dropdown-item" href="customer-details.php?id=${c.id}"><i class="bi bi-eye"></i>View Profile</a>
            <a class="dropdown-item" href="#" onclick="openCustomerModal(${c.id});return false;"><i class="bi bi-pencil"></i>Edit</a>
            <div class="dropdown-divider"></div>
            <a class="dropdown-item text-danger" href="#" onclick="deleteCustomer(event,${c.id},'${(c.name||'').replace(/'/g,"\\'")}')"><i class="bi bi-trash"></i>Delete</a>
          </div>
        </div>
      </td>
    </tr>`;
  }).join('') || `<tr><td colspan="7"><div class="empty-state"><div class="es-icon"><i class="bi bi-search"></i></div><h6>No customers found</h6><p>Try adjusting your search or filters.</p></div></td></tr>`;
  KTG.makeResponsiveTable(document.getElementById('custTableBody').closest('table'));
}

function renderGrid(list){
  document.getElementById('gridView').innerHTML = list.map(c => {
    const cc = companyCustomerLabel(c);
    return `
    <div class="col-sm-6 col-lg-4 col-xxl-3">
      <div class="section-card h-100 hover-lift p-3">
        <div class="d-flex align-items-start justify-content-between mb-2">
          <div class="avatar-lg" style="background:${avatarColor(c.name)}">${initials(c.name)}</div>
          <span class="chip chip-${statusChip(c.status)}">${c.status}</span>
        </div>
        <a href="customer-details.php?id=${c.id}" class="cell-title d-block fs-15 mb-1">${cc.title}</a>
        <div class="cell-sub mb-3">${cc.sub}</div>
        <div class="fs-12 text-secondary mb-1"><i class="bi bi-telephone me-2 text-muted"></i>${c.mobile}</div>
        <div class="fs-12 text-secondary mb-1"><i class="bi bi-envelope me-2 text-muted"></i>${c.email || '—'}</div>
        <div class="fs-12 text-secondary mb-3"><i class="bi bi-geo-alt me-2 text-muted"></i>${c.city || '—'}</div>
        ${c.project_type_name ? `<span class="chip chip-inprogress">${c.project_type_name}</span>` : ''}
        <hr>
        <div class="d-flex gap-2">
          <a href="customer-details.php?id=${c.id}" class="btn btn-sm btn-light flex-fill">View</a>
          <button class="btn btn-sm btn-soft-primary flex-fill" onclick="openCustomerModal(${c.id})">Edit</button>
        </div>
      </div>
    </div>`;
  }).join('');
}

function openCustomerModal(id){
  resetCustomerForm();
  if (id) {
    $.get('ajax/customers.php', { action: 'view', id }).done((res) => {
      if (!res.success) { KTG.toast(res.message || 'Could not load customer.', 'danger', 'Error'); return; }
      const c = res.data;
      document.getElementById('customerModalTitle').innerHTML = '<i class="bi bi-pencil-square text-primary-KTG me-2"></i>Edit Customer';
      document.getElementById('cmId').value = c.id;
      document.getElementById('cmName').value = c.name || '';
      setCompanyRows(c.companies && c.companies.length ? c.companies : (c.company_name ? [c.company_name] : []));
      document.getElementById('cmMobile').value = c.mobile || '';
      document.getElementById('cmWhatsapp').value = c.whatsapp || '';
      document.getElementById('cmEmail').value = c.email || '';
      document.getElementById('cmGst').value = c.gst_number || '';
      document.getElementById('cmCity').value = c.city || '';
      document.getElementById('cmAddress').value = c.address || '';
      document.getElementById('cmStatus').value = c.status || 'pending';
      document.getElementById('cmNotes').value = c.notes || '';
      document.getElementById('modalProjectType').value = c.project_type_id || '';
      new bootstrap.Modal(document.getElementById('addCustomerModal')).show();
    });
  } else {
    document.getElementById('customerModalTitle').innerHTML = '<i class="bi bi-person-plus-fill text-primary-KTG me-2"></i>Add New Customer';
    new bootstrap.Modal(document.getElementById('addCustomerModal')).show();
  }
}

function resetCustomerForm(){
  document.getElementById('addCustomerForm').reset();
  document.getElementById('cmId').value = '';
  setCompanyRows([]);
  document.getElementById('customerFormError').classList.add('d-none');
}

function saveCustomer(){
  const id = document.getElementById('cmId').value;
  const isEdit = !!id;
  const payload = {
    action: isEdit ? 'edit' : 'add',
    id,
    name: document.getElementById('cmName').value.trim(),
    companies: JSON.stringify(getCompanyValues()),
    mobile: document.getElementById('cmMobile').value.trim(),
    whatsapp: document.getElementById('cmWhatsapp').value.trim(),
    email: document.getElementById('cmEmail').value.trim(),
    gst_number: document.getElementById('cmGst').value.trim(),
    city: document.getElementById('cmCity').value.trim(),
    address: document.getElementById('cmAddress').value.trim(),
    project_type_id: document.getElementById('modalProjectType').value,
    status: document.getElementById('cmStatus').value,
    notes: document.getElementById('cmNotes').value.trim()
  };
  const $btn = $('#saveCustomerBtn').prop('disabled', true);
  const $err = $('#customerFormError').addClass('d-none');

  $.post('ajax/customers.php', payload).done((res) => {
    if (res.success) {
      bootstrap.Modal.getInstance(document.getElementById('addCustomerModal')).hide();
      KTG.toast(isEdit ? 'Customer has been updated.' : 'Customer has been added successfully.', 'success', isEdit ? 'Customer Updated' : 'Customer Added');
      loadStats();
      loadCustomers();
    } else {
      $err.text(res.message || 'Could not save customer.').removeClass('d-none');
    }
  }).fail(() => {
    $err.text('Something went wrong. Please try again.').removeClass('d-none');
  }).always(() => $btn.prop('disabled', false));
}

function deleteCustomer(e, id, name){
  e.preventDefault();
  KTG.confirmDialog({
    title: 'Delete Customer?',
    body: `This will remove ${name} (including all their companies) from the customer list.`,
    confirmText: 'Delete', confirmClass: 'btn-danger',
    onConfirm: () => {
      $.post('ajax/customers.php', { action: 'delete', id }).done((res) => {
        if (res.success) {
          KTG.toast(`${name} has been deleted.`, 'danger', 'Customer Deleted');
          loadStats();
          loadCustomers();
        } else {
          KTG.toast(res.message || 'Could not delete customer.', 'danger', 'Error');
        }
      });
    }
  });
}
JS;
?>
<?php include __DIR__ . '/components/footer.php'; ?>
