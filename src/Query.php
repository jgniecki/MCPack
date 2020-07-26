<?php declare(strict_types=1);
/**
 * @author Jakub Gniecki
 * @copyright Jakub Gniecki <kubuspl@onet.eu>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */


namespace DevLancer\MCPack;


use xPaw\MinecraftQueryException;
use xPaw\MinecraftQuery;

/**
 * Class Query
 * @package DevLancer\MCPack
 */
class Query
{
    /**
     * @var MinecraftQuery
     */
    private MinecraftQuery $query;

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
     * Query constructor.
     * @param string $host
     * @param int $port
     * @param int $timeout
     * @param MinecraftQuery|null $query
     */
    public function __construct(string $host, int $port = 25565, int $timeout = 3, ?MinecraftQuery $query = null)
    {
        if (!$query)
            $query = new MinecraftQuery();

        $this->query = $query;
        $this->host = $host;
        $this->port = $port;
        $this->timeout = $timeout;
    }

    /**
     * @return bool
     */
    public function connect(): bool
    {
        try {
            $this->query->Connect($this->host, $this->port, $this->timeout);
            $connected = true;
        } catch (MinecraftQueryException $exception) {
            $connected = false;
        }

        return $connected;
    }

    /**
     * @return bool
     */
    public function isConnected(): bool
    {
        return $this->connect();
    }

    /**
     * @return array
     */
    public function getPlayers(): array
    {
        if (!$this->isConnected())
            return [];

        return (array) $this->query->GetPlayers();
    }

    /**
     * @return array
     */
    public function getInfo(): array
    {
        if (!$this->isConnected())
            return [];

        return (array) $this->query->GetInfo();
    }

}