<?php
require_once realpath("../../db_connect.php");

$page_title = "Contact";
$stylesheet = "/assets/css/style.css";

include_once realpath("../components/head.inc");
include_once realpath("../layouts/wide.inc");

?>
<div class="regular-padding">
    <h1>Contact</h1>
    <h2>Send a Message</h2>
    <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
        <label for="email">Email Address:</label>
        <input type="email" id="email" name="email" placeholder="Enter your email" required>
        <br>
        <!-- TODO add honeypot input -->
        <label for="message">Message:</label><br>
        <textarea id="message" name="message" rows="5" placeholder="Compose your message" required></textarea>
        <br>
        <button type="submit">Send Message</button>
    </form>
</div>
<?php
// Check if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $ip = $_SERVER['REMOTE_ADDR'];
    $maxSubmissionsPerHour = 5;
    $expireTime = time() - 36000;
  
    $sql = "SELECT COUNT(*) AS submissions FROM contact_messages WHERE ip = ? AND created_at > ?";

    $stmt = $mysqli->prepare($sql);
    $stmt->bind_param("ss", $ip, date('Y-m-d H:i:s', $expireTime));
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    $submissions = (int) $row['submissions'];
    $stmt->close();

    if ($submissions >= $maxSubmissionsPerHour) {
      $errorMessage = "Too many submissions. Please try again later.";
      goto after_validation;
    }
  
    $honeyPot = isset($_POST['honeyPot']) ? $_POST['honeyPot'] : '';
    if (!empty($honeyPot)) {
      $errorMessage = "Invalid submission.";
      goto after_validation;
    }


    $email = $_POST["email"];
    $message = $_POST["message"];

  
    $sql = "INSERT INTO contact_messages (email, message, ip) VALUES (?, ?, ?)";
    $stmt = $mysqli->prepare($sql);
    $stmt->bind_param("sss", $email, $message, $ip);
  
    if ($stmt->execute() === TRUE) {
        echo '<p class="success">Thank you for contacting us! We will get back to you soon.</p>';
    } else {
        echo "Error: " . $sql . "<br>" . $mysqli->error;
    }
  
    $stmt->close();

    after_validation:
    if (!!$errorMessage) {
        echo '<p class="alert">Wooopsie daisy... ' . $errorMessage . '</p>';
    }

    $mysqli->close();
}

include_once realpath("../components/tail.inc");
?>

