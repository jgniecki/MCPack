<?php declare(strict_types=1);
/**
 * @author Jakub Gniecki
 * @copyright Jakub Gniecki <kubuspl@onet.eu>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */


namespace DevLancer\MCPack;


use DevLancer\MinecraftStatus\AbstractPing;
use DevLancer\MinecraftStatus\Exception\ConnectionException;
use DevLancer\MinecraftStatus\Exception\ReceiveStatusException;
use DevLancer\MinecraftStatus\FaviconInterface;
use DevLancer\MinecraftStatus\PingPreOld17;
use DevLancer\MinecraftStatus\PlayerListInterface;

/**
 * Class Ping
 * @package DevLancer\MCPack
 * @deprecated since dev-lancer/mc-pack 2.2, use \DevLancer\MinecraftStatus\Ping instead
 */
class Ping implements ServerInfo
{
    private AbstractPing $ping;

    /**
     *
     */
    const MOTD_RAW = "text";
    /**
     *
     */
    const MOTD_EXTRA = "extra";
    /**
     *
     */
    const MOTD_SAMPLE = "sample";


    /**
     * Ping constructor.
     * @param string $host
     * @param int $port
     * @param bool $oldPre17
     * @param int $timeout
     * @param AbstractPing|null $ping
     */
    public function __construct(string $host, int $port = 25565, bool $oldPre17 = false, int $timeout = 3, ?Object $ping = null)
    {
        if (!$ping instanceof AbstractPing) {
            $ping = ($oldPre17)? new \DevLancer\MinecraftStatus\Ping($host, $port, $timeout) : new PingPreOld17($host, $port, $timeout);
        }

        $this->ping = $ping;
    }

    /**
     * @inheritDoc
     */
    public function connect(): bool
    {
        try {
            $this->ping->connect();
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
        return $this->ping->isConnected();
    }

    /**
     * @inheritDoc
     */
    public function getPlayers(): array
    {
        if (!$this->isConnected())
            return [];

        if (!$this->ping instanceof PlayerListInterface)
            return [];

        return $this->ping->getPlayers();
    }

    /**
     * @inheritDoc
     */
    public function getCountPlayers(): int
    {
        if (!$this->isConnected())
            return 0;

        return $this->ping->getCountPlayers();
    }

    /**
     * @inheritDoc
     */
    public function getMaxPlayers(): int
    {
        if (!$this->isConnected())
            return 0;

        return $this->ping->getMaxPlayers();
    }

    /**
     * @inheritDoc
     */
    public function getInfo(): array
    {
        if (!$this->isConnected())
            return [];

        return (array) $this->ping->getInfo();
    }

    /**
     * @return string|null
     */
    public function getFavicon(): ?string
    {
        if (!$this->isConnected())
            return null;

        if (!$this->ping instanceof FaviconInterface)
            return null;

        return (string) $this->ping->getFavicon();
    }
}