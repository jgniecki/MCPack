<?php declare(strict_types=1);
/**
 * @author Jakub Gniecki
 * @copyright Jakub Gniecki <kubuspl@onet.eu>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */


namespace DevLancer\MCPack;


use xPaw\MinecraftPing;
use xPaw\MinecraftPingException;

/**
 * Class Ping
 * @package DevLancer\MCPack
 */
class Ping implements ServerInfo
{
    /**
     * @var MinecraftPing|null
     */
    private ?MinecraftPing $ping = null;

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
     * @var string
     */
    private string $host;

    /**
     * @var int
     */
    private int $port;

    /**
     * @var int
     */
    private int $timeout;

    /**
     * @var bool
     */
    private bool $oldPre17;

    /**
     * Ping constructor.
     * @param string $host
     * @param int $port
     * @param bool $oldPre17
     * @param int $timeout
     * @param MinecraftPing|null $ping
     */
    public function __construct(string $host, int $port = 25565, bool $oldPre17 = false, int $timeout = 3, ?MinecraftPing $ping = null)
    {
        if (!$ping)
            $this->ping = $ping;

        $this->host = $host;
        $this->port = $port;
        $this->oldPre17 = $oldPre17;
        $this->timeout = $timeout;
    }

    /**
     * @inheritDoc
     */
    public function connect(): bool
    {
        try {
            if (!$this->ping)
                $this->create();

            $this->ping->Connect();
            $connected = true;
        } catch (MinecraftPingException $exception) {
            $connected = false;
        }

        return $connected;
    }
    
    private function create(): void
    {
        $this->ping = new MinecraftPing($this->host, $this->port, $this->timeout);
    }

    /**
     * @inheritDoc
     */
    public function isConnected(): bool
    {
        return $this->connect();
    }

    /**
     * @inheritDoc
     */
    public function getPlayers(): array
    {
        if (!$this->isConnected())
            return [];

        return $this->getInfo()['players']['sample'] ?? [];
    }

    /**
     * @inheritDoc
     */
    public function getCountPlayers(): int
    {
        if (!$this->isConnected())
            return 0;

        return $this->ping->Query()['players']['online'];
    }

    /**
     * @inheritDoc
     */
    public function getMaxPlayers(): int
    {
        if (!$this->isConnected())
            return 0;

        return $this->ping->Query()['players']['max'];
    }

    /**
     * @inheritDoc
     */
    public function getInfo(): array
    {
        if (!$this->isConnected())
            return [];

        return (array) $this->ping->Query();
    }

    /**
     * @return string|null
     * @throws MinecraftPingException
     */
    public function getFavicon(): ?string
    {
        if (!$this->isConnected())
            return null;

        return (string) $this->ping->Query()['favicon'];
    }
}