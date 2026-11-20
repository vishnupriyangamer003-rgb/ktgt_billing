<?php
$pageTitle = "Customer Profile";
$pageKey = "customers";
include __DIR__ . '/components/header.php';
?>
      <div class="page-head fade-in-up">
        <div>
          <h1>Customer Profile</h1>
          <nav class="breadcrumb"><span class="breadcrumb-item"><a href="index.php">Dashboard</a></span><span class="breadcrumb-item"><a href="customers.php">Customers</a></span><span class="breadcrumb-item active" id="bcName">&hellip;</span></nav>
        </div>
        <div class="d-flex gap-2 d-none" id="profileActions">
          <a href="#" class="btn btn-light" id="editProfileBtn"><i class="bi bi-pencil me-1"></i>Edit Profile</a>
          <a href="quotation-create.php" class="btn btn-primary"><i class="bi bi-file-earmark-plus me-1"></i>New Quotation</a>
        </div>
      </div>

      <div id="customerNotFound" class="section-card fade-in-up d-none">
        <div class="empty-state py-5"><div class="es-icon"><i class="bi bi-person-x"></i></div><h6>Customer not found</h6><p>It may have been deleted. <a href="customers.php">Back to Customers</a></p></div>
      </div>

      <div class="row g-3 d-none" id="profileContent">
        <!-- Left profile card -->
        <div class="col-xl-4">
          <div class="section-card fade-in-up mb-3">
            <div class="section-body text-center">
              <div class="avatar-lg mx-auto mb-3" id="cdAvatar" style="width:84px;height:84px;font-size:26px;">?</div>
              <h5 class="mb-0" id="cdName">&nbsp;</h5>
              <div class="text-muted fs-13 mb-2" id="cdSub">&nbsp;</div>
              <div class="d-flex flex-wrap justify-content-center gap-1 mb-2" id="cdCompanies"></div>
              <span class="chip mb-3" id="cdStatusChip"><i class="bi"></i>&nbsp;</span>
              <div class="d-flex justify-content-center gap-2 mb-3">
                <a href="#" id="cdCallLink" class="btn btn-icon btn-light"><i class="bi bi-telephone-fill"></i></a>
                <a href="#" id="cdWhatsappLink" class="btn btn-icon btn-light"><i class="bi bi-whatsapp"></i></a>
                <a href="#" id="cdEmailLink" class="btn btn-icon btn-light"><i class="bi bi-envelope-fill"></i></a>
              </div>
              <hr>
              <div class="text-start">
                <div class="d-flex justify-content-between py-2 border-bottom"><span class="fs-12 text-muted">Mobile</span><span class="fs-13 fw-600" id="cdMobile">—</span></div>
                <div class="d-flex justify-content-between py-2 border-bottom"><span class="fs-12 text-muted">WhatsApp</span><span class="fs-13 fw-600" id="cdWhatsapp">—</span></div>
                <div class="d-flex justify-content-between py-2 border-bottom"><span class="fs-12 text-muted">Email</span><span class="fs-13 fw-600" id="cdEmail">—</span></div>
                <div class="d-flex justify-content-between py-2 border-bottom"><span class="fs-12 text-muted">GST No.</span><span class="fs-13 fw-600" id="cdGst">—</span></div>
                <div class="d-flex justify-content-between py-2 border-bottom"><span class="fs-12 text-muted">Created</span><span class="fs-13 fw-600" id="cdCreated">—</span></div>
                <div class="d-flex justify-content-between py-2"><span class="fs-12 text-muted">Address</span><span class="fs-13 fw-600 text-end" id="cdAddress">—</span></div>
              </div>
            </div>
          </div>

          <div class="section-card fade-in-up">
            <div class="section-head"><h5>Notes</h5></div>
            <div class="section-body">
              <p class="fs-13 text-secondary mb-0" id="cdNotes">No notes added yet.</p>
            </div>
          </div>
        </div>

        <!-- Right content -->
        <div class="col-xl-8">
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

                <?php
                // Quotations, Invoices, Documents, and Activity all depend on modules that
                // aren't part of this build phase yet — an honest "not available yet" state
                // beats fabricating data for a module that doesn't exist.
                $notReady = function (string $icon, string $title, string $module) {
                    return '<div class="empty-state py-5"><div class="es-icon"><i class="bi ' . $icon . '"></i></div><h6>' . $title . '</h6><p>This will be available once the ' . $module . ' module is built.</p></div>';
                };
                ?>

                <!-- Project Details tab — Renewals module isn't built yet either -->
                <div class="tab-pane fade show active" id="tabProject">
                  <?php echo $notReady('bi-kanban', 'No project details yet', 'Renewals &amp; Projects'); ?>
                </div>

                <div class="tab-pane fade" id="tabQuotes"><?php echo $notReady('bi-file-earmark-text', 'No quotations yet', 'Quotations'); ?></div>
                <div class="tab-pane fade" id="tabInvoices"><?php echo $notReady('bi-receipt', 'No invoices yet', 'Invoices'); ?></div>
                <div class="tab-pane fade" id="tabDocs"><?php echo $notReady('bi-folder2-open', 'No documents yet', 'Documents'); ?></div>
                <div class="tab-pane fade" id="tabActivity"><?php echo $notReady('bi-clock-history', 'No activity yet', 'Activity Log'); ?></div>

              </div>
            </div>
          </div>
        </div>
      </div>

<?php
$pageScript = <<<'JS'
document.addEventListener('DOMContentLoaded', () => setTimeout(init, 150));

function avatarColor(name){
  const colors = ['#2563eb','#7c3aed','#0891b2','#16a34a','#d97706','#dc2626'];
  return colors[(name || '?').charCodeAt(0) % colors.length];
}
function initials(name){ return (name || '?').split(' ').map(w=>w[0]).slice(0,2).join('').toUpperCase(); }
function statusChip(status){ return status === 'active' ? 'active' : status === 'pending' ? 'pending' : 'cancelled'; }

function init(){
  const id = KTG.qs('id');
  if (!id) { showNotFound(); return; }

  $.get('ajax/customers.php', { action: 'view', id }).done((res) => {
    if (!res.success) { showNotFound(); return; }
    renderCustomer(res.data);
  }).fail(showNotFound);
}

function showNotFound(){
  document.getElementById('customerNotFound').classList.remove('d-none');
  document.getElementById('profileContent').classList.add('d-none');
}

function renderCustomer(c){
  document.title = c.name + ' · KTG Invoice Suite';
  document.getElementById('bcName').textContent = c.name;
  document.getElementById('profileActions').classList.remove('d-none');
  document.getElementById('profileContent').classList.remove('d-none');
  document.getElementById('editProfileBtn').href = 'customers.php?edit=' + c.id;

  const avatar = document.getElementById('cdAvatar');
  avatar.style.background = avatarColor(c.name);
  avatar.textContent = initials(c.name);

  document.getElementById('cdName').textContent = c.name;
  document.getElementById('cdSub').textContent = [c.contact_person, c.city].filter(Boolean).join(' · ') || '—';

  document.getElementById('cdCompanies').innerHTML = (c.companies || []).map(n => `<span class="chip chip-inprogress"><i class="bi bi-building me-1"></i>${KTG.escAttr(n)}</span>`).join('');

  const chip = document.getElementById('cdStatusChip');
  chip.className = 'chip chip-' + statusChip(c.status) + ' mb-3';
  chip.innerHTML = '<i class="bi"></i>' + c.status.charAt(0).toUpperCase() + c.status.slice(1) + ' Customer';

  const mobileDigits = (c.mobile || '').replace(/\D/g, '');
  document.getElementById('cdCallLink').href = mobileDigits ? 'tel:' + mobileDigits : '#';
  document.getElementById('cdWhatsappLink').href = mobileDigits ? 'https://wa.me/91' + mobileDigits : '#';
  document.getElementById('cdEmailLink').href = c.email ? 'mailto:' + c.email : '#';

  document.getElementById('cdMobile').textContent = c.mobile || '—';
  document.getElementById('cdWhatsapp').textContent = c.whatsapp || '—';
  document.getElementById('cdEmail').textContent = c.email || '—';
  document.getElementById('cdGst').textContent = c.gst_number || '—';
  document.getElementById('cdCreated').textContent = c.created_at ? c.created_at.slice(0, 10) : '—';
  document.getElementById('cdAddress').innerHTML = c.address ? c.address.replace(/\n/g, '<br>') : '—';
  document.getElementById('cdNotes').textContent = c.notes || 'No notes added yet.';
}
JS;
?>
<?php include __DIR__ . '/components/footer.php'; ?>
