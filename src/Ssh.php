<?php declare(strict_types=1);
/**
 * @author Jakub Gniecki
 * @copyright Jakub Gniecki <kubuspl@onet.eu>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */


namespace DevLancer\MCPack;


use DevLancer\MCPack\Exception\SshConnectionException;
use DevLancer\MCPack\Ssh\SshInterface;
use \phpseclib\Net\SFTP;

/**
 * Class Ssh
 * @package DevLancer\MCPack
 * @deprecated since dev-lancer/mc-pack 2.2, use DevLancer\MCPack\Terminal instead
 */
class Ssh implements SshInterface
{
    private string $username;
    private string $password;
    private string $response;
    private SFTP $sftp;

    /**
     * @throws SshConnectionException No SFTP connection
     */
    public function __construct(SFTP $sftp, string $username, string $password)
    {
        $this->username = $username;
        $this->password = $password;

        $sftp->login($username, $password);
        $this->sftp = $sftp;
        if (!$this->sftp->isConnected())
            throw new SshConnectionException("No SFTP connection");
    }

    public function terminal(string $command, ?string $regex = null): bool
    {
        $this->response = (string) $this->sftp->exec($command);
        if (!$regex && $this->response == "")
            return true;

        if ($regex)
            return (bool) preg_match($regex, $this->response);

        return (bool) $this->response;
    }

    public function interactiveTerminal(string $command, ?string $regex = null): bool
    {
        $this->sftp->read('/.*@.*[$|#]/', SFTP::READ_REGEX);
        $this->sftp->write($command . "\n");
        $this->sftp->setTimeout(3);

        $this->response = (string) $this->sftp->read();

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
        $this->sftp->write($this->getPassword()."\n");
        $this->response = (string) $this->sftp->read('/.*@.*[$|#]/', SFTP::READ_REGEX);

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

    public function getSftp(): SFTP
    {
        return $this->sftp;
    }
}
