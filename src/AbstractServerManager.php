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
 * @deprecated since dev-lancer/mc-pack 2.2, use ServerManagerInterface instead
 */
abstract class AbstractServerManager
{
    protected ServerInfo $info;
    protected ConsoleInterface $console;

    public function __construct(ServerInfo $info, ConsoleInterface $console)
    {
        $this->info = $info;
        $this->console = $console;
    }

    public function getInfo(): ServerInfo
    {
        return $this->info;
    }

    public function getConsole(): ConsoleInterface
    {
        return $this->console;
    }

    public function isPlayer(string $player): bool
    {
        return in_array($player, $this->info->getPlayers());
    }

    public function isOnline(): bool
    {
        return $this->info->isConnected();
    }
}
