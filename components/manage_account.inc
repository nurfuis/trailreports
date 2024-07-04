<div>
  <h2>Account Management</h2>

  <p>
    Welcome,
    <?php echo $_SESSION['username']; ?>!
  </p>

  <ul class="account-management-list">
    <li><?php include ("../components/update_email_form.inc"); ?></li>
    <li><?php include ("../components/update_password_form.inc"); ?></li>
    <li><?php include ("../components/update_account_status.inc"); ?></li>
  </ul>
</div>