<div class="header">
  
  <div class="dropdown" data-dropdown>
    <button class="link" data-dropdown-button>Login</button>
    <div class="dropdown-menu sign-in">
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
    <button class="link" data-dropdown-button>Menu</button>
    <div class="dropdown-menu link-grid">
      <a href="./pages/display_reports.php" class="link">View Trail Reports</a>
      <a href="./pages/add_report.php" class="link">Submit Trail Reports</a>
      <a href="./pages/features.php" class="link">Trail Index</a>
      <a href="./pages/topo-map.html" class="link">Topo Map</a>
    </div>
  </div>


  <div class="dropdown" data-dropdown>
    <button class="link" data-dropdown-button>About</button>
  </div>

  <div class="dropdown" data-dropdown>
    <button class="link" data-dropdown-button>Support Us</ </div>

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