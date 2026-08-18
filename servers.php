<?php
$pageTitle = 'Server Name';
$pageKey = 'masters';
include __DIR__ . '/components/header.php';
?>
      <div class="page-head fade-in-up">
        <div>
          <h1>Server Name</h1>
          <nav class="breadcrumb"><span class="breadcrumb-item"><a href="index.php">Dashboard</a></span><span class="breadcrumb-item"><a href="project-types.php">Masters</a></span><span class="breadcrumb-item active">Server Name</span></nav>
        </div>
      </div>

      <ul class="nav nav-pills-soft gap-2 mb-3 fade-in-up">
        <li class="nav-item"><a class="nav-link" href="project-types.php">Project Types</a></li>
        <li class="nav-item"><a class="nav-link" href="services.php">Services</a></li>
        <li class="nav-item"><a class="nav-link" href="taxes.php">Tax Type</a></li>
        <li class="nav-item"><a class="nav-link" href="followup-types.php">Follow Up Types</a></li>
        <li class="nav-item"><a class="nav-link active" href="servers.php">Server Name</a></li>
        <li class="nav-item"><a class="nav-link" href="renewal-types.php">Renewal Type</a></li>
        <li class="nav-item"><a class="nav-link" href="paid-status.php">Paid Status Type</a></li>
      </ul>

      <div class="row justify-content-center">
        <div class="col-xl-6 col-lg-8">
          <div class="section-card fade-in-up">
            <div class="section-head"><h5><i class="bi bi-hdd-network-fill text-primary-KTG me-2"></i>Server Name</h5><div class="subtitle">Used in the Renewals module to record where each project is hosted</div></div>
            <div class="section-body">
              <form id="serverForm" class="d-flex gap-2 mb-3">
                <input class="form-control" id="serverInput" placeholder="e.g. DigitalOcean" required>
                <button class="btn btn-primary flex-shrink-0"><i class="bi bi-plus-lg me-1"></i>Add</button>
              </form>
              <div id="serverList"></div>
            </div>
          </div>
        </div>
      </div>
<?php
$pageScript = <<<'JS'
document.addEventListener('DOMContentLoaded', () => setTimeout(init, 150));
const MASTER_KEY = 'servers';
let items = [];

function init(){
  render();
  document.getElementById('serverForm').addEventListener('submit', (e) => {
    e.preventDefault();
    const input = document.getElementById('serverInput');
    const name = input.value.trim();
    if (!name) return;
    $.post('ajax/masters.php', { master: MASTER_KEY, action: 'add', name })
      .done((res) => {
        if (res.success) {
          input.value = '';
          KTG.toast('Server has been added to Masters.', 'success', 'Server Added');
          render();
        } else {
          KTG.toast(res.message || 'Could not add server.', 'danger', 'Error');
        }
      })
      .fail(() => KTG.toast('Something went wrong. Please try again.', 'danger', 'Error'));
  });
}

function render(){
  $.get('ajax/masters.php', { master: MASTER_KEY, action: 'list' }).done((res) => {
    items = (res && res.data) || [];
    renderList();
  });
}

function renderList(){
  document.getElementById('serverList').innerHTML = items.map(s => `
    <div class="master-row" id="mrow-${s.id}">
      <i class="bi bi-hdd-network text-primary-KTG"></i>
      <span class="fw-600 fs-13 flex-grow-1">${s.name}</span>
      <button class="btn btn-sm btn-light" onclick="editItem(${s.id})" title="Edit"><i class="bi bi-pencil text-primary-KTG"></i></button>
      <button class="btn btn-sm btn-light" onclick="removeItem(${s.id})" title="Delete"><i class="bi bi-trash text-danger"></i></button>
    </div>`).join('') || `<div class="text-center text-muted fs-12 py-3">No servers yet.</div>`;
}

function editItem(id){
  const s = items.find(x => x.id === id);
  if (!s) return;
  document.getElementById('mrow-' + id).innerHTML = `
    <i class="bi bi-hdd-network text-primary-KTG"></i>
    <input class="form-control form-control-sm flex-grow-1" id="editInput-${id}" value="${KTG.escAttr(s.name)}">
    <button class="btn btn-sm btn-soft-primary" onclick="saveEdit(${id})" title="Save"><i class="bi bi-check-lg"></i></button>
    <button class="btn btn-sm btn-light" onclick="renderList()" title="Cancel"><i class="bi bi-x-lg"></i></button>`;
  document.getElementById('editInput-' + id).focus();
}

function saveEdit(id){
  const name = document.getElementById('editInput-' + id).value.trim();
  if (!name) return;
  $.post('ajax/masters.php', { master: MASTER_KEY, action: 'edit', id, name }).done((res) => {
    if (res.success) { KTG.toast('Server updated.', 'success', 'Updated'); render(); }
    else KTG.toast(res.message || 'Could not update.', 'danger', 'Error');
  });
}

function removeItem(id){
  KTG.confirmDialog({
    title: 'Delete Server?',
    body: 'This removes it from Masters.',
    confirmText: 'Delete', confirmClass: 'btn-danger',
    onConfirm: () => {
      $.post('ajax/masters.php', { master: MASTER_KEY, action: 'delete', id }).done((res) => {
        if (res.success) { KTG.toast('Server deleted.', 'success', 'Deleted'); render(); }
        else KTG.toast(res.message || 'Could not delete.', 'danger', 'Error');
      });
    }
  });
}
JS;
include __DIR__ . '/components/footer.php';
