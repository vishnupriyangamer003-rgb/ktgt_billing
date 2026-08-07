<?php
$pageTitle = "Notifications";
$pageKey = "notifications";
include __DIR__ . '/components/header.php';
?>
      <div class="page-head fade-in-up">
        <div>
          <h1>Notifications</h1>
          <nav class="breadcrumb"><span class="breadcrumb-item"><a href="index.php">Dashboard</a></span><span class="breadcrumb-item active">Notifications</span></nav>
        </div>
        <div class="d-flex gap-2">
          <button class="btn btn-light" id="markAllBtn"><i class="bi bi-check-all me-1"></i>Mark All Read</button>
          <button class="btn btn-outline-primary" id="clearAllBtn"><i class="bi bi-trash me-1"></i>Clear All</button>
        </div>
      </div>

      <div class="row g-3">
        <div class="col-xl-5">
          <div class="section-card fade-in-up">
            <div class="section-head"><h5>All Notifications</h5><span class="chip chip-pending" id="unreadCountChip">0 unread</span></div>
            <div class="section-body" id="notifList" style="max-height:640px; overflow-y:auto;"></div>
          </div>
        </div>

        <div class="col-xl-7">
          <div class="section-card fade-in-up" id="detailCard">
            <div class="section-body" id="notifDetail"></div>
          </div>
        </div>
      </div>

<?php
$pageScript = <<<'JS'
document.addEventListener('DOMContentLoaded', () => setTimeout(init, 150));
let selectedId = null;

function init(){
  selectedId = Number(KTG.qs('id')) || null;
  render();

  document.getElementById('markAllBtn').addEventListener('click', () => {
    KTG.Notifications.markAllRead();
    KTG.renderNotifDropdown();
    render();
    KTG.toast('All notifications marked as read.', 'success');
  });
  document.getElementById('clearAllBtn').addEventListener('click', () => {
    KTG.confirmDialog({
      title: 'Clear all notifications?',
      body: 'This will remove every notification from your list.',
      confirmText: 'Clear All', confirmClass: 'btn-danger',
      onConfirm: () => {
        KTG.Notifications.clearAll();
        KTG.renderNotifDropdown();
        selectedId = null;
        render();
        KTG.toast('All notifications cleared.', 'success');
      }
    });
  });
}

function render(){
  const items = KTG.Notifications.list();
  if (!selectedId && items.length) selectedId = items[0].id;

  document.getElementById('unreadCountChip').textContent = items.filter(n=>!n.read).length + ' unread';

  document.getElementById('notifList').innerHTML = items.map(n => `
    <div class="notif-row ${n.read ? '' : 'unread'} ${n.id === selectedId ? 'active-row' : ''}" onclick="selectNotif(${n.id})">
      <div class="d-flex gap-3">
        <div class="avatar-sm bg-soft-${n.color}"><i class="bi ${n.icon}"></i></div>
        <div class="flex-grow-1">
          <div class="d-flex justify-content-between align-items-start gap-2">
            <div class="fw-700 fs-13">${n.title}</div>
            ${!n.read ? '<span class="dot-unread mt-1"></span>' : ''}
          </div>
          <div class="fs-12 text-muted mt-1">${n.desc}</div>
          <div class="fs-11 text-muted mt-2"><i class="bi bi-clock me-1"></i>${n.time} &middot; <span class="chip chip-inprogress">${n.type}</span></div>
        </div>
      </div>
    </div>`).join('') || `<div class="empty-state"><div class="es-icon"><i class="bi bi-bell-slash"></i></div><h6>No notifications</h6><p>You're all caught up.</p></div>`;

  renderDetail(items);
}

function selectNotif(id){
  selectedId = id;
  KTG.Notifications.markRead(id);
  KTG.renderNotifDropdown();
  render();
}

function renderDetail(items){
  const n = items.find(x => x.id === selectedId);
  const box = document.getElementById('notifDetail');
  if (!n) { box.innerHTML = `<div class="empty-state"><div class="es-icon"><i class="bi bi-bell"></i></div><h6>Select a notification</h6><p>Choose a notification from the list to view its details.</p></div>`; return; }
  box.innerHTML = `
    <div class="d-flex align-items-start gap-3 mb-3">
      <div class="avatar-lg bg-soft-${n.color}" style="width:56px;height:56px;font-size:22px;"><i class="bi ${n.icon}"></i></div>
      <div class="flex-grow-1">
        <span class="chip chip-inprogress mb-2">${n.type}</span>
        <h5 class="mb-1">${n.title}</h5>
        <div class="fs-12 text-muted"><i class="bi bi-clock me-1"></i>${n.time} &middot; Due ${n.dueDate}</div>
      </div>
      ${n.read ? '<span class="chip chip-completed">Read</span>' : '<span class="chip chip-pending">Unread</span>'}
    </div>
    <p class="fs-14 text-secondary">${n.desc}</p>
    <hr>
    <div class="d-flex gap-2 flex-wrap">
      <a href="${n.link}" class="btn btn-primary"><i class="bi bi-box-arrow-up-right me-1"></i>${n.linkLabel || 'View Details'}</a>
      ${!n.read ? `<button class="btn btn-light" onclick="selectNotif(${n.id})"><i class="bi bi-check-lg me-1"></i>Mark as Read</button>` : ''}
      <button class="btn btn-outline-danger ms-auto" onclick="clearOne(${n.id})"><i class="bi bi-trash me-1"></i>Clear Notification</button>
    </div>`;
}

function clearOne(id){
  KTG.Notifications.clear(id);
  KTG.renderNotifDropdown();
  selectedId = null;
  render();
  KTG.toast('Notification cleared.', 'success');
}
JS;
?>
<?php include __DIR__ . '/components/footer.php'; ?>
