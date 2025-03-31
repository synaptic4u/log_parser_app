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

// use Exception;
use Synaptic4UParser\Core\App;
use Synaptic4UParser\Logs\Error;
use Synaptic4UParser\Logs\Log;

// declare(strict_types=1);

try {
    // Initializes Class::App
    $app = new App();
} catch (Exception $e) {
    new Log([
        'Location' => dirname(__FILE__, 1).'/app.php',
        'Exception' => $e->__toString(),
    ], new Error());
}
