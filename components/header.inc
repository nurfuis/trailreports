<div class="header">

  <div class="dropdown" data-dropdown>
    <button class="dropdown__link <?php echo isset($_SESSION['username']) ? 'user-btn' : ''; ?>" data-dropdown-button>
      <?php
      if (isset($_SESSION['username'])) {
        $username = $_SESSION['username'];
        $short_username = substr($username, 0, 6);
        echo $short_username;

        if (strlen($username) > 6) {
          echo "...";
        }
      } else {
        echo "Login";
      }
      ?>
    </button>

    <div class="dropdown__menu dropdown__menu--sign-in">
      <?php
      if (isset($_SESSION['user_id'])) {
        $username = $_SESSION['username'];
        include_once realpath("./components/sign_out_form.inc");
      } else {
        include_once realpath("./components/sign_in_form.inc");
      }
      ?>
    </div>
  </div>

  <div class="dropdown" data-dropdown>
    <button class="dropdown__link" data-dropdown-button>Menu</button>
    <div class="dropdown__menu dropdown__link-grid">
      <a href="./pages/display_reports.php" class="dropdown__link">All Reports</a>
      <a href="./pages/add_report.php" class="dropdown__link">Submit a Report</a>
      <a href="./pages/features.php" class="dropdown__link hidden">Index</a>
      <a href="./pages/topo_map.php" class="dropdown__link">Trail Map</a>
    </div>
  </div>

  <div class="dropdown" data-dropdown>
    <button class="dropdown__link" data-dropdown-button>About</button>
    <div class="dropdown__menu dropdown__link-grid">
      <a href="./pages/support_us.php" class="dropdown__link">Support Us</a>
      <a href="/" class="dropdown__link hidden">Learn More</a>
      <a href="./pages/contact.php" class="dropdown__link">Contact</a>

    </div>
  </div>
</div>

<script>
  document.addEventListener('click', e => {
    const isDropdownButton = e.target.matches("[data-dropdown-button]")
    if (!isDropdownButton && e.target.closest('[data-dropdown') != null) return

    let currentDopdown;
    if (isDropdownButton) {
      currentDopdown = e.target.closest('[data-dropdown')
      currentDopdown.classList.toggle('active')
    }
    document.querySelectorAll("[data-dropdown].active").forEach(dropdown => {
      if (dropdown === currentDopdown) return
      dropdown.classList.remove('active');
    });
  })
</script>
