<?php
$pageTitle = "Zen Foods Pvt Ltd";
$pageKey = "customers";
include __DIR__ . '/components/header.php';
?>
      <div class="page-head fade-in-up">
        <div>
          <h1>Customer Profile</h1>
          <nav class="breadcrumb"><span class="breadcrumb-item"><a href="index.php">Dashboard</a></span><span class="breadcrumb-item"><a href="customers.php">Customers</a></span><span class="breadcrumb-item active">Zen Foods Pvt Ltd</span></nav>
        </div>
        <div class="d-flex gap-2">
          <button class="btn btn-light"><i class="bi bi-pencil me-1"></i>Edit Profile</button>
          <a href="quotation-create.php" class="btn btn-primary"><i class="bi bi-file-earmark-plus me-1"></i>New Quotation</a>
        </div>
      </div>

      <div class="row g-3">
        <!-- Left profile card -->
        <div class="col-xl-4">
          <div class="section-card fade-in-up mb-3">
            <div class="section-body text-center">
              <div class="avatar-lg mx-auto mb-3" style="width:84px;height:84px;font-size:26px;background:#2563eb;">ZF</div>
              <h5 class="mb-0">Zen Foods Pvt Ltd</h5>
              <div class="text-muted fs-13 mb-2">Arun Kumar · Owner</div>
              <span class="chip chip-active mb-3"><i class="bi"></i>Active Customer</span>
              <div class="d-flex justify-content-center gap-2 mb-3">
                <a href="tel:9876543210" class="btn btn-icon btn-light"><i class="bi bi-telephone-fill"></i></a>
                <a href="https://wa.me/919876543210" class="btn btn-icon btn-light"><i class="bi bi-whatsapp"></i></a>
                <a href="mailto:arun@zenfoods.com" class="btn btn-icon btn-light"><i class="bi bi-envelope-fill"></i></a>
              </div>
              <hr>
              <div class="text-start">
                <div class="d-flex justify-content-between py-2 border-bottom"><span class="fs-12 text-muted">Mobile</span><span class="fs-13 fw-600">98765 43210</span></div>
                <div class="d-flex justify-content-between py-2 border-bottom"><span class="fs-12 text-muted">WhatsApp</span><span class="fs-13 fw-600">98765 43210</span></div>
                <div class="d-flex justify-content-between py-2 border-bottom"><span class="fs-12 text-muted">Email</span><span class="fs-13 fw-600">arun@zenfoods.com</span></div>
                <div class="d-flex justify-content-between py-2 border-bottom"><span class="fs-12 text-muted">GST No.</span><span class="fs-13 fw-600">33ZENFD1234F1Z2</span></div>
                <div class="d-flex justify-content-between py-2 border-bottom"><span class="fs-12 text-muted">Created</span><span class="fs-13 fw-600">14 Jan 2026</span></div>
                <div class="d-flex justify-content-between py-2"><span class="fs-12 text-muted">Address</span><span class="fs-13 fw-600 text-end">12 Anna Nagar,<br>Karur, TN</span></div>
              </div>
            </div>
          </div>

          <div class="section-card fade-in-up">
            <div class="section-head"><h5>Notes</h5></div>
            <div class="section-body">
              <p class="fs-13 text-secondary mb-2">Prefers WhatsApp communication over email. Decision maker is Arun Kumar; finance approvals go via Priya (Accounts).</p>
              <p class="fs-13 text-secondary mb-0">Referred by Bright Retail Solutions — good long-term relationship, low payment risk.</p>
            </div>
          </div>
        </div>

        <!-- Right content -->
        <div class="col-xl-8">
          <!-- Quick stats -->
          <div class="row g-3 mb-3">
            <div class="col-4">
              <div class="stat-card py-3"><div class="stat-label">Total Projects</div><div class="stat-value fs-4">3</div></div>
            </div>
            <div class="col-4">
              <div class="stat-card py-3"><div class="stat-label">Total Invoiced</div><div class="stat-value fs-4">₹6.1L</div></div>
            </div>
            <div class="col-4">
              <div class="stat-card py-3"><div class="stat-label">Outstanding</div><div class="stat-value fs-4 text-danger">₹0</div></div>
            </div>
          </div>

          <div class="section-card fade-in-up">
            <div class="section-head p-0 px-2 pt-2">
              <ul class="nav nav-tabs border-0" role="tablist">
                <li class="nav-item"><button class="nav-link active" data-bs-toggle="tab" data-bs-target="#tabProject">Project Details</button></li>
                <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#tabQuotes">Quotations</button></li>
                <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#tabInvoices">Invoices</button></li>
                <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#tabDocs">Documents</button></li>
                <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#tabActivity">Activity</button></li>
              </ul>
            </div>
            <div class="section-body">
              <div class="tab-content">

                <!-- Project Details tab -->
                <div class="tab-pane fade show active" id="tabProject">
                  <div class="d-flex align-items-center justify-content-between mb-3">
                    <div>
                      <span class="chip chip-completed mb-2"><i class="bi"></i>Project Completed</span>
                      <h6 class="mb-0">Corporate Website Revamp</h6>
                    </div>
                    <span class="chip chip-inprogress">Website</span>
                  </div>
                  <p class="fs-13 text-secondary">Since the project type <b>Website</b> is marked complete, deployment &amp; access details are recorded below.</p>

                  <div class="row g-3">
                    <div class="col-md-6">
                      <label class="form-label">Domain Name</label>
                      <div class="input-group"><span class="input-group-text"><i class="bi bi-globe2"></i></span><input class="form-control" value="www.zenfoods.com" readonly></div>
                    </div>
                    <div class="col-md-6">
                      <label class="form-label">Live Link</label>
                      <div class="input-group"><span class="input-group-text"><i class="bi bi-link-45deg"></i></span><input class="form-control" value="https://zenfoods.com" readonly></div>
                    </div>
                    <div class="col-md-6">
                      <label class="form-label">Registered Date</label>
                      <input class="form-control" value="18 Jan 2026" readonly>
                    </div>
                    <div class="col-md-6">
                      <label class="form-label">Expiry Date</label>
                      <input class="form-control text-danger fw-600" value="18 Jan 2027" readonly>
                    </div>
                    <div class="col-md-6">
                      <label class="form-label">Hosted On</label>
                      <div class="input-group"><span class="input-group-text"><i class="bi bi-hdd-network"></i></span><input class="form-control" value="Hostinger — Business Plan" readonly></div>
                    </div>
                    <div class="col-md-6">
                      <label class="form-label">Server IP / Panel</label>
                      <input class="form-control" value="cpanel.zenfoods.com" readonly>
                    </div>
                    <div class="col-12">
                      <div class="section-card p-3" style="background:var(--surface-alt);">
                        <div class="d-flex align-items-center justify-content-between mb-2">
                          <span class="fs-12 fw-700 text-uppercase text-muted"><i class="bi bi-shield-lock-fill me-1"></i>Admin Login Credentials</span>
                          <button class="btn btn-sm btn-light" onclick="toggleCreds()"><i class="bi bi-eye" id="credsIcon"></i> <span id="credsLabel">Show</span></button>
                        </div>
                        <div class="row g-2">
                          <div class="col-md-6"><label class="form-label mb-1">Admin URL</label><input class="form-control form-control-sm" value="zenfoods.com/wp-admin" readonly></div>
                          <div class="col-md-3"><label class="form-label mb-1">Username</label><input class="form-control form-control-sm" value="zenfoods_admin" readonly></div>
                          <div class="col-md-3"><label class="form-label mb-1">Password</label><input class="form-control form-control-sm creds-pass" type="password" value="••••••••••" readonly></div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>

                <!-- Quotations tab -->
                <div class="tab-pane fade" id="tabQuotes">
                  <div class="table-responsive-wrap">
                    <table class="table table-modern mb-0">
                      <thead><tr><th>Quotation #</th><th>Project</th><th>Date</th><th>Amount</th><th>Status</th><th></th></tr></thead>
                      <tbody>
                        <tr><td class="cell-title">QUO-2026-0091</td><td>Corporate Website Revamp</td><td>20 Jul 2026</td><td class="fw-600">₹1,85,000</td><td><span class="chip chip-approved">Approved</span></td><td><a href="quotation-create.php" class="btn btn-sm btn-light">View</a></td></tr>
                        <tr><td class="cell-title">QUO-2025-0134</td><td>Logo &amp; Branding Kit</td><td>02 Nov 2025</td><td class="fw-600">₹35,000</td><td><span class="chip chip-approved">Approved</span></td><td><a href="quotation-create.php" class="btn btn-sm btn-light">View</a></td></tr>
                      </tbody>
                    </table>
                  </div>
                </div>

                <!-- Invoices tab -->
                <div class="tab-pane fade" id="tabInvoices">
                  <div class="table-responsive-wrap">
                    <table class="table table-modern mb-0">
                      <thead><tr><th>Invoice #</th><th>Date</th><th>Amount</th><th>Status</th><th></th></tr></thead>
                      <tbody>
                        <tr><td class="cell-title">INV-2026-0150</td><td>25 Jul 2026</td><td class="fw-600">₹1,85,000</td><td><span class="chip chip-paid">Paid</span></td><td><a href="invoice-create.php" class="btn btn-sm btn-light">View</a></td></tr>
                        <tr><td class="cell-title">INV-2025-0298</td><td>05 Nov 2025</td><td class="fw-600">₹35,000</td><td><span class="chip chip-paid">Paid</span></td><td><a href="invoice-create.php" class="btn btn-sm btn-light">View</a></td></tr>
                      </tbody>
                    </table>
                  </div>
                </div>

                <!-- Documents tab -->
                <div class="tab-pane fade" id="tabDocs">
                  <div class="row g-3">
                    <div class="col-6 col-md-3"><div class="doc-thumb mb-2"><i class="bi bi-file-earmark-pdf"></i></div><div class="fs-12 fw-600">GST_Certificate.pdf</div><div class="fs-11 text-muted">220 KB</div></div>
                    <div class="col-6 col-md-3"><div class="doc-thumb mb-2"><i class="bi bi-file-earmark-word"></i></div><div class="fs-12 fw-600">Agreement.docx</div><div class="fs-11 text-muted">88 KB</div></div>
                    <div class="col-6 col-md-3"><div class="doc-thumb mb-2"><i class="bi bi-file-earmark-image"></i></div><div class="fs-12 fw-600">PAN_Card.jpg</div><div class="fs-11 text-muted">1.1 MB</div></div>
                    <div class="col-6 col-md-3"><div class="doc-thumb mb-2 text-primary-KTG" style="cursor:pointer;"><i class="bi bi-cloud-upload"></i></div><div class="fs-12 fw-600">Upload New</div></div>
                  </div>
                </div>

                <!-- Activity tab -->
                <div class="tab-pane fade" id="tabActivity">
                  <div class="timeline">
                    <div class="timeline-item"><div class="t-dot" style="color:var(--success)"><i class="bi bi-check-lg"></i></div><div class="t-time">25 Jul 2026, 3:40 PM</div><div class="t-title">Invoice INV-2026-0150 marked Paid</div></div>
                    <div class="timeline-item"><div class="t-dot" style="color:var(--primary)"><i class="bi bi-send"></i></div><div class="t-time">21 Jul 2026, 11:20 AM</div><div class="t-title">Invoice INV-2026-0150 sent via email</div></div>
                    <div class="timeline-item"><div class="t-dot" style="color:var(--success)"><i class="bi bi-check-lg"></i></div><div class="t-time">20 Jul 2026, 4:05 PM</div><div class="t-title">Quotation QUO-2026-0091 approved</div></div>
                    <div class="timeline-item"><div class="t-dot" style="color:var(--info)"><i class="bi bi-eye"></i></div><div class="t-time">19 Jul 2026, 9:12 AM</div><div class="t-title">Quotation QUO-2026-0091 viewed by customer</div></div>
                    <div class="timeline-item"><div class="t-dot" style="color:var(--purple)"><i class="bi bi-person-plus"></i></div><div class="t-time">14 Jan 2026, 10:00 AM</div><div class="t-title">Customer profile created</div></div>
                  </div>
                </div>

              </div>
            </div>
          </div>
        </div>
      </div>

<?php
$pageScript = <<<'JS'
document.addEventListener('DOMContentLoaded', () => setTimeout(() => {
  KTG.makeResponsiveTable(document.querySelector('#tabQuotes table'));
  KTG.makeResponsiveTable(document.querySelector('#tabInvoices table'));
}, 150));

function toggleCreds(){
  const field = document.querySelector('.creds-pass');
  const icon = document.getElementById('credsIcon');
  const label = document.getElementById('credsLabel');
  if(field.type === 'password'){ field.type = 'text'; field.value = 'Zf@2026#Secure'; icon.className='bi bi-eye-slash'; label.textContent='Hide'; }
  else { field.type = 'password'; field.value = '••••••••••'; icon.className='bi bi-eye'; label.textContent='Show'; }
}
JS;
?>
<?php include __DIR__ . '/components/footer.php'; ?>
