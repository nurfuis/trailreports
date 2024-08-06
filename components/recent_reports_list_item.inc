<?php

while ($recent_report = $result->fetch_assoc()) {
    $id = $recent_report['id'];
    $title = htmlspecialchars($recent_report['report_title'], ENT_QUOTES, 'UTF-8');
    $feature = $recent_report['feature_name'];
?>
    <div class="recent-report">

        <span><?php echo $feature; ?></span> -
        <a href="./pages/trail_report.php?id=<?php echo $id; ?>"><?php echo $title; ?></a>
    </div>
<?php
}
?>