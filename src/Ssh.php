<?php declare(strict_types=1);
/**
 * @author Jakub Gniecki
 * @copyright Jakub Gniecki <kubuspl@onet.eu>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */


namespace DevLancer\MCPack;


use Exception;
use \phpseclib\Net\SFTP;

/**
 * Class Ssh
 * @package DevLancer\MCPack
 */
class Ssh
{
    /**
     * @var string
     */
    private string $username;

    /**
     * @var string
     */
    private string $password;

    /**
     * @var string
     */
    private string $response;

    /**
     * @var SFTP
     */
    private SFTP $sftp;

    /**
     * Ssh constructor.
     * @param SFTP $sftp
     * @param string $username
     * @param string $password
     * @throws Exception
     */
    public function __construct(SFTP $sftp, string $username, string $password)
    {
        $this->username = $username;
        $this->password = $password;

        $sftp->login($username, $password);
        $this->sftp = $sftp;
        if (!$this->sftp->isConnected())
            throw new Exception("No SFTP connection");
    }

    /**
     * @param string $command
     * @param string|null $regex
     * @return bool
     */
    public function terminal(string $command, ?string $regex = null): bool
    {
        $this->response = (string) $this->sftp->exec($command);
        if (!$regex && $this->response == "")
            return true;

        if ($regex)
            return (bool) preg_match($regex, $this->response);

        return (bool) $this->response;
    }

    /**
     * @param string $command
     * @param string|null $regex
     * @return bool
     */
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

    /**
     * @return bool
     */
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

    /**
     * @return string
     */
    public function getUsername(): string
    {
        return $this->username;
    }

    /**
     * @return string
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    /**
     * @return string
     */
    public function getResponse(): string
    {
        return $this->response;
    }

    /**
     * @return SFTP
     */
    public function getSftp(): SFTP
    {
        return $this->sftp;
    }


}