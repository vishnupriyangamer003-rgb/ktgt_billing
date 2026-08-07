<?php
$pageTitle = "Invoice Entry Sample 3 · Inline Compact";
$pageKey = "invoices";
$extraHead = <<<'HEAD'
<style>
.gst-toggle-btn{ flex:1; border:2px solid var(--border); border-radius:14px; padding:.8rem; text-align:center; cursor:pointer; transition: all .18s var(--ease); background:#fff; }
.gst-toggle-btn.active{ border-color: var(--primary); background: var(--primary-light); }
.gst-toggle-btn i{ font-size:1.3rem; }
.sample-badge{ display:inline-flex; align-items:center; gap:.4rem; background:var(--primary-light); color:var(--primary); font-size:11.5px; font-weight:700; padding:.35rem .8rem; border-radius:20px; }
.sample-nav{ display:flex; gap:.5rem; flex-wrap:wrap; }
.sample-nav a{ font-size:12.5px; font-weight:600; padding:.45rem .9rem; border-radius:8px; background:#fff; border:1px solid var(--border); color:var(--text-secondary); text-decoration:none; transition:all .15s; }
.sample-nav a:hover{ border-color:var(--primary); color:var(--primary); }
.sample-nav a.active{ background:var(--primary); border-color:var(--primary); color:#fff; }

/* ---- Sample 3: Inline / minimal flex entry rows ---- */
.ie3-list{ border-top:1px solid var(--border-soft); }
.ie3-item{ display:flex; flex-wrap:wrap; align-items:center; gap:.4rem .7rem; padding:.9rem 0; border-bottom:1px solid var(--border-soft); }
.ie3-service{ flex:1 1 100%; }
.ie3-service select{ border:none; border-bottom:2px solid var(--border); border-radius:0; padding:.3rem .1rem; font-weight:600; background:transparent; box-shadow:none !important; }
.ie3-service select:focus{ border-color:var(--primary); }
.ie3-desc{ flex:1 1 100%; }
.ie3-desc input{ border:none; border-bottom:1px dashed var(--border-soft); border-radius:0; padding:.25rem .1rem; font-size:12px; background:transparent; box-shadow:none !important; }
.ie3-desc input:focus{ border-color:var(--primary); }
.ie3-nums{ flex:1 1 100%; display:flex; gap:.5rem; align-items:center; flex-wrap:wrap; }
.ie3-nums .num-field{ display:flex; align-items:center; gap:.3rem; background:var(--surface-alt); border-radius:8px; padding:.3rem .55rem; }
.ie3-nums .num-field label{ font-size:10.5px; color:var(--text-muted); font-weight:700; margin:0; }
.ie3-nums .num-field input{ width:56px; border:none; background:transparent; padding:0; text-align:right; font-weight:600; box-shadow:none !important; }
.ie3-x{ color:var(--text-muted); font-size:12px; }
.ie3-amount{ margin-left:auto; font-weight:800; color:var(--secondary); font-size:14.5px; white-space:nowrap; }
.ie3-remove{ flex-shrink:0; }
</style>
HEAD;
include __DIR__ . '/components/header.php';
?>
      <div class="page-head fade-in-up">
        <div>
          <h1>Create Invoice</h1>
          <nav class="breadcrumb"><span class="breadcrumb-item"><a href="index.php">Dashboard</a></span><span class="breadcrumb-item"><a href="invoice-list.php">Invoices</a></span><span class="breadcrumb-item active">Entry Sample 3</span></nav>
        </div>
        <div class="sample-badge"><i class="bi bi-3-circle-fill"></i>Sample 3 — Inline Compact List</div>
      </div>

      <div class="section-card mb-3 fade-in-up">
        <div class="section-body py-3">
          <div class="sample-nav">
            <a href="invoice-entry-sample1.php">1 · Compact Cards</a>
            <a href="invoice-entry-sample2.php">2 · Detailed Form</a>
            <a href="invoice-entry-sample3.php" class="active">3 · Inline Compact</a>
            <a href="invoice-create.php" class="ms-auto"><i class="bi bi-arrow-left me-1"></i>Back to Current Design</a>
          </div>
        </div>
      </div>

      <!-- Tax mode -->
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
        <div id="itemsBody" class="ie3-list mb-4"></div>

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
}

function onCustomerChange(){
  const name = document.getElementById('customerSelect').value;
  const c = KTG.DATA.customers.find(x => x.name === name);
  const meta = document.getElementById('customerMeta');
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
    <div class="ie3-item">
      <div class="ie3-service">
        <select class="form-select form-select-sm" onchange="onServiceChange(${i}, this.value)">
          ${services.map(s => `<option value="${s.name}" ${s.name === it.service ? 'selected' : ''}>${s.name}</option>`).join('')}
        </select>
      </div>
      <div class="ie3-desc">
        <input class="form-control form-control-sm" placeholder="Description / link (optional)" value="${it.desc}" onchange="items[${i}].desc=this.value">
      </div>
      <div class="ie3-nums">
        <div class="num-field"><label>Qty</label><input type="number" value="${it.qty}" onchange="items[${i}].qty=+this.value; calcTotals();"></div>
        <span class="ie3-x">&times;</span>
        <div class="num-field"><label>₹</label><input type="number" value="${it.rate}" onchange="items[${i}].rate=+this.value; calcTotals();"></div>
        <span class="ie3-amount item-amount" data-i="${i}">₹0</span>
        <button class="btn btn-sm btn-light ie3-remove" onclick="items.splice(${i},1); renderItems();"><i class="bi bi-trash text-danger"></i></button>
      </div>
    </div>`).join('');
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
JS;
?>
<?php include __DIR__ . '/components/footer.php'; ?>
