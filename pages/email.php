<?php
session_start();
if (!isset($_SESSION['user_id'])) {
  header("Location: /index.php");
}

$page_title = "Verify email";
$page_css = "/assets/css/style.css";

include_once realpath("../components/head.inc");
include_once realpath("../layouts/wide.inc");
?>

<div class="regular-padding">
  <h2>Join Our Trail Reporting Community</h2>
  <p>
    To ensure the quality and credibility of trail reports on our platform, we
    highly recommend verifying your email address. We take your privacy seriously and will never
    sell or share your information with third parties. Your email will not be used
    to identify you on the website. </p>
  <p>You will only recieve emails in response to a request
    for verification, login, or a password change. This is to
    securely manage access to your account.
  </p>

  <?php
  include_once realpath("../components/update_email_form.inc");
  ?>
</div>
<div class="regular-padding">
  <form class="hidden">
    <button type="button" id="skip-button" onclick="skipVerification()">Skip Verification</button>
  </form>
  <div id="skip-verification-alert">
    <p>
      By skipping verification, your account setup will not be complete. This may
      limit your access to certain features on the platform. Are you sure you want
      to continue?
    </p>
    <button id="contine" onclick="continueWithoutEmail()">Continue (Limited Access)</button>
    <br><br>
  </div>
</div>
<script>
  function skipVerification() {
    document.getElementById("skip-verification-alert").style.display = "block";
    document.getElementById("skip-button").style.display = "none";
  }
  function continueWithoutEmail() {
    // Redirect to next page
    window.location.href = "/home.php";
  }
</script>
<?php
include_once realpath("../components/tail.inc");
?>