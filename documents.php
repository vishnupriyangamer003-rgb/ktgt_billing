<?php
$pageTitle = "Documents";
$pageKey = "documents";
include __DIR__ . '/components/header.php';
?>
      <div class="page-head fade-in-up">
        <div>
          <h1>Documents</h1>
          <nav class="breadcrumb"><span class="breadcrumb-item"><a href="index.php">Dashboard</a></span><span class="breadcrumb-item active">Documents</span></nav>
        </div>
        <div class="fs-12 text-muted">Auto-collected from attachments added during project creation</div>
      </div>

      <div class="section-card mb-3 fade-in-up">
        <div class="section-body py-3">
          <div class="d-flex flex-wrap gap-2 align-items-center">
            <div class="position-relative flex-grow-1" style="min-width:220px; max-width:340px;">
              <i class="bi bi-search position-absolute" style="left:14px; top:50%; transform:translateY(-50%); color:var(--text-muted);"></i>
              <input type="text" id="docSearch" class="form-control ps-5" placeholder="Search documents or projects...">
            </div>
            <select class="form-select" id="docCustomerFilter" style="max-width:220px;"><option value="">All Customers</option></select>
          </div>
        </div>
      </div>

      <div id="docsByProject"></div>

<?php
$pageScript = <<<'JS'
document.addEventListener('DOMContentLoaded', () => setTimeout(init, 150));

function init(){
  const customers = [...new Set(KTG.DATA.projects.map(p => p.customer))];
  document.getElementById('docCustomerFilter').innerHTML = '<option value="">All Customers</option>' + customers.map(c => `<option>${c}</option>`).join('');
  document.getElementById('docSearch').addEventListener('input', render);
  document.getElementById('docCustomerFilter').addEventListener('change', render);
  render();
}

function iconColor(icon){
  if (icon.includes('pdf')) return 'danger';
  if (icon.includes('word')) return 'primary';
  if (icon.includes('excel')) return 'success';
  if (icon.includes('zip')) return 'warning';
  return 'secondary';
}

function render(){
  const q = document.getElementById('docSearch').value.toLowerCase();
  const custFilter = document.getElementById('docCustomerFilter').value;

  const projects = KTG.DATA.projects.filter(p =>
    (!custFilter || p.customer === custFilter) &&
    p.attachments.length &&
    (!q || p.name.toLowerCase().includes(q) || p.customer.toLowerCase().includes(q) || p.attachments.some(a => a.name.toLowerCase().includes(q)))
  );

  const box = document.getElementById('docsByProject');
  if (!projects.length) {
    box.innerHTML = `<div class="section-card"><div class="empty-state"><div class="es-icon"><i class="bi bi-folder2-open"></i></div><h6>No documents found</h6><p>Attach files while creating a project and they will show up here automatically.</p></div></div>`;
    return;
  }

  box.innerHTML = projects.map(p => `
    <div class="section-card mb-3 fade-in-up">
      <div class="section-head">
        <div><h5>${p.name}</h5><div class="subtitle">${p.customer} &middot; <span class="chip chip-inprogress">${p.type}</span></div></div>
        <span class="chip chip-inprogress"><i class="bi bi-paperclip"></i>${p.attachments.length} file${p.attachments.length>1?'s':''}</span>
      </div>
      <div class="section-body">
        <div class="row g-3">
          ${p.attachments.filter(a => !q || a.name.toLowerCase().includes(q) || p.name.toLowerCase().includes(q) || p.customer.toLowerCase().includes(q)).map(a => `
            <div class="col-6 col-md-3">
              <div class="section-card p-3 hover-lift h-100">
                <div class="doc-thumb mb-2 text-${iconColor(a.icon)}"><i class="bi ${a.icon}"></i></div>
                <div class="fs-13 fw-600 text-truncate">${a.name}</div>
                <div class="fs-11 text-muted mb-2">${a.size}</div>
                <div class="d-flex gap-2">
                  <button class="btn btn-sm btn-light flex-fill"><i class="bi bi-download"></i></button>
                  <button class="btn btn-sm btn-light flex-fill"><i class="bi bi-eye"></i></button>
                </div>
              </div>
            </div>`).join('')}
        </div>
      </div>
    </div>`).join('');
}
JS;
?>
<?php include __DIR__ . '/components/footer.php'; ?>
