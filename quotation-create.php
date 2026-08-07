<?php
$pageTitle = "Create Quotation";
$pageKey = "quotations";
$extraHead = <<<'HTML'
<style>
.item-table input, .item-table select{ font-size:12.8px; padding:.4rem .6rem; }
.item-table th{ font-size:10.8px; }
.sign-box{ border:1.5px dashed var(--border); border-radius:12px; height:80px; display:flex; align-items:center; justify-content:center; color:var(--text-muted); font-size:12px; }
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
        <div class="bill-letterhead">
          <div class="text-center">
            <div class="bl-logo mx-auto"><img src="assets/images/logo.png" alt="KTG"></div>
            <div class="bl-tagline">Give Us Your Hands …!</div>
          </div>
          <div class="bl-right">
            <div class="bill-doctype">QUOTATION</div>
            <div class="fs-12 text-muted">GST : 33ABCDE1234F1Z5</div>
            <div class="bl-company">Knock The Globe Technologies</div>
            <div class="bl-meta">4th Cross Rd, Vaiyapuri Nagar<br>Karur, Tamil Nadu 639002<br>India</div>
            <div class="bl-meta mt-1">Mobile: 91 72001 23452<br>www.knocktheglobe.com</div>
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
        <div class="table-responsive mb-4">
          <table class="table table-modern item-table mb-0">
            <thead>
              <tr><th style="min-width:240px;">Product</th><th class="col-qty">Qty</th><th style="width:120px;">Price</th><th style="width:130px;">Amount</th><th style="width:40px;"></th></tr>
            </thead>
            <tbody id="itemsBody"></tbody>
          </table>
        </div>

        <div class="row g-3 mb-4">
          <div class="col-md-7">
            <div class="bill-block-label mb-1">Notes / Terms</div>
            <textarea class="form-control" rows="4" id="termsText">Quotation valid for 15 days from the date of issue. 50% advance required to begin work; balance on delivery. Third-party licenses (domain/hosting) billed separately.</textarea>
          </div>
          <div class="col-md-5">
            <div class="bill-totals">
              <div class="bt-row bt-grand"><span>Total Amount</span><span id="sumTotal">₹0</span></div>
            </div>
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

function init(){
  const custSelect = document.getElementById('customerSelect');
  custSelect.innerHTML = '<option value="">Select customer...</option>' + KTG.DATA.customers.map(c => `<option value="${c.name}">${c.name}</option>`).join('');
  custSelect.value = 'Zen Foods Pvt Ltd';
  custSelect.addEventListener('change', onCustomerChange);
  onCustomerChange();

  items = [
    {service:'Website Designing', desc:'Custom homepage + 5 inner pages, responsive UI', qty:1, rate:65000},
    {service:'Web Development', desc:'WordPress based content management setup', qty:1, rate:45000},
    {service:'SEO Services', desc:'On-page SEO, meta tags, sitemap, analytics', qty:1, rate:18000},
    {service:'Web Hosting', desc:'1 year hosting configuration & SSL', qty:1, rate:6000},
  ];
  renderItems();

  document.getElementById('addItemBtn').addEventListener('click', () => {
    const svc = KTG.Masters.services()[0];
    items.push({service: svc.name, desc:'', qty:1, rate: svc.rate});
    renderItems();
  });

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
  document.getElementById('itemsBody').innerHTML = items.map((it, i) => `
    <tr>
      <td>
        <select class="form-select mb-1" onchange="onServiceChange(${i}, this.value)">
          ${services.map(s => `<option value="${s.name}" ${s.name === it.service ? 'selected' : ''}>${s.name}</option>`).join('')}
        </select>
        <input class="form-control form-control-sm" placeholder="Description / link (optional)" value="${it.desc}" onchange="items[${i}].desc=this.value">
      </td>
      <td><input type="number" class="form-control" value="${it.qty}" min="1" onchange="items[${i}].qty=+this.value; calcTotals();"></td>
      <td><input type="number" class="form-control" value="${it.rate}" onchange="items[${i}].rate=+this.value; calcTotals();"></td>
      <td class="fw-600 item-amount" data-i="${i}">₹0</td>
      <td><button class="btn btn-sm btn-light" onclick="items.splice(${i},1); renderItems();"><i class="bi bi-trash text-danger"></i></button></td>
    </tr>`).join('');
  calcTotals();
}

function onServiceChange(i, name){
  const svc = KTG.Masters.services().find(s => s.name === name);
  if (svc) { items[i].service = svc.name; items[i].rate = svc.rate; }
  renderItems();
}

function calcTotals(){
  let subtotal = 0;
  items.forEach((it, i) => {
    const amount = it.qty * it.rate;
    subtotal += amount;
    const cell = document.querySelector(`.item-amount[data-i="${i}"]`);
    if (cell) cell.textContent = KTG.money(Math.round(amount));
  });
  document.getElementById('sumTotal').textContent = KTG.money(Math.round(subtotal));
}

function openPreview(autoprint){
  const name = document.getElementById('customerSelect').value;
  const c = KTG.DATA.customers.find(x => x.name === name);
  let total = 0;
  items.forEach(it => total += it.qty * it.rate);

  const data = {
    quoNumber: 'QUO-2026-0091',
    quoDate: document.getElementById('quoDate').value,
    expiryDate: document.getElementById('expiryDate').value,
    customer: c ? {name: c.name, gst: c.gst, address: c.address, mobile: c.mobile} : {name: name || '—', gst: '—', address: '—', mobile: '—'},
    items: items,
    total: Math.round(total),
    terms: document.getElementById('termsText').value,
    bank: {name:'Knock The Globe Technologies', account:'0012299171', bank:'Kotak Bank', ifsc:'KKBK0008775'}
  };
  sessionStorage.setItem('ktg_quotation_preview', JSON.stringify(data));
  window.open('quotation-preview.php' + (autoprint ? '?autoprint=1' : ''), '_blank');
}
JS;
?>
<?php include __DIR__ . '/components/footer.php'; ?>
