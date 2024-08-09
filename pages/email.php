<?php
session_start();
if (!isset($_SESSION['user_id'])) {
}
$currentPagePath = $_SESSION['referrer'];
$page_title = "Verify email";
$stylesheet = "/assets/css/style.css";
include_once realpath("../components/head.inc");
include_once realpath("../layouts/wide.inc");
?>

<div class="regular-padding">
  <h2>Join Our Trail Reporting Community</h2>
  <p>
    We take your privacy seriously and will never
    sell or share your information with third parties. Your email will not be used
    to identify you on the website.</p>
  <p>You will only recieve emails to
    securely manage access to your account including a request
    for verification, login, or a password change. This step may be completed later.
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
    console.log(<?php echo 1; ?>);
    window.location.href = "<?php echo $currentPagePath; ?>";
  }
</script>
<?php
include_once realpath("../components/tail.inc");
?>