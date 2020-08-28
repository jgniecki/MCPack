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
    /**
     * @param string $command
     * @return bool
     */
    public function sendCommand(string $command): bool;

    /**
     * @return string|null
     */
    public function getResponse(): ?string;

    /**
     * @return bool
     */
    public function isConnected(): bool;
}