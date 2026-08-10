<?php
$pageTitle = "Settings";
$pageKey = "settings";
$extraHead = <<<'HTML'
<style>
.settings-nav .nav-link{ display:flex; align-items:center; gap:.7rem; padding:.7rem .9rem; border-radius:11px; color:var(--text-secondary); font-weight:600; font-size:13.3px; }
.settings-nav .nav-link.active{ background: var(--primary-light); color: var(--primary); }
</style>
HTML;
include __DIR__ . '/components/header.php';
?>
      <div class="page-head fade-in-up">
        <div>
          <h1>Settings</h1>
          <nav class="breadcrumb"><span class="breadcrumb-item"><a href="index.php">Dashboard</a></span><span class="breadcrumb-item active">Settings</span></nav>
        </div>
        <button class="btn btn-primary"><i class="bi bi-check-lg me-1"></i>Save Changes</button>
      </div>

      <div class="row g-3">
        <div class="col-xl-3">
          <div class="section-card fade-in-up">
            <div class="section-body p-2">
              <div class="nav flex-column settings-nav" role="tablist">
                <button class="nav-link active" data-bs-toggle="pill" data-bs-target="#tabCompany"><i class="bi bi-building"></i>Company Profile</button>
                <button class="nav-link" data-bs-toggle="pill" data-bs-target="#tabPrefix"><i class="bi bi-hash"></i>Prefixes</button>
                <button class="nav-link" data-bs-toggle="pill" data-bs-target="#tabModes"><i class="bi bi-credit-card"></i>Payment Modes</button>
                <button class="nav-link" data-bs-toggle="pill" data-bs-target="#tabUsers"><i class="bi bi-people"></i>Users</button>
                <button class="nav-link" data-bs-toggle="pill" data-bs-target="#tabRoles"><i class="bi bi-shield-lock"></i>Roles &amp; Permissions</button>
              </div>
            </div>
          </div>
        </div>

        <div class="col-xl-9">
          <div class="tab-content">

            <!-- Company profile -->
            <div class="tab-pane fade show active" id="tabCompany">
              <div class="section-card fade-in-up">
                <div class="section-head"><h5>Company Profile</h5><div class="subtitle">Shown on the invoice letterhead — kept separate per tax mode since a GST and a Non-GST bill can carry different company details</div></div>
                <div class="section-body pb-2">
                  <ul class="nav nav-pills-soft gap-2 mb-3" id="companyProfileTabs">
                    <li class="nav-item"><button class="nav-link active" data-bs-toggle="pill" data-bs-target="#tabCompanyGst"><i class="bi bi-receipt-cutoff me-1"></i>GST Invoices</button></li>
                    <li class="nav-item"><button class="nav-link" data-bs-toggle="pill" data-bs-target="#tabCompanyNonGst"><i class="bi bi-receipt me-1"></i>Non-GST Invoices</button></li>
                  </ul>
                </div>
                <div class="tab-content">
                  <div class="tab-pane fade show active" id="tabCompanyGst">
                    <div class="section-body pt-0">
                      <div class="row g-3">
                        <div class="col-md-6"><label class="form-label">Company Name</label><input class="form-control" id="cpGstName"></div>
                        <div class="col-md-6"><label class="form-label">Website</label><input class="form-control" id="cpGstWebsite"></div>
                        <div class="col-md-6"><label class="form-label">Phone</label><input class="form-control" id="cpGstPhone"></div>
                        <div class="col-md-6"><label class="form-label">Email</label><input class="form-control" id="cpGstEmail"></div>
                        <div class="col-12"><label class="form-label">Address</label><textarea class="form-control" rows="2" id="cpGstAddress"></textarea></div>
                        <div class="col-md-6"><label class="form-label">GSTIN</label><input class="form-control" id="cpGstGstin"></div>
                        <div class="col-md-6"><label class="form-label">PAN</label><input class="form-control" id="cpGstPan"></div>
                      </div>
                      <button class="btn btn-primary mt-3" id="saveCompanyGstBtn"><i class="bi bi-check-lg me-1"></i>Save GST Company Details</button>
                    </div>
                  </div>
                  <div class="tab-pane fade" id="tabCompanyNonGst">
                    <div class="section-body pt-0">
                      <div class="row g-3">
                        <div class="col-md-6"><label class="form-label">Company Name</label><input class="form-control" id="cpNonGstName"></div>
                        <div class="col-md-6"><label class="form-label">Website</label><input class="form-control" id="cpNonGstWebsite"></div>
                        <div class="col-md-6"><label class="form-label">Phone</label><input class="form-control" id="cpNonGstPhone"></div>
                        <div class="col-md-6"><label class="form-label">Email</label><input class="form-control" id="cpNonGstEmail"></div>
                        <div class="col-12"><label class="form-label">Address</label><textarea class="form-control" rows="2" id="cpNonGstAddress"></textarea></div>
                        <div class="col-md-6"><label class="form-label">GSTIN <span class="fs-11 text-muted fw-400">(leave blank to hide on the bill)</span></label><input class="form-control" id="cpNonGstGstin"></div>
                        <div class="col-md-6"><label class="form-label">PAN</label><input class="form-control" id="cpNonGstPan"></div>
                      </div>
                      <button class="btn btn-primary mt-3" id="saveCompanyNonGstBtn"><i class="bi bi-check-lg me-1"></i>Save Non-GST Company Details</button>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <!-- Prefixes -->
            <div class="tab-pane fade" id="tabPrefix">
              <div class="section-card fade-in-up">
                <div class="section-head"><h5>Document Prefixes</h5></div>
                <div class="section-body">
                  <div class="row g-3">
                    <div class="col-md-6"><label class="form-label">Quotation Prefix</label><input class="form-control" value="QUO-2026-"></div>
                    <div class="col-md-6"><label class="form-label">Next Quotation Number</label><input class="form-control" value="0092"></div>
                    <div class="col-md-6"><label class="form-label">Invoice Prefix</label><input class="form-control" value="INV-2026-"></div>
                    <div class="col-md-6"><label class="form-label">Next Invoice Number</label><input class="form-control" value="0151"></div>
                    <div class="col-md-6"><label class="form-label">Payment Prefix</label><input class="form-control" value="PAY-"></div>
                    <div class="col-md-6"><label class="form-label">Next Payment Number</label><input class="form-control" value="2002"></div>
                  </div>
                </div>
              </div>
            </div>

            <!-- Payment modes -->
            <div class="tab-pane fade" id="tabModes">
              <div class="section-card fade-in-up">
                <div class="section-head"><h5>Payment Modes</h5><button class="btn btn-sm btn-soft-primary"><i class="bi bi-plus-lg me-1"></i>Add Mode</button></div>
                <div class="section-body p-0">
                  <div class="list-group list-group-flush">
                    <div class="list-group-item px-3 py-3 d-flex align-items-center gap-3"><i class="bi bi-cash fs-5 text-success"></i><span class="fw-600 fs-13 flex-grow-1">Cash</span><div class="form-check form-switch"><input class="form-check-input" type="checkbox" checked></div></div>
                    <div class="list-group-item px-3 py-3 d-flex align-items-center gap-3"><i class="bi bi-phone fs-5 text-primary-KTG"></i><span class="fw-600 fs-13 flex-grow-1">UPI</span><div class="form-check form-switch"><input class="form-check-input" type="checkbox" checked></div></div>
                    <div class="list-group-item px-3 py-3 d-flex align-items-center gap-3"><i class="bi bi-bank fs-5 text-info"></i><span class="fw-600 fs-13 flex-grow-1">Bank Transfer</span><div class="form-check form-switch"><input class="form-check-input" type="checkbox" checked></div></div>
                    <div class="list-group-item px-3 py-3 d-flex align-items-center gap-3"><i class="bi bi-postcard fs-5 text-warning"></i><span class="fw-600 fs-13 flex-grow-1">Cheque</span><div class="form-check form-switch"><input class="form-check-input" type="checkbox" checked></div></div>
                    <div class="list-group-item px-3 py-3 d-flex align-items-center gap-3"><i class="bi bi-credit-card fs-5 text-purple" style="color:#7c3aed"></i><span class="fw-600 fs-13 flex-grow-1">Card</span><div class="form-check form-switch"><input class="form-check-input" type="checkbox" checked></div></div>
                  </div>
                </div>
              </div>
            </div>

            <!-- Users -->
            <div class="tab-pane fade" id="tabUsers">
              <div class="section-card fade-in-up">
                <div class="section-head"><h5>Users</h5><button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#addUserModal"><i class="bi bi-plus-lg me-1"></i>Add User</button></div>
                <div class="table-responsive">
                  <table class="table table-modern mb-0">
                    <thead><tr><th>User</th><th>Username</th><th>Email</th><th>Role</th><th>Status</th><th></th></tr></thead>
                    <tbody>
                      <tr><td><div class="d-flex align-items-center gap-2"><div class="avatar-sm" style="background:#2563eb">VK</div><span class="cell-title">Vicky</span></div></td><td class="fs-13">vicky.kumar</td><td>ktgkrr@gmail.com</td><td><span class="chip chip-inprogress">Administrator</span></td><td><span class="chip chip-active">Active</span></td><td><button class="btn btn-sm btn-light"><i class="bi bi-pencil"></i></button></td></tr>
                      <tr><td><div class="d-flex align-items-center gap-2"><div class="avatar-sm" style="background:#7c3aed">DN</div><span class="cell-title">Divya N</span></div></td><td class="fs-13">divya.n</td><td>divya@knocktheglobe.com</td><td><span class="chip chip-pending">Accountant</span></td><td><span class="chip chip-active">Active</span></td><td><button class="btn btn-sm btn-light"><i class="bi bi-pencil"></i></button></td></tr>
                      <tr><td><div class="d-flex align-items-center gap-2"><div class="avatar-sm" style="background:#0891b2">KR</div><span class="cell-title">Karthik R</span></div></td><td class="fs-13">karthik.r</td><td>karthik@knocktheglobe.com</td><td><span class="chip chip-viewed">Sales Executive</span></td><td><span class="chip chip-cancelled">Inactive</span></td><td><button class="btn btn-sm btn-light"><i class="bi bi-pencil"></i></button></td></tr>
                    </tbody>
                  </table>
                </div>
              </div>
            </div>

            <!-- Roles & Permissions — assigned per named user -->
            <div class="tab-pane fade" id="tabRoles">
              <div class="section-card fade-in-up">
                <div class="section-head"><h5>Roles &amp; Permissions</h5><div class="subtitle">Permissions are assigned to each user individually</div></div>
                <div class="section-body">
                  <div class="accordion" id="rolesAccordion">
                    <div class="accordion-item">
                      <h2 class="accordion-header"><button class="accordion-button" data-bs-toggle="collapse" data-bs-target="#roleVicky">
                        <div class="avatar-sm me-2" style="background:#2563eb">VK</div>Vicky <span class="chip chip-inprogress ms-2">Administrator</span>
                      </button></h2>
                      <div id="roleVicky" class="accordion-collapse collapse show" data-bs-parent="#rolesAccordion">
                        <div class="accordion-body">
                          <div class="row g-2">
                            <div class="col-6 col-md-4"><div class="form-check"><input class="form-check-input" type="checkbox" checked disabled><label class="form-check-label fs-13">Manage Customers</label></div></div>
                            <div class="col-6 col-md-4"><div class="form-check"><input class="form-check-input" type="checkbox" checked disabled><label class="form-check-label fs-13">Manage Quotations</label></div></div>
                            <div class="col-6 col-md-4"><div class="form-check"><input class="form-check-input" type="checkbox" checked disabled><label class="form-check-label fs-13">Manage Invoices</label></div></div>
                            <div class="col-6 col-md-4"><div class="form-check"><input class="form-check-input" type="checkbox" checked disabled><label class="form-check-label fs-13">Manage Payments</label></div></div>
                            <div class="col-6 col-md-4"><div class="form-check"><input class="form-check-input" type="checkbox" checked disabled><label class="form-check-label fs-13">View Reports</label></div></div>
                            <div class="col-6 col-md-4"><div class="form-check"><input class="form-check-input" type="checkbox" checked disabled><label class="form-check-label fs-13">Manage Users</label></div></div>
                          </div>
                        </div>
                      </div>
                    </div>
                    <div class="accordion-item">
                      <h2 class="accordion-header"><button class="accordion-button collapsed" data-bs-toggle="collapse" data-bs-target="#roleDivya">
                        <div class="avatar-sm me-2" style="background:#7c3aed">DN</div>Divya N <span class="chip chip-pending ms-2">Accountant</span>
                      </button></h2>
                      <div id="roleDivya" class="accordion-collapse collapse" data-bs-parent="#rolesAccordion">
                        <div class="accordion-body">
                          <div class="row g-2">
                            <div class="col-6 col-md-4"><div class="form-check"><input class="form-check-input" type="checkbox"><label class="form-check-label fs-13">Manage Customers</label></div></div>
                            <div class="col-6 col-md-4"><div class="form-check"><input class="form-check-input" type="checkbox"><label class="form-check-label fs-13">Manage Quotations</label></div></div>
                            <div class="col-6 col-md-4"><div class="form-check"><input class="form-check-input" type="checkbox" checked><label class="form-check-label fs-13">Manage Invoices</label></div></div>
                            <div class="col-6 col-md-4"><div class="form-check"><input class="form-check-input" type="checkbox" checked><label class="form-check-label fs-13">Manage Payments</label></div></div>
                            <div class="col-6 col-md-4"><div class="form-check"><input class="form-check-input" type="checkbox" checked><label class="form-check-label fs-13">View Reports</label></div></div>
                            <div class="col-6 col-md-4"><div class="form-check"><input class="form-check-input" type="checkbox"><label class="form-check-label fs-13">Manage Users</label></div></div>
                          </div>
                        </div>
                      </div>
                    </div>
                    <div class="accordion-item">
                      <h2 class="accordion-header"><button class="accordion-button collapsed" data-bs-toggle="collapse" data-bs-target="#roleKarthik">
                        <div class="avatar-sm me-2" style="background:#0891b2">KR</div>Karthik R <span class="chip chip-viewed ms-2">Sales Executive</span>
                      </button></h2>
                      <div id="roleKarthik" class="accordion-collapse collapse" data-bs-parent="#rolesAccordion">
                        <div class="accordion-body">
                          <div class="row g-2">
                            <div class="col-6 col-md-4"><div class="form-check"><input class="form-check-input" type="checkbox" checked><label class="form-check-label fs-13">Manage Customers</label></div></div>
                            <div class="col-6 col-md-4"><div class="form-check"><input class="form-check-input" type="checkbox" checked><label class="form-check-label fs-13">Manage Quotations</label></div></div>
                            <div class="col-6 col-md-4"><div class="form-check"><input class="form-check-input" type="checkbox"><label class="form-check-label fs-13">Manage Invoices</label></div></div>
                            <div class="col-6 col-md-4"><div class="form-check"><input class="form-check-input" type="checkbox"><label class="form-check-label fs-13">Manage Payments</label></div></div>
                            <div class="col-6 col-md-4"><div class="form-check"><input class="form-check-input" type="checkbox"><label class="form-check-label fs-13">View Reports</label></div></div>
                            <div class="col-6 col-md-4"><div class="form-check"><input class="form-check-input" type="checkbox"><label class="form-check-label fs-13">Manage Users</label></div></div>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>

          </div>
        </div>
      </div>

<?php
$pageModals = <<<'HTML'
<!-- Add User Modal -->
<div class="modal fade" id="addUserModal" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header"><h5 class="modal-title">Add User</h5><button class="btn-close" data-bs-dismiss="modal"></button></div>
      <div class="modal-body">
        <div class="row g-3">
          <div class="col-12"><label class="form-label">Full Name <span class="req">*</span></label><input class="form-control"></div>
          <div class="col-md-6"><label class="form-label">Username <span class="req">*</span></label><input class="form-control" placeholder="e.g. karthik.r"></div>
          <div class="col-md-6"><label class="form-label">Password <span class="req">*</span></label><input type="password" class="form-control" placeholder="Set a login password"></div>
          <div class="col-12"><label class="form-label">Email <span class="req">*</span></label><input type="email" class="form-control"></div>
          <div class="col-12"><label class="form-label">Role</label><select class="form-select"><option>Administrator</option><option>Accountant</option><option>Sales Executive</option></select></div>
        </div>
      </div>
      <div class="modal-footer"><button class="btn btn-light" data-bs-dismiss="modal">Cancel</button><button class="btn btn-primary" onclick="bootstrap.Modal.getInstance(document.getElementById('addUserModal')).hide(); KTG.toast('User has been created.','success','User Added');">Add User</button></div>
    </div>
  </div>
</div>
HTML;
$pageScript = <<<'JS'
document.addEventListener('DOMContentLoaded', () => setTimeout(() => {
  KTG.makeResponsiveTable(document.querySelector('#tabUsers table'));
  initCompanyProfileForms();
}, 150));

function fillCompanyForm(prefix, gstType){
  const c = KTG.CompanyProfile.get(gstType);
  document.getElementById(prefix + 'Name').value = c.name || '';
  document.getElementById(prefix + 'Website').value = c.website || '';
  document.getElementById(prefix + 'Phone').value = c.phone || '';
  document.getElementById(prefix + 'Email').value = c.email || '';
  document.getElementById(prefix + 'Address').value = c.address || '';
  document.getElementById(prefix + 'Gstin').value = c.gstin || '';
  document.getElementById(prefix + 'Pan').value = c.pan || '';
}
function saveCompanyForm(prefix, gstType){
  KTG.CompanyProfile.save(gstType, {
    name: document.getElementById(prefix + 'Name').value.trim(),
    website: document.getElementById(prefix + 'Website').value.trim(),
    phone: document.getElementById(prefix + 'Phone').value.trim(),
    email: document.getElementById(prefix + 'Email').value.trim(),
    address: document.getElementById(prefix + 'Address').value.trim(),
    gstin: document.getElementById(prefix + 'Gstin').value.trim(),
    pan: document.getElementById(prefix + 'Pan').value.trim()
  });
  KTG.toast(`${gstType === 'nongst' ? 'Non-GST' : 'GST'} company details saved.`, 'success', 'Saved');
}
function initCompanyProfileForms(){
  fillCompanyForm('cpGst', 'gst');
  fillCompanyForm('cpNonGst', 'nongst');
  document.getElementById('saveCompanyGstBtn').addEventListener('click', () => saveCompanyForm('cpGst', 'gst'));
  document.getElementById('saveCompanyNonGstBtn').addEventListener('click', () => saveCompanyForm('cpNonGst', 'nongst'));
}
JS;
?>
<?php include __DIR__ . '/components/footer.php'; ?>
