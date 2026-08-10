<?php
$pageTitle = "Create Invoice";
$pageKey = "invoices";
$extraHead = <<<'HTML'
<style>
.item-table input, .item-table select{ font-size:12.8px; padding:.4rem .6rem; }
.item-table th{ font-size:10.8px; }
.gst-toggle-btn{ flex:1; border:2px solid var(--border); border-radius:14px; padding:.8rem; text-align:center; cursor:pointer; transition: all .18s var(--ease); background:#fff; }
.gst-toggle-btn.active{ border-color: var(--primary); background: var(--primary-light); }
.gst-toggle-btn i{ font-size:1.3rem; }

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
          <h1>Create Invoice</h1>
          <nav class="breadcrumb"><span class="breadcrumb-item"><a href="index.php">Dashboard</a></span><span class="breadcrumb-item"><a href="invoice-list.php">Invoices</a></span><span class="breadcrumb-item active">New</span></nav>
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
        <div class="bill-accordion-head" data-bs-toggle="collapse" data-bs-target="#letterheadBody" role="button" aria-expanded="false" aria-controls="letterheadBody" id="letterheadToggle">
          <div class="bah-title">
            <div class="bill-doctype" id="docTypeLabel">TAX INVOICE</div>
            <span class="fs-12 text-muted" id="letterheadSummary">Knock The Globe Technologies</span>
          </div>
          <button class="btn btn-sm btn-light bah-toggle" type="button" tabindex="-1"><i class="bi bi-chevron-down me-1"></i>Company Details</button>
        </div>
        <div class="collapse" id="letterheadBody">
          <div class="bill-letterhead">
            <div class="text-center">
              <div class="bl-logo mx-auto"><img src="assets/images/logo.png" alt="KTG"></div>
              <div class="bl-tagline">Give Us Your Hands …!</div>
            </div>
            <div class="bl-right" id="letterheadCompanyBlock"></div>
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
              <tr><td>Invoice Number</td><td><span id="invNumberDisplay">INV-2026-0151</span></td></tr>
              <tr><td>Invoice Date</td><td><input type="date" class="form-control form-control-sm d-inline-block w-auto" id="invDate" value="2026-07-25"></td></tr>
              <tr><td>Due Date</td><td><input type="date" class="form-control form-control-sm d-inline-block w-auto" id="dueDate" value="2026-08-08"></td></tr>
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
            <thead><tr><th style="min-width:240px;">Product</th><th class="col-qty">Qty</th><th style="width:120px;">Price</th><th style="width:130px;">Amount</th><th style="width:40px;"></th></tr></thead>
            <tbody id="itemsBodyDesktop"></tbody>
          </table>
        </div>

        <!-- Mobile: card-based entry rows -->
        <div class="itc-mobile mb-4" id="itemsBodyMobile"></div>

        <div class="row g-3 mb-4">
          <div class="col-md-7">
            <div class="bill-block-label mb-1">Notes / Terms</div>
            <textarea class="form-control mb-3" rows="3" id="termsText">Payment due within the terms stated above. Late payments may attract a delay fee. Thank you for your business.</textarea>
          </div>
          <div class="col-md-5">
            <div class="bill-totals">
              <div class="bt-row"><span class="text-muted">Subtotal</span><span class="fw-600" id="sumSubtotal">₹0</span></div>
              <div class="bt-row"><span class="text-muted">Discount (₹)</span>
                <span class="fw-600"><input type="number" class="form-control form-control-sm text-end" style="width:120px; display:inline-block;" id="discountAmount" placeholder="0" min="0"></span>
              </div>
              <div class="bt-row" id="sgstRow"><span class="text-muted" id="sgstLabel">SGST 9%</span><span class="fw-600" id="sumSgst">₹0</span></div>
              <div class="bt-row" id="cgstRow"><span class="text-muted" id="cgstLabel">CGST 9%</span><span class="fw-600" id="sumCgst">₹0</span></div>
              <div class="bt-row" id="igstRow"><span class="text-muted" id="igstLabel">IGST 18%</span><span class="fw-600" id="sumIgst">₹0</span></div>
              <div class="bt-row" id="roundoffRow"><span class="text-muted">Round Off</span><span class="fw-600" id="sumRoundoff">₹0</span></div>
              <div class="bt-row bt-grand"><span>Total</span>
                <span class="fw-800"><input type="number" class="form-control form-control-sm text-end fw-800" style="width:130px; display:inline-block; font-size:1.05rem;" id="grandTotalInput" value="0"></span>
              </div>
              <div class="bt-row"><span class="text-muted">Advance Paid</span>
                <span class="fw-600 text-success"><input type="number" class="form-control form-control-sm text-end" style="width:120px; display:inline-block;" id="advancePaid" placeholder="0"></span>
              </div>
              <div class="bt-row"><span class="fw-700">Amount Due</span><span class="fw-700 text-danger" id="sumBalance">₹0</span></div>
            </div>
            <div class="fs-11 text-muted mt-1"><i class="bi bi-info-circle me-1"></i>Edit Total to auto-adjust the discount, or edit Discount to recompute the Total.</div>
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

function emptyItem(){ return {service:'', desc:'', qty:'', rate:''}; }

function init(){
  const custSelect = document.getElementById('customerSelect');
  custSelect.innerHTML = '<option value="">Select customer...</option>' + KTG.DATA.customers.map(c => `<option value="${c.name}">${c.name}</option>`).join('');
  custSelect.addEventListener('change', onCustomerChange);
  onCustomerChange();

  const taxSelect = document.getElementById('taxSelect');
  taxSelect.innerHTML = KTG.Masters.taxes().filter(t => t.percent > 0).map(t => `<option value="${t.percent}">${t.name}</option>`).join('');
  taxSelect.addEventListener('change', calcTotals);
  document.getElementById('taxTypeSelect').addEventListener('change', calcTotals);

  items = [emptyItem()];
  renderItems();
  refreshInvoiceNumber();
  refreshLetterhead();

  document.getElementById('addItemBtn').addEventListener('click', () => {
    items.push(emptyItem());
    renderItems();
  });
  document.getElementById('withGstBtn').addEventListener('click', () => setGst(true));
  document.getElementById('withoutGstBtn').addEventListener('click', () => setGst(false));
  document.getElementById('advancePaid').addEventListener('input', calcTotals);
  document.getElementById('discountAmount').addEventListener('input', calcTotals);
  document.getElementById('grandTotalInput').addEventListener('input', onGrandTotalChange);
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
  const gstLine = gstEnabled ? `GST : ${c.gst}<br>` : '';
  meta.innerHTML = `<span class="bill-to-name">${c.name}</span><br>${gstLine}${c.address}<br>${c.mobile}`;
}

function refreshInvoiceNumber(){
  document.getElementById('invNumberDisplay').textContent = KTG.nextInvoiceNumber(gstEnabled ? 'gst' : 'nongst');
}

function refreshLetterhead(){
  const docType = gstEnabled ? 'TAX INVOICE' : 'BILL OF SUPPLY';
  const gstType = gstEnabled ? 'gst' : 'nongst';
  document.getElementById('letterheadCompanyBlock').innerHTML = KTG.companyLetterheadHtml(gstType, docType);
  const c = KTG.CompanyProfile.get(gstType);
  document.getElementById('letterheadSummary').textContent = c.name || '';
}

function setGst(state){
  gstEnabled = state;
  document.getElementById('withGstBtn').classList.toggle('active', state);
  document.getElementById('withoutGstBtn').classList.toggle('active', !state);
  document.getElementById('gstOptionsWrap').style.display = state ? 'block' : 'none';
  document.getElementById('docTypeLabel').textContent = state ? 'TAX INVOICE' : 'BILL OF SUPPLY';
  refreshInvoiceNumber();
  refreshLetterhead();
  onCustomerChange();
  calcTotals();
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
      <td><input type="number" class="form-control" placeholder="0" value="${it.qty}" onchange="items[${i}].qty=+this.value; calcTotals();"></td>
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

function currentTax(subtotal){
  const percent = gstEnabled ? (+document.getElementById('taxSelect').value || 0) : 0;
  const taxType = document.getElementById('taxTypeSelect').value;
  const taxAmount = subtotal * percent / 100;
  return { percent, taxType: gstEnabled ? taxType : 'none', taxAmount };
}

function itemsSubtotal(list){
  return list.reduce((s, it) => s + (+it.qty || 0) * (+it.rate || 0), 0);
}

/* Subtotal → Discount → taxable amount → tax → raw total → rounded Total (Round Off = the gap).
   The Total field is editable: typing a target total back-solves the Discount needed to reach it. */
function calcTotals(){
  let subtotal = 0;
  items.forEach((it, i) => {
    const amount = (+it.qty || 0) * (+it.rate || 0);
    subtotal += amount;
    document.querySelectorAll(`.item-amount[data-i="${i}"]`).forEach(cell => cell.textContent = KTG.money(Math.round(amount)));
  });

  const discount = Math.min(Math.max(+document.getElementById('discountAmount').value || 0, 0), subtotal);
  const taxable = subtotal - discount;
  const { percent, taxType, taxAmount } = currentTax(taxable);
  const half = percent / 2;
  const rawTotal = taxable + taxAmount;
  const roundedTotal = Math.round(rawTotal);
  const roundOff = roundedTotal - rawTotal;
  const advance = +document.getElementById('advancePaid').value || 0;
  const due = Math.max(roundedTotal - advance, 0);

  document.getElementById('sumSubtotal').textContent = KTG.money(Math.round(subtotal));
  document.getElementById('sgstLabel').textContent = `SGST ${half}%`;
  document.getElementById('cgstLabel').textContent = `CGST ${half}%`;
  document.getElementById('igstLabel').textContent = `IGST ${percent}%`;
  document.getElementById('sumSgst').textContent = KTG.money(Math.round(taxAmount/2));
  document.getElementById('sumCgst').textContent = KTG.money(Math.round(taxAmount/2));
  document.getElementById('sumIgst').textContent = KTG.money(Math.round(taxAmount));
  document.getElementById('sgstRow').classList.toggle('bt-hidden', taxType !== 'sgst_cgst');
  document.getElementById('cgstRow').classList.toggle('bt-hidden', taxType !== 'sgst_cgst');
  document.getElementById('igstRow').classList.toggle('bt-hidden', taxType !== 'igst');
  document.getElementById('roundoffRow').classList.toggle('bt-hidden', Math.abs(roundOff) <= 0.001);
  document.getElementById('sumRoundoff').textContent = (roundOff >= 0 ? '+' : '−') + KTG.money(Math.abs(roundOff));
  document.getElementById('grandTotalInput').value = roundedTotal;
  document.getElementById('sumBalance').textContent = KTG.money(due);
}

function onGrandTotalChange(){
  const target = +document.getElementById('grandTotalInput').value || 0;
  const subtotal = itemsSubtotal(items);
  const percent = gstEnabled ? (+document.getElementById('taxSelect').value || 0) : 0;
  const taxable = percent > 0 ? target / (1 + percent / 100) : target;
  const discount = Math.min(Math.max(subtotal - taxable, 0), subtotal);
  document.getElementById('discountAmount').value = Math.round(discount);
  calcTotals();
}

function openPreview(autoprint){
  const name = document.getElementById('customerSelect').value;
  const c = KTG.DATA.customers.find(x => x.name === name);
  const realItems = items.filter(it => it.service);
  const subtotal = itemsSubtotal(realItems);
  const discount = Math.min(Math.max(+document.getElementById('discountAmount').value || 0, 0), subtotal);
  const taxable = subtotal - discount;
  const { percent, taxType, taxAmount } = currentTax(taxable);
  const rawTotal = taxable + taxAmount;
  const grandTotal = Math.round(rawTotal);
  const roundOff = grandTotal - rawTotal;
  const advance = +document.getElementById('advancePaid').value || 0;
  const balanceDue = Math.max(grandTotal - advance, 0);

  const data = {
    docType: document.getElementById('docTypeLabel').textContent,
    gstType: gstEnabled ? 'gst' : 'nongst',
    invNumber: document.getElementById('invNumberDisplay').textContent,
    invDate: document.getElementById('invDate').value,
    dueDate: document.getElementById('dueDate').value,
    customer: c ? {name: c.name, gst: gstEnabled ? c.gst : '', address: c.address, mobile: c.mobile} : {name: name || '—', gst: '', address: '—', mobile: '—'},
    items: realItems,
    subtotal: Math.round(subtotal), discount: Math.round(discount),
    taxType, percent, halfPercent: percent/2, taxAmount: Math.round(taxAmount), roundOff,
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
