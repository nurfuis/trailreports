<form id="showReportForm" action="show_report.php" method="post"
    onsubmit="return confirmShowReport(<?php echo $report['id']; ?>)">
    <input type="hidden" name="report_id" value="<?php echo $report['id']; ?>">
    <button type="submit">Show Report</button>
</form>
<script>
    function confirmShowReport(reportId) {
        if (confirm("Are you sure you want to show this report?")) {
            document.getElementById("showReportForm").submit(); // Submit the form if confirmed
        } else {
            // Do nothing if user cancels
        }
        return false; // Prevent default form submission
    }
</script>