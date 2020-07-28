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
     * ServerManagerSell constructor.
     * @param Server $server
     * @throws \Exception
     */
    public function __construct(Server $server)
    {
        parent::__construct($server);

        if (!$this->server->hasSftp())
            throw new \Exception("The \$sftp parameter for Server.php must be provided"); 
    }

    /**
     * @param int $memory
     * @return bool
     *
     * example: 512 this is 512 MB
     */
    public function run(int $memory = 512): bool
    {
        $port = $this->server->getPort();
        if ($this->isRunning()) {
            $this->notice("Server mc<strong>$port</strong> is running");
            return false;
        }

        if (!$this->server->hasPath()) {
            $this->notice("There is no path to the server");
            return false;
        }

        $path = explode("/", $this->server->getPath());
        $name = end($path);

        if (strpos($name, ".jar") === false) {
            $this->notice("The path does not lead to a server");

            return false;
        }

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
        $port = $this->server->getPort();

        if (!$this->isRunning()) {
            $this->notice("Server mc<strong>$port</strong> isn't running");
            return false;
        }

        if (!$this->sendCommand("stop"))
            return false;

        $command = "screen -X -S mc" . $port . " quit";

        return $this->terminal($command);
    }

    /**
     * @return bool
     */
    public function kill(): bool
    {
        if (!$this->isRunning() || !$this->getPid()) {
            $this->notice("Server mc<strong>" . $this->server->getPort() . "</strong> isn't running");
            return false;
        }

        $command = "kill -9 " . $this->getPid();

        return $this->terminal($command);
    }

    /**
     * @return int|null
     */
    public function getPid(): ?int
    {
        if (!$this->pid)
            $this->setPid();

        return $this->pid;

    }

    /**
     *
     */
    private function setPid(): void
    {
        $port = $this->server->getPort();
        if (!$this->isRunning()) {
            $this->notice("Server mc<strong>$port</strong> isn't running");
            return;
        }

        $command = "screen -ls";
        if (!$this->terminal($command))
            return;

        if (!preg_match('/([0-9]{1,}).mc' . $port . '/', $this->responseTerminal, $pid))
            return;

        $pid = (int) $pid[1];

        $command = "ps -h";
        if (!$this->terminal($command))
            return;

        $process = explode("\n", $this->responseTerminal);
        $current_pid = null;
        foreach ($process as $id => $value) {
            if (strpos($value, "java") === false)
                continue;

            preg_match('/[0-9]{1,}/', $value, $pid_ps);
            $pid_ps = $pid_ps[0];

            if (!$current_pid || $pid_ps < $current_pid)
                $current_pid = $pid_ps;
        }

        $this->pid = (int) $current_pid;
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
        preg_match_all('/[a-zA-Z0-9,.:_-]{1,}/', $process[$id], $process);

        $cpu = $process[0][8];
        $memory = $process[0][9];

        return [
            'memory' => $memory,
            'cpu' => $cpu
        ];
    }

    /**
     * @param string $command
     * @return bool
     */
    private function terminal(string $command): bool
    {
        $sftp = $this->server->getSftp();
        if(!$sftp->isConnected()) {
            $this->notice("Failed to connect to SFTP");
            return false;
        }

        $this->responseTerminal =  $sftp->exec($command);
        return true;
    }

    /**
     * @return float
     */
    public function getCpuUsage(): float
    {
        if (!$this->isRunning()) {
            $this->notice("Server mc<strong>" . $this->server->getPort() . "</strong> isn't running");
            return 0.0;
        }

        $cpu = $this->serverProcess()['cpu'];
        $cpu = str_replace(",", ".", $cpu);

        return (float) $cpu;
    }

    /**
     * @return float
     */
    public function getMemoryUsage()
    {
        if (!$this->isRunning()) {
            $this->notice("Server mc<strong>" . $this->server->getPort() . "</strong> isn't running");
            return 0.0;
        }

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

        if (!$this->isRunning()) {
            $this->notice("Server mc<strong>" . $this->server->getPort() . "</strong> isn't running");
            return "0M";
        }

        $command = "ps -h -p" . $this->getPid();
        if(!$this->terminal($command))
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
    /*public function getRunTime(): string
    {
        $time = $this->serverProcess()['time'];
        $time = explode(".", $time)[0];

        return (string) $time;
    }*/

    /**
     * @param string $message
     */
    private function notice(string $message): void
    {
        $array = debug_backtrace();
        $caller = next($array);
        trigger_error($message.' in <strong>'.$caller['function'].'</strong> called from <strong>'.$caller['file'].'</strong> on line <strong>'.$caller['line'].'</strong>'."\n<br />error handler");

    }
}