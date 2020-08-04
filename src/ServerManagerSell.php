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
     *
     * @param Server $server
     * @throws ServerManagerException
     */
    public function __construct(Server $server)
    {
        parent::__construct($server);

        if (!$this->server->hasSftp())
            throw new ServerManagerException("The \$sftp parameter for " . __CLASS__ . " must be provided");
    }

    /**
     * 512 this is 512MB
     *
     * @param int $memory -Xmx parameter
     * @param int|null $runMemory -Xms parameter
     * @param bool $strictPort use port from Server::getPort() to start server and changes server-port, --port parameter
     * @return bool
     */
    public function run(int $memory = 512, ?int $runMemory = null, bool $strictPort = false): bool
    {
        $port = $this->server->getPort();

        if ($this->isRunning()) {
            trigger_error("Server <strong>mc$port</strong> is running", E_USER_WARNING);
            return false;
        }

        if (!$this->server->hasPath()) {
            trigger_error("There is no path to the server", E_USER_WARNING);
            return false;
        }

        $path = explode("/", $this->server->getPath());
        $name = end($path);

        if (strpos($name, ".jar") === false) {
            trigger_error("The path " . $this->server->getPath() . " does not lead to a server", E_USER_WARNING);
            return false;
        }

        if ($memory > $this->getTotalMemory() || $runMemory > $this->getTotalMemory()) {
            trigger_error("Not enough memory, total memory is " . $this->getTotalMemory() . "MB", E_USER_WARNING);
            return false;
        }

        if (!$runMemory > 0)
            $runMemory = $memory;

        unset($path[array_key_last($path)]);
        $path = implode("/", $path);

        $parmPort = ($strictPort)? " --port " . $port : "";

        $command = "cd " . $path . "; screen -dmS mc" . $port . " java -Xmx" . $memory . "M -Xms" . $runMemory . "M  -jar " . $name . " nogui " . $parmPort;
        return $this->terminal($command);
    }

    /**
     * @return bool
     */
    public function isRunning(): bool
    {
        $port = $this->server->getPort();
        $command = "screen -ls mc" . $port;
        if (!$this->terminal($command))
            return false;

        return (strpos($this->responseTerminal, "mc" . $port) !== false);
    }

    /**
     * @return bool
     */
    public function stop(): bool
    {
        $port = $this->server->getPort();

        if (!$this->isRunning()) {
            trigger_error("Server <strong>mc$port</strong> isn't running");
            return false;
        }

        if (!$this->sendCommand("stop"))
            return false;

        $command = "screen -X -S mc" . $port . " quit";

        return $this->terminal($command);
    }

    /**
     * Kills the server process
     *
     * @param int $mode
     * @return bool
     */
    public function kill(int $mode = 9): bool
    {
        if (!$this->isRunning()) {
            trigger_error("Server <strong>mc" . $this->server->getPort() . "</strong> isn't running");
            return false;
        }

        if (!$this->getPid())
            return false;

        $command = "kill -" . $mode . " " . $this->getPid();

        return $this->terminal($command);
    }

    /**
     * Returns the process id (pid) from linux server for minecraft server.
     *
     * @return int|null
     */
    public function getPid(): ?int
    {
        if (!$this->pid)
            $this->setPid();

        return $this->pid;
    }

    /**
     * Tries to determine the PID for the server
     */
    private function setPid(): void
    {
        $port = $this->server->getPort();
        if (!$this->isRunning()) {
            trigger_error("Server <strong>mc$port</strong> isn't running");
            return;
        }

        $command = "screen -ls mc" . $port;

        if (!$this->terminal($command))
            return;

        if (!preg_match('/([0-9]{1,}).mc' . $port . '/', $this->responseTerminal, $pid))
            return;

        $pid = (int)$pid[1];
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

            if (!$current_pid || ($pid_ps < $current_pid && $pid_ps >= $pid))
                $current_pid = $pid_ps;
        }

        $this->pid = (int)$current_pid;
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

        if (strpos($this->responseTerminal, (string)$pid) === FALSE)
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

        if (!$sftp->isConnected()) {
            trigger_error("No SFTP connection", E_USER_WARNING);
            return false;
        }

        $this->responseTerminal = $sftp->exec($command);
        return (bool)$this->responseTerminal;
    }

    /**
     * @param string $name
     * @return float
     */
    private function usage(string $name): float
    {
        if (!$this->isRunning()) {
            trigger_error("Server <strong>mc" . $this->server->getPort() . "</strong> isn't running", E_USER_WARNING);
            return 0.0;
        }

        $value = $this->serverProcess()[$name];
        $value = str_replace(",", ".", $value);

        return (float) $value;
    }

    /**
     * @return float as %
     */
    public function getCpuUsage(): float
    {
        return $this->usage("cpu");
    }

    /**
     * @return float as %
     */
    public function getMemoryUsage(): float
    {
        return $this->usage("memory");
    }

    /**
     * The amount of memory assigned to the server
     *
     * @return string 1024M this is 1024MB
     */
    public function getMemory(): string
    {
        if (!$this->isRunning()) {
            trigger_error("Server <strong>mc" . $this->server->getPort() . "</strong> isn't running", E_USER_WARNING);
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
     * returns in:
     *  k - kilobyte,
     *  m - megabyte,
     *  g - gigabyte
     *
     * @param bool $usage
     * @param string $type k/m/g
     * @return int
     */
    public function getTotalMemory(bool $usage = false, string $type = "m"): int
    {
        $command = "free -" . $type;

        $type = strtolower($type);
        if (!preg_match('/[kmg]{1}/', $type)) {
            trigger_error("\"" . $type . "\" is bad type", E_USER_WARNING);
            return 0;
        }

        if(!$this->terminal($command))
            return 0;

        preg_match_all('/[0-9]{1,}/',  $this->responseTerminal, $memory);

        return (int) $memory[0][(int) $usage];
    }

    /**
     * @return null|string format DD.MM.YYYY hh:mm:ss
     */
    public function getRunTime(): ?string
    {
        if (!$this->isRunning()) {
            trigger_error("Server <strong>mc" . $this->server->getPort() . "</strong> isn't running", E_USER_WARNING);
            return null;
        }

        $command = "screen -ls mc" . $this->server->getPort();

        if (!$this->terminal($command))
            return null;

        preg_match('/([0-9]{1,2}.){2}[0-9]{2,4} ([0-9:]{1,2}){4}/', $this->responseTerminal, $time);

        return (string) $time[0];
    }
}