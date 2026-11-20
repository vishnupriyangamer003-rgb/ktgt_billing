<?php
$pageTitle = 'Services';
$pageKey = 'masters';
include __DIR__ . '/components/header.php';
?>
      <div class="page-head fade-in-up">
        <div>
          <h1>Services</h1>
          <nav class="breadcrumb"><span class="breadcrumb-item"><a href="index.php">Dashboard</a></span><span class="breadcrumb-item"><a href="project-types.php">Masters</a></span><span class="breadcrumb-item active">Services</span></nav>
        </div>
      </div>

      <ul class="nav nav-pills-soft gap-2 mb-3 fade-in-up">
        <li class="nav-item"><a class="nav-link" href="project-types.php">Project Types</a></li>
        <li class="nav-item"><a class="nav-link active" href="services.php">Services</a></li>
        <li class="nav-item"><a class="nav-link" href="taxes.php">Tax Type</a></li>
        <li class="nav-item"><a class="nav-link" href="followup-types.php">Follow Up Types</a></li>
        <li class="nav-item"><a class="nav-link" href="servers.php">Server Name</a></li>
        <li class="nav-item"><a class="nav-link" href="renewal-types.php">Renewal Type</a></li>
        <li class="nav-item"><a class="nav-link" href="paid-status.php">Paid Status Type</a></li>
        <li class="nav-item"><a class="nav-link" href="domain-controls.php">Domain Control</a></li>
      </ul>

      <div class="row justify-content-center">
        <div class="col-xl-6 col-lg-8">
          <div class="section-card fade-in-up">
            <div class="section-head"><h5><i class="bi bi-gear-wide-connected text-primary-KTG me-2"></i>Services</h5><div class="subtitle">Item dropdown &amp; default rate used in Quotations &amp; Invoices</div></div>
            <div class="section-body">
              <form id="serviceForm" class="row g-2 mb-3">
                <div class="col-md-7"><input class="form-control" id="serviceName" placeholder="Service name e.g. Domain Registration" required></div>
                <div class="col-md-3"><input type="number" class="form-control" id="serviceRate" placeholder="Default rate"></div>
                <div class="col-md-2"><button class="btn btn-primary w-100"><i class="bi bi-plus-lg"></i></button></div>
              </form>
              <div id="serviceList"></div>
            </div>
          </div>
        </div>
      </div>
<?php
$pageScript = <<<'JS'
document.addEventListener('DOMContentLoaded', () => setTimeout(init, 150));
const MASTER_KEY = 'services';
let items = [];

function init(){
  render();
  document.getElementById('serviceForm').addEventListener('submit', (e) => {
    e.preventDefault();
    const nameInput = document.getElementById('serviceName');
    const rateInput = document.getElementById('serviceRate');
    const name = nameInput.value.trim();
    if (!name) return;
    $.post('ajax/masters.php', { master: MASTER_KEY, action: 'add', name, rate: +rateInput.value || 0 })
      .done((res) => {
        if (res.success) {
          nameInput.value = ''; rateInput.value = '';
          KTG.toast('Service has been added to Masters.', 'success', 'Service Added');
          render();
        } else {
          KTG.toast(res.message || 'Could not add service.', 'danger', 'Error');
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
  document.getElementById('serviceList').innerHTML = items.map(s => `
    <div class="master-row" id="mrow-${s.id}">
      <i class="bi bi-tools text-primary-KTG"></i>
      <div class="flex-grow-1">
        <div class="fw-600 fs-13">${s.name}</div>
        <div class="fs-11 text-muted">${KTG.money(+s.rate || 0)}</div>
      </div>
      <button class="btn btn-sm btn-light" onclick="editItem(${s.id})" title="Edit"><i class="bi bi-pencil text-primary-KTG"></i></button>
      <button class="btn btn-sm btn-light" onclick="removeItem(${s.id})" title="Delete"><i class="bi bi-trash text-danger"></i></button>
    </div>`).join('') || `<div class="text-center text-muted fs-12 py-3">No services yet.</div>`;
}

function editItem(id){
  const s = items.find(x => x.id === id);
  if (!s) return;
  document.getElementById('mrow-' + id).innerHTML = `
    <i class="bi bi-tools text-primary-KTG"></i>
    <input class="form-control form-control-sm flex-grow-1" id="editName-${id}" value="${KTG.escAttr(s.name)}" placeholder="Service name">
    <input type="number" class="form-control form-control-sm" style="max-width:130px;" id="editRate-${id}" value="${+s.rate || 0}" placeholder="Rate">
    <button class="btn btn-sm btn-soft-primary" onclick="saveEdit(${id})" title="Save"><i class="bi bi-check-lg"></i></button>
    <button class="btn btn-sm btn-light" onclick="renderList()" title="Cancel"><i class="bi bi-x-lg"></i></button>`;
  document.getElementById('editName-' + id).focus();
}

function saveEdit(id){
  const name = document.getElementById('editName-' + id).value.trim();
  const rate = +document.getElementById('editRate-' + id).value || 0;
  if (!name) return;
  $.post('ajax/masters.php', { master: MASTER_KEY, action: 'edit', id, name, rate }).done((res) => {
    if (res.success) { KTG.toast('Service updated.', 'success', 'Updated'); render(); }
    else KTG.toast(res.message || 'Could not update.', 'danger', 'Error');
  });
}

function removeItem(id){
  KTG.confirmDialog({
    title: 'Delete Service?',
    body: 'This removes it from Masters.',
    confirmText: 'Delete', confirmClass: 'btn-danger',
    onConfirm: () => {
      $.post('ajax/masters.php', { master: MASTER_KEY, action: 'delete', id }).done((res) => {
        if (res.success) { KTG.toast('Service deleted.', 'success', 'Deleted'); render(); }
        else KTG.toast(res.message || 'Could not delete.', 'danger', 'Error');
      });
    }
  });
}
JS;
include __DIR__ . '/components/footer.php';
