<?php
$pageTitle = "Profile";
$pageKey = "profile";
include __DIR__ . '/components/header.php';
?>
      <div class="page-head fade-in-up">
        <div>
          <h1>Company Profile</h1>
          <nav class="breadcrumb"><span class="breadcrumb-item"><a href="index.php">Dashboard</a></span><span class="breadcrumb-item active">Profile</span></nav>
        </div>
        <button class="btn btn-primary"><i class="bi bi-check-lg me-1"></i>Save Profile</button>
      </div>

      <div class="row g-3">
        <div class="col-xl-4">
          <div class="section-card fade-in-up mb-3">
            <div class="section-body text-center">
              <div class="mx-auto mb-3 d-flex align-items-center justify-content-center" style="width:110px;height:110px; filter: drop-shadow(0 6px 14px rgba(37,99,235,.4));"><img src="assets/images/logo.png" alt="KTG" style="width:100%;height:100%;object-fit:contain;"></div>
              <h5 class="mb-0">Knock The Globe Technologies</h5>
              <div class="text-muted fs-13 mb-3">IT Services &amp; Software Development</div>
              <button class="btn btn-sm btn-light"><i class="bi bi-upload me-1"></i>Change Logo</button>
            </div>
          </div>

          <div class="section-card fade-in-up mb-3">
            <div class="section-head"><h5>UPI QR Code</h5></div>
            <div class="section-body text-center">
              <div class="qr-box mx-auto mb-3"><i class="bi bi-qr-code fs-1"></i></div>
              <div class="fs-13 fw-600">knockus@okhdfcbank</div>
              <button class="btn btn-sm btn-light mt-2"><i class="bi bi-upload me-1"></i>Upload QR Image</button>
            </div>
          </div>

          <div class="section-card fade-in-up">
            <div class="section-head"><h5>Authorized Signature</h5></div>
            <div class="section-body text-center">
              <div class="sign-box mx-auto" style="border:1.5px dashed var(--border); border-radius:12px; height:90px; display:flex; align-items:center; justify-content:center; color:var(--text-muted); font-size:12px;"><i class="bi bi-pen me-2"></i>No signature uploaded</div>
              <button class="btn btn-sm btn-light mt-3"><i class="bi bi-upload me-1"></i>Upload Signature</button>
            </div>
          </div>
        </div>

        <div class="col-xl-8">
          <div class="section-card fade-in-up mb-3">
            <div class="section-head"><h5>Company Details</h5></div>
            <div class="section-body">
              <div class="row g-3">
                <div class="col-md-6"><label class="form-label">Company Name</label><input class="form-control" value="Knock The Globe Technologies"></div>
                <div class="col-md-6"><label class="form-label">Short Name</label><input class="form-control" value="KTG"></div>
                <div class="col-md-6"><label class="form-label">Phone</label><input class="form-control" value="72001 23452"></div>
                <div class="col-md-6"><label class="form-label">Email</label><input class="form-control" value="knockus@knocktheglobe.com"></div>
                <div class="col-md-6"><label class="form-label">Website</label><input class="form-control" value="www.knocktheglobe.com"></div>
                <div class="col-md-6"><label class="form-label">Established</label><input class="form-control" value="2024"></div>
                <div class="col-12"><label class="form-label">Registered Address</label><textarea class="form-control" rows="2">4th Cross Rd, Vaiyapuri Nagar, Karur, Tamil Nadu 639002</textarea></div>
              </div>
            </div>
          </div>

          <div class="section-card fade-in-up mb-3">
            <div class="section-head"><h5>GST &amp; Tax Details</h5></div>
            <div class="section-body">
              <div class="row g-3">
                <div class="col-md-6"><label class="form-label">GSTIN</label><input class="form-control" value="33ABCDE1234F1Z5"></div>
                <div class="col-md-6"><label class="form-label">PAN</label><input class="form-control" value="ABCDE1234F"></div>
                <div class="col-md-6"><label class="form-label">State</label><input class="form-control" value="Tamil Nadu"></div>
                <div class="col-md-6"><label class="form-label">State Code</label><input class="form-control" value="33"></div>
              </div>
            </div>
          </div>

          <div class="section-card fade-in-up">
            <div class="section-head"><h5>Bank Details</h5></div>
            <div class="section-body">
              <div class="row g-3">
                <div class="col-md-6"><label class="form-label">Bank Name</label><input class="form-control" value="HDFC Bank"></div>
                <div class="col-md-6"><label class="form-label">Branch</label><input class="form-control" value="Karur Main Branch"></div>
                <div class="col-md-6"><label class="form-label">Account Holder Name</label><input class="form-control" value="Knock The Globe Technologies"></div>
                <div class="col-md-6"><label class="form-label">Account Number</label><input class="form-control" value="50100234567890"></div>
                <div class="col-md-6"><label class="form-label">IFSC Code</label><input class="form-control" value="HDFC0001234"></div>
                <div class="col-md-6"><label class="form-label">Account Type</label><select class="form-select"><option>Current</option><option>Savings</option></select></div>
              </div>
            </div>
          </div>
        </div>
      </div>

<?php include __DIR__ . '/components/footer.php'; ?>
