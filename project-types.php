<?php
$pageTitle = 'Project Types';
$pageKey = 'masters';
include __DIR__ . '/components/header.php';
?>
      <div class="page-head fade-in-up">
        <div>
          <h1>Project Types</h1>
          <nav class="breadcrumb"><span class="breadcrumb-item"><a href="index.php">Dashboard</a></span><span class="breadcrumb-item"><a href="project-types.php">Masters</a></span><span class="breadcrumb-item active">Project Types</span></nav>
        </div>
      </div>

      <ul class="nav nav-pills-soft gap-2 mb-3 fade-in-up">
        <li class="nav-item"><a class="nav-link active" href="project-types.php">Project Types</a></li>
        <li class="nav-item"><a class="nav-link" href="services.php">Services</a></li>
        <li class="nav-item"><a class="nav-link" href="taxes.php">Tax Type</a></li>
        <li class="nav-item"><a class="nav-link" href="followup-types.php">Follow Up Types</a></li>
        <li class="nav-item"><a class="nav-link" href="servers.php">Server Name</a></li>
        <li class="nav-item"><a class="nav-link" href="renewal-types.php">Renewal Type</a></li>
        <li class="nav-item"><a class="nav-link" href="paid-status.php">Paid Status Type</a></li>
      </ul>

      <div class="row justify-content-center">
        <div class="col-xl-6 col-lg-8">
          <div class="section-card fade-in-up">
            <div class="section-head"><h5><i class="bi bi-diagram-3-fill text-primary-KTG me-2"></i>Project Types</h5><div class="subtitle">Used in Add Customer &amp; Add Project — Website, Web Application, etc.</div></div>
            <div class="section-body">
              <form id="typeForm" class="d-flex gap-2 mb-3">
                <input class="form-control" id="typeInput" placeholder="e.g. Mobile Application" required>
                <button class="btn btn-primary flex-shrink-0"><i class="bi bi-plus-lg me-1"></i>Add</button>
              </form>
              <div id="typeList"></div>
            </div>
          </div>
        </div>
      </div>
<?php
$pageScript = <<<'JS'
document.addEventListener('DOMContentLoaded', () => setTimeout(init, 150));
const MASTER_KEY = 'project_types';

function init(){
  render();
  document.getElementById('typeForm').addEventListener('submit', (e) => {
    e.preventDefault();
    const input = document.getElementById('typeInput');
    const name = input.value.trim();
    if (!name) return;
    $.post('ajax/masters.php', { master: MASTER_KEY, action: 'add', name })
      .done((res) => {
        if (res.success) {
          input.value = '';
          KTG.toast('Project type has been added to Masters.', 'success', 'Type Added');
          render();
        } else {
          KTG.toast(res.message || 'Could not add project type.', 'danger', 'Error');
        }
      })
      .fail(() => KTG.toast('Something went wrong. Please try again.', 'danger', 'Error'));
  });
}

function render(){
  $.get('ajax/masters.php', { master: MASTER_KEY, action: 'list' }).done((res) => {
    const list = (res && res.data) || [];
    document.getElementById('typeList').innerHTML = list.map(t => `
      <div class="master-row">
        <i class="bi bi-diagram-3 text-primary-KTG"></i>
        <span class="fw-600 fs-13 flex-grow-1">${t.name}</span>
        <button class="btn btn-sm btn-light" onclick="removeItem(${t.id})"><i class="bi bi-trash text-danger"></i></button>
      </div>`).join('') || `<div class="text-center text-muted fs-12 py-3">No project types yet.</div>`;
  });
}

function removeItem(id){
  KTG.confirmDialog({
    title: 'Delete Project Type?',
    body: 'This removes it from Masters. Customers already using it keep their current value.',
    confirmText: 'Delete', confirmClass: 'btn-danger',
    onConfirm: () => {
      $.post('ajax/masters.php', { master: MASTER_KEY, action: 'delete', id }).done((res) => {
        if (res.success) { KTG.toast('Project type deleted.', 'success', 'Deleted'); render(); }
        else KTG.toast(res.message || 'Could not delete.', 'danger', 'Error');
      });
    }
  });
}
JS;
include __DIR__ . '/components/footer.php';
