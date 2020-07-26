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
    
    use DevLancer\MCPack\Query;
    use DevLancer\MCPack\ServerManager;
    use DevLancer\MCPack\Server;
    use Thedudeguy\Rcon;

    $query = new Query("some.minecraftserver.com", 25565);
    $rcon = new Rcon("some.minecraftserver.com", 25575, "pass", 3);
    $server = new Server($query, $rcon);
    $server_manager = new ServerManager($server);

    $players = count($server_manager->getPlayers());
    echo $players . "/" . $server_manager->getMaxPlayer();
```

### Query & Rcon with Shell

It enables downloading basic server information, sending commands and server management.

```php
<?php
    require 'vendor/autoload.php';
    
    use DevLancer\MCPack\Query;
    use DevLancer\MCPack\ServerManagerSell;
    use DevLancer\MCPack\Server;
    use phpseclib\Net\SFTP;
    use Thedudeguy\Rcon;

    $host = "some.minecraftserver.com";
    $login = "user";
    $password = "password";
    $sftp = new SFTP($host);
    $sftp->login($login, $password);
    $sftp->setTimeout(3);

    $path = "path/to/minecraft/server.jar";
    $query = new Query($host, 25565);
    $rcon = new Rcon($host, 25575, "pass", 3);
    $server = new Server($query, $rcon, 25565, $sftp, $path);
    $server_manager = new ServerManagerSell($server);

    if(!$server_manager->isRunning()) {
        if ($server_manager->run(1024))
            echo "server started";
    }
```

### Server logs

This class allows downloading logs from the server.

```php
<?php
    require 'vendor/autoload.php';
    
    use DevLancer\MCPack\Logs;
    use phpseclib\Net\SFTP;

    $host = "some.minecraftserver.com";
    $login = "user";
    $password = "password";
    $sftp = new SFTP($host);
    $sftp->login($login, $password);
    $sftp->setTimeout(3);

    $path = "path/to/minecraft/logs/lastest.log";
    $logs = new Logs($sftp, $path);
    echo $logs->getLogs(true);
```

## License
[MIT](LICENSE)