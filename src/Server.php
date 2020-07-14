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
     * @var MinecraftQuery
     */
    private MinecraftQuery $query;

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
     * Server constructor.
     * @param MinecraftQuery $query
     * @param Rcon $rcon
     * @param SFTP|null $sftp
     * @param string|null $path
     */
    public function __construct(MinecraftQuery $query, Rcon $rcon, ?SFTP $sftp = null, ?string $path = null)
    {
        $this->query = $query;
        $this->rcon = $rcon;

        if($sftp)
            $this->sftp = $sftp;

        if ($path)
            $this->path = $path;
    }

    /**
     * @return MinecraftQuery
     */
    public function getQuery(): MinecraftQuery
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