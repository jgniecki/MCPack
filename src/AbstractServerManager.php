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
     * @var ServerInfo
     */
    protected ServerInfo $info;

    /**
     * @var ConsoleInterface
     */
    protected ConsoleInterface $console;

    public function __construct(ServerInfo $info, ConsoleInterface $console)
    {
        $this->info = $info;
        $this->console = $console;
    }

    /**
     * @return Query
     */
    public function getInfo(): ServerInfo
    {
        return $this->info;
    }

    /**
     * @return ConsoleInterface
     */
    public function getConsole(): ConsoleInterface
    {
        return $this->console;
    }

    /**
     * @param string $player
     * @return bool
     */
    public function isPlayer(string $player): bool
    {
        return (bool) in_array($player, $this->info->getPlayers());
    }

    /**
     * @return bool
     */
    public function isOnline(): bool
    {
        return $this->info->isConnected();
    }
}