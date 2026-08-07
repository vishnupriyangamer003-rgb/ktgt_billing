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

function init(){
  render();
  document.getElementById('serverForm').addEventListener('submit', (e) => {
    e.preventDefault();
    const input = document.getElementById('serverInput');
    if (!input.value.trim()) return;
    KTG.Masters.addServer(input.value.trim());
    input.value = '';
    render();
    KTG.toast('Server has been added to Masters.', 'success', 'Server Added');
  });
}

function render(){
  const list = KTG.Masters.servers();
  document.getElementById('serverList').innerHTML = list.map((s,i) => `
    <div class="master-row">
      <i class="bi bi-hdd-network text-primary-KTG"></i>
      <span class="fw-600 fs-13 flex-grow-1">${s}</span>
      <button class="btn btn-sm btn-light" onclick="removeItem(${i})"><i class="bi bi-trash text-danger"></i></button>
    </div>`).join('') || `<div class="text-center text-muted fs-12 py-3">No servers yet.</div>`;
}

function removeItem(i){ KTG.Masters.removeServer(i); render(); }
JS;
include __DIR__ . '/components/footer.php';
