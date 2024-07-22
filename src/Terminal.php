<?php declare(strict_types=1);
/**
 * @author Jakub Gniecki <kubuspl@onet.eu>
 * @copyright
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace DevLancer\MCPack;

use DevLancer\MCPack\Exception\SshConnectionException;
use DevLancer\MCPack\Ssh\SshInterface;
use Exception;
use phpseclib\Net\SFTP;

class Terminal

{
    private string $username;
    private string $password;
    private string $response;
    private SshInterface $ssh;

    /**
     * @throws SshConnectionException No SSH2 connection
     */
    public function __construct(SshInterface $ssh, string $username, string $password)
    {
        $this->username = $username;
        $this->password = $password;
        $this->ssh = $ssh;

        $this->ssh->login($username, $password);
        if (!$this->ssh->isConnected())
            throw new SshConnectionException("No SSH2 connection");
    }

    public function terminal(string $command, ?string $regex = null): bool
    {
        $this->response = (string) $this->ssh->exec($command);
        if (!$regex && $this->response == "")
            return true;

        if ($regex)
            return (bool) preg_match($regex, $this->response);

        return (bool) $this->response;
    }

    public function interactiveTerminal(string $command, ?string $regex = null): bool
    {
        $this->ssh->read('/.*@.*[$|#]/', SFTP::READ_REGEX);
        $this->ssh->write($command . "\n");
        $this->ssh->setTimeout(3);

        $this->response = (string) $this->ssh->read();

        if (preg_match('/(\[sudo\])/', $this->response)) {
            if (!$this->sudo())
                return  false;
        }

        if (!$regex && $this->response == "")
            return true;

        if ($regex)
            return (bool) preg_match($regex, $this->response);

        return (bool) $this->response;
    }

    private function sudo(): bool
    {
        $this->ssh->write($this->getPassword()."\n");
        $this->response = (string) $this->ssh->read('/.*@.*[$|#]/', SFTP::READ_REGEX);

        if (preg_match('/(\[sudo\])/', $this->response)) {
            trigger_error("Wrong password for sudo", E_USER_WARNING);
            return false;
        }

        return true;
    }

    public function getUsername(): string
    {
        return $this->username;
    }

    public function getPassword(): string
    {
        return $this->password;
    }

    public function getResponse(): string
    {
        return $this->response;
    }

    public function getSsh(): SshInterface
    {
        return $this->ssh;
    }
}