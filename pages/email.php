<?php

$page_title = "Verify email";
$page_css = "/assets/css/style.css";

include_once realpath("../components/head.inc");
include_once realpath("../layouts/single.inc");

session_start();

if (!isset($_SESSION['user_id'])) {
  header("Location: /index.php");
}
?>

<div class="regular-padding">
  <h2>Join Our Trail Reporting Community</h2>
  <p>
    To ensure the quality and credibility of trail reports on our platform, we
    highly recommend verifying your email address. This email will also be used
    for password recovery if needed. We take your privacy seriously and will never
    sell or share your information with third parties.
  </p>

  <?php
  include_once realpath("../components/update_email_form.inc");
  ?>
</div>
<div class="regular-padding">
  <form>
    <button type="button" onclick="skipVerification()">Skip</button>

  </form>

  <div id="skip-verification-alert" style="display: none">
    <p>
      By skipping verification, your account setup will not be complete. This may
      limit your access to certain features on the platform. Are you sure you want
      to continue?
    </p>
    <button onclick="closeSkipAlert(true)">Fill Out Email</button>
    <button onclick="continueWithoutEmail()">Continue (Limited Access)</button>
  </div>
</div>
<script>
  function skipVerification() {
    document.getElementById("skip-verification-alert").style.display = "block";
  }

  function closeSkipAlert(focusEmail = false) {
    document.getElementById("skip-verification-alert").style.display = "none";
    if (focusEmail) {
      document.getElementById("email").focus();
    }
  }

  function continueWithoutEmail() {
    // Redirect to next page
    window.location.href = "/";
  }
</script>
<?php
include_once realpath("../components/tail.inc");
?>