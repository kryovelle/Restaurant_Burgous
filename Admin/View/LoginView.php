<?php
require_once __DIR__ . "/GlobalView.php";
Class LoginView extends GlobalView{

function content($mark){
  ?>
<body>

<div class="auth-page">
  <div class="auth-card">

    <!-- LEFT: PHOTO PANEL -->
    <div class="auth-photo-panel">
      <div class="brandmark"><span class="crown"></span><?php echo htmlspecialchars($mark) ?? '';?> </div>
      <div class="plate-wrap">
        <img src="/images/hero-photo.jpg" alt="hero-photo.jpg" class="plate">
      </div>
    </div>

    <!-- RIGHT: FORM PANEL -->
    <div class="auth-form-panel">
      <div class="logo">
        <div class="accent">CHEF</div>
        <div class="sub">Admin Dashboard</div>
      </div>

      <div class="auth-heading">
        <h1><span class="accent">Sign In</span> to Your<span class="accent"> Account</span></h1>
        <p>Enter your credentials to manage reservations and the menu.</p>
      </div><br><br>

      <div class="auth-error" id="authError" style="display:none;">
  Wrong email or password.
</div>

      <form class="auth-form" id="loginForm" method="post" action="/Admin/redirect.php">

        <div class="form-row">
          <label for="email">Email</label>
          <input type="email" id="email" name="email" placeholder="admin@rest.com" required>
        </div>

        <div class="form-row">
          <label for="password">Password</label>
          <input type="password" id="password" name="password" placeholder="••••••••" required>
        </div>

        <div class="auth-meta-row">
          <a href="/Admin/ForgotPassword/" class="auth-forgot">Forgot password?</a>
        </div>

        <button type="submit" class="form-submit" name="LoginForm">Login</button>

      </form>
    </div>

  </div>
</div>
<script>
  const params = new URLSearchParams(window.location.search);
  const success = params.get('success');

  if (success === '0') {
    document.getElementById('authError').style.display = 'block';
  }
</script>
</body>
</html>
<?php
}

function displayForgotPasswordView(){
    $this->header('Admin');
    ?>
    <div class="auth-page">
      <div class="auth-card">
        <div class="auth-form-panel" style="grid-column: 1 / -1;">
          <div class="logo">
            <div class="mark">CHEF</div>
            <div class="sub">Admin Dashboard</div>
          </div>

          <?php if (isset($_GET['sent'])): ?>
            <div class="auth-heading">
              <h1>Check Your Email</h1>
              <p>If that email is registered, we've sent a password reset link. It expires in 1 hour.</p>
            </div>
            <div class="modal-actions" style="margin-top:24px;">
              <a href="/Admin/" class="btn-outline-sm">Back to Login</a>
            </div>
          <?php else: ?>
            <div class="auth-heading">
              <h1>Forgot Password</h1>
              <p>Enter your email and we'll send you a link to reset your password.</p>
            </div>

            <form class="auth-form" method="post" action="/Admin/redirect.php">
              <input type="hidden" name="forgotPassword" value="1">
              <div class="form-row">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" placeholder="admin@flavoria.com" required>
              </div>
              <button type="submit" class="auth-submit">Send Reset Link</button>
            </form>
          <?php endif; ?>

        </div>
      </div>
    </div>
    <?php
}

function displayResetPasswordView($token){
    $this->header('Admin');
    $error = $_GET['error'] ?? null;
    ?>
    <div class="auth-page">
      <div class="auth-card">
        <div class="auth-form-panel" style="grid-column: 1 / -1;">
          <div class="logo">
            <div class="mark">CHEF</div>
            <div class="sub">Admin Dashboard</div>
          </div>

          <div class="auth-heading">
            <h1>Set a New Password</h1>
            <p>Choose a new password for your account.</p>
          </div>

          <?php if ($error === 'mismatch'): ?>
            <div class="auth-error">Passwords didn't match — try again.</div>
          <?php elseif ($error === 'tooshort'): ?>
            <div class="auth-error">Password must be at least 8 characters.</div>
          <?php endif; ?>

          <form class="auth-form" method="post" action="/Admin/redirect.php">
            <input type="hidden" name="resetPassword" value="1">
            <input type="hidden" name="token" value="<?php echo htmlspecialchars($token); ?>">

            <div class="form-row">
              <label for="password">New Password</label>
              <input type="password" id="password" name="password" placeholder="••••••••" required minlength="8">
            </div>
            <div class="form-row">
              <label for="confirmPassword">Confirm Password</label>
              <input type="password" id="confirmPassword" name="confirmPassword" placeholder="••••••••" required minlength="8">
            </div>

            <button type="submit" class="auth-submit">Reset Password</button>
          </form>

        </div>
      </div>
    </div>
    <?php
}

function displayResetPasswordInvalidView(){
    $this->header('Admin');
    ?>
    <div class="auth-page">
      <div class="auth-card">
        <div class="auth-form-panel" style="grid-column: 1 / -1;">
          <div class="logo">
            <div class="mark">CHEF</div>
            <div class="sub">Admin Dashboard</div>
          </div>
          <div class="auth-heading">
            <h1>Link Expired</h1>
            <p>This password reset link is invalid or has expired. Please request a new one.</p>
          </div>
          <div class="modal-actions" style="margin-top:24px;">
            <a href="/Admin/ForgotPassword/" class="btn-solid-sm">Request New Link</a>
          </div>
        </div>
      </div>
    </div>
    <?php
}

function displayLoginView($mark){
  $this->header($mark);
  $this->content($mark);
}
}