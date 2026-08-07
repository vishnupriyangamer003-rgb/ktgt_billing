<?php
$pageTitle = "Mobile List Design · Sample";
$pageKey = "";
$extraHead = <<<'HEAD'
<style>
  .preview-toolbar{ display:flex; align-items:center; gap:.6rem; flex-wrap:wrap; }
  .preview-toolbar .btn-group .btn{ font-size:12.5px; }
  .sample-callout{ background: var(--primary-light); border:1px solid var(--primary); border-radius: var(--radius-lg); padding:1rem 1.2rem; margin-bottom:1.25rem; }
  .sample-callout h6{ color:var(--primary); font-weight:700; margin-bottom:.5rem; }
  .sample-callout ul{ margin:0; padding-left:1.1rem; font-size:12.8px; color:var(--text-secondary); }
  .sample-callout ul li{ margin-bottom:.2rem; }

  #previewRoot{ transition: max-width .2s ease; margin:0 auto; }
  #previewRoot[data-preview="mobile"]{ max-width:420px; }
  #previewRoot[data-preview="mobile"] .ls-table-wrap{ display:none; }
  #previewRoot[data-preview="desktop"] .ls-card-list{ display:none; }

  /* ---- New mobile-friendly card-list pattern (replaces DataTable on small screens) ---- */
  .ls-card-list{ padding: .25rem; }
  .ls-card{ background:#fff; border:1px solid var(--border-soft); border-radius:var(--radius-lg); padding:1rem 1.1rem; margin-bottom:.85rem; box-shadow:var(--shadow-sm); }
  .ls-card-top{ display:flex; align-items:center; gap:.7rem; margin-bottom:.8rem; }
  .ls-avatar{ width:40px; height:40px; border-radius:11px; background:var(--primary-light); color:var(--primary); display:flex; align-items:center; justify-content:center; font-weight:700; font-size:13px; flex-shrink:0; }
  .ls-card-title{ flex:1; min-width:0; }
  .ls-name{ font-weight:700; font-size:13.8px; color:var(--text-primary); white-space:nowrap; overflow:hidden; text-overflow:ellipsis; }
  .ls-sub{ font-size:11.6px; color:var(--text-muted); margin-top:.1rem; white-space:nowrap; overflow:hidden; text-overflow:ellipsis; }
  .ls-meta{ display:flex; flex-wrap:wrap; gap:.5rem .6rem; margin-bottom:.85rem; padding-bottom:.85rem; border-bottom:1px dashed var(--border-soft); }
  .ls-meta-item{ display:flex; align-items:center; gap:.35rem; font-size:11.8px; color:var(--text-secondary); background:var(--surface-alt); padding:.3rem .6rem; border-radius:20px; max-width:100%; }
  .ls-meta-item i{ color:var(--text-muted); font-size:12px; flex-shrink:0; }
  .ls-actions{ display:flex; gap:.5rem; flex-wrap:wrap; }
  .ls-actions .btn{ flex:1 1 auto; min-width:78px; font-size:12px; }

  @media (max-width: 767.98px){
    .ls-table-wrap{ display:none !important; }
    .ls-card-list{ display:block !important; }
  }
</style>
HEAD;
include __DIR__ . '/components/header.php';
?>
      <div class="page-head fade-in-up">
        <div>
          <h1>Mobile List Design</h1>
          <nav class="breadcrumb"><span class="breadcrumb-item"><a href="index.php">Dashboard</a></span><span class="breadcrumb-item active">Mobile List Sample</span></nav>
        </div>
        <div class="preview-toolbar">
          <span class="fs-13 text-muted me-1">Preview as:</span>
          <div class="btn-group" role="group">
            <button type="button" class="btn btn-light" id="btnPreviewDesktop"><i class="bi bi-laptop me-1"></i>Desktop (current table)</button>
            <button type="button" class="btn btn-light" id="btnPreviewMobile"><i class="bi bi-phone me-1"></i>Mobile (new card list)</button>
          </div>
        </div>
      </div>

      <div class="sample-callout fade-in-up">
        <h6><i class="bi bi-info-circle me-1"></i>What this sample demonstrates</h6>
        <ul>
          <li>No horizontal scrolling — every field wraps vertically or into pill chips, never runs off-screen.</li>
          <li>No expand/collapse rows or accordions — all key information is visible immediately on the card.</li>
          <li>Actions (View / Edit / Delete) are always visible as real buttons, not hidden behind a menu tap.</li>
          <li>Same pattern works for any list — shown here with both Customers and Invoices data.</li>
          <li>On an actual phone (&lt;768px width) this card list appears automatically; the toggle above is just for reviewing it on a desktop monitor.</li>
        </ul>
      </div>

      <div id="previewRoot" data-preview="desktop">

        <div class="section-card mb-4 fade-in-up">
          <div class="section-head"><h5>Customers</h5></div>
          <div class="section-body">
            <div class="ls-table-wrap table-responsive">
              <table class="table table-modern align-middle mb-0" id="custSampleTable">
                <thead><tr><th>#</th><th>Customer</th><th>Contact</th><th>Type</th><th>GST</th><th>Status</th><th>Created</th><th class="text-end">Actions</th></tr></thead>
                <tbody id="custTableBody"></tbody>
              </table>
            </div>
            <div class="ls-card-list" id="custCardList"></div>
          </div>
        </div>

        <div class="section-card mb-4 fade-in-up">
          <div class="section-head"><h5>Invoices</h5></div>
          <div class="section-body">
            <div class="ls-table-wrap table-responsive">
              <table class="table table-modern align-middle mb-0" id="invSampleTable">
                <thead><tr><th>#</th><th>Invoice No.</th><th>Customer</th><th>Date</th><th>Due Date</th><th>Amount</th><th>Status</th><th class="text-end">Actions</th></tr></thead>
                <tbody id="invTableBody"></tbody>
              </table>
            </div>
            <div class="ls-card-list" id="invCardList"></div>
          </div>
        </div>

      </div>
<?php
$pageScript = <<<'JS'
function initials(name){
  return name.split(/\s+/).filter(Boolean).slice(0, 2).map(w => w[0].toUpperCase()).join('');
}
function custStatusChip(s){
  const cls = s === 'active' ? 'active' : s === 'pending' ? 'pending' : 'cancelled';
  return `<span class="chip chip-${cls}">${s}</span>`;
}

function renderCustomers(){
  const list = KTG.DATA.customers;

  document.getElementById('custTableBody').innerHTML = list.map((c, i) => `
    <tr>
      <td>${i + 1}</td>
      <td><div class="fw-600">${c.name}</div><div class="fs-12 text-muted">${c.city}</div></td>
      <td><div>${c.mobile}</div><div class="fs-12 text-muted">${c.email}</div></td>
      <td><span class="chip chip-inprogress">${c.type}</span></td>
      <td class="fs-13">${c.gst}</td>
      <td>${custStatusChip(c.status)}</td>
      <td class="fs-13">${c.created}</td>
      <td class="text-end">
        <button class="btn btn-sm btn-light" title="View"><i class="bi bi-eye"></i></button>
        <button class="btn btn-sm btn-light" title="Edit"><i class="bi bi-pencil"></i></button>
        <button class="btn btn-sm btn-light text-danger" title="Delete"><i class="bi bi-trash"></i></button>
      </td>
    </tr>`).join('');

  document.getElementById('custCardList').innerHTML = list.map(c => `
    <div class="ls-card">
      <div class="ls-card-top">
        <div class="ls-avatar">${initials(c.name)}</div>
        <div class="ls-card-title">
          <div class="ls-name">${c.name}</div>
          <div class="ls-sub">${c.contact} &middot; ${c.city}</div>
        </div>
        ${custStatusChip(c.status)}
      </div>
      <div class="ls-meta">
        <span class="ls-meta-item"><i class="bi bi-telephone"></i>${c.mobile}</span>
        <span class="ls-meta-item"><i class="bi bi-tag"></i>${c.type}</span>
        <span class="ls-meta-item"><i class="bi bi-calendar3"></i>${c.created}</span>
      </div>
      <div class="ls-actions">
        <button class="btn btn-sm btn-soft-primary"><i class="bi bi-eye me-1"></i>View</button>
        <button class="btn btn-sm btn-light"><i class="bi bi-pencil me-1"></i>Edit</button>
        <button class="btn btn-sm btn-light text-danger"><i class="bi bi-trash me-1"></i>Delete</button>
      </div>
    </div>`).join('');
}

function renderInvoices(){
  const list = KTG.DATA.invoices;

  document.getElementById('invTableBody').innerHTML = list.map((inv, i) => `
    <tr>
      <td>${i + 1}</td>
      <td class="fw-600">${inv.id}</td>
      <td>${inv.customer}</td>
      <td class="fs-13">${inv.date}</td>
      <td class="fs-13">${inv.due}</td>
      <td class="fw-600">${KTG.money(inv.amount)}</td>
      <td><span class="chip chip-${inv.status}">${inv.status}</span></td>
      <td class="text-end">
        <button class="btn btn-sm btn-light" title="View"><i class="bi bi-eye"></i></button>
        <button class="btn btn-sm btn-light" title="Download"><i class="bi bi-download"></i></button>
        <button class="btn btn-sm btn-light text-danger" title="Delete"><i class="bi bi-trash"></i></button>
      </td>
    </tr>`).join('');

  document.getElementById('invCardList').innerHTML = list.map(inv => `
    <div class="ls-card">
      <div class="ls-card-top">
        <div class="ls-avatar"><i class="bi bi-receipt"></i></div>
        <div class="ls-card-title">
          <div class="ls-name">${inv.id}</div>
          <div class="ls-sub">${inv.customer}</div>
        </div>
        <span class="chip chip-${inv.status}">${inv.status}</span>
      </div>
      <div class="ls-meta">
        <span class="ls-meta-item"><i class="bi bi-calendar3"></i>${inv.date}</span>
        <span class="ls-meta-item"><i class="bi bi-calendar-event"></i>Due ${inv.due}</span>
        <span class="ls-meta-item"><i class="bi bi-cash-stack"></i>${KTG.money(inv.amount)}</span>
      </div>
      <div class="ls-actions">
        <button class="btn btn-sm btn-soft-primary"><i class="bi bi-eye me-1"></i>View</button>
        <button class="btn btn-sm btn-light"><i class="bi bi-download me-1"></i>PDF</button>
        <button class="btn btn-sm btn-light text-danger"><i class="bi bi-trash me-1"></i>Delete</button>
      </div>
    </div>`).join('');
}

function setPreview(mode){
  document.getElementById('previewRoot').setAttribute('data-preview', mode);
  document.getElementById('btnPreviewDesktop').classList.toggle('btn-primary', mode === 'desktop');
  document.getElementById('btnPreviewDesktop').classList.toggle('btn-light', mode !== 'desktop');
  document.getElementById('btnPreviewMobile').classList.toggle('btn-primary', mode === 'mobile');
  document.getElementById('btnPreviewMobile').classList.toggle('btn-light', mode !== 'mobile');
}

document.addEventListener('DOMContentLoaded', () => {
  renderCustomers();
  renderInvoices();
  setPreview(window.matchMedia('(max-width: 767.98px)').matches ? 'mobile' : 'desktop');
  document.getElementById('btnPreviewDesktop').addEventListener('click', () => setPreview('desktop'));
  document.getElementById('btnPreviewMobile').addEventListener('click', () => setPreview('mobile'));
});
JS;
include __DIR__ . '/components/footer.php';
?>
