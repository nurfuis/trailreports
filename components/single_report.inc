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
$userId = $report['user_id'];

?>

<div class="single-report">
    <?php if (is_admin() && $report['active'] == 1) : ?>

        <form id="hideReportForm" action="hide_report.php" method="post" onsubmit="return confirmHideReport()">
            <input type="hidden" name="report_id" value="<?php echo $report['id']; ?>">
            <button type="submit">Hide Report</button>
        </form>

        <a class="edit" href="./edit_report.php?id=<?php echo $reportId; ?>"><span>Edit</span></a>

        <script>
            function confirmHideReport() {
                const reportId = document.getElementById("hideReportForm").elements["report_id"].value;
                if (confirm(`Are you sure you want to hide report?`)) {
                    return true;
                } else {
                    return false;
                }
            }
        </script>
    <?php endif; ?>


    <div class="whitespace-container">
        <div class="vertical-divider"></div>

        <div class="scroll-text">scroll</div>
        <div class="vertical-divider"></div>
    </div>

    <div class="slider__header-container">
        <h1 class="slider__header">Trail Report</h1>
    </div>

    
    <div class="content">
        <div class="trigger-element">Trigger slide-in</div>
        <div class="second-element">Second element</div>
    </div>

    <script>
        const header = document.querySelector('.slider__header');

        function slideInHeader() {
            header.style.left = '0';
        }

        function slideOutHeader() {
            header.style.left = '-100%';
        }
        const triggerElement = document.querySelector('.trigger-element');
        const secondElement = document.querySelector('.second-element');

        const headerOptions = {
            rootMargin: '0px 0px -100px 0px' // Trigger when element is 100px above viewport
        };

        const headerObserver = new IntersectionObserver(entries => {
            if (entries[0].isIntersecting) {
                header.classList.add('slide-in');
                headerObserver.unobserve(triggerElement); // Stop observing after first intersection
            }
        }, headerOptions);

        headerObserver.observe(triggerElement);

        // Optional: Observe second element to trigger another action
        const secondElementObserver = new IntersectionObserver(entries => {
            if (entries[0].isIntersecting) {
                // Trigger action when second element enters viewport
                console.log('Second element entered viewport');
            }
        });

        secondElementObserver.observe(secondElement);
    </script>

    <h2><?php echo $report['feature_name']; ?></h2>
    <p><strong>Submitted by:</strong> <?php echo $report['username']; ?></p>
    <p><strong><?php echo $postedOnText; ?></strong><?php echo $formattedTime; ?></p>


    <h3><?php echo nl2br($title); ?> </h3>
    <p><strong>Rating:</strong> <?php echo $ratings[$report['rating']]; ?></p>
    <p class="indented larger"><?php echo nl2br($summary); ?></p>
</div>