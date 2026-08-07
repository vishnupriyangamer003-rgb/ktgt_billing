<?php
$pageTitle = "Follow Ups";
$pageKey = "calendar";
include __DIR__ . '/components/header.php';
?>
      <div class="page-head fade-in-up">
        <div>
          <h1>Follow Ups</h1>
          <nav class="breadcrumb"><span class="breadcrumb-item"><a href="index.php">Dashboard</a></span><span class="breadcrumb-item active">Follow Ups</span></nav>
        </div>
        <div class="d-flex gap-2">
          <div class="dropdown">
            <button class="btn btn-light" data-bs-toggle="dropdown"><i class="bi bi-calendar3 me-1"></i>Calendar</button>
            <div class="dropdown-menu dropdown-menu-end calendar-popover">
              <div class="d-flex align-items-center justify-content-between mb-2">
                <button class="btn btn-icon btn-light btn-sm"><i class="bi bi-chevron-left"></i></button>
                <strong class="fs-13">August 2026</strong>
                <button class="btn btn-icon btn-light btn-sm"><i class="bi bi-chevron-right"></i></button>
              </div>
              <div class="cal-grid mb-1">
                <div class="cal-dow">S</div><div class="cal-dow">M</div><div class="cal-dow">T</div><div class="cal-dow">W</div><div class="cal-dow">T</div><div class="cal-dow">F</div><div class="cal-dow">S</div>
              </div>
              <div class="cal-grid" id="calGrid" style="gap:3px;"></div>
              <div class="chart-legend mt-2 mb-0">
                <span class="leg-item"><span class="leg-dot" style="background:#2563eb"></span>Follow Up</span>
                <span class="leg-item"><span class="leg-dot" style="background:#d97706"></span>Alert</span>
              </div>
            </div>
          </div>
          <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#followupModal"><i class="bi bi-plus-lg me-1"></i>Schedule Follow Up</button>
        </div>
      </div>

      <div class="row g-3 mb-3 stagger">
        <div class="col-6 col-md-3"><div class="stat-card accent-primary"><div class="stat-icon bg-soft-primary"><i class="bi bi-telephone-fill"></i></div><div class="stat-label">Upcoming Calls</div><div class="stat-value">6</div></div></div>
        <div class="col-6 col-md-3"><div class="stat-card accent-info"><div class="stat-icon bg-soft-info"><i class="bi bi-camera-video-fill"></i></div><div class="stat-label">Meetings</div><div class="stat-value">3</div></div></div>
        <div class="col-6 col-md-3"><div class="stat-card accent-warning"><div class="stat-icon bg-soft-warning"><i class="bi bi-hourglass-split"></i></div><div class="stat-label">Pending Follow Ups</div><div class="stat-value">9</div></div></div>
        <div class="col-6 col-md-3"><div class="stat-card accent-success"><div class="stat-icon bg-soft-success"><i class="bi bi-check-circle-fill"></i></div><div class="stat-label">Completed</div><div class="stat-value">21</div></div></div>
      </div>

      <div class="section-card mb-3 fade-in-up">
        <div class="section-body py-3">
          <div class="d-flex flex-wrap gap-2 align-items-center">
            <label class="fs-12 text-muted mb-0">From</label>
            <input type="date" class="form-control" id="fromDate" value="2026-08-01" style="max-width:160px;">
            <label class="fs-12 text-muted mb-0">To</label>
            <input type="date" class="form-control" id="toDate" value="2026-08-31" style="max-width:160px;">
            <button class="btn btn-soft-primary" id="applyDateBtn"><i class="bi bi-funnel me-1"></i>Apply</button>
            <button class="btn btn-light ms-auto" id="exportBtn"><i class="bi bi-filetype-xlsx me-1"></i>Export</button>
          </div>
        </div>
      </div>

      <ul class="nav nav-pills-soft gap-2 mb-3 fade-in-up" id="fuTabs"></ul>

      <div class="section-card fade-in-up">
        <div class="section-body p-0">
          <div class="list-group list-group-flush" id="fuList"></div>
        </div>
      </div>

<?php
$pageModals = <<<'HTML'
<!-- Follow Up Modal -->
<div class="modal fade" id="followupModal" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header"><h5 class="modal-title"><i class="bi bi-telephone-outbound-fill text-primary-KTG me-2"></i>Schedule Follow Up</h5><button class="btn-close" data-bs-dismiss="modal"></button></div>
      <div class="modal-body">
        <div class="row g-3">
          <div class="col-md-6">
            <label class="form-label">Customer <span class="req">*</span></label>
            <select class="form-select" id="fuCustomer">
              <option value="__new__" selected>+ New Customer</option>
            </select>
          </div>
          <div class="col-md-6"><label class="form-label">Type</label><select class="form-select" id="fuTypeSelect"></select></div>
          <div class="col-12" id="newCustomerWrap">
            <label class="form-label">New Customer / Lead Name <span class="req">*</span></label>
            <input class="form-control" id="newCustomerName" placeholder="e.g. Skyline Interiors">
          </div>
          <div class="col-12"><label class="form-label">Subject <span class="req">*</span></label><input class="form-control" placeholder="e.g. Discuss proposal revisions"></div>
          <div class="col-md-6"><label class="form-label">Next Follow Up Date</label><input type="date" class="form-control" value="2026-08-06"></div>
          <div class="col-md-6"><label class="form-label">Priority</label><select class="form-select"><option>High</option><option selected>Medium</option><option>Low</option></select></div>
          <div class="col-12"><label class="form-label">Notes</label><textarea class="form-control" rows="2"></textarea></div>
        </div>
      </div>
      <div class="modal-footer"><button class="btn btn-light" data-bs-dismiss="modal">Cancel</button><button class="btn btn-primary" onclick="bootstrap.Modal.getInstance(document.getElementById('followupModal')).hide(); KTG.toast('Follow up has been scheduled.','success','Follow Up Added');">Schedule</button></div>
    </div>
  </div>
</div>
HTML;
?>
<?php
$pageScript = <<<'JS'
document.addEventListener('DOMContentLoaded', () => setTimeout(init, 150));

const NOTIF_COLOR_HEX = {warning:'#d97706', info:'#0891b2', danger:'#dc2626', purple:'#7c3aed', primary:'#2563eb', success:'#16a34a'};
const TYPE_COLOR_PALETTE = ['#2563eb', '#7c3aed', '#0891b2', '#16a34a', '#d97706', '#dc2626'];
const TYPE_ICON_PALETTE = {call:'bi-telephone-fill', meeting:'bi-camera-video-fill', email:'bi-envelope-fill', whatsapp:'bi-whatsapp'};
let activeFuTab = 'all';
let followupsList = [];

function typeColor(type){
  const types = KTG.Masters.followupTypes().map(t => t.toLowerCase());
  const idx = types.indexOf((type||'').toLowerCase());
  return TYPE_COLOR_PALETTE[idx >= 0 ? idx % TYPE_COLOR_PALETTE.length : 0];
}
function typeIcon(type){ return TYPE_ICON_PALETTE[(type||'').toLowerCase()] || 'bi-telephone-outbound-fill'; }

function init(){
  followupsList = KTG.DATA.followups.map(f => ({...f}));

  const customerOpts = KTG.DATA.customers.map(c => `<option>${c.name}</option>`).join('');
  document.getElementById('fuCustomer').innerHTML = '<option value="__new__" selected>+ New Customer</option>' + customerOpts;
  document.getElementById('fuCustomer').addEventListener('change', (e) => {
    document.getElementById('newCustomerWrap').classList.toggle('d-none', e.target.value !== '__new__');
  });

  const types = KTG.Masters.followupTypes();
  document.getElementById('fuTypeSelect').innerHTML = types.map(t => `<option>${t}</option>`).join('');

  const tabsHtml = [`<li class="nav-item"><button class="nav-link active" data-f="all">All</button></li>`]
    .concat(types.map(t => `<li class="nav-item"><button class="nav-link" data-f="${t.toLowerCase()}">${t}s</button></li>`))
    .concat([`<li class="nav-item"><button class="nav-link" data-f="notif">Renewal Alerts</button></li>`]);
  document.getElementById('fuTabs').innerHTML = tabsHtml.join('');
  document.querySelectorAll('#fuTabs .nav-link').forEach(btn => {
    btn.addEventListener('click', () => {
      document.querySelectorAll('#fuTabs .nav-link').forEach(b => b.classList.remove('active'));
      btn.classList.add('active');
      activeFuTab = btn.dataset.f;
      renderFuList();
    });
  });
  document.getElementById('applyDateBtn').addEventListener('click', renderFuList);
  document.getElementById('exportBtn').addEventListener('click', exportFollowUps);

  renderCalendar();
  renderFuList();
}

function inRange(dateStr){
  const from = document.getElementById('fromDate').value;
  const to = document.getElementById('toDate').value;
  return (!from || dateStr >= from) && (!to || dateStr <= to);
}

function renderCalendar(){
  const grid = document.getElementById('calGrid');
  const daysInMonth = 31, startDow = 6; // Aug 1 2026 is Saturday
  const events = {};
  followupsList.forEach(f => {
    if (!f.next.startsWith('2026-08-')) return;
    const day = +f.next.split('-')[2];
    (events[day] = events[day] || []).push(typeColor(f.type));
  });
  KTG.DATA.notifications.forEach(n => {
    if (!n.dueDate.startsWith('2026-08-')) return;
    const day = +n.dueDate.split('-')[2];
    (events[day] = events[day] || []).push(NOTIF_COLOR_HEX[n.color] || '#d97706');
  });

  let html = '';
  for (let i=0;i<startDow;i++) html += `<div class="cal-cell muted" style="min-height:30px;"></div>`;
  for (let d=1; d<=daysInMonth; d++){
    const isToday = d === 4;
    const dots = (events[d]||[]).slice(0,3).map(c => `<span style="display:inline-block;width:5px;height:5px;border-radius:50%;background:${c};margin:0 1px;"></span>`).join('');
    html += `<div class="cal-cell ${isToday?'today':''}" style="min-height:30px; padding:.25rem;"><div class="cal-date" style="font-size:10.5px;">${d}</div><div>${dots}</div></div>`;
  }
  grid.innerHTML = html;
}

function renderFuList(){
  const prClass = {high:'priority-high', medium:'priority-medium', low:'priority-low'};
  const list = document.getElementById('fuList');

  if (activeFuTab === 'notif') {
    list.innerHTML = KTG.Notifications.list().filter(n => inRange(n.dueDate)).map(n => `
      <a href="${n.link}" class="list-group-item list-group-item-action px-3 py-3">
        <div class="d-flex align-items-start gap-3">
          <div class="avatar-sm bg-soft-${n.color}"><i class="bi ${n.icon}"></i></div>
          <div class="flex-grow-1">
            <div class="cell-title fs-13">${n.title}</div>
            <div class="cell-sub">${n.desc}</div>
          </div>
          <span class="chip chip-inprogress">${n.dueDate}</span>
        </div>
      </a>`).join('') || `<div class="empty-state"><div class="es-icon"><i class="bi bi-bell"></i></div><h6>No alerts</h6></div>`;
    return;
  }

  const items = followupsList
    .map((f,i) => ({f,i}))
    .filter(({f}) => (activeFuTab === 'all' || f.type === activeFuTab) && inRange(f.next));

  list.innerHTML = items.map(({f,i}) => `
    <div class="list-group-item px-3 py-3">
      <div class="d-flex align-items-start gap-3">
        <div class="avatar-sm" style="background:${typeColor(f.type)}22; color:${typeColor(f.type)};"><i class="bi ${typeIcon(f.type)}"></i></div>
        <div class="flex-grow-1">
          <div class="d-flex justify-content-between flex-wrap gap-2">
            <div>
              <div class="cell-title fs-13"><span class="priority-dot ${prClass[f.priority]}"></span>${f.customer}</div>
              <div class="cell-sub">${f.subject}</div>
            </div>
            <span class="fs-11 text-muted"><i class="bi bi-person me-1"></i>${f.owner}</span>
          </div>
          <div class="d-flex align-items-center gap-2 mt-2 flex-wrap">
            <label class="fs-11 text-muted mb-0">Next:</label>
            <input type="date" class="fu-date-input" value="${f.next}" onchange="updateNextDate(${i}, this.value)">
            <select class="status-pick ms-auto" onchange="updateStatus(${i}, this.value)">
              <option value="pending" ${f.status==='pending'?'selected':''}>Pending</option>
              <option value="completed" ${f.status==='completed'?'selected':''}>Completed</option>
            </select>
          </div>
        </div>
      </div>
    </div>`).join('') || `<div class="empty-state"><div class="es-icon"><i class="bi bi-telephone"></i></div><h6>No follow ups</h6><p>Nothing scheduled in this date range / category.</p></div>`;
}

function updateStatus(i, status){
  followupsList[i].status = status;
  renderFuList();
  KTG.toast(`Follow up marked as "${status}".`, 'success', 'Status Updated');
}

function updateNextDate(i, date){
  followupsList[i].next = date;
  renderFuList();
  renderCalendar();
  KTG.toast('Next follow up date updated.', 'success', 'Follow Up Rescheduled');
}

function exportFollowUps(){
  const items = followupsList.filter(f => (activeFuTab === 'all' || f.type === activeFuTab) && inRange(f.next));
  const rows = [['Customer','Type','Subject','Next Follow Up','Priority','Status','Owner']];
  items.forEach(f => rows.push([f.customer, f.type, f.subject, f.next, f.priority, f.status, f.owner]));
  KTG.exportCsv('Followups.csv', rows);
  KTG.toast('Follow ups exported as CSV (opens in Excel).', 'success', 'Export Ready');
}
JS;
?>
<?php include __DIR__ . '/components/footer.php'; ?>
