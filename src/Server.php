<?php declare(strict_types=1);
/**
 * @author Jakub Gniecki <kubuspl@onet.eu>
 * @copyright
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */


namespace DevLancer\MCPack;


use phpseclib\Net\SFTP;
use Thedudeguy\Rcon;

/**
 * Class Server
 * @package DevLancer\ForestMC
 */
class Server
{
    /**
     * @var Query
     */
    private Query $query;

    /**
     * @var Rcon
     */
    private Rcon $rcon;

    /**
     * @var SFTP
     */
    private ?SFTP $sftp = null;

    /**
     * @var string
     */
    private string $path = "";

    /**
     * @var int
     */
    private int $port;

    /**
     * Server constructor.
     * @param Query $query
     * @param Rcon $rcon
     * @param int $serverPort
     * @param SFTP|null $sftp
     * @param string|null $path
     */
    public function __construct(Query $query, Rcon $rcon, int $serverPort = 25565, ?SFTP $sftp = null, ?string $path = null)
    {
        $this->query = $query;
        $this->rcon = $rcon;
        $this->port = $serverPort;

        if($sftp)
            $this->sftp = $sftp;

        if ($path)
            $this->path = $path;
    }

    /**
     * @return int
     */
    public function getPort(): int
    {
        return $this->port;
    }

    /**
     * @return Query
     */
    public function getQuery(): Query
    {
        return $this->query;
    }

    /**
     * @return Rcon
     */
    public function getRcon(): Rcon
    {
        return $this->rcon;
    }

    /**
     * @return SFTP
     */
    public function getSftp(): SFTP
    {
        return $this->sftp;
    }

    /**
     * @return string
     */
    public function getPath(): string
    {
        return $this->path;
    }

    /**
     * @return bool
     */
    public function hasSftp(): bool
    {
        return ($this->sftp != null);
    }

    /**
     * @return bool
     */
    public function hasPath(): bool
    {
        return ($this->path)? true : false;
    }
}