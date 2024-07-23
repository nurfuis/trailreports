<?php

$page_title = "Contact";
$page_css = "/assets/css/style.css";

require_once realpath("../../db_connect.php");
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
        <label for="message">Message:</label><br>
        <textarea id="message" name="message" rows="5" placeholder="Compose your message" required></textarea>
        <br>
        <button type="submit">Send Message</button>
    </form>
</div>
<?php
// Check if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST["email"];
    $message = $_POST["message"];
    // Check connection
    if ($mysqli->connect_error) {
        die("Connection failed: " . $mysqli->connect_error);
    }

    $sql = "INSERT INTO contact_messages (email, message) VALUES ('$email', '$message')";

    if ($mysqli->query($sql) === TRUE) {
        echo '<p class="success">Thank you for contacting us! We will get back to you soon.</p>';
    } else {
        echo "Error: " . $sql . "<br>" . $mysqli->error;
    }

    $mysqli->close();
}

include_once realpath("../components/tail.inc");
?>