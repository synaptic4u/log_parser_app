<?php

if (file_exists(dirname(__FILE__, 1).'/vendor/autoload.php')) {
    require_once dirname(__FILE__, 1).'/vendor/autoload.php';
}

// use Exception;
use Synaptic4UParser\Core\App;
use Synaptic4UParser\Core\Log;

try {
    $base_dir = dirname(__FILE__, 1);

    print_r('Depending on the size & quantity of your logs, this may take a while!'.PHP_EOL.PHP_EOL);

    print_r(
        'Please checkout the config files: '.PHP_EOL.
        $base_dir.'/config_path_list.json'.PHP_EOL.
        $base_dir.'/config.json'.PHP_EOL.
        $base_dir.'/db_config.json'.PHP_EOL.PHP_EOL
    );

    print_r('You can run the following script to see which process is writing to the database: '.PHP_EOL.'Please use a separate CLI tab...'.PHP_EOL);
    print_r($base_dir.'/progressReport.php'.PHP_EOL.PHP_EOL);

    print_r('You can view the live activity log here: '.PHP_EOL.$base_dir.'/logs/activity.txt'.PHP_EOL.PHP_EOL);

    $app = new App();

    print_r('Completed without breaking!'.PHP_EOL.PHP_EOL);
} catch (Exception $e) {
    new Log([
        'Location' => dirname(__FILE__, 1).'/app.php',
        'Exception' => $e->__toString(),
    ], 'error');
}
