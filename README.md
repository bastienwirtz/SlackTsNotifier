# SlackTsNotifier

SlackTsNotifier provide a slack command to list all connected users

## Installation

### Install deps:
```bash
composer install
```

### Configure the app:
create a config.ini file according to the following:

```ini
[teamspeak]
host = '127.0.0.1:10011'
server_port = 9987
user = 'serveradmin'
password = 'xxxxxxxx'

[slack]
endpoint = 'https://hooks.slack.com/services/xxxxxxxx/xxxxxxxx'
username = 'Teamspeak'
channel = '#gamez'
join_us_message = '*-> Join them !* server: <myTeamspeakServer>'
```

### serv content
Set up a webserver serving the **web** directory.   

## Usage

To activate the integration, you need to
- Add a custom slack command (*/teamspeak* for exemple) pointing on http://<YourSlackTsNotifier.tld>/app.php/clients
- Configure slack incoming webhook
