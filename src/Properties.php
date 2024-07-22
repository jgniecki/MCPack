<?php declare(strict_types=1);
/**
 * @author Jakub Gniecki <kubuspl@onet.eu>
 * @copyright
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */


namespace DevLancer\MCPack;


use Exception;
use phpseclib\Net\SFTP;

/**
 * Class Properties
 * @package DevLancer\MCPack
 */
class Properties
{
    /**
     * @var array
     */
    private array $properties = [];

    /**
     * @var SFTP
     */
    private SFTP $sftp;

    /**
     * @var string
     */
    private string $path;

    /**
     * Properties constructor.
     * @param SFTP $sftp
     * @param string $path
     * @throws Exception
     */
    public function __construct(SFTP $sftp, string $path)
    {
        if (!$sftp->isConnected())
            throw new Exception("No SFTP connection");

        if (!$sftp->file_exists($path))
            throw new Exception("File $path does not exist");

        $this->path = $path;
        $this->sftp = $sftp;
        $this->properties = explode("\n", $this->sftp->get($path));
    }

    /**
     * @param string $name
     * @param mixed $value
     * @return self
     */
    public function setProperty(string $name, $value): self
    {
        $property = preg_grep('/' . $name . '/', $this->properties);
        $key = key($property);

        if ($value === true) {
            $value = "true";
        } else {
            $value = "false";
        }

        $this->properties[$key] = $name . "=" . $value;
        return $this;
    }

    /**
     * @param string $name
     * @return bool
     */
    public function hasProperty(string $name):bool
    {
        $property = preg_grep('/' . $name . '/', $this->properties);
        return $property != [];
    }

    /**
     * @param string $name
     * @return string|null
     */
    public function getProperty(string $name):?string
    {
        if (!$this->hasProperty($name))
            return null;

        $property = preg_grep('/' . $name . '/', $this->properties);
        $key = key($property);
        return trim(explode("=", $property[$key])[1]);
    }

    /**
     * @return array
     */
    public function getProperties():array
    {
        return $this->properties;
    }

    /**
     * @return bool
     */
    public function save(): bool
    {
        return $this->sftp->put($this->path, (string) implode("\n", $this->properties));
    }
}