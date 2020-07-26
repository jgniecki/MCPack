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
     * @var int|null
     */
    private ?int $pid = null;

    /**
     * @param int $memory
     * @return bool
     *
     * example: 512 this is 512 MB
     */
    public function run(int $memory = 512): bool
    {
        $port = $this->server->getPort();
        if ($this->isRunning())
            return false;

        if (!$this->server->hasPath())
            return false;

        $path = explode("/", $this->server->getPath());
        $name = end($path);

        if (strpos($name, ".jar") === false)
            return false;

        unset($path[array_key_last($path)]);
        $path = implode("/", $path);

        $command = "cd " . $path . "; screen -dmS mc" . $port . " java -Xmx" . $memory . "M -Xms" . $memory . "M -jar " . $name . " nogui";

        return $this->terminal($command);
    }

    /**
     * @return bool
     */
    public function isRunning(): bool
    {
        $port = $this->server->getPort();
        $command = "screen -ls";
        $this->terminal($command);

        return (strpos($this->responseTerminal, "mc" . $port) !== false);
    }

    /**
     * @return bool
     */
    public function stop(): bool
    {
        if (!$this->isRunning())
            return false;

        if (!$this->sendCommand("stop"))
            return false;

        $port = $this->server->getPort();
        $command = "screen -X -S mc" . $port . " quit";

        return $this->terminal($command);
    }

    /**
     * @return int|null
     */
    public function getPid(): ?int
    {
        if (!$this->isRunning())
            return null;

        if (!$this->pid)
            $this->setPid();

        return $this->pid;

    }

    private function setPid(): void
    {
        $command = "screen -ls";
        if (!$this->terminal($command))
            return;

        $port = $this->server->getPort();
        if (!preg_match('/([0-9]{1,}).mc' . $port . '/', $this->responseTerminal, $pid))
            return;

        $pid = (int) $pid[1];

        $this->pid = ++$pid;
    }

    /**
     * @return array
     */
    public function serverProcess(): array
    {
        $pid = $this->getPid();
        if (!$pid)
            return [];

        $command = "top -bin 1 -p " . $pid;
        if (!$this->terminal($command))
            return [];

        if (strpos($this->responseTerminal, (string) $pid) === FALSE)
            return [];

        $process = explode("\n", $this->responseTerminal);
        $id = array_search($pid, $process);
        $process = explode(" ", $process[$id]);

        $cpu = $process[14];
        $memory = $process[15];
        $time = $process[17];

        return [
            'memory' => $memory,
            'cpu' => $cpu,
            'time' => $time
        ];
    }

    /**
     * @param string $command
     * @return bool
     */
    private function terminal(string $command): bool
    {
        if (!$this->server->hasSftp())
            return false;

        $sftp = $this->server->getSftp();
        if(!$sftp->isConnected())
            return false;

        $this->responseTerminal =  $sftp->exec($command);
        return true;
    }

    /**
     * @return float
     */
    public function getCpuUsage(): float
    {
        $cpu = $this->serverProcess()['cpu'];
        $cpu = str_replace(",", ".", $cpu);

        return (float) $cpu;
    }

    /**
     * @return float
     */
    public function getMemoryUsage()
    {
        $memory = $this->serverProcess()['memory'];
        $memory = str_replace(",", ".", $memory);

        return (float) $memory;
    }

    /**
     * @return string
     *
     * example: 1024M this is 1024 MB
     */
    public function getMemory(): string
    {
        $command = "ps -h -p" . $this->getPid();
        if (!$this->terminal($command))
            return "0M";

        preg_match('/Xmx([0-9]{1,}.)/i', $this->responseTerminal, $xmx);

        $xmx = (isset($xmx[1]))? $xmx[1] : "0M";

        return $xmx;
    }

    /**
     * @return string
     *
     * format hh:mm:ss
     */
    public function getRunTime(): string
    {
        $time = $this->serverProcess()['time'];
        $time = explode(".", $time)[0];

        return (string) $time;
    }
}