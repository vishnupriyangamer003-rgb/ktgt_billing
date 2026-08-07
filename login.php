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

        <div class="auth2-divider">OR</div>

        <form id="loginForm">
          <div class="filled-field">
            <i class="bi bi-envelope-fill ff-icon"></i>
            <label for="loginEmail">Email</label>
            <input type="email" id="loginEmail" placeholder="example@gmail.com" value="ktgkrr@gmail.com" required>
          </div>
          <div class="filled-field">
            <i class="bi bi-key-fill ff-icon"></i>
            <label for="loginPassword">Password</label>
            <input type="password" id="loginPassword" placeholder="Enter your password" required>
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

        <!-- <p class="text-center fs-13 mt-4 mb-0">Don't have an account? <a href="#" class="fw-600 text-primary-KTG">Register</a></p> -->

        <div class="alert alert-primary d-flex gap-2 mt-4 mb-0" style="background:var(--primary-light); border:1px solid var(--primary-100); border-radius:12px;">
          <i class="bi bi-info-circle-fill fs-5 flex-shrink-0"></i>
          <div class="fs-11">Demo mode — any email + password combination signs you in. Google/Facebook buttons also sign in instantly (no real OAuth in this demo).</div>
        </div>
      </div>
    </div>
  </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="assets/js/app.js"></script>
<script>
document.getElementById('togglePw').addEventListener('click', () => {
  const field = document.getElementById('loginPassword');
  const icon = document.querySelector('#togglePw i');
  if (field.type === 'password') { field.type = 'text'; icon.className = 'bi bi-eye-slash'; }
  else { field.type = 'password'; icon.className = 'bi bi-eye'; }
});

const remembered = localStorage.getItem('ktg_remember_email');
if (remembered) document.getElementById('loginEmail').value = remembered;

function signIn(email, remember){
  KTG.Auth.login(email, remember);
  window.location.href = 'index.php';
}

document.getElementById('loginForm').addEventListener('submit', (e) => {
  e.preventDefault();
  const email = document.getElementById('loginEmail').value.trim();
  const pw = document.getElementById('loginPassword').value;
  const errBox = document.getElementById('loginError');
  if (!email || !pw) {
    errBox.textContent = 'Please enter both email and password.';
    errBox.classList.remove('d-none');
    return;
  }
  errBox.classList.add('d-none');
  const submitBtn = document.getElementById('loginSubmitBtn');
  submitBtn.disabled = true;
  document.getElementById('loginSubmitLabel').innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Signing in…';
  const remember = document.getElementById('rememberMe').checked;
  setTimeout(() => signIn(email, remember), 350);
});

document.getElementById('googleBtn').addEventListener('click', () => {
  KTG.toast('Signing in with Google (demo)…', 'primary', 'Google Sign-In');
  setTimeout(() => signIn('ktgkrr@gmail.com', true), 500);
});
document.getElementById('facebookBtn').addEventListener('click', () => {
  KTG.toast('Signing in with Facebook (demo)…', 'primary', 'Facebook Sign-In');
  setTimeout(() => signIn('ktgkrr@gmail.com', true), 500);
});
</script>
</body>
</html>
