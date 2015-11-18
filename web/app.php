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
        $channel = $app->request->post('channel_name');
        $slackTsNotifier = new SlackTsNotifier($app->getLog(), $channel);
        $slackTsNotifier->sendUserList();
    });

    $app->run();
