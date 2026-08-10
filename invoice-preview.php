<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Invoice Preview · KTG Invoice Suite</title>
<link rel="icon" href="data:image/svg+xml,<svg xmlns=%22http://www.w3.org/2000/svg%22 viewBox=%220 0 100 100%22><rect width=%22100%22 height=%22100%22 rx=%2224%22 fill=%22%232563eb%22/><text x=%2250%22 y=%2264%22 font-size=%2244%22 fill=%22white%22 font-family=%22Arial%22 text-anchor=%22middle%22 font-weight=%22bold%22>K</text></svg>">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
<link href="assets/css/style.css?v=<?php echo @filemtime(__DIR__ . '/assets/css/style.css') ?: time(); ?>" rel="stylesheet">
<style>
  /* Notes/Bank/Totals block always sits at the bottom of the sheet, even for a 1-item invoice.
     min-height must stay pinned to a full A4 page at print time too — the global .a4-page
     print reset sets min-height:auto, which would otherwise collapse this flex container to
     content height, breaking the bottom-pin (content jumps to the top, leaving blank paper
     below instead of before it). */
  #billRoot{ display:flex; flex-direction:column; }
  #billRoot .bill-bottom-block{ margin-top:auto; }
  @media print{ #billRoot{ min-height: 297mm; } }
</style>
</head>
<body>

<div class="a4-shell">
  <div class="a4-toolbar no-print">
    <button class="btn btn-light" onclick="window.close(); history.back();"><i class="bi bi-arrow-left me-1"></i>Back to Invoice</button>
    <div class="d-flex gap-2">
      <button class="btn btn-light" id="downloadBtn"><i class="bi bi-download me-1"></i>Download PDF</button>
      <button class="btn btn-primary" onclick="window.print()"><i class="bi bi-printer me-1"></i>Print</button>
    </div>
  </div>

  <div class="a4-page bill-sheet" id="billRoot" style="box-shadow:none;"></div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="assets/js/app.js?v=<?php echo @filemtime(__DIR__ . '/assets/js/app.js') ?: time(); ?>"></script>
<script>
document.addEventListener('DOMContentLoaded', () => {
  const raw = sessionStorage.getItem('ktg_invoice_preview');
  const root = document.getElementById('billRoot');
  if (!raw) {
    root.innerHTML = `<div class="empty-state"><div class="es-icon"><i class="bi bi-receipt"></i></div><h6>No invoice data found</h6><p>Go back and click Preview from the Create Invoice page.</p></div>`;
    return;
  }
  const d = JSON.parse(raw);
  const rows = d.items.map(it => `
    <tr>
      <td><span class="bill-to-name" style="font-size:13px;">${it.service}</span>${it.desc ? `<div class="bill-to-meta">${it.desc}</div>` : ''}</td>
      <td>${it.qty}</td>
      <td>${KTG.money(it.rate)}</td>
      <td class="text-end fw-600">${KTG.money(it.qty * it.rate)}</td>
    </tr>`).join('');

  let taxRowsHtml = '';
  if (d.taxType === 'sgst_cgst') {
    taxRowsHtml = `
      <div class="bt-row"><span class="text-muted">SGST ${d.halfPercent}%</span><span>${KTG.money(d.taxAmount/2)}</span></div>
      <div class="bt-row"><span class="text-muted">CGST ${d.halfPercent}%</span><span>${KTG.money(d.taxAmount/2)}</span></div>`;
  } else if (d.taxType === 'igst') {
    taxRowsHtml = `<div class="bt-row"><span class="text-muted">IGST ${d.percent}%</span><span>${KTG.money(d.taxAmount)}</span></div>`;
  }

  root.innerHTML = `
    <div class="bill-letterhead">
      <div class="text-center">
        <div class="bl-logo mx-auto"><img src="assets/images/logo.png" alt="KTG"></div>
        <div class="bl-tagline">Give Us Your Hands …!</div>
      </div>
      <div class="bl-right">${KTG.companyLetterheadHtml(d.gstType || 'gst', d.docType)}</div>
    </div>

    <div class="row g-4 mb-4">
      <div class="col-md-6">
        <div class="bill-block-label">Bill To</div>
        <span class="bill-to-name">${d.customer.name}</span>
        <div class="bill-to-meta">${d.customer.gst ? `GST : ${d.customer.gst}<br>` : ''}${d.customer.address}<br>${d.customer.mobile}</div>
      </div>
      <div class="col-md-6">
        <table class="bill-meta-table w-100">
          <tr><td>Invoice Number</td><td>${d.invNumber}</td></tr>
          <tr><td>Invoice Date</td><td>${d.invDate}</td></tr>
          <tr><td>Due Date</td><td>${d.dueDate}</td></tr>
        </table>
        <div class="bill-amount-due"><span class="lbl">Amount Due (INR)</span><span class="val">${KTG.money(d.balanceDue)}</span></div>
      </div>
    </div>

    <table class="table table-modern mb-4">
      <thead><tr><th style="min-width:240px;">Product</th><th class="col-qty">Qty</th><th>Price</th><th class="text-end">Amount</th></tr></thead>
      <tbody>${rows}</tbody>
    </table>

    <div class="bill-bottom-block">
      <div class="row g-3 mb-4">
        <div class="col-md-7">
          <div class="bill-notes-label mb-1">Notes / Terms</div>
          <p class="fs-13 text-secondary mb-3">${d.terms}</p>
          <div class="bill-notes-label mb-1">Bank Account Details</div>
          <div class="fs-13">Account Name: <span class="bill-notes-value">${d.bank.name}</span></div>
          <div class="fs-13">Account Number: <span class="bill-notes-value">${d.bank.account}</span></div>
          <div class="fs-13">Bank Name: <span class="bill-notes-value">${d.bank.bank}</span></div>
          <div class="fs-13">IFSC Code: <span class="bill-notes-value">${d.bank.ifsc}</span></div>
        </div>
        <div class="col-md-5">
          <div class="bill-totals">
            <div class="bt-row"><span class="text-muted">Subtotal</span><span>${KTG.money(d.subtotal)}</span></div>
            ${d.discount > 0 ? `<div class="bt-row"><span class="text-muted">Discount</span><span class="text-danger">-${KTG.money(d.discount)}</span></div>` : ''}
            ${taxRowsHtml}
            ${Math.abs(d.roundOff || 0) > 0.001 ? `<div class="bt-row"><span class="text-muted">Round Off</span><span>${d.roundOff >= 0 ? '+' : '−'}${KTG.money(Math.abs(d.roundOff))}</span></div>` : ''}
            <div class="bt-row"><span class="text-muted">Total</span><span class="fw-600">${KTG.money(d.grandTotal)}</span></div>
            ${d.advancePaid > 0 ? `<div class="bt-row"><span class="text-muted">Advance Paid</span><span class="text-success fw-600">${KTG.money(d.advancePaid)}</span></div>` : ''}
            <div class="bt-row bt-grand"><span>Amount Due</span><span>${KTG.money(d.balanceDue)}</span></div>
          </div>
        </div>
      </div>

      <div class="bill-footer-note">This Invoice is Computer Generated | Below Twenty Lakhs Only</div>
    </div>`;

  if (new URLSearchParams(location.search).get('autoprint') === '1') {
    setTimeout(() => window.print(), 350);
  }
  document.getElementById('downloadBtn').addEventListener('click', () => {
    KTG.toast('Use the Print dialog and choose "Save as PDF" as the destination.', 'primary', 'Generating PDF');
    window.print();
  });
});
</script>
</body>
</html>
