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
use DevLancer\MinecraftStatus\Exception\ReceiveStatusException;

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

    /**
     * @inheritDoc
     */
    public function connect(): bool
    {
        try {
            $this->query->connect();
        } catch (ReceiveStatusException|ConnectionException $e) {
            return false;
        }

        return true;
    }

    /**
     * @inheritDoc
     */
    public function isConnected(): bool
    {
        return $this->query->isConnected();
    }

    /**
     * @inheritDoc
     */
    public function getPlayers(): array
    {
        if (!$this->isConnected())
            return [];

        return (array) $this->query->getPlayers();
    }

    /**
     * @inheritDoc
     */
    public function getInfo(): array
    {
        if (!$this->isConnected())
            return [];

        return (array) $this->query->getInfo();
    }

    /**
     * @inheritDoc
     */
    public function getCountPlayers(): int
    {
        return $this->query->getCountPlayers();
    }

    /**
     * @inheritDoc
     */
    public function getMaxPlayers(): int
    {
        if (!$this->isConnected())
            return 0;

        return (int) $this->query->getMaxPlayers();
    }

    /**
     * @param null $type
     * @return string|null
     */
    public function getMotd($type = null): ?string
    {
        if (!$this->isConnected())
            return null;

        return (string) $this->query->getMotd();
    }
}
