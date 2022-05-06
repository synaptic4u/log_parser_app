<?php
/**
 * Progress Report.
 * Called from the command line.
 * Calculates the variance with a 10 second time delay to find which table is being written too.
 * Produces a summary of all the tables in the database, showing the number of rows in each table.
 */
if (file_exists(dirname(__FILE__, 1).'/vendor/autoload.php')) {
    require_once dirname(__FILE__, 1).'/vendor/autoload.php';
    // var_dump(dirname(__FILE__, 1).'/vendor/autoload.php');
}

// use Exception;
use Synaptic4UParser\Core\Log;
use Synaptic4UParser\DB\DB;

/**
 * Gets a list of all the tables.
 * Cycles through the list querying the row count for each table.
 *
 * @return $table_report object - each row in object has row count
 */
function getCount(): mixed
{
    $db = new DB();

    $table_list = [];
    $table_report = [];

    $sql = 'show tables where 1=?;';
    $result = $db->query([1], $sql);

    foreach ($result as $res) {
        $table_list[] = $res[0];
    }

    foreach ($table_list as $table) {
        $sql = 'select count(*) as num from '.$table.' where 1=?;';
        $result = $db->query([1], $sql);
        $table_report[$table] = $result[0]['num'];
    }

    return $table_report;
}

try {
    $start = microtime(true);

    print_r('The report sleeps for 10 sec to calculate the active process.'.PHP_EOL);
    print_r('With large tables, it may take longer.'.PHP_EOL.PHP_EOL);

    $report = getCount();

    sleep(10);

    $report2 = getCount();

    $diff = array_diff($report, $report2);

    foreach ($diff as $key => $value) {
        $report[$key] = [
            'status' => 'Active',
            'count1' => $report[$key],
            'count2' => $report2[$key],
            'difference' => $report2[$key] - $report[$key],
        ];
    }

    $tot_records = array_sum($report2);

    $finish = microtime(true);

    $app_timer = [
        'Date & Time:               ' => date('Y-m-d H:i:s'),
        'Total records:             ' => number_format($tot_records),
        // 'Start:                     ' => $start,
        // 'Finish:                    ' => $finish,
        'Duration min:sec:          ' => (($finish - $start) > 60) ? (floor(($finish - $start) / 60)).':'.(($finish - $start) % 60) : '0:'.(($finish - $start) % 60),
        'Duration sec.microseconds: ' => $finish - $start,
    ];

    print_r('App timer: '.date('Y-m-d H:i:s').PHP_EOL);
    print_r('Total records: '.number_format($tot_records).PHP_EOL);
    print_r('App Stats: '.json_encode($app_timer, JSON_PRETTY_PRINT).PHP_EOL.PHP_EOL);

    $result = array_merge([
        'Table names' => 'Rows per table',
    ], $report);

    print_r('DB Stats: '.json_encode($report, JSON_PRETTY_PRINT).PHP_EOL);
} catch (Exception $e) {
    new Log([
        'Location' => dirname(__FILE__, 1).'/app.php',
        'Exception' => $e->__toString(),
    ], 'error');
}
