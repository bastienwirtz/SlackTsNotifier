<?php
    namespace SlackTeamspeakIntegration;

    class Config {

        const CONFIG_FILE_PATH = __dir__ . "/../../config.ini";

        private $config;
        private $logger;

        function __construct($logger) {
            $this->logger = $logger;
            $this->config = $this->loadConfig();
        }

        /**
         * Read config file
         * @return null
         */
        private function loadConfig() {

            if (!is_readable(self::CONFIG_FILE_PATH)) {
                $this->logger->critical('[slack-teamspeak-integration] Unable to find config file');
                return;
            }

            $config = parse_ini_file(self::CONFIG_FILE_PATH, true);
            if (!$config) {
                $this->logger->critical('[slack-teamspeak-integration] Fail to load config file');
                return;
            }

            return $config;
        }

        /**
         * Get configuration
         */
        public function get() {
            return $this->config;
        }
    }

