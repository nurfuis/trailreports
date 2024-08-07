<?php

$isUpdated = false;
// $isUpdated = $report['time_updated'] !== $report['created_at'];
$postedOnText = $isUpdated ? 'Updated:' : 'Posted:';

$OVERALL_RATINGS = [
    'Good' => 1,
    'Passable' => 2,
    'Poor' => 3,
    'Impassable' => 4,
    'Gone' => 5
];

$ratings = array_flip($OVERALL_RATINGS);

$time = $report['time_updated'];
$formattedTime = date("F j, Y", strtotime($time));
$title = htmlspecialchars($report['report_title'], ENT_QUOTES, 'UTF-8');
$summary = htmlspecialchars($report['summary'], ENT_QUOTES, 'UTF-8');
$submitterId = $report['user_id'];
$reportId = $report['id'];
$userId = get_user_id();
$geometryType = $report["geometry_type"];
$selected_feature_id = $report['feature_id'];
?>

<div class="single-report">
    <!-- <div class="whitespace-container">
        <div class="vertical-divider"></div>

        <div class="scroll-text">scroll</div>
        <div class="vertical-divider"></div>
    </div> -->


    <?php if ($userId == $submitterId || is_admin()) : ?>
        <a class="small-button" href="/pages/edit_report.php?id=<?php echo $reportId; ?>"><span>Edit</span></a>

    <?php endif; ?>

    <div class="slider__header-container">
    </div>
    <div class="map-icon__wrapper">
        <img class="map-icon" src="../assets/images/map-icon-lg.webp">
    </div>
    <h1>Trail Report</h1>

    <h2><?php echo $report['feature_name']; ?></h2>

    <p><strong>Submitted by:</strong> <?php echo $report['username']; ?></p>
    <p><strong><?php echo $postedOnText; ?></strong> <?php echo $formattedTime; ?></p>


    <h3><?php echo nl2br($title); ?> </h3>

    <p><strong>Rating:</strong> <?php echo $ratings[$report['rating']]; ?></p>
    <p class="indented larger">
        <?php echo nl2br($summary); ?>
    </p>
    <!-- 
    <div class="whitespace-container">
        <div class="vertical-divider"></div>

        <div class="scroll-text">scroll</div>
        <div class="vertical-divider"></div>
    </div> -->
</div>