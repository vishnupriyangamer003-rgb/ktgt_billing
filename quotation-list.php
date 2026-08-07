<?php
$pageTitle = "Quotations";
$pageKey = "quotations";
include __DIR__ . '/components/header.php';
?>
      <div class="page-head fade-in-up">
        <div>
          <h1>Quotations</h1>
          <nav class="breadcrumb"><span class="breadcrumb-item"><a href="index.php">Dashboard</a></span><span class="breadcrumb-item active">Quotations</span></nav>
        </div>
        <div class="d-flex gap-2">
          <div class="btn-group">
            <button class="btn btn-soft-primary" id="viewTableBtn"><i class="bi bi-list-ul"></i></button>
            <button class="btn btn-light" id="viewGridBtn"><i class="bi bi-grid-3x3-gap"></i></button>
          </div>
          <a href="quotation-create.php" class="btn btn-primary"><i class="bi bi-plus-lg me-1"></i>New Quotation</a>
        </div>
      </div>

      <!-- Status tabs -->
      <ul class="nav nav-pills-soft gap-2 mb-3 fade-in-up" id="statusTabs">
        <li class="nav-item"><button class="nav-link active" data-status="">All (72)</button></li>
        <li class="nav-item"><button class="nav-link" data-status="draft">Draft (6)</button></li>
        <li class="nav-item"><button class="nav-link" data-status="sent">Sent (22)</button></li>
        <li class="nav-item"><button class="nav-link" data-status="viewed">Viewed (10)</button></li>
        <li class="nav-item"><button class="nav-link" data-status="approved">Approved (46)</button></li>
        <li class="nav-item"><button class="nav-link" data-status="rejected">Rejected (8)</button></li>
        <li class="nav-item"><button class="nav-link" data-status="expired">Expired (6)</button></li>
      </ul>

      <div class="section-card mb-3 fade-in-up">
        <div class="section-body py-3">
          <div class="d-flex flex-wrap gap-2 align-items-center">
            <div class="position-relative flex-grow-1" style="min-width:220px; max-width:340px;">
              <i class="bi bi-search position-absolute" style="left:14px; top:50%; transform:translateY(-50%); color:var(--text-muted);"></i>
              <input type="text" id="quoSearch" class="form-control ps-5" placeholder="Search quotation #, customer...">
            </div>
            <input type="date" class="form-control" style="max-width:160px;">
            <select class="form-select" style="max-width:160px;"><option>All Amount</option><option>Below ₹1L</option><option>₹1L–₹5L</option><option>Above ₹5L</option></select>
            <button class="btn btn-light ms-auto"><i class="bi bi-filetype-xlsx me-1"></i>Excel</button>
            <button class="btn btn-light"><i class="bi bi-filetype-pdf me-1"></i>PDF</button>
            <button class="btn btn-light"><i class="bi bi-printer me-1"></i>Print</button>
          </div>
        </div>
      </div>

      <!-- Grid view -->
      <div id="gridView" class="row g-3 stagger fade-in-up d-none" style="margin-bottom:1rem;"></div>
      <div class="d-flex justify-content-between align-items-center d-none" id="gridFooter">
        <span class="fs-12 text-muted">Showing <b id="quoCountGrid">8</b> of 72 quotations</span>
      </div>

      <!-- Table view (default) -->
      <div class="section-card fade-in-up" id="tableView">
        <div class="table-responsive">
          <table class="table table-modern mb-0">
            <thead>
              <tr><th><input type="checkbox" class="form-check-input"></th><th>Quotation #</th><th>Customer</th><th>Date</th><th>Expiry</th><th>Amount</th><th>Status</th><th></th></tr>
            </thead>
            <tbody id="quoTableBody"></tbody>
          </table>
        </div>
        <div class="d-flex justify-content-between align-items-center p-3 border-top">
          <span class="fs-12 text-muted">Showing <b id="quoCount">8</b> of 72 quotations</span>
          <nav><ul class="pagination mb-0">
            <li class="page-item"><a class="page-link" href="#">Prev</a></li>
            <li class="page-item active"><a class="page-link" href="#">1</a></li>
            <li class="page-item"><a class="page-link" href="#">2</a></li>
            <li class="page-item"><a class="page-link" href="#">Next</a></li>
          </ul></nav>
        </div>
      </div>

<?php
$pageScript = <<<'JS'
document.addEventListener('DOMContentLoaded', () => setTimeout(init, 150));
let activeStatus = '';

const STATUS_ICON = {draft:'bi-file-earmark', sent:'bi-send', viewed:'bi-eye', approved:'bi-check-circle-fill', rejected:'bi-x-circle-fill', expired:'bi-hourglass-bottom'};
const STATUS_COLOR = {draft:'secondary', sent:'info', viewed:'purple', approved:'success', rejected:'danger', expired:'warning'};

function init(){
  render(KTG.DATA.quotations);
  document.getElementById('quoSearch').addEventListener('input', applyFilters);
  document.querySelectorAll('#statusTabs .nav-link').forEach(btn => {
    btn.addEventListener('click', () => {
      document.querySelectorAll('#statusTabs .nav-link').forEach(b => b.classList.remove('active'));
      btn.classList.add('active');
      activeStatus = btn.dataset.status;
      applyFilters();
    });
  });
  document.getElementById('viewGridBtn').addEventListener('click', () => toggleView('grid'));
  document.getElementById('viewTableBtn').addEventListener('click', () => toggleView('table'));
}

function toggleView(mode){
  document.getElementById('gridView').classList.toggle('d-none', mode !== 'grid');
  document.getElementById('gridFooter').classList.toggle('d-none', mode !== 'grid');
  document.getElementById('tableView').classList.toggle('d-none', mode !== 'table');
  document.getElementById('viewGridBtn').className = 'btn ' + (mode === 'grid' ? 'btn-soft-primary' : 'btn-light');
  document.getElementById('viewTableBtn').className = 'btn ' + (mode === 'table' ? 'btn-soft-primary' : 'btn-light');
}

function applyFilters(){
  const q = document.getElementById('quoSearch').value.toLowerCase();
  const filtered = KTG.DATA.quotations.filter(item => {
    const matchQ = !q || item.id.toLowerCase().includes(q) || item.customer.toLowerCase().includes(q);
    const matchStatus = !activeStatus || item.status === activeStatus;
    return matchQ && matchStatus;
  });
  render(filtered);
}

function render(list){
  document.getElementById('quoCount').textContent = list.length;
  document.getElementById('quoCountGrid').textContent = list.length;

  document.getElementById('gridView').innerHTML = list.map(q => `
    <div class="col-sm-6 col-xl-4">
      <div class="doc-grid-card st-${q.status}">
        <div class="d-flex justify-content-between align-items-start mb-2">
          <div class="avatar-sm bg-soft-${STATUS_COLOR[q.status]}"><i class="bi ${STATUS_ICON[q.status]}"></i></div>
          <span class="chip chip-${q.status}">${q.status}</span>
        </div>
        <a href="quotation-create.php" class="cell-title d-block fs-14 mb-1">${q.id}</a>
        <div class="cell-sub mb-2">${q.customer}</div>
        <div class="fs-12 text-secondary mb-3"><i class="bi bi-calendar3 me-2 text-muted"></i>${q.date} &rarr; ${q.expiry}</div>
        <div class="d-flex justify-content-between align-items-center">
          <span class="dg-amt">${KTG.money(q.amount)}</span>
          <div class="dropdown">
            <button class="btn btn-sm btn-light" data-bs-toggle="dropdown"><i class="bi bi-three-dots-vertical"></i></button>
            <div class="dropdown-menu dropdown-menu-end">
              <a class="dropdown-item" href="quotation-create.php"><i class="bi bi-eye"></i>Preview</a>
              <a class="dropdown-item" href="#"><i class="bi bi-pencil"></i>Edit</a>
              <a class="dropdown-item" href="#"><i class="bi bi-files"></i>Duplicate</a>
              <a class="dropdown-item" href="#"><i class="bi bi-envelope"></i>Send Email</a>
              <a class="dropdown-item" href="invoice-create.php"><i class="bi bi-arrow-repeat"></i>Convert to Invoice</a>
              <div class="dropdown-divider"></div>
              <a class="dropdown-item text-danger" href="#"><i class="bi bi-trash"></i>Delete</a>
            </div>
          </div>
        </div>
      </div>
    </div>`).join('') || `<div class="col-12"><div class="section-card"><div class="empty-state"><div class="es-icon"><i class="bi bi-file-earmark-text"></i></div><h6>No quotations found</h6><p>Try a different search or status filter.</p></div></div></div>`;

  document.getElementById('quoTableBody').innerHTML = list.map(q => `
    <tr>
      <td><input type="checkbox" class="form-check-input"></td>
      <td><a href="quotation-create.php" class="cell-title">${q.id}</a></td>
      <td>${q.customer}</td>
      <td class="fs-13">${q.date}</td>
      <td class="fs-13">${q.expiry}</td>
      <td class="fw-600">${KTG.money(q.amount)}</td>
      <td><span class="chip chip-${q.status}">${q.status}</span></td>
      <td>
        <div class="dropdown">
          <button class="btn btn-sm btn-light" data-bs-toggle="dropdown"><i class="bi bi-three-dots-vertical"></i></button>
          <div class="dropdown-menu dropdown-menu-end">
            <a class="dropdown-item" href="quotation-create.php"><i class="bi bi-eye"></i>Preview</a>
            <a class="dropdown-item" href="#"><i class="bi bi-pencil"></i>Edit</a>
            <a class="dropdown-item" href="#"><i class="bi bi-files"></i>Duplicate</a>
            <a class="dropdown-item" href="#"><i class="bi bi-envelope"></i>Send Email</a>
            <a class="dropdown-item" href="invoice-create.php"><i class="bi bi-arrow-repeat"></i>Convert to Invoice</a>
            <a class="dropdown-item" href="#"><i class="bi bi-download"></i>Download PDF</a>
            <div class="dropdown-divider"></div>
            <a class="dropdown-item text-danger" href="#"><i class="bi bi-trash"></i>Delete</a>
          </div>
        </div>
      </td>
    </tr>`).join('') || `<tr><td colspan="8"><div class="empty-state"><div class="es-icon"><i class="bi bi-file-earmark-text"></i></div><h6>No quotations found</h6><p>Try a different search or status filter.</p></div></td></tr>`;
  KTG.makeResponsiveTable(document.getElementById('quoTableBody').closest('table'));
}
JS;
?>
<?php include __DIR__ . '/components/footer.php'; ?>
