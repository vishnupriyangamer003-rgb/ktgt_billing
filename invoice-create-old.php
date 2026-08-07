<?php
$pageTitle = "Create Invoice (Old Design — Archived)";
$pageKey = "invoices";
$extraHead = <<<'HTML'
<style>
.item-table input, .item-table select{ font-size:12.8px; padding:.4rem .6rem; }
.item-table th{ font-size:10.8px; }
.gst-toggle-btn{ flex:1; border:2px solid var(--border); border-radius:14px; padding:.8rem; text-align:center; cursor:pointer; transition: all .18s var(--ease); background:#fff; }
.gst-toggle-btn.active{ border-color: var(--primary); background: var(--primary-light); }
.gst-toggle-btn i{ font-size:1.3rem; }
</style>
HTML;
include __DIR__ . '/components/header.php';
?>
      <div class="page-head fade-in-up">
        <div>
          <h1>Create Invoice <span class="chip chip-cancelled ms-2">Old Design — Archived</span></h1>
          <nav class="breadcrumb"><span class="breadcrumb-item"><a href="index.php">Dashboard</a></span><span class="breadcrumb-item"><a href="invoice-list.php">Invoices</a></span><span class="breadcrumb-item active">Old Design</span></nav>
        </div>
        <div class="d-flex gap-2 flex-wrap">
          <button class="btn btn-light" id="previewBtn"><i class="bi bi-eye me-1"></i>Preview</button>
          <button class="btn btn-light" id="printBtn"><i class="bi bi-printer me-1"></i>Print</button>
          <button class="btn btn-light" id="downloadBtn"><i class="bi bi-download me-1"></i>Download PDF</button>
          <button class="btn btn-outline-primary"><i class="bi bi-send me-1"></i>Send</button>
          <button class="btn btn-outline-primary"><i class="bi bi-share me-1"></i>Share</button>
          <a class="btn btn-primary" id="paymentEntryLink" href="payments.php"><i class="bi bi-cash-coin me-1"></i>Payment Entry</a>
        </div>
      </div>

      <!-- Tax mode — single flow, chosen first -->
      <div class="section-card mb-3 fade-in-up">
        <div class="section-body py-3">
          <div class="row g-3 align-items-end">
            <div class="col-md-7">
              <div class="bill-block-label mb-2">Tax Mode</div>
              <div class="d-flex gap-3">
                <div class="gst-toggle-btn active" id="withGstBtn"><i class="bi bi-receipt-cutoff d-block mb-1 text-primary-KTG"></i><div class="fw-700 fs-14">With GST</div><div class="fs-11 text-muted">Tax Invoice</div></div>
                <div class="gst-toggle-btn" id="withoutGstBtn"><i class="bi bi-receipt d-block mb-1 text-secondary"></i><div class="fw-700 fs-14">Without GST</div><div class="fs-11 text-muted">Bill of Supply</div></div>
              </div>
            </div>
            <div class="col-md-5" id="gstOptionsWrap">
              <label class="form-label">GST Type</label>
              <select class="form-select mb-2" id="taxTypeSelect">
                <option value="sgst_cgst">SGST + CGST (Intra-state)</option>
                <option value="igst">IGST Only (Inter-state)</option>
              </select>
              <label class="form-label">GST Rate</label>
              <select class="form-select" id="taxSelect"></select>
            </div>
          </div>
        </div>
      </div>

      <!-- Bill sheet — mirrors the printed invoice design -->
      <div class="bill-sheet fade-in-up mb-3">
        <div class="bill-letterhead">
          <div class="text-center">
            <div class="bl-logo mx-auto"><img src="assets/images/logo.png" alt="KTG"></div>
            <div class="bl-tagline">Give Us Your Hands …!</div>
          </div>
          <div class="bl-right">
            <div class="bill-doctype" id="docTypeLabel">TAX INVOICE</div>
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
              <tr><td>Invoice Number</td><td>INV-2026-0150</td></tr>
              <tr><td>Invoice Date</td><td><input type="date" class="form-control form-control-sm d-inline-block w-auto" id="invDate" value="2026-07-25"></td></tr>
              <tr><td>Due Date</td><td><input type="date" class="form-control form-control-sm d-inline-block w-auto" id="dueDate" value="2026-08-08"></td></tr>
            </table>
          </div>
        </div>

        <div class="d-flex align-items-center justify-content-between mb-2">
          <div class="bill-block-label mb-0">Items &amp; Services</div>
          <button class="btn btn-sm btn-soft-primary" id="addItemBtn"><i class="bi bi-plus-lg me-1"></i>Add Item</button>
        </div>
        <div class="table-responsive mb-4">
          <table class="table table-modern item-table mb-0">
            <thead><tr><th style="min-width:240px;">Product</th><th class="col-qty">Qty</th><th style="width:120px;">Price</th><th style="width:130px;">Amount</th><th style="width:40px;"></th></tr></thead>
            <tbody id="itemsBody"></tbody>
          </table>
        </div>

        <div class="row g-3 mb-4">
          <div class="col-md-7">
            <div class="bill-block-label mb-1">Notes / Terms</div>
            <textarea class="form-control mb-3" rows="3" id="termsText">Payment due within the terms stated above. Late payments may attract a delay fee. Thank you for your business.</textarea>
          </div>
          <div class="col-md-5">
            <div class="bill-totals">
              <div class="bt-row"><span class="text-muted">Subtotal</span><span class="fw-600" id="sumSubtotal">₹0</span></div>
              <div class="bt-row" id="sgstRow"><span class="text-muted" id="sgstLabel">SGST 9%</span><span class="fw-600" id="sumSgst">₹0</span></div>
              <div class="bt-row" id="cgstRow"><span class="text-muted" id="cgstLabel">CGST 9%</span><span class="fw-600" id="sumCgst">₹0</span></div>
              <div class="bt-row" id="igstRow"><span class="text-muted" id="igstLabel">IGST 18%</span><span class="fw-600" id="sumIgst">₹0</span></div>
              <div class="bt-row bt-grand"><span>Total</span><span id="sumTotal">₹0</span></div>
              <div class="bt-row"><span class="text-muted">Advance Paid</span>
                <span class="fw-600 text-success"><input type="number" class="form-control form-control-sm text-end" style="width:110px; display:inline-block;" id="advancePaid" value="185000"></span>
              </div>
              <div class="bt-row"><span class="fw-700">Amount Due</span><span class="fw-700 text-danger" id="sumBalance">₹0</span></div>
            </div>
          </div>
        </div>
      </div>

      <div class="row g-3">
        <div class="col-md-6">
          <div class="section-card fade-in-up h-100">
            <div class="section-head"><h5>Status &amp; Save</h5></div>
            <div class="section-body">
              <select class="form-select mb-3">
                <option>Draft</option><option>Pending</option><option>Partially Paid</option><option selected>Paid</option><option>Overdue</option><option>Cancelled</option>
              </select>
              <button class="btn btn-primary w-100"><i class="bi bi-save me-1"></i>Save Invoice</button>
            </div>
          </div>
        </div>
        <div class="col-md-6">
          <div class="section-card fade-in-up h-100">
            <div class="section-head"><h5>Payment History</h5></div>
            <div class="table-responsive">
              <table class="table table-modern mb-0">
                <thead><tr><th>Payment ID</th><th>Date</th><th>Amount</th><th>Mode</th></tr></thead>
                <tbody>
                  <tr><td class="cell-title">PAY-1042</td><td>26 Jul 2026</td><td class="fw-600 text-success">₹1,85,000</td><td><span class="chip chip-inprogress">UPI</span></td></tr>
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>

<?php
$pageScript = <<<'JS'
document.addEventListener('DOMContentLoaded', () => setTimeout(init, 150));
let gstEnabled = true;
let items = [];

function init(){
  const custSelect = document.getElementById('customerSelect');
  custSelect.innerHTML = '<option value="">Select customer...</option>' + KTG.DATA.customers.map(c => `<option value="${c.name}">${c.name}</option>`).join('');
  custSelect.value = 'Zen Foods Pvt Ltd';
  custSelect.addEventListener('change', onCustomerChange);
  onCustomerChange();

  const taxSelect = document.getElementById('taxSelect');
  taxSelect.innerHTML = KTG.Masters.taxes().filter(t => t.percent > 0).map(t => `<option value="${t.percent}">${t.name}</option>`).join('');
  taxSelect.addEventListener('change', calcTotals);
  document.getElementById('taxTypeSelect').addEventListener('change', calcTotals);

  items = [
    {service:'Website Designing', desc:'Custom homepage + 5 inner pages', qty:1, rate:65000},
    {service:'Web Development', desc:'WordPress based CMS setup', qty:1, rate:45000},
    {service:'SEO Services', desc:'On-page SEO & analytics', qty:1, rate:20000},
    {service:'Web Hosting', desc:'1 year hosting + SSL', qty:1, rate:55000},
  ];
  renderItems();

  document.getElementById('addItemBtn').addEventListener('click', () => {
    const svc = KTG.Masters.services()[0];
    items.push({service: svc.name, desc:'', qty:1, rate: svc.rate});
    renderItems();
  });
  document.getElementById('withGstBtn').addEventListener('click', () => setGst(true));
  document.getElementById('withoutGstBtn').addEventListener('click', () => setGst(false));
  document.getElementById('advancePaid').addEventListener('input', calcTotals);
  document.getElementById('previewBtn').addEventListener('click', () => openPreview(false));
  document.getElementById('printBtn').addEventListener('click', () => openPreview(true));
  document.getElementById('downloadBtn').addEventListener('click', () => openPreview(true));
}

function onCustomerChange(){
  const name = document.getElementById('customerSelect').value;
  const c = KTG.DATA.customers.find(x => x.name === name);
  const meta = document.getElementById('customerMeta');
  document.getElementById('paymentEntryLink').href = name ? `payments.php?customer=${encodeURIComponent(name)}` : 'payments.php';
  if (!c) { meta.textContent = 'Select a customer to auto-fill billing details.'; return; }
  meta.innerHTML = `<span class="bill-to-name">${c.name}</span><br>GST : ${c.gst}<br>${c.address}<br>${c.mobile}`;
}

function setGst(state){
  gstEnabled = state;
  document.getElementById('withGstBtn').classList.toggle('active', state);
  document.getElementById('withoutGstBtn').classList.toggle('active', !state);
  document.getElementById('gstOptionsWrap').style.display = state ? 'block' : 'none';
  document.getElementById('docTypeLabel').textContent = state ? 'TAX INVOICE' : 'BILL OF SUPPLY';
  calcTotals();
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
      <td><input type="number" class="form-control" value="${it.qty}" onchange="items[${i}].qty=+this.value; calcTotals();"></td>
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

function currentTax(subtotal){
  const percent = gstEnabled ? (+document.getElementById('taxSelect').value || 0) : 0;
  const taxType = document.getElementById('taxTypeSelect').value;
  const taxAmount = subtotal * percent / 100;
  return { percent, taxType: gstEnabled ? taxType : 'none', taxAmount };
}

function calcTotals(){
  let subtotal = 0;
  items.forEach((it, i) => {
    const amount = it.qty * it.rate;
    subtotal += amount;
    const cell = document.querySelector(`.item-amount[data-i="${i}"]`);
    if (cell) cell.textContent = KTG.money(Math.round(amount));
  });

  const { percent, taxType, taxAmount } = currentTax(subtotal);
  const half = percent / 2;
  const grand = subtotal + taxAmount;
  const advance = +document.getElementById('advancePaid').value || 0;
  const due = Math.max(Math.round(grand - advance), 0);

  document.getElementById('sumSubtotal').textContent = KTG.money(Math.round(subtotal));
  document.getElementById('sgstLabel').textContent = `SGST ${half}%`;
  document.getElementById('cgstLabel').textContent = `CGST ${half}%`;
  document.getElementById('igstLabel').textContent = `IGST ${percent}%`;
  document.getElementById('sumSgst').textContent = KTG.money(Math.round(taxAmount/2));
  document.getElementById('sumCgst').textContent = KTG.money(Math.round(taxAmount/2));
  document.getElementById('sumIgst').textContent = KTG.money(Math.round(taxAmount));
  document.getElementById('sgstRow').style.display = taxType === 'sgst_cgst' ? 'flex' : 'none';
  document.getElementById('cgstRow').style.display = taxType === 'sgst_cgst' ? 'flex' : 'none';
  document.getElementById('igstRow').style.display = taxType === 'igst' ? 'flex' : 'none';
  document.getElementById('sumTotal').textContent = KTG.money(Math.round(grand));
  document.getElementById('sumBalance').textContent = KTG.money(due);
}

function openPreview(autoprint){
  const name = document.getElementById('customerSelect').value;
  const c = KTG.DATA.customers.find(x => x.name === name);
  let subtotal = 0;
  items.forEach(it => subtotal += it.qty * it.rate);
  const { percent, taxType, taxAmount } = currentTax(subtotal);
  const grandTotal = Math.round(subtotal + taxAmount);
  const advance = +document.getElementById('advancePaid').value || 0;
  const balanceDue = Math.max(grandTotal - advance, 0);

  const data = {
    docType: document.getElementById('docTypeLabel').textContent,
    invNumber: 'INV-2026-0150',
    invDate: document.getElementById('invDate').value,
    dueDate: document.getElementById('dueDate').value,
    customer: c ? {name: c.name, gst: c.gst, address: c.address, mobile: c.mobile} : {name: name || '—', gst: '—', address: '—', mobile: '—'},
    items: items,
    subtotal: Math.round(subtotal),
    taxType, percent, halfPercent: percent/2, taxAmount: Math.round(taxAmount),
    grandTotal, advancePaid: advance, balanceDue,
    terms: document.getElementById('termsText').value,
    bank: {name:'Knock The Globe Technologies', account:'0012299171', bank:'Kotak Bank', ifsc:'KKBK0008775'}
  };
  sessionStorage.setItem('ktg_invoice_preview', JSON.stringify(data));
  window.open('invoice-preview.php' + (autoprint ? '?autoprint=1' : ''), '_blank');
}
JS;
?>
<?php include __DIR__ . '/components/footer.php'; ?>
