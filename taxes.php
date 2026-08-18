<?php
$pageTitle = 'Tax Type';
$pageKey = 'masters';
include __DIR__ . '/components/header.php';
?>
      <div class="page-head fade-in-up">
        <div>
          <h1>Tax Type</h1>
          <nav class="breadcrumb"><span class="breadcrumb-item"><a href="index.php">Dashboard</a></span><span class="breadcrumb-item"><a href="project-types.php">Masters</a></span><span class="breadcrumb-item active">Tax Type</span></nav>
        </div>
      </div>

      <ul class="nav nav-pills-soft gap-2 mb-3 fade-in-up">
        <li class="nav-item"><a class="nav-link" href="project-types.php">Project Types</a></li>
        <li class="nav-item"><a class="nav-link" href="services.php">Services</a></li>
        <li class="nav-item"><a class="nav-link active" href="taxes.php">Tax Type</a></li>
        <li class="nav-item"><a class="nav-link" href="followup-types.php">Follow Up Types</a></li>
        <li class="nav-item"><a class="nav-link" href="servers.php">Server Name</a></li>
        <li class="nav-item"><a class="nav-link" href="renewal-types.php">Renewal Type</a></li>
        <li class="nav-item"><a class="nav-link" href="paid-status.php">Paid Status Type</a></li>
      </ul>

      <div class="row justify-content-center">
        <div class="col-xl-6 col-lg-8">
          <div class="section-card fade-in-up">
            <div class="section-head"><h5><i class="bi bi-percent text-primary-KTG me-2"></i>Tax Type</h5><div class="subtitle">GST rate dropdown used in Invoices &amp; Renewal invoices</div></div>
            <div class="section-body">
              <form id="taxForm" class="row g-2 mb-3">
                <div class="col-7"><input class="form-control" id="taxName" placeholder="e.g. GST 18%" required></div>
                <div class="col-3"><input type="number" class="form-control" id="taxPercent" placeholder="%" required></div>
                <div class="col-2"><button class="btn btn-primary w-100"><i class="bi bi-plus-lg"></i></button></div>
              </form>
              <div id="taxList"></div>
            </div>
          </div>
        </div>
      </div>
<?php
$pageScript = <<<'JS'
document.addEventListener('DOMContentLoaded', () => setTimeout(init, 150));
const MASTER_KEY = 'taxes';

function init(){
  render();
  document.getElementById('taxForm').addEventListener('submit', (e) => {
    e.preventDefault();
    const nameInput = document.getElementById('taxName');
    const pctInput = document.getElementById('taxPercent');
    const name = nameInput.value.trim();
    if (!name) return;
    $.post('ajax/masters.php', { master: MASTER_KEY, action: 'add', name, percent: +pctInput.value || 0 })
      .done((res) => {
        if (res.success) {
          nameInput.value = ''; pctInput.value = '';
          KTG.toast('Tax rate has been added to Masters.', 'success', 'Tax Added');
          render();
        } else {
          KTG.toast(res.message || 'Could not add tax rate.', 'danger', 'Error');
        }
      })
      .fail(() => KTG.toast('Something went wrong. Please try again.', 'danger', 'Error'));
  });
}

function render(){
  $.get('ajax/masters.php', { master: MASTER_KEY, action: 'list' }).done((res) => {
    const list = (res && res.data) || [];
    document.getElementById('taxList').innerHTML = list.map(t => `
      <div class="master-row">
        <i class="bi bi-receipt-cutoff text-primary-KTG"></i>
        <span class="fw-600 fs-13 flex-grow-1">${t.name}</span>
        <span class="chip chip-inprogress">${+t.percent}%</span>
        <button class="btn btn-sm btn-light" onclick="removeItem(${t.id})"><i class="bi bi-trash text-danger"></i></button>
      </div>`).join('') || `<div class="text-center text-muted fs-12 py-3">No tax rates yet.</div>`;
  });
}

function removeItem(id){
  KTG.confirmDialog({
    title: 'Delete Tax Rate?',
    body: 'This removes it from Masters.',
    confirmText: 'Delete', confirmClass: 'btn-danger',
    onConfirm: () => {
      $.post('ajax/masters.php', { master: MASTER_KEY, action: 'delete', id }).done((res) => {
        if (res.success) { KTG.toast('Tax rate deleted.', 'success', 'Deleted'); render(); }
        else KTG.toast(res.message || 'Could not delete.', 'danger', 'Error');
      });
    }
  });
}
JS;
include __DIR__ . '/components/footer.php';
