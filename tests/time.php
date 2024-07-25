<?php
// Server time
echo "Server time: " . date('Y-m-d H:i:s') . "<br>";

// Machine time (assuming you're running this on a machine with PHP)
echo "Machine time: " . date('Y-m-d H:i:s', time()) . "<br>";

// California time (assuming Pacific Daylight Time)
date_default_timezone_set('America/Los_Angeles');
echo "California time: " . date('Y-m-d H:i:s') . "<br>";
?>