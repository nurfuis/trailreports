<?php
session_start();
require_once realpath("../../db_connect.php");

$page_title = "Received Messages";
$stylesheet = "/assets/css/style.css";

require_once realpath("../components/is_admin.inc");


include_once realpath("../components/head.inc");
include_once realpath("../layouts/wide.inc");

$sql = "SELECT id, email, message, ip, created_at FROM contact_messages ORDER BY created_at DESC"; // Order messages by creation date (DESC = descending)

$result = mysqli_query($mysqli, $sql);
if (!$result) {
  echo "Error: " . mysqli_error($mysqli);
  exit;
}

if (mysqli_num_rows($result) > 0) {
  echo "<h1>Messages</h1>";
  ?>
  <div class="user-list">
  <div class="user-list-body">
    <?php while ($row = mysqli_fetch_assoc($result)) {
      ?>
      <div class="user-item">
        <div><span>message id:</span> <?php echo $row["id"]; ?></div>
        <div>
          <div ><span>Email:</span> <?php echo htmlspecialchars($row["email"], ENT_QUOTES, 'UTF-8'); ?></div>
          <div><span>Message:</span> <?php echo htmlspecialchars($row["message"], ENT_QUOTES, 'UTF-8'); ?></div>
          <div><span>IP:</span> <?php echo $row["ip"]; ?></div>

          <div><span>Created at:</span> <?php echo $row["created_at"]; ?>
          </div>
        </div>
      </div>
    <?php } ?>
  </div>
</div>

<?php
  echo "<table>";
  echo "<tr><th>ID</th><th>Email</th><th>Message</th><th>IP</th><th>Received On</th></tr>";

  while ($row = mysqli_fetch_assoc($result)) {
    echo "<tr>";
    echo "<td>" . $row["id"] . "</td>";
    echo "<td>" . htmlspecialchars($row["email"], ENT_QUOTES, 'UTF-8') . "</td>";
    echo "<td>" . htmlspecialchars($row["message"], ENT_QUOTES, 'UTF-8') . "</td>";
    echo "<td>" . $row["ip"] . "</td>";
    echo "<td>" . $row["created_at"] . "</td>";
    echo "</tr>";
  }

  echo "</table>";
} else {
  echo "<h2>No messages received yet!</h2>";
}

mysqli_close($mysqli);

include_once realpath("../components/tail.inc");

?>