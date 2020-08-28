# PHP MCPack [![Packagist](https://img.shields.io/packagist/dt/dev-lancer/mc-pack.svg)](https://packagist.org/packages/dev-lancer/mc-pack)

## Installation
This library can installed by issuing the following command:
```bash
composer require dev-lancer/mc-pack
```

### Query
This method uses GameSpy4 protocol, and requires enabling `query` listener in your `server.properties` like this:

> *enable-query=true*<br>
> *query.port=25565*

### Rcon
This method allows you to send commands, it is used in item shop, and requires enabling `rcon` listener in your `server.properties` like this:

> *enable-rcon=true*<br>
> *rcon.port=25575*<br>
> *rcon.password=pass*

## Example
### Query & Rcon

It enables downloading basic server information and sending commands.

```php
<?php
    require 'vendor/autoload.php';
    
    use DevLancer\MCPack\ConsoleRcon;
    use DevLancer\MCPack\Query;
    use DevLancer\MCPack\ServerManager;

    $info = new Query("some.minecraftserver.com", 25565);
    $console = new ConsoleRcon("some.minecraftserver.com", 25575, "pass", 3);
    $server = new ServerManager($info, $console);

    $players = $server_manager->getInfo()->getCountPlayers();
    echo $players . "/" . $server_manager->getInfo()->getMaxPlayers();

    $server_manager->getConsole()->sendCommand("bc MCPack");
```

### Query & Rcon with SSH

It enables downloading basic server information, sending commands and server management.

```php
<?php
    require 'vendor/autoload.php';
    
    use DevLancer\MCPack\ConsoleRcon;
    use DevLancer\MCPack\Query;
    use DevLancer\MCPack\ServerManagerSsh;
    use DevLancer\MCPack\Ssh;
    use phpseclib\Net\SFTP;

    $host = "some.minecraftserver.com";
    $ssh = new Ssh(new SFTP($host), "username", "password");


    $info = new Query($host, 25565);
    $console = new ConsoleRcon($host, 25575, "pass", 3);
    $server = new ServerManagerSsh($info, $console, $ssh, 25565);

    $path = "path/to/minecraft/server.jar";
    if(!$server_manager->isRunning()) {
        if ($server_manager->run(["-Xmx1G"], $path))
            echo "server started";
    }
```

### Server logs

This class allows downloading logs from the server.

```php
<?php
    require 'vendor/autoload.php';
    
    use DevLancer\MCPack\Logs;
    use DevLancer\MCPack\Ssh;
    use phpseclib\Net\SFTP;

    $host = "some.minecraftserver.com";
    $ssh = new Ssh(new SFTP($host), "username", "password");

    $path = "path/to/minecraft/logs/latest.log";
    $logs = new Logs($ssh->getSftp(), $path);
    echo implode("<br />", $logs->getLogs(true));
```

### Properties

```php
<?php
    require 'vendor/autoload.php';
    
    use DevLancer\MCPack\Properties;
    use DevLancer\MCPack\Ssh;
    use phpseclib\Net\SFTP;

    $host = "some.minecraftserver.com";
    $ssh = new Ssh(new SFTP($host), "username", "password");

    $properties = new Properties($ssh->getSftp(), "path/to/minecraft/server.properties");
    $port = (int) $properties->getProperty("server-port");
    $query_port = (int) $properties->getProperty("query.port");
    $rcon_port = (int) $properties->getProperty("rcon.port");
    $rcon_pass = $properties->getProperty("rcon.password");
```

### Motd

```php
<?php
    require 'vendor/autoload.php';
    
    use DevLancer\MCPack\Motd;use DevLancer\MCPack\Ping;

    $host = "some.minecraftserver.com";
    $info = new Ping($host, 25565);

    $motd = new Motd($info);
    $motd->sendRequest(Motd::REQUEST_EXTRA);
    
    echo $motd->getResponse(Motd::RESPONSE_HTML);
```

## License
[MIT](LICENSE)