<?php declare(strict_types=1);
/**
 * @author Jakub Gniecki <kubuspl@onet.eu>
 * @copyright
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace DevLancer\MCPack\Manager;

use DevLancer\MCPack\Terminal;


class ServerManager implements ServerManagerInterface
{
    private ?int $pid = null;
    private Terminal $terminal;
    private int $port;
    const GIGABYTE_MEMORY = "g";
    const MEGABYTE_MEMORY = "m";
    public function __construct(Terminal $terminal, int $serverPort = 25565)
    {
        $this->terminal = $terminal;
        $this->port = $serverPort;
    }

    public function getTerminal(): Terminal
    {
        return $this->terminal;
    }

    public function getPort(): int
    {
        return $this->port;
    }

    /**
     * @param array $parameters
     * @param string $path
     * @param bool $strictPort use port from self::getPort() to start server and changes server-port for him, --port parameter
     * @return bool
     */
    public function run(array $parameters, string $path, bool $strictPort = true): bool
    {
        if ($this->isRunning()) {
            trigger_error("Server <strong>mc" . $this->port . "</strong> is running", E_USER_WARNING);
            return false;
        }

        $path = $this->generatePath($path);
        $name = $path['name'];
        $path = $path['path'];

        if (strpos($name, ".jar") === false) {
            trigger_error("The path " . $path . "/" . $name . " does not lead to a server", E_USER_WARNING);
            return false;
        }

        $parameters[] = "-jar  $name nogui";

        $command = "cd $path; screen -dmS mc" . $this->port;
        $command .= " " . $this->generateRunCommand($parameters);
        if ($strictPort)
            $command .= " --port " . $this->port;

        return $this->terminal->terminal($command);
    }

    private function generateRunCommand(array $parameters): string
    {
        $command = "";
        $memory = "";

        foreach ($parameters as $value) {
            if (preg_match('/Xmx/', $value)) {
                $memory = $value;
                continue;
            }

            $command .= " " . $value;
        }

        $command = "java $memory" . $command;

        return $command;
    }

    private function generatePath(string $path): array
    {
        $path = explode("/", $path);
        $name = end($path);
        unset($path[array_key_last($path)]);
        $path = implode("/", $path);

        return ["path" => $path, "name" => $name];
    }

    public function isRunning(): bool
    {

        $command = "screen -ls mc" . $this->port;
        return $this->terminal->terminal($command, '/mc' . $this->port . '/' );
    }

    public function stop(): bool
    {
        if (!$this->isRunning()) {
            trigger_error("Server <strong>mc" . $this->port . "</strong> isn't running");
            return false;
        }

        $command = "screen -X -S mc" . $this->port . " quit";

        return $this->terminal->terminal($command);
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
            trigger_error("Server <strong>mc" . $this->port . "</strong> isn't running");
            return false;
        }

        if (!$this->getPid())
            return false;

        $command = "kill -" . $mode . " " . $this->getPid();

        return $this->terminal->terminal($command);
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
        if (!$this->isRunning()) {
            trigger_error("Server <strong>mc" . $this->port . "</strong> isn't running", E_USER_WARNING);
            return;
        }

        $command = "sudo netstat -tulpn | grep " . $this->port;
        $this->terminal->interactiveTerminal($command);

        if (preg_match('/([0-9]{1,})\/java/', $this->terminal->getResponse(), $info) === false)
            return;

        $this->pid = (int) $info[1];
    }

    public function serverProcess(): array
    {
        if (!$this->getPid())
            return [];

        $command = "top -bin 1 -p " . $this->getPid();

        if (!$this->terminal->terminal($command))
            return [];

        if (strpos($this->terminal->getResponse(), (string) $this->getPid()) === FALSE)
            return [];

        $process = explode("\n", $this->terminal->getResponse());
        $id = array_search($this->getPid(), $process);
        preg_match_all('/[a-zA-Z0-9,.:_-]{1,}/', $process[$id], $process);

        $cpu = $process[0][8];
        $memory = $process[0][9];

        return [
            'memory' => $memory,
            'cpu' => $cpu
        ];
    }

    private function usageCM(string $name): float
    {
        if (!$this->isRunning()) {
            trigger_error("Server <strong>mc" . $this->port . "</strong> isn't running", E_USER_WARNING);
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
        return $this->usageCM("cpu");
    }

    /**
     * @return float as %
     */
    public function getMemoryUsage(): float
    {
        return $this->usageCM("memory");
    }

    /**
     * The amount of memory assigned to the server
     *
     * @param string $type
     * @return float
     */
    public function getMemory(string $type = self::MEGABYTE_MEMORY): float
    {
        if (!$this->isRunning()) {
            trigger_error("Server <strong>mc" . $this->port . "</strong> isn't running", E_USER_WARNING);
            return 0.0;
        }

        $command = "ps -h -p" . $this->getPid();

        if(!$this->terminal->terminal($command))
            return 0.0;

        preg_match('/Xmx([0-9]{1,}.)/i', $this->terminal->getResponse(), $xmx);

        if (!isset($xmx[1]))
            return 0.0;

        $factor = ['g' => pow(1024,3), 'm' => pow(1024,2)];

        preg_match('/[mMgG]/', $xmx[1], $code);
        $code = strtolower($code[0]);
        $xmx = floatval($xmx[1]);

        if ($code == $type)
            return $xmx;

        $xmx *= $factor[$code];
        $xmx /= $factor[$type];

        return $xmx;
    }

    /**
     * @param bool $usage
     * @param string $type self::GIGABYTE_MEMORY | self::MEGABYTE_MEMORY
     * @return int
     */
    private function totalMemory(bool $usage = false, string $type = self::MEGABYTE_MEMORY): int
    {
        $command = "free -" . $type;

        if(!$this->terminal->terminal($command))
            return 0;

        preg_match_all('/[0-9]{1,}/',  $this->terminal->getResponse(), $memory);

        return (int) $memory[0][(int) $usage];
    }

    /**
     * @param string $type self::GIGABYTE_MEMORY | self::MEGABYTE_MEMORY
     * @return int
     */
    public function getTotalMemory(string $type = self::MEGABYTE_MEMORY): int
    {
        return $this->totalMemory(false, $type);
    }

    /**
     * @param string $type self::GIGABYTE_MEMORY | self::MEGABYTE_MEMORY
     * @return int
     */
    public function getTotalMemoryUsage(string $type = self::MEGABYTE_MEMORY): int
    {
        return $this->totalMemory(true, $type);
    }

    /**
     * @return null|string format DD.MM.YYYY hh:mm:ss
     */
    public function getRunTime(): ?string
    {
        if (!$this->isRunning()) {
            trigger_error("Server <strong>mc" . $this->port . "</strong> isn't running", E_USER_WARNING);
            return null;
        }

        $command = "screen -ls mc" . $this->port;

        if (!$this->terminal->terminal($command))
            return null;

        preg_match('/([0-9]{1,2}.){2}[0-9]{2,4} ([0-9:]{1,2}){4}/', $this->terminal->getResponse(), $time);

        return (string) $time[0];
    }
}