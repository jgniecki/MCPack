<?php declare(strict_types=1);
/**
 * @author Jakub Gniecki
 * @copyright Jakub Gniecki <kubuspl@onet.eu>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */


namespace DevLancer\MCPack;


/**
 * Interface ServerInfo
 * @package DevLancer\MCPack
 * @deprecated since dev-lancer/mc-pack 2.2, use \DevLancer\MinecraftStatus\StatusInterface instead
 */
interface ServerInfo
{
    /**
     * @deprecated since dev-lancer/mc-pack 2.2, use \DevLancer\MinecraftStatus\StatusInterface instead
     * @return bool
     */
    public function connect():bool;

    /**
     * @return bool
     */
    public function isConnected(): bool;

    /**
     * @return array
     */
    public function getPlayers(): array;

    /**
     * @return int
     */
    public function getCountPlayers(): int;

    /**
     * @return int
     */
    public function getMaxPlayers(): int;

    /**
     * @return array
     */
    public function getInfo(): array;
}