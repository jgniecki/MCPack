<?php declare(strict_types=1);
/**
 * @author Jakub Gniecki
 * @copyright Jakub Gniecki <kubuspl@onet.eu>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */


namespace DevLancer\MCPack;


use Thedudeguy\Rcon;

/**
 * Class ConsoleRcon
 * @package DevLancer\MCPack
 */
class ConsoleRcon extends Rcon implements ConsoleInterface
{
    /**
     * @param string $command
     * @return bool
     */
    public function sendCommand($command): bool
    {
        if (!$this->isConnected())
            $this->connect();

        return (bool) parent::sendCommand($command);
    }

    public function getResponse(): ?string
    {
        if ($this->isConnected())
            return parent::getResponse();

        return null;
    }

    public function isConnected(): bool
    {
        return parent::isConnected();
    }
}
