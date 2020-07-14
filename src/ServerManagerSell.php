<?php declare(strict_types=1);
/**
 * @author Jakub Gniecki <kubuspl@onet.eu>
 * @copyright
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */


namespace DevLancer\MCPack;


/**
 * Class ServerManager
 * @package DevLancer\MCPack
 */
class ServerManagerSell extends AbstractServerManager
{
    /**
     * @var string
     */
    private string $responseTerminal = "";

    /**
     * @param int $memory
     * @param int $port
     * @return bool
     */
    public function run(int $memory = 512, int $port = 25565): bool
    {
        if ($this->isRunning($port))
            return false;

        if (!$this->server->hasPath())
            return false;

        $path = \explode("/", $this->server->getPath());
        $name = \end($path);

        if (\strpos($name, ".jar") === false)
            return false;

        unset($path[\array_key_last($path)]);
        $path = \implode("/", $path);

        $command = "cd " . $path . "; screen -dmS mc" . $port . " java -Xmx" . $memory . "M -Xms" . $memory . "M -jar " . $name . " nogui";

        return $this->terminal($command);
    }

    /**
     * @param int $port
     * @return bool
     */
    public function isRunning(int $port = 25565): bool
    {
        $command = "screen -ls";
        $this->terminal($command);

        return (\strpos($this->responseTerminal, "mc" . $port) !== false);
    }

    /**
     * @param int $port
     * @return bool
     */
    public function stop(int $port = 25565):bool
    {
        if (!$this->isRunning($port))
            return false;

        if (!$this->sendCommand("stop"))
            return false;

        $command = "screen -X -S mc" . $port . " quit";

        return $this->terminal($command);
    }

    /**
     * @param string $command
     * @return bool
     */
    private function terminal(string $command):bool
    {
        if (!$this->server->hasSftp())
            return false;

        $sftp = $this->server->getSftp();
        if(!$sftp->isConnected())
            return false;

        $this->responseTerminal =  $sftp->exec($command);
        return true;
    }
}