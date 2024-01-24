Current Time : <?php echo $app_timer['date_time']; ?><?php echo $eol; ?>
Unix start: <?php echo $app_timer['start']; ?><?php echo $eol; ?>
Unix finish: <?php echo $app_timer['finish']; ?><?php echo $eol; ?>
Duration min:sec: <?php echo $app_timer['duration_min_sec']; ?><?php echo $eol; ?>
Duration sec.microseconds: <?php echo $app_timer['duration_sec_microseconds']; ?><?php echo $eol; ?><?php echo $eol; ?>

<?php foreach ($log_files as $report) {
    foreach ($report as $key => $value) {
        switch (true) {
        case 'start' === $key:
            echo $key.' '.$value; echo $eol;

            break;

        case 'finish' === $key:
            echo $key.' '.$value; echo $eol;

            break;

        case 'duration_min_sec' === $key:
            echo $key.' '.$value; echo $eol;

            break;

        case 'duration_sec_microseconds' === $key:
            echo $key.' '.$value; echo $eol;

            break;

        case 'nu_rows' === $key:
            echo $key.' '.$value; echo $eol;

            break;

        case 'nu_result' === $key:
            echo $key.' '.$value; echo $eol;

            break;
        }
    }
} ?>