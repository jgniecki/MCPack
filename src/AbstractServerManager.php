<?php declare(strict_types=1);
/**
 * @author Jakub Gniecki
 * @copyright Jakub Gniecki <kubuspl@onet.eu>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */


namespace DevLancer\MCPack;


/**
 * Class AbstractServerManager
 * @package DevLancer\MCPack
 */
abstract class AbstractServerManager
{
    /**
     * @var Server
     */
    protected Server $server;

    /**
     * AbstractServerManager constructor.
     * @param Server $server
     */
    public function __construct(Server $server)
    {
        $this->server = $server;
    }


    private function connectRcon(): void
    {
        $rcon = $this->server->getRcon();
        if (!$rcon->isConnected())
            $rcon->connect();
    }

    /**
     * @param string $command
     * @return bool
     */
    public function sendCommand(string $command): bool
    {
        $this->connectRcon();
        if (!$this->server->getRcon()->isConnected())
            return false;

        return (bool) $this->server->getRcon()->sendCommand($command);
    }

    /**
     * @return string|null
     */
    public function responseCommand(): ?string
    {
        if (!$this->server->getRcon()->isConnected())
            return null;

        return $this->server->getRcon()->getResponse();
    }

    /**
     * @return Server
     */
    public function getServer(): Server
    {
        return $this->server;
    }

    /**
     * @return array
     */
    public function getPlayers(): array
    {
        return $this->server->getQuery()->getPlayers();
    }

    /**
     * @return int
     */
    public function getMaxPlayers(): int
    {
        if ($this->isOnline())
            return $this->server->getQuery()->getInfo()['MaxPlayers'];

        return 0;
    }

    /**
     * @param string $player
     * @return bool
     */
    public function isPlayer(string $player): bool
    {
        return (bool) in_array($player, $this->getPlayers());
    }

    /**
     * @return bool
     */
    public function isOnline(): bool
    {
        return $this->server->getQuery()->isConnected();
    }
}