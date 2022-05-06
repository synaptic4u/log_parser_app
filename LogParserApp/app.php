<?php
/**
 * Application entry script.
 * Called from the command line.
 * Shows the user the link to the Progress Report and activity log.
 * Creates an instance of the App class.
 */
if (file_exists(dirname(__FILE__, 1).'/vendor/autoload.php')) {
    require_once dirname(__FILE__, 1).'/vendor/autoload.php';
}

use Synaptic4UParser\Core\App;
use Synaptic4UParser\Core\Log;

try {
    $base_dir = dirname(__FILE__, 1);

    // User messages
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

    // Initiates Class::App
    $app = new App();

    // End output of app.
    print_r('Completed without breaking!'.PHP_EOL.PHP_EOL);
} catch (Exception $e) {
    new Log([
        'Location' => dirname(__FILE__, 1).'/app.php',
        'Exception' => $e->__toString(),
    ], 'error');
}
