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
    public function connect():bool;
    public function isConnected(): bool;
    public function getPlayers(): array;
    public function getCountPlayers(): int;
    public function getMaxPlayers(): int;
    public function getInfo(): array;
    public function getMotd(): ?string;
}
