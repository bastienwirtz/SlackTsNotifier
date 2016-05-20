<?php
  namespace SlackTeamspeakIntegration;

  use TeamSpeak3\Helper\String;
  use TeamSpeak3\TeamSpeak3;
  use TeamSpeak3\Helper\Signal;
  use TeamSpeak3\Node\Host;
  use TeamSpeak3\Adapter\ServerQuery\Event;

  use Maknz;

  class EventWorker {

      private $config;
      private $ts3Server;

      const EVENT_CATEGORY = "server";
      const EVENT_CONNECT = "notifyCliententerview";
      const EVENT_DISCONNECT = "notifyClientleftview";

      /**
       * EventWorker constructor.
       *
       * @param $logger
       */
      public function __construct($logger) {

          $cfg = new Config($logger);
          $this->config  = $cfg->get();
          $this->logger  = $logger;

          $TsServerConfig = array(
              $this->config['teamspeak']['user'],
              $this->config['teamspeak']['password'],
              $this->config['teamspeak']['host'],
              $this->config['teamspeak']['server_port']
          );

          $serverUri = vsprintf("serverquery://%s:%s@%s/?server_port=%d&blocking=0", $TsServerConfig);

          try{
            $this->ts3Server = TeamSpeak3::factory($serverUri);
          } catch (\Exception $e) {
              echo '[slack-teamspeak-integration] Fail to connect to the TeamSpeak server';
              exit;
          }

          // Listen for user connection / disconnection from the server
          $this->ts3Server->notifyRegister(self::EVENT_CATEGORY);
          Signal::getInstance()->subscribe(self::EVENT_CONNECT, array($this, self::EVENT_CONNECT));
          Signal::getInstance()->subscribe(self::EVENT_DISCONNECT, array($this, self::EVENT_DISCONNECT));
      }

      /**
       * Listen for events
       */
      public function run(){
          while(1) $this->ts3Server->getAdapter()->wait();
      }

      /**
       * User connect event handler
       *
       * @param Event $event
       * @param Host $host
       */
      public function notifyCliententerview(Event $event, Host $host)
      {
          $eventData = $event->getData();

          $this->send($eventData['client_nickname']);
      }

      /**
       * User disconnect event handler
       *
       * @param Event $event
       * @param Host $host
       */
      public function notifyClientleftview(Event $event, Host $host)
      {
          // @todo
      }

      /**
       * Send a message to the configured slack channel
       * @param $message
       * @return null
       */
      protected function send($message) {
          $settings = [
              'username' => $this->config['slack']['username'],
              'channel'  => $this->config['slack']['channel']
          ];

          $client = new Maknz\Slack\Client($this->config['slack']['endpoint'],$settings);
          $client->attach([
              'text' => (String) $message,
              'color' => 'good',
          ])->send('New user connected');
      }
  }
