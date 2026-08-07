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

function init(){
  render();
  document.getElementById('serviceForm').addEventListener('submit', (e) => {
    e.preventDefault();
    const name = document.getElementById('serviceName');
    const rate = document.getElementById('serviceRate');
    if (!name.value.trim()) return;
    KTG.Masters.addService({name: name.value.trim(), rate: +rate.value || 0});
    name.value = ''; rate.value = '';
    render();
    KTG.toast('Service has been added to Masters.', 'success', 'Service Added');
  });
}

function render(){
  const list = KTG.Masters.services();
  document.getElementById('serviceList').innerHTML = list.map((s,i) => `
    <div class="master-row">
      <i class="bi bi-tools text-primary-KTG"></i>
      <div class="flex-grow-1">
        <div class="fw-600 fs-13">${s.name}</div>
        <div class="fs-11 text-muted">${KTG.money(s.rate||0)}</div>
      </div>
      <button class="btn btn-sm btn-light" onclick="removeItem(${i})"><i class="bi bi-trash text-danger"></i></button>
    </div>`).join('') || `<div class="text-center text-muted fs-12 py-3">No services yet.</div>`;
}

function removeItem(i){ KTG.Masters.removeService(i); render(); }
JS;
include __DIR__ . '/components/footer.php';
