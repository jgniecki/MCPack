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
    use DevLancer\MinecraftStatus\Query;

    $info = new Query("some.minecraftserver.com", 25565);
    $info->connect();
    
    $console = new ConsoleRcon("some.minecraftserver.com", 25575, "pass", 3);
    $console->connect();

    $players = $info->getCountPlayers();
    echo $players . "/" . $info->getMaxPlayers();

    $console->sendCommand("bc MCPack");
```

Look [here](https://github.com/jgniecki/MinecraftStatus)

### ServerManager with SSH

It enables downloading basic server information, sending commands and server management.

```php
<?php
    require 'vendor/autoload.php';
    
    use DevLancer\MCPack\ConsoleRcon;
    use DevLancer\MCPack\Manager\ServerManager;
    use DevLancer\MCPack\Sftp\Sftp;

    $host = "some.minecraftserver.com";
    $sftp = new Sftp($host);
    $sftp->login("username", "password");

    $server = new ServerManager($sftp, 25565);

    $path = "path/to/minecraft/server.jar";
    if(!$server->isRunning()) {
        if ($server->run(["-Xmx1G"], $path))
            echo "server started";
    }
```

### Server logs

This class allows downloading logs from the server.

```php
<?php
    require 'vendor/autoload.php';
    
    use DevLancer\MCPack\Logs;
    use DevLancer\MCPack\Sftp\Sftp;

    $host = "some.minecraftserver.com";
    $sftp = new Sftp($host);
    $sftp->login("username", "password");

    $path = "path/to/minecraft/logs/latest.log";
    $logs = new Logs($sftp, $path);
    echo implode("<br />", $logs->getLogs(true));
```

### Properties

```php
<?php
    require 'vendor/autoload.php';
    
    use DevLancer\MCPack\Manager\PropertiesManager;
    use DevLancer\MCPack\Sftp\Sftp;

    $sftp = new Sftp("some.minecraftserver.com");
    $sftp->login("username", "password");
    
    $manager = new PropertiesManager("path/to/minecraft/server.properties", $sftp);
    $properties = $manager->getProperties();
    $properties->setRconPassword("new-password");
    $manager->saveProperties($properties);
```

### Motd

```php
<?php
    require 'vendor/autoload.php';
    
    use DevLancer\MCPack\Motd;
    use DevLancer\MinecraftStatus\Ping;

    $host = "some.minecraftserver.com";
    $info = new Ping($host, 25565);
    $info->connect();
    
    $motd = new Motd($info);
    $motd->sendRequest();
    
    echo $motd->getResponse(Motd::RESPONSE_HTML);
```

Look [here](https://github.com/jgniecki/MinecraftMotdParser)

## License
[MIT](LICENSE)