<?php
require_once __DIR__ . '/includes/auth.php';
require_guest();
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Sign In · KTG Invoice Suite</title>
<link rel="icon" href="data:image/svg+xml,<svg xmlns=%22http://www.w3.org/2000/svg%22 viewBox=%220 0 100 100%22><rect width=%22100%22 height=%22100%22 rx=%2224%22 fill=%22%232563eb%22/><text x=%2250%22 y=%2264%22 font-size=%2244%22 fill=%22white%22 font-family=%22Arial%22 text-anchor=%22middle%22 font-weight=%22bold%22>K</text></svg>">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
<link href="assets/css/style.css" rel="stylesheet">
</head>
<body>

<div class="auth2-shell">

  <!-- Left — brand + illustration -->
  <div class="auth2-left">
    <div class="auth2-logo">
      <div class="auth2-logo-box"><img src="assets/images/logo.png" alt="KTG"></div>
      <div>
        <div class="auth2-logo-text">Knock The Globe</div>
        <div class="auth2-logo-sub">Invoice Suite</div>
      </div>
    </div>

    <div class="auth2-illustration">
      <img src="assets/images/login.webp" alt="KTG Illustration">
    </div>

    <div>
      <p class="fs-13 mb-3" style="color:rgba(255,255,255,.8); max-width:340px;">Quotations, invoices, payments and renewals — all in one clean workspace built for growing IT teams.</p>
    </div>
  </div>

  <!-- Right — credentials -->
  <div class="auth2-right-wrap">
    <div class="auth2-right">
      <div class="auth2-form-wrap">
        <div class="d-lg-none text-center mb-4">
          <div class="auth2-logo-box mx-auto" style="background:var(--primary-light);"><img src="assets/images/logo.png" alt="KTG"></div>
        </div>

        <h1 class="mb-4" style="font-size:1.9rem; font-weight:800;text-align: center;">Login</h1>

        <form id="loginForm">
          <div class="filled-field">
            <i class="bi bi-person-fill ff-icon"></i>
            <label for="loginIdentifier">Username or Email</label>
            <input type="text" id="loginIdentifier" placeholder="e.g. vicky.kumar or you@company.com" required autocomplete="username">
          </div>
          <div class="filled-field">
            <i class="bi bi-key-fill ff-icon"></i>
            <label for="loginPassword">Password</label>
            <input type="password" id="loginPassword" placeholder="Enter your password" required autocomplete="current-password">
            <button type="button" class="ff-toggle" id="togglePw"><i class="bi bi-eye"></i></button>
          </div>

          <div class="d-flex justify-content-between align-items-center mb-3 mt-2">
            <div class="form-check">
              <input class="form-check-input" type="checkbox" id="rememberMe" checked>
              <label class="form-check-label fs-13" for="rememberMe">Remember me</label>
            </div>
            <a href="#" class="fs-13 fw-600 text-primary-KTG">Forgot Password?</a>
          </div>

          <div id="loginError" class="alert alert-danger py-2 fs-13 d-none" style="border-radius:11px;"></div>

          <button type="submit" class="btn auth2-submit" id="loginSubmitBtn">
            <span id="loginSubmitLabel">Login</span>
          </button>
        </form>
      </div>
    </div>
  </div>

</div>

<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
document.getElementById('togglePw').addEventListener('click', () => {
  const field = document.getElementById('loginPassword');
  const icon = document.querySelector('#togglePw i');
  if (field.type === 'password') { field.type = 'text'; icon.className = 'bi bi-eye-slash'; }
  else { field.type = 'password'; icon.className = 'bi bi-eye'; }
});

const remembered = localStorage.getItem('ktg_remember_identifier');
if (remembered) document.getElementById('loginIdentifier').value = remembered;

$('#loginForm').on('submit', function (e) {
  e.preventDefault();
  const identifier = $('#loginIdentifier').val().trim();
  const password = $('#loginPassword').val();
  const $err = $('#loginError').addClass('d-none');

  if (!identifier || !password) {
    $err.text('Please enter both a username/email and password.').removeClass('d-none');
    return;
  }

  const remember = $('#rememberMe').is(':checked');
  if (remember) localStorage.setItem('ktg_remember_identifier', identifier);
  else localStorage.removeItem('ktg_remember_identifier');

  const $btn = $('#loginSubmitBtn').prop('disabled', true);
  $('#loginSubmitLabel').html('<span class="spinner-border spinner-border-sm me-2"></span>Signing in…');

  $.ajax({
    url: 'ajax/auth.php',
    method: 'POST',
    dataType: 'json',
    data: { action: 'login', identifier, password }
  }).done(function (res) {
    if (res && res.success) {
      window.location.href = 'index.php';
    } else {
      $err.text((res && res.message) || 'Login failed.').removeClass('d-none');
      $btn.prop('disabled', false);
      $('#loginSubmitLabel').text('Login');
    }
  }).fail(function (xhr) {
    const msg = (xhr.responseJSON && xhr.responseJSON.message) || 'Login failed. Please try again.';
    $err.text(msg).removeClass('d-none');
    $btn.prop('disabled', false);
    $('#loginSubmitLabel').text('Login');
  });
});
</script>
</body>
</html>
