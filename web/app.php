<?php

    require '../vendor/autoload.php';

    use Slim\Slim;
    use SlackTeamspeakIntegration\SlackTsNotifier;


    $app = new Slim();

    /**
     * List TeamSpeak connected clients for custom slack slash commands
     * @see https://api.slack.com/slash-commands
     */
    $app->post('/clients', function () use ($app) {
        $slackTsNotifier = new SlackTsNotifier($app->getLog());
        $slackTsNotifier->sendUserList();
    });

    $app->run();
