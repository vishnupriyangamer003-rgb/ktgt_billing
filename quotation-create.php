<?php
$pageTitle = "Create Quotation";
$pageKey = "quotations";
$extraHead = <<<'HTML'
<style>
.item-table input, .item-table select{ font-size:12.8px; padding:.4rem .6rem; }
.item-table th{ font-size:10.8px; }
.sign-box{ border:1.5px dashed var(--border); border-radius:12px; height:80px; display:flex; align-items:center; justify-content:center; color:var(--text-muted); font-size:12px; }

/* Collapsed-by-default company letterhead accordion */
.bill-accordion-head{ display:flex; align-items:center; justify-content:space-between; gap:1rem; padding-bottom:1rem; margin-bottom:1rem; border-bottom:2px solid var(--secondary); cursor:pointer; }
.bill-accordion-head .bah-title{ display:flex; align-items:center; gap:.6rem; flex-wrap:wrap; }
.bill-accordion-head .bill-doctype{ margin:0; text-align:left; }
.bill-accordion-head .bah-toggle i{ transition: transform .2s var(--ease); }
.bill-accordion-head[aria-expanded="true"] .bah-toggle i{ transform: rotate(180deg); }
#letterheadBody .bill-letterhead{ border-bottom:none; padding-bottom:0; margin-bottom:1rem; }

/* ---- Mobile item entry cards (Sample 2 design) — mobile viewport only ---- */
.ie2-item{ border:1px solid var(--border-soft); border-radius:var(--radius-lg); padding:1.1rem 1.2rem; margin-bottom:1rem; background:var(--surface-alt); position:relative; }
.ie2-item .ie2-remove{ position:absolute; top:.7rem; right:.7rem; }
.ie2-field{ margin-bottom:.75rem; }
.ie2-field label{ font-size:11px; font-weight:700; color:var(--text-secondary); text-transform:uppercase; letter-spacing:.03em; margin-bottom:.3rem; display:block; }
.ie2-2col{ display:grid; grid-template-columns:1fr 1fr; gap:.75rem; }
.ie2-amount-bar{ display:flex; justify-content:space-between; align-items:center; background:#fff; border:1px dashed var(--border); border-radius:10px; padding:.6rem .9rem; margin-top:.5rem; }
.ie2-amount-bar .val{ font-weight:800; font-size:15px; color:var(--primary); }

/* Desktop (old table) vs Mobile (new cards) — only one renders per viewport */
.itc-mobile{ display:none; }
@media (max-width: 767.98px){
  .itc-desktop{ display:none !important; }
  .itc-mobile{ display:block; }
}
</style>
HTML;
include __DIR__ . '/components/header.php';
?>
      <div class="page-head fade-in-up">
        <div>
          <h1>Create Quotation</h1>
          <nav class="breadcrumb"><span class="breadcrumb-item"><a href="index.php">Dashboard</a></span><span class="breadcrumb-item"><a href="quotation-list.php">Quotations</a></span><span class="breadcrumb-item active">New</span></nav>
        </div>
        <div class="d-flex gap-2 flex-wrap">
          <button class="btn btn-light" id="previewBtn"><i class="bi bi-eye me-1"></i>Preview</button>
          <button class="btn btn-light" id="printBtn"><i class="bi bi-printer me-1"></i>Print</button>
          <button class="btn btn-light" id="downloadBtn"><i class="bi bi-download me-1"></i>Download PDF</button>
          <button class="btn btn-light"><i class="bi bi-files me-1"></i>Duplicate</button>
          <button class="btn btn-outline-primary"><i class="bi bi-envelope me-1"></i>Send Email</button>
          <select class="form-select form-select-sm" style="max-width:150px;" id="statusSelect">
            <option>Draft</option><option>Sent</option><option>Viewed</option><option selected>Approved</option><option>Rejected</option><option>Expired</option>
          </select>
          <button class="btn btn-primary" id="convertBtn"><i class="bi bi-arrow-repeat me-1"></i>Convert to Invoice</button>
        </div>
      </div>

      <!-- Bill sheet — mirrors the printed quotation design -->
      <div class="bill-sheet fade-in-up mb-3">
        <div class="bill-accordion-head" data-bs-toggle="collapse" data-bs-target="#letterheadBody" role="button" aria-expanded="false" aria-controls="letterheadBody" id="letterheadToggle">
          <div class="bah-title">
            <div class="bill-doctype">QUOTATION</div>
            <span class="fs-12 text-muted">Knock The Globe Technologies &middot; GST : 33ABCDE1234F1Z5</span>
          </div>
          <button class="btn btn-sm btn-light bah-toggle" type="button" tabindex="-1"><i class="bi bi-chevron-down me-1"></i>Company Details</button>
        </div>
        <div class="collapse" id="letterheadBody">
          <div class="bill-letterhead">
            <div class="text-center">
              <div class="bl-logo mx-auto"><img src="assets/images/logo.png" alt="KTG"></div>
              <div class="bl-tagline">Give Us Your Hands …!</div>
            </div>
            <div class="bl-right">
              <div class="fs-12 text-muted">GST : 33ABCDE1234F1Z5</div>
              <div class="bl-company">Knock The Globe Technologies</div>
              <div class="bl-meta">4th Cross Rd, Vaiyapuri Nagar<br>Karur, Tamil Nadu 639002<br>India</div>
              <div class="bl-meta mt-1">Mobile: 91 72001 23452<br>www.knocktheglobe.com</div>
            </div>
          </div>
        </div>

        <div class="row g-4 mb-4">
          <div class="col-md-6">
            <div class="bill-block-label">Bill To</div>
            <select class="form-select mb-2" id="customerSelect"><option value="">Select customer...</option></select>
            <div id="customerMeta" class="bill-to-meta">Select a customer to auto-fill billing details.</div>
          </div>
          <div class="col-md-6">
            <table class="bill-meta-table w-100">
              <tr><td>Quotation Number</td><td>QUO-2026-0091</td></tr>
              <tr><td>Quotation Date</td><td><input type="date" class="form-control form-control-sm d-inline-block w-auto" id="quoDate" value="2026-07-20"></td></tr>
              <tr><td>Expiry Date</td><td><input type="date" class="form-control form-control-sm d-inline-block w-auto" id="expiryDate" value="2026-08-05"></td></tr>
            </table>
          </div>
        </div>

        <div class="d-flex align-items-center justify-content-between mb-2">
          <div class="bill-block-label mb-0">Items &amp; Services</div>
          <button class="btn btn-sm btn-soft-primary" id="addItemBtn"><i class="bi bi-plus-lg me-1"></i>Add Item</button>
        </div>

        <!-- Desktop: classic table rows (unchanged from the original design) -->
        <div class="table-responsive mb-4 itc-desktop">
          <table class="table table-modern item-table mb-0">
            <thead>
              <tr><th style="min-width:240px;">Product</th><th class="col-qty">Qty</th><th style="width:120px;">Price</th><th style="width:130px;">Amount</th><th style="width:40px;"></th></tr>
            </thead>
            <tbody id="itemsBodyDesktop"></tbody>
          </table>
        </div>

        <!-- Mobile: card-based entry rows -->
        <div class="itc-mobile mb-4" id="itemsBodyMobile"></div>

        <div class="row g-3 mb-4">
          <div class="col-md-7">
            <div class="bill-block-label mb-1">Notes / Terms</div>
            <textarea class="form-control" rows="4" id="termsText">Quotation valid for 15 days from the date of issue. 50% advance required to begin work; balance on delivery. Third-party licenses (domain/hosting) billed separately.</textarea>
          </div>
          <div class="col-md-5">
            <div class="bill-totals">
              <div class="bt-row"><span class="text-muted">Subtotal</span><span class="fw-600" id="sumSubtotal">₹0</span></div>
              <div class="bt-row"><span class="text-muted">Discount (₹)</span>
                <span class="fw-600"><input type="number" class="form-control form-control-sm text-end" style="width:120px; display:inline-block;" id="discountAmount" placeholder="0" min="0"></span>
              </div>
              <div class="bt-row" id="roundoffRow"><span class="text-muted">Round Off</span><span class="fw-600" id="sumRoundoff">₹0</span></div>
              <div class="bt-row bt-grand"><span>Total Amount</span>
                <span class="fw-800"><input type="number" class="form-control form-control-sm text-end fw-800" style="width:130px; display:inline-block; font-size:1.05rem;" id="grandTotalInput" value="0"></span>
              </div>
            </div>
            <div class="fs-11 text-muted mt-1"><i class="bi bi-info-circle me-1"></i>Edit Total to auto-adjust the discount, or edit Discount to recompute the Total.</div>
          </div>
        </div>

        <div class="row g-3">
          <div class="col-md-6">
            <div class="bill-block-label">Authorized Signature</div>
            <div class="sign-box"><i class="bi bi-pen me-2"></i>Signature will appear here</div>
          </div>
          <div class="col-md-6">
            <div class="bill-block-label">Company Seal</div>
            <div class="sign-box"><i class="bi bi-patch-check me-2"></i>Company seal / stamp</div>
          </div>
        </div>
      </div>

      <div class="section-card mb-3 fade-in-up">
        <div class="section-body py-3">
          <button class="btn btn-primary"><i class="bi bi-save me-1"></i>Save Quotation</button>
          <button class="btn btn-light">Save as Draft</button>
        </div>
      </div>

<?php
$pageScript = <<<'JS'
document.addEventListener('DOMContentLoaded', () => setTimeout(init, 150));

let items = [];

function emptyItem(){ return {service:'', desc:'', qty:'', rate:''}; }

function init(){
  const custSelect = document.getElementById('customerSelect');
  custSelect.innerHTML = '<option value="">Select customer...</option>' + KTG.DATA.customers.map(c => `<option value="${c.name}">${c.name}</option>`).join('');
  custSelect.addEventListener('change', onCustomerChange);
  onCustomerChange();

  items = [emptyItem()];
  renderItems();

  document.getElementById('addItemBtn').addEventListener('click', () => {
    items.push(emptyItem());
    renderItems();
  });

  document.getElementById('discountAmount').addEventListener('input', calcTotals);
  document.getElementById('grandTotalInput').addEventListener('input', onGrandTotalChange);
  document.getElementById('convertBtn').addEventListener('click', () => window.location.href = 'invoice-create.php');
  document.getElementById('previewBtn').addEventListener('click', () => openPreview(false));
  document.getElementById('printBtn').addEventListener('click', () => openPreview(true));
  document.getElementById('downloadBtn').addEventListener('click', () => openPreview(true));
}

function onCustomerChange(){
  const name = document.getElementById('customerSelect').value;
  const c = KTG.DATA.customers.find(x => x.name === name);
  const meta = document.getElementById('customerMeta');
  if (!c) { meta.textContent = 'Select a customer to auto-fill billing details.'; return; }
  meta.innerHTML = `<span class="bill-to-name">${c.name}</span><br>GST : ${c.gst}<br>${c.address}<br>${c.mobile}`;
}

function renderItems(){
  const services = KTG.Masters.services();
  const serviceOptions = (it) => `<option value="" ${it.service ? '' : 'selected'} disabled>Select Product</option>` +
    services.map(s => `<option value="${s.name}" ${s.name === it.service ? 'selected' : ''}>${s.name}</option>`).join('');

  document.getElementById('itemsBodyDesktop').innerHTML = items.map((it, i) => `
    <tr>
      <td>
        <select class="form-select mb-1 no-search" onchange="onServiceChange(${i}, this.value)">
          ${serviceOptions(it)}
        </select>
        <input class="form-control form-control-sm" placeholder="Description / link (optional)" value="${it.desc}" onchange="items[${i}].desc=this.value">
      </td>
      <td><input type="number" class="form-control" placeholder="0" value="${it.qty}" min="1" onchange="items[${i}].qty=+this.value; calcTotals();"></td>
      <td><input type="number" class="form-control" placeholder="0" value="${it.rate}" onchange="items[${i}].rate=+this.value; calcTotals();"></td>
      <td class="fw-600 item-amount" data-i="${i}">₹0</td>
      <td><button class="btn btn-sm btn-light" onclick="removeItem(${i})"><i class="bi bi-trash text-danger"></i></button></td>
    </tr>`).join('');

  document.getElementById('itemsBodyMobile').innerHTML = items.map((it, i) => `
    <div class="ie2-item">
      <button class="btn btn-sm btn-light ie2-remove" onclick="removeItem(${i})"><i class="bi bi-trash text-danger"></i></button>
      <div class="ie2-field">
        <label>Product</label>
        <select class="form-select no-search" onchange="onServiceChange(${i}, this.value)">
          ${serviceOptions(it)}
        </select>
      </div>
      <div class="ie2-field">
        <label>Description</label>
        <textarea class="form-control" rows="2" onchange="items[${i}].desc=this.value">${it.desc}</textarea>
      </div>
      <div class="ie2-2col">
        <div class="ie2-field mb-0"><label>Qty</label><input type="number" class="form-control" placeholder="0" value="${it.qty}" onchange="items[${i}].qty=+this.value; calcTotals();"></div>
        <div class="ie2-field mb-0"><label>Rate (₹)</label><input type="number" class="form-control" placeholder="0" value="${it.rate}" onchange="items[${i}].rate=+this.value; calcTotals();"></div>
      </div>
      <div class="ie2-amount-bar"><span class="text-muted fs-13">Line Amount</span><span class="val item-amount" data-i="${i}">₹0</span></div>
    </div>`).join('');

  calcTotals();
}

function removeItem(i){
  items.splice(i, 1);
  if (!items.length) items.push(emptyItem());
  renderItems();
}

function onServiceChange(i, name){
  const svc = KTG.Masters.services().find(s => s.name === name);
  if (!svc) return;
  items[i].service = svc.name;
  items[i].rate = svc.rate;
  if (!items[i].qty) items[i].qty = 1;
  if (i === items.length - 1) items.push(emptyItem());
  renderItems();
}

function itemsSubtotal(list){
  return list.reduce((s, it) => s + (+it.qty || 0) * (+it.rate || 0), 0);
}

function calcTotals(){
  let subtotal = 0;
  items.forEach((it, i) => {
    const amount = (+it.qty || 0) * (+it.rate || 0);
    subtotal += amount;
    document.querySelectorAll(`.item-amount[data-i="${i}"]`).forEach(cell => cell.textContent = KTG.money(Math.round(amount)));
  });

  const discount = Math.min(Math.max(+document.getElementById('discountAmount').value || 0, 0), subtotal);
  const rawTotal = subtotal - discount;
  const roundedTotal = Math.round(rawTotal);
  const roundOff = roundedTotal - rawTotal;

  document.getElementById('sumSubtotal').textContent = KTG.money(Math.round(subtotal));
  document.getElementById('roundoffRow').classList.toggle('bt-hidden', Math.abs(roundOff) <= 0.001);
  document.getElementById('sumRoundoff').textContent = (roundOff >= 0 ? '+' : '−') + KTG.money(Math.abs(roundOff));
  document.getElementById('grandTotalInput').value = roundedTotal;
}

function onGrandTotalChange(){
  const target = +document.getElementById('grandTotalInput').value || 0;
  const subtotal = itemsSubtotal(items);
  const discount = Math.min(Math.max(subtotal - target, 0), subtotal);
  document.getElementById('discountAmount').value = Math.round(discount);
  calcTotals();
}

function openPreview(autoprint){
  const name = document.getElementById('customerSelect').value;
  const c = KTG.DATA.customers.find(x => x.name === name);
  const realItems = items.filter(it => it.service);
  const subtotal = itemsSubtotal(realItems);
  const discount = Math.min(Math.max(+document.getElementById('discountAmount').value || 0, 0), subtotal);
  const rawTotal = subtotal - discount;
  const total = Math.round(rawTotal);
  const roundOff = total - rawTotal;

  const data = {
    quoNumber: 'QUO-2026-0091',
    quoDate: document.getElementById('quoDate').value,
    expiryDate: document.getElementById('expiryDate').value,
    customer: c ? {name: c.name, gst: c.gst, address: c.address, mobile: c.mobile} : {name: name || '—', gst: '—', address: '—', mobile: '—'},
    items: realItems,
    subtotal: Math.round(subtotal), discount: Math.round(discount), roundOff,
    total,
    terms: document.getElementById('termsText').value,
    bank: {name:'Knock The Globe Technologies', account:'0012299171', bank:'Kotak Bank', ifsc:'KKBK0008775'}
  };
  sessionStorage.setItem('ktg_quotation_preview', JSON.stringify(data));
  window.open('quotation-preview.php' + (autoprint ? '?autoprint=1' : ''), '_blank');
}
JS;
?>
<?php include __DIR__ . '/components/footer.php'; ?>
