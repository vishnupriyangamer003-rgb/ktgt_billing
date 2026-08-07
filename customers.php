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
          <button class="btn btn-light"><i class="bi bi-upload me-1"></i>Import</button>
          <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addCustomerModal"><i class="bi bi-plus-lg me-1"></i>Add Customer</button>
        </div>
      </div>

      <!-- Mini stats -->
      <div class="row g-3 mb-3 stagger">
        <div class="col-6 col-md-3">
          <div class="stat-card"><div class="stat-icon bg-soft-primary"><i class="bi bi-people-fill"></i></div><div class="stat-label">Total Customers</div><div class="stat-value">128</div></div>
        </div>
        <div class="col-6 col-md-3">
          <div class="stat-card"><div class="stat-icon bg-soft-success"><i class="bi bi-person-check-fill"></i></div><div class="stat-label">Active</div><div class="stat-value">104</div></div>
        </div>
        <div class="col-6 col-md-3">
          <div class="stat-card"><div class="stat-icon bg-soft-warning"><i class="bi bi-person-exclamation"></i></div><div class="stat-label">Pending Onboard</div><div class="stat-value">16</div></div>
        </div>
        <div class="col-6 col-md-3">
          <div class="stat-card"><div class="stat-icon bg-soft-danger"><i class="bi bi-person-dash-fill"></i></div><div class="stat-label">Inactive</div><div class="stat-value">8</div></div>
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
            <button class="btn btn-light"><i class="bi bi-funnel me-1"></i>More Filters</button>
            <div class="ms-auto btn-group" role="group">
              <button class="btn btn-soft-primary" id="viewTableBtn"><i class="bi bi-list-ul"></i></button>
              <button class="btn btn-light" id="viewGridBtn"><i class="bi bi-grid-3x3-gap"></i></button>
            </div>
          </div>
        </div>
      </div>

      <!-- Table View -->
      <div class="section-card fade-in-up" id="tableView">
        <div class="table-responsive">
          <table class="table table-modern mb-0">
            <thead>
              <tr>
                <th><input type="checkbox" class="form-check-input"></th>
                <th>Customer</th><th>Contact</th><th>Project Type</th><th>GST Number</th><th>Status</th><th>Created</th><th></th>
              </tr>
            </thead>
            <tbody id="custTableBody"></tbody>
          </table>
        </div>
        <div class="d-flex justify-content-between align-items-center p-3 border-top">
          <span class="fs-12 text-muted">Showing <b id="custCount">8</b> of 128 customers</span>
          <nav><ul class="pagination mb-0">
            <li class="page-item"><a class="page-link" href="#">Prev</a></li>
            <li class="page-item active"><a class="page-link" href="#">1</a></li>
            <li class="page-item"><a class="page-link" href="#">2</a></li>
            <li class="page-item"><a class="page-link" href="#">3</a></li>
            <li class="page-item"><a class="page-link" href="#">Next</a></li>
          </ul></nav>
        </div>
      </div>

      <!-- Grid View -->
      <div class="row g-3 d-none" id="gridView"></div>

<?php
$pageModals = <<<'HTML'
<!-- Add Customer Modal -->
<div class="modal fade" id="addCustomerModal" tabindex="-1">
  <div class="modal-dialog modal-lg modal-dialog-scrollable">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title"><i class="bi bi-person-plus-fill text-primary-KTG me-2"></i>Add New Customer</h5>
        <button class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <form id="addCustomerForm">
          <div class="divider-label">Basic Details</div>
          <div class="row g-3">
            <div class="col-md-6"><label class="form-label">Customer Name <span class="req">*</span></label><input class="form-control" placeholder="e.g. Arun Kumar" required></div>
            <div class="col-md-6"><label class="form-label">Company Name</label><input class="form-control" placeholder="e.g. Zen Foods Pvt Ltd"></div>
            <div class="col-md-6"><label class="form-label">Mobile Number <span class="req">*</span></label><input class="form-control" placeholder="+91 98765 43210" required></div>
            <div class="col-md-6"><label class="form-label">WhatsApp Number</label><input class="form-control" placeholder="+91 98765 43210"></div>
            <div class="col-md-6"><label class="form-label">Email Address</label><input type="email" class="form-control" placeholder="name@company.com"></div>
            <div class="col-md-6"><label class="form-label">GST Number</label><input class="form-control" placeholder="33ABCDE1234F1Z5"></div>
            <div class="col-12"><label class="form-label">Address</label><textarea class="form-control" rows="2" placeholder="Full billing address"></textarea></div>
          </div>

          <div class="divider-label">Project Type</div>
          <div class="row g-3">
            <div class="col-md-6">
              <label class="form-label">Select Type</label>
              <select class="form-select" id="modalProjectType"></select>
            </div>
            <div class="col-md-6"><label class="form-label">Status</label>
              <select class="form-select">
                <option>Active</option><option>Pending</option><option>Inactive</option>
              </select>
            </div>
          </div>
          <div class="alert alert-primary d-flex gap-2 mt-3 mb-0" style="background:var(--primary-light); border:1px solid var(--primary-100); border-radius:12px;">
            <i class="bi bi-info-circle-fill fs-5"></i>
            <div class="fs-12">Domain/App link, hosting server, and login credential fields unlock automatically on the customer profile once the project is marked <b>Completed</b>.</div>
          </div>

          <div class="divider-label">Notes</div>
          <textarea class="form-control" rows="2" placeholder="Internal notes about this customer"></textarea>
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
let currentCustomers = [];
document.addEventListener('DOMContentLoaded', () => setTimeout(init, 150));

function init(){
  currentCustomers = KTG.DATA.customers;
  renderTable(currentCustomers);
  renderGrid(currentCustomers);

  const types = KTG.Masters.projectTypes();
  document.getElementById('modalProjectType').innerHTML = types.map(t => `<option value="${t}">${t}</option>`).join('');
  document.getElementById('filterType').innerHTML = '<option value="">All Project Types</option>' + types.map(t => `<option value="${t}">${t}</option>`).join('');

  document.getElementById('custSearch').addEventListener('input', applyFilters);
  document.getElementById('filterStatus').addEventListener('change', applyFilters);
  document.getElementById('filterType').addEventListener('change', applyFilters);

  document.getElementById('viewTableBtn').addEventListener('click', () => toggleView('table'));
  document.getElementById('viewGridBtn').addEventListener('click', () => toggleView('grid'));

  document.getElementById('saveCustomerBtn').addEventListener('click', () => {
    bootstrap.Modal.getInstance(document.getElementById('addCustomerModal')).hide();
    KTG.toast('Customer has been added successfully.', 'success', 'Customer Added');
    document.getElementById('addCustomerForm').reset();
  });
}

function toggleView(mode){
  const tableBtn = document.getElementById('viewTableBtn'), gridBtn = document.getElementById('viewGridBtn');
  document.getElementById('tableView').classList.toggle('d-none', mode !== 'table');
  document.getElementById('gridView').classList.toggle('d-none', mode !== 'grid');
  tableBtn.className = 'btn ' + (mode === 'table' ? 'btn-soft-primary' : 'btn-light');
  gridBtn.className = 'btn ' + (mode === 'grid' ? 'btn-soft-primary' : 'btn-light');
}

function applyFilters(){
  const q = document.getElementById('custSearch').value.toLowerCase();
  const status = document.getElementById('filterStatus').value;
  const type = document.getElementById('filterType').value;
  const filtered = KTG.DATA.customers.filter(c => {
    const matchQ = !q || c.name.toLowerCase().includes(q) || c.company.toLowerCase().includes(q) || c.mobile.includes(q) || c.email.toLowerCase().includes(q);
    const matchStatus = !status || c.status === status;
    const matchType = !type || c.type === type;
    return matchQ && matchStatus && matchType;
  });
  currentCustomers = filtered;
  renderTable(filtered);
  renderGrid(filtered);
}

function avatarColor(name){
  const colors = ['#2563eb','#7c3aed','#0891b2','#16a34a','#d97706','#dc2626'];
  return colors[name.charCodeAt(0) % colors.length];
}
function initials(name){ return name.split(' ').map(w=>w[0]).slice(0,2).join('').toUpperCase(); }

function renderTable(list){
  document.getElementById('custCount').textContent = list.length;
  document.getElementById('custTableBody').innerHTML = list.map(c => `
    <tr>
      <td><input type="checkbox" class="form-check-input"></td>
      <td>
        <div class="d-flex align-items-center gap-3">
          <div class="avatar-sm" style="background:${avatarColor(c.name)}">${initials(c.name)}</div>
          <div>
            <a href="customer-details.php" class="cell-title d-block">${c.name}</a>
            <div class="cell-sub">${c.city}</div>
          </div>
        </div>
      </td>
      <td><div>${c.mobile}</div><div class="cell-sub">${c.email}</div></td>
      <td><span class="chip chip-inprogress">${c.type}</span></td>
      <td class="fs-13">${c.gst}</td>
      <td><span class="chip chip-${c.status === 'active' ? 'active' : c.status === 'pending' ? 'pending' : 'cancelled'}">${c.status}</span></td>
      <td class="fs-13">${c.created}</td>
      <td>
        <div class="dropdown">
          <button class="btn btn-sm btn-light" data-bs-toggle="dropdown"><i class="bi bi-three-dots-vertical"></i></button>
          <div class="dropdown-menu dropdown-menu-end">
            <a class="dropdown-item" href="customer-details.php"><i class="bi bi-eye"></i>View Profile</a>
            <a class="dropdown-item" href="quotation-create.php"><i class="bi bi-file-earmark-plus"></i>New Quotation</a>
            <a class="dropdown-item" href="#"><i class="bi bi-pencil"></i>Edit</a>
            <div class="dropdown-divider"></div>
            <a class="dropdown-item text-danger" href="#" onclick="deleteCustomer(event,'${c.name}')"><i class="bi bi-trash"></i>Delete</a>
          </div>
        </div>
      </td>
    </tr>`).join('') || `<tr><td colspan="8"><div class="empty-state"><div class="es-icon"><i class="bi bi-search"></i></div><h6>No customers found</h6><p>Try adjusting your search or filters.</p></div></td></tr>`;
  KTG.makeResponsiveTable(document.getElementById('custTableBody').closest('table'));
}

function renderGrid(list){
  document.getElementById('gridView').innerHTML = list.map(c => `
    <div class="col-sm-6 col-lg-4 col-xxl-3">
      <div class="section-card h-100 hover-lift p-3">
        <div class="d-flex align-items-start justify-content-between mb-2">
          <div class="avatar-lg" style="background:${avatarColor(c.name)}">${initials(c.name)}</div>
          <span class="chip chip-${c.status === 'active' ? 'active' : c.status === 'pending' ? 'pending' : 'cancelled'}">${c.status}</span>
        </div>
        <a href="customer-details.php" class="cell-title d-block fs-15 mb-1">${c.name}</a>
        <div class="cell-sub mb-3">${c.company}</div>
        <div class="fs-12 text-secondary mb-1"><i class="bi bi-telephone me-2 text-muted"></i>${c.mobile}</div>
        <div class="fs-12 text-secondary mb-1"><i class="bi bi-envelope me-2 text-muted"></i>${c.email}</div>
        <div class="fs-12 text-secondary mb-3"><i class="bi bi-geo-alt me-2 text-muted"></i>${c.city}</div>
        <span class="chip chip-inprogress">${c.type}</span>
        <hr>
        <div class="d-flex gap-2">
          <a href="customer-details.php" class="btn btn-sm btn-light flex-fill">View</a>
          <a href="quotation-create.php" class="btn btn-sm btn-soft-primary flex-fill">Quote</a>
        </div>
      </div>
    </div>`).join('');
}

function deleteCustomer(e, name){
  e.preventDefault();
  KTG.confirmDialog({
    title: 'Delete Customer?',
    body: `This will permanently remove ${name} and all associated records.`,
    confirmText: 'Delete', confirmClass: 'btn-danger',
    onConfirm: () => KTG.toast(`${name} has been deleted.`, 'danger', 'Customer Deleted')
  });
}
JS;
?>
<?php include __DIR__ . '/components/footer.php'; ?>
