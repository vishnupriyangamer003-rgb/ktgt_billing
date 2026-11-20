<?php
$pageTitle = 'Paid Status Type';
$pageKey = 'masters';
include __DIR__ . '/components/header.php';
?>
      <div class="page-head fade-in-up">
        <div>
          <h1>Paid Status Type</h1>
          <nav class="breadcrumb"><span class="breadcrumb-item"><a href="index.php">Dashboard</a></span><span class="breadcrumb-item"><a href="project-types.php">Masters</a></span><span class="breadcrumb-item active">Paid Status Type</span></nav>
        </div>
      </div>

      <ul class="nav nav-pills-soft gap-2 mb-3 fade-in-up">
        <li class="nav-item"><a class="nav-link" href="project-types.php">Project Types</a></li>
        <li class="nav-item"><a class="nav-link" href="services.php">Services</a></li>
        <li class="nav-item"><a class="nav-link" href="taxes.php">Tax Type</a></li>
        <li class="nav-item"><a class="nav-link" href="followup-types.php">Follow Up Types</a></li>
        <li class="nav-item"><a class="nav-link" href="servers.php">Server Name</a></li>
        <li class="nav-item"><a class="nav-link" href="renewal-types.php">Renewal Type</a></li>
        <li class="nav-item"><a class="nav-link active" href="paid-status.php">Paid Status Type</a></li>
        <li class="nav-item"><a class="nav-link" href="domain-controls.php">Domain Control</a></li>
      </ul>

      <div class="row justify-content-center">
        <div class="col-xl-6 col-lg-8">
          <div class="section-card fade-in-up">
            <div class="section-head"><h5><i class="bi bi-cash-coin text-primary-KTG me-2"></i>Paid Status Type</h5><div class="subtitle">Paid, Unpaid, Hold, etc. — used as the status dropdown on the Renewals module</div></div>
            <div class="section-body">
              <form id="paidStatusForm" class="d-flex gap-2 mb-3">
                <input class="form-control" id="paidStatusInput" placeholder="e.g. Partially Paid" required>
                <button class="btn btn-primary flex-shrink-0"><i class="bi bi-plus-lg me-1"></i>Add</button>
              </form>
              <div id="paidStatusList"></div>
            </div>
          </div>
        </div>
      </div>
<?php
$pageScript = <<<'JS'
document.addEventListener('DOMContentLoaded', () => setTimeout(init, 150));
const MASTER_KEY = 'paid_status_types';
let items = [];

function init(){
  render();
  document.getElementById('paidStatusForm').addEventListener('submit', (e) => {
    e.preventDefault();
    const input = document.getElementById('paidStatusInput');
    const name = input.value.trim();
    if (!name) return;
    $.post('ajax/masters.php', { master: MASTER_KEY, action: 'add', name })
      .done((res) => {
        if (res.success) {
          input.value = '';
          KTG.toast('Paid status type has been added to Masters.', 'success', 'Type Added');
          render();
        } else {
          KTG.toast(res.message || 'Could not add paid status type.', 'danger', 'Error');
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
  document.getElementById('paidStatusList').innerHTML = items.map(t => `
    <div class="master-row" id="mrow-${t.id}">
      <i class="bi bi-circle-fill fs-11 text-primary-KTG"></i>
      <span class="fw-600 fs-13 flex-grow-1">${t.name}</span>
      <button class="btn btn-sm btn-light" onclick="editItem(${t.id})" title="Edit"><i class="bi bi-pencil text-primary-KTG"></i></button>
      <button class="btn btn-sm btn-light" onclick="removeItem(${t.id})" title="Delete"><i class="bi bi-trash text-danger"></i></button>
    </div>`).join('') || `<div class="text-center text-muted fs-12 py-3">No paid status types yet.</div>`;
}

function editItem(id){
  const t = items.find(x => x.id === id);
  if (!t) return;
  document.getElementById('mrow-' + id).innerHTML = `
    <i class="bi bi-circle-fill fs-11 text-primary-KTG"></i>
    <input class="form-control form-control-sm flex-grow-1" id="editInput-${id}" value="${KTG.escAttr(t.name)}">
    <button class="btn btn-sm btn-soft-primary" onclick="saveEdit(${id})" title="Save"><i class="bi bi-check-lg"></i></button>
    <button class="btn btn-sm btn-light" onclick="renderList()" title="Cancel"><i class="bi bi-x-lg"></i></button>`;
  document.getElementById('editInput-' + id).focus();
}

function saveEdit(id){
  const name = document.getElementById('editInput-' + id).value.trim();
  if (!name) return;
  $.post('ajax/masters.php', { master: MASTER_KEY, action: 'edit', id, name }).done((res) => {
    if (res.success) { KTG.toast('Paid status type updated.', 'success', 'Updated'); render(); }
    else KTG.toast(res.message || 'Could not update.', 'danger', 'Error');
  });
}

function removeItem(id){
  KTG.confirmDialog({
    title: 'Delete Paid Status Type?',
    body: 'This removes it from Masters.',
    confirmText: 'Delete', confirmClass: 'btn-danger',
    onConfirm: () => {
      $.post('ajax/masters.php', { master: MASTER_KEY, action: 'delete', id }).done((res) => {
        if (res.success) { KTG.toast('Paid status type deleted.', 'success', 'Deleted'); render(); }
        else KTG.toast(res.message || 'Could not delete.', 'danger', 'Error');
      });
    }
  });
}
JS;
include __DIR__ . '/components/footer.php';
