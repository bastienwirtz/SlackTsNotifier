<?php
    /**
     * Listen for teamspeak server events.
     */

    require './vendor/autoload.php';

    use Slim\Slim;
    use SlackTeamspeakIntegration\EventWorker;

    $app = new Slim();
    $app->config('debug', false);
    
    $eventWorker = new EventWorker($app->getLog());
    $eventWorker->run();
