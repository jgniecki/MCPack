<?php declare(strict_types=1);
/**
 * @author Jakub Gniecki
 * @copyright Jakub Gniecki <kubuspl@onet.eu>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */


namespace DevLancer\MCPack;


use DevLancer\MinecraftStatus\AbstractQuery;
use DevLancer\MinecraftStatus\Exception\ConnectionException;
use DevLancer\MinecraftStatus\Exception\NotConnectedException;
use DevLancer\MinecraftStatus\Exception\ReceiveStatusException;
use DevLancer\MinecraftStatus\PlayerListInterface;

/**
 * Class Query
 * @package DevLancer\MCPack
 * @deprecated since dev-lancer/mc-pack 2.2, use \DevLancer\MinecraftStatus\Query instead
 */
class Query implements ServerInfo
{
    private AbstractQuery $query;

    /**
     * Query constructor.
     * @param string $host
     * @param int $port
     * @param int $timeout
     * @param AbstractQuery|null $query
     */
    public function __construct(string $host, int $port = 25565, int $timeout = 3, ?Object $query = null)
    {
        if (!$query instanceof AbstractQuery) {
            $query = new \DevLancer\MinecraftStatus\Query($host, $port, $timeout);
        }

        $this->query = $query;
    }

    public function connect(): bool
    {
        try {
            $this->query->connect();
        } catch (ReceiveStatusException|ConnectionException $e) {
            return false;
        }

        return true;
    }

    public function isConnected(): bool
    {
        return $this->query->isConnected();
    }

    public function getPlayers(): array
    {
        if (!$this->query instanceof PlayerListInterface)
            return [];

        try {
            return $this->query->getPlayers();
        } catch (NotConnectedException $e) {
            return [];
        }
    }

    public function getInfo(): array
    {
        try {
            return $this->query->getInfo();
        } catch (NotConnectedException $e) {
            return [];
        }
    }

    public function getCountPlayers(): int
    {
        try {
            return $this->query->getCountPlayers();
        } catch (NotConnectedException $e) {
            return 0;
        }
    }

    public function getMaxPlayers(): int
    {
        try {
            return $this->query->getMaxPlayers();
        } catch (NotConnectedException $e) {
            return 0;
        }
    }

    public function getMotd($type = null): ?string
    {
        try {
            return $this->query->getMotd();
        } catch (NotConnectedException $e) {
            return null;
        }
    }
}
