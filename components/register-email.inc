<h2>Join Our Trail Reporting Community</h2>
<p>To ensure the quality and credibility of trail reports on our platform, we highly recommend verifying your email address.  This email will also be used for password recovery if needed.  We take your privacy seriously and will never sell or share your information with third parties.</p>
<form action="register.php" method="post">
  <label for="email">Email Address:</label>
  <input type="email" name="email" id="email" placeholder="Enter your email (recommended)">  <br>
  <button type="submit">Join Now (with Email Verification)</button>
  <button type="button" onclick="skipVerification()">Skip Verification (Limited Access)</button>
  <p class="info">A verification email will be sent to the address you provide.</p>
</form>

<div id="skip-verification-alert" style="display: none;">
  <p>By skipping verification, your account setup will not be complete. This may limit your access to certain features on the platform. Are you sure you want to continue?</p>
  <button onclick="closeSkipAlert()">Fill Out Email</button>
  <button onclick="continueWithoutEmail()">Continue (Limited Access)</button>
</div>

<script>
function skipVerification() {
  document.getElementById("skip-verification-alert").style.display = "block";
}

function closeSkipAlert() {
  document.getElementById("skip-verification-alert").style.display = "none";
}

function continueWithoutEmail() {
  // Redirect to next page (replace with your actual URL)
  window.location.href = "next-page.php";
}
</script>
