<?php
    namespace SlackTeamspeakIntegration;

    use Maknz;
    use TeamSpeak3\TeamSpeak3;

    class SlackTsNotifier {

        private $config;
        private $logger;
        private $ts3Server;

        /**
         * Connect to TeamSpeak3 server
         */
        function __construct($logger) {

          $cfg = new Config($logger);
          $this->config = $cfg->get();
          $this->logger = $logger;

          $TsServerConfig = array(
              $this->config['teamspeak']['user'],
              $this->config['teamspeak']['password'],
              $this->config['teamspeak']['host'],
              $this->config['teamspeak']['server_port']
          );

          $serverUri = vsprintf("serverquery://%s:%s@%s/?server_port=%d", $TsServerConfig);

          try{
            $this->ts3Server = TeamSpeak3::factory($serverUri);
          } catch (\Exception $e) {
            $logger->critical('[slack-teamspeak-integration] Fail to connect to the TeamSpeak server');
          }
        }

        /**
         * List TeamSpeak connected clients
         * @return array
         */
        protected function getTeamspeakUsers() {

          if (is_null($this->ts3Server))
            return array();

          // filter client_type to exclude server query command
          $clients = $this->ts3Server->clientList(array('client_type' => 0));
          $usernames = array_map('strval', $clients);

          return $usernames;
        }

        /**
         * Send a message to the configured slack channel
         */

        /**
         * Send a message to the configured slack channel
         * @param $message
         * @return null
         */
        protected function send($message){
          $settings = [
              'username' => $this->config['slack']['username'],
              'channel' => $this->config['slack']['channel']
          ];

          $client = new Maknz\Slack\Client($this->config['slack']['endpoint'],$settings);
          $client->send($message);
        }

        /**
         * Send the connected users list to Slack
         * @return null
         */
        public function sendUserList() {

          $users = $this->getTeamspeakUsers();
          $message = count($users) . " user(s) connected\n";

          foreach ($users as $user) {
            $message .= "\t - " . $user . "\n";
          }
          $message .= "\n" . $this->config['slack']['join_us_message'];

          $this->send($message);
        }
    }
