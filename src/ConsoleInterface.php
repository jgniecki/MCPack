<?php declare(strict_types=1);
/**
 * @author Jakub Gniecki
 * @copyright Jakub Gniecki <kubuspl@onet.eu>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */


namespace DevLancer\MCPack;


/**
 * Interface ConsoleInterface
 * @package DevLancer\MCPack
 */
interface ConsoleInterface
{
    public function sendCommand($command): bool;
    public function getResponse(): ?string;
    public function isConnected(): bool;
}
