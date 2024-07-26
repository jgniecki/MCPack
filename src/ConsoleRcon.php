<?php declare(strict_types=1);
/**
 * @author Jakub Gniecki
 * @copyright Jakub Gniecki <kubuspl@onet.eu>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */


namespace DevLancer\MCPack;


use DevLancer\MinecraftRcon\Rcon;

/**
 * Class ConsoleRcon
 * @package DevLancer\MCPack
 */
class ConsoleRcon extends Rcon implements ConsoleInterface
{
    public function sendCommand(string $command): bool
    {
        if (!$this->isConnected() && !$this->connect()) {
            return false;
        }

        return parent::sendCommand($command) !== false;
    }
}
