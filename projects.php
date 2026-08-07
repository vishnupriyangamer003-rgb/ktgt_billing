<?php
$pageTitle = "Projects";
$pageKey = "projects";
$extraHead = <<<'HTML'
<style>
.kanban-col{ background: var(--surface-alt); border-radius: var(--radius); padding: .9rem; min-width: 280px; flex: 1; }
.kanban-col .kcol-head{ display:flex; align-items:center; justify-content:space-between; margin-bottom:.85rem; padding: 0 .3rem;}
.kanban-card{ background:#fff; border:1px solid var(--border); border-radius: 13px; padding: .9rem 1rem; margin-bottom:.7rem; box-shadow: var(--shadow-xs); transition: all .18s var(--ease); }
.kanban-card:hover{ box-shadow: var(--shadow-md); transform: translateY(-2px); }
.kanban-board{ display:flex; gap:1rem; overflow-x:auto; padding-bottom:.5rem; }
</style>
HTML;
include __DIR__ . '/components/header.php';
?>
      <div class="page-head fade-in-up">
        <div>
          <h1>Projects</h1>
          <nav class="breadcrumb"><span class="breadcrumb-item"><a href="index.php">Dashboard</a></span><span class="breadcrumb-item active">Projects</span></nav>
        </div>
        <div class="d-flex gap-2">
          <div class="btn-group">
            <button class="btn btn-soft-primary" id="viewListBtn"><i class="bi bi-list-ul"></i> List</button>
            <button class="btn btn-light" id="viewKanbanBtn"><i class="bi bi-kanban"></i> Grid</button>
          </div>
          <button class="btn btn-primary" id="newProjectBtn" data-bs-toggle="modal" data-bs-target="#addProjectModal"><i class="bi bi-plus-lg me-1"></i>New Project</button>
        </div>
      </div>

      <div class="row g-3 mb-3 stagger">
        <div class="col-6 col-md-3"><div class="stat-card accent-primary"><div class="stat-icon bg-soft-primary"><i class="bi bi-kanban-fill"></i></div><div class="stat-label">Total Projects</div><div class="stat-value">64</div></div></div>
        <div class="col-6 col-md-3"><div class="stat-card accent-info"><div class="stat-icon bg-soft-info"><i class="bi bi-hourglass-split"></i></div><div class="stat-label">In Progress</div><div class="stat-value">18</div></div></div>
        <div class="col-6 col-md-3"><div class="stat-card accent-success"><div class="stat-icon bg-soft-success"><i class="bi bi-check-circle-fill"></i></div><div class="stat-label">Completed</div><div class="stat-value">39</div></div></div>
        <div class="col-6 col-md-3"><div class="stat-card accent-danger"><div class="stat-icon bg-soft-danger"><i class="bi bi-exclamation-triangle-fill"></i></div><div class="stat-label">Overdue</div><div class="stat-value">7</div></div></div>
      </div>

      <!-- Grid (Kanban) View -->
      <div id="kanbanView" class="fade-in-up d-none">
        <div class="kanban-board" id="kanbanBoard"></div>
      </div>

      <!-- List View — default -->
      <div id="listView" class="section-card">
        <div class="table-responsive">
          <table class="table table-modern mb-0">
            <thead><tr><th style="width:50px;">S.No</th><th>Project</th><th>Customer</th><th>Type</th><th>Status</th><th style="width:160px;"></th></tr></thead>
            <tbody id="projectListBody"></tbody>
          </table>
        </div>
      </div>

<?php
$pageModals = <<<'HTML'
<!-- Add / Edit Project Modal -->
<div class="modal fade" id="addProjectModal" tabindex="-1">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header"><h5 class="modal-title" id="projectModalTitle"><i class="bi bi-kanban-fill text-primary-KTG me-2"></i>New Project</h5><button class="btn-close" data-bs-dismiss="modal"></button></div>
      <div class="modal-body">
        <div class="row g-3">
          <div class="col-md-6"><label class="form-label">Project Name <span class="req">*</span></label><input class="form-control" id="pmName" placeholder="e.g. Corporate Website Revamp"></div>
          <div class="col-md-6"><label class="form-label">Customer <span class="req">*</span></label>
            <select class="form-select" id="modalProjectCustomer"></select>
          </div>
          <div class="col-md-6"><label class="form-label">Select Type</label>
            <select class="form-select" id="modalProjectTypeSelect"></select>
          </div>
          <div class="col-md-6"><label class="form-label">Status</label>
            <select class="form-select" id="pmStatus">
              <option value="planning">Planning</option>
              <option value="inprogress">In Progress</option>
              <option value="review">Review</option>
              <option value="completed">Completed</option>
              <option value="overdue">Overdue</option>
            </select>
          </div>
          <div class="col-12"><label class="form-label">Description</label><textarea class="form-control" rows="3" placeholder="Scope of work..."></textarea></div>
        </div>
      </div>
      <div class="modal-footer"><button class="btn btn-light" data-bs-dismiss="modal">Cancel</button><button class="btn btn-primary" id="saveProjectBtn">Create Project</button></div>
    </div>
  </div>
</div>
HTML;
?>
<?php
$pageScript = <<<'JS'
document.addEventListener('DOMContentLoaded', () => setTimeout(init, 150));

const STATUS_META = {
  planning:  {label:'Planning',    chip:'chip-draft',      bar:'bg-secondary'},
  inprogress:{label:'In Progress', chip:'chip-pending',     bar:'bg-primary'},
  review:    {label:'Review',      chip:'chip-viewed',      bar:'bg-info'},
  completed: {label:'Completed',   chip:'chip-completed',   bar:'bg-success'},
  overdue:   {label:'Overdue',     chip:'chip-overdue',     bar:'bg-danger'}
};

let projectsList = [];
let editIndex = null;

function init(){
  const customers = document.getElementById('modalProjectCustomer');
  customers.innerHTML = KTG.DATA.customers.map(c => `<option>${c.name}</option>`).join('');
  document.getElementById('modalProjectTypeSelect').innerHTML = KTG.Masters.projectTypes().map(t => `<option>${t}</option>`).join('');

  projectsList = KTG.DATA.projects.map(p => ({...p}));

  document.getElementById('newProjectBtn').addEventListener('click', () => openModal(null));
  document.getElementById('saveProjectBtn').addEventListener('click', saveProject);

  renderKanban();
  renderList();

  document.getElementById('viewKanbanBtn').addEventListener('click', () => toggleView('kanban'));
  document.getElementById('viewListBtn').addEventListener('click', () => toggleView('list'));
}

function toggleView(mode){
  document.getElementById('kanbanView').classList.toggle('d-none', mode !== 'kanban');
  document.getElementById('listView').classList.toggle('d-none', mode !== 'list');
  document.getElementById('viewKanbanBtn').className = 'btn ' + (mode === 'kanban' ? 'btn-soft-primary' : 'btn-light');
  document.getElementById('viewListBtn').className = 'btn ' + (mode === 'list' ? 'btn-soft-primary' : 'btn-light');
}

function openModal(index){
  editIndex = index;
  const modalTitle = document.getElementById('projectModalTitle');
  const saveBtn = document.getElementById('saveProjectBtn');
  if (index === null) {
    modalTitle.innerHTML = '<i class="bi bi-kanban-fill text-primary-KTG me-2"></i>New Project';
    saveBtn.textContent = 'Create Project';
    document.getElementById('pmName').value = '';
    document.getElementById('modalProjectCustomer').value = projectsList[0] ? projectsList[0].customer : '';
    document.getElementById('modalProjectTypeSelect').value = KTG.Masters.projectTypes()[0];
    document.getElementById('pmStatus').value = 'planning';
  } else {
    const p = projectsList[index];
    modalTitle.innerHTML = '<i class="bi bi-pencil-square text-primary-KTG me-2"></i>Edit Project';
    saveBtn.textContent = 'Save Changes';
    document.getElementById('pmName').value = p.name;
    document.getElementById('modalProjectCustomer').value = p.customer;
    document.getElementById('modalProjectTypeSelect').value = p.type;
    document.getElementById('pmStatus').value = p.status;
  }
  new bootstrap.Modal(document.getElementById('addProjectModal')).show();
}

function saveProject(){
  const name = document.getElementById('pmName').value.trim();
  const customer = document.getElementById('modalProjectCustomer').value;
  const type = document.getElementById('modalProjectTypeSelect').value;
  const status = document.getElementById('pmStatus').value;
  if (!name) { KTG.toast('Project name is required.', 'danger', 'Missing Name'); return; }

  if (editIndex === null) {
    projectsList.unshift({id: Date.now(), name, customer, type, status, services:[], attachments:[]});
    KTG.toast('New project has been created.', 'success', 'Project Created');
  } else {
    Object.assign(projectsList[editIndex], {name, customer, type, status});
    KTG.toast('Project has been updated.', 'success', 'Project Updated');
  }
  bootstrap.Modal.getInstance(document.getElementById('addProjectModal')).hide();
  renderKanban();
  renderList();
}

function deleteProject(index){
  const p = projectsList[index];
  KTG.confirmDialog({
    title: 'Delete Project?',
    body: `This will remove "${p.name}" from your active project list.`,
    confirmText: 'Delete', confirmClass: 'btn-danger',
    onConfirm: () => {
      projectsList.splice(index, 1);
      renderKanban();
      renderList();
      KTG.toast('Project has been deleted.', 'danger', 'Project Deleted');
    }
  });
}

function changeStatus(index, status){
  projectsList[index].status = status;
  renderKanban();
  renderList();
  KTG.toast(`Status updated to "${STATUS_META[status].label}".`, 'success', 'Status Updated');
}

function statusOptions(current){
  return Object.entries(STATUS_META).map(([k,v]) => `<option value="${k}" ${k===current?'selected':''}>${v.label}</option>`).join('');
}

function renderKanban(){
  const cols = [
    {key:'planning', title:'Planning', chip:'chip-draft'},
    {key:'inprogress', title:'In Progress', chip:'chip-pending', includeOverdue:true},
    {key:'review', title:'Review', chip:'chip-viewed'},
    {key:'completed', title:'Completed', chip:'chip-completed'}
  ];
  const board = document.getElementById('kanbanBoard');
  board.innerHTML = cols.map(col => {
    const items = projectsList
      .map((p,i) => ({p,i}))
      .filter(({p}) => p.status === col.key || (col.includeOverdue && p.status === 'overdue'));
    return `
    <div class="kanban-col">
      <div class="kcol-head"><span class="fw-700 fs-13"><span class="chip ${col.chip}">${col.title}</span></span><span class="fs-12 text-muted">${items.length}</span></div>
      ${items.map(({p,i}) => `
        <div class="kanban-card">
          <div class="cell-title fs-13 mb-1">${p.name}</div>
          <div class="cell-sub mb-2">${p.customer}</div>
          <div class="d-flex align-items-center justify-content-between gap-2">
            <span class="chip chip-inprogress">${p.type}</span>
            <select class="status-pick" onchange="changeStatus(${i}, this.value)">${statusOptions(p.status)}</select>
          </div>
          <div class="d-flex gap-2 mt-2">
            <button class="btn btn-sm btn-light flex-fill py-0" onclick="openModal(${i})"><i class="bi bi-pencil"></i></button>
            <button class="btn btn-sm btn-light flex-fill py-0" onclick="deleteProject(${i})"><i class="bi bi-trash text-danger"></i></button>
          </div>
        </div>`).join('')}
    </div>`;
  }).join('');
}

function renderList(){
  document.getElementById('projectListBody').innerHTML = projectsList.map((p, i) => {
    const meta = STATUS_META[p.status];
    return `
    <tr>
      <td class="fs-13 text-muted">${i+1}</td>
      <td class="cell-title">${p.name}</td>
      <td>${p.customer}</td>
      <td><span class="chip chip-inprogress">${p.type}</span></td>
      <td style="width:160px;"><select class="status-pick w-100" onchange="changeStatus(${i}, this.value)">${statusOptions(p.status)}</select></td>
      <td>
        <div class="d-flex gap-1">
          <button class="btn btn-sm btn-light" onclick="openModal(${i})" title="Edit"><i class="bi bi-pencil"></i></button>
          <button class="btn btn-sm btn-light" onclick="deleteProject(${i})" title="Delete"><i class="bi bi-trash text-danger"></i></button>
        </div>
      </td>
    </tr>`;
  }).join('') || `<tr><td colspan="6"><div class="empty-state"><div class="es-icon"><i class="bi bi-kanban"></i></div><h6>No projects yet</h6></div></td></tr>`;
  KTG.makeResponsiveTable(document.getElementById('projectListBody').closest('table'));
}
JS;
?>
<?php include __DIR__ . '/components/footer.php'; ?>
