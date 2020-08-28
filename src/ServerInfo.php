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
 */
interface ServerInfo
{
    /**
     * @return bool
     */
    public function connect(): bool;

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