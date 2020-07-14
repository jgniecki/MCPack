<?php declare(strict_types=1);
/**
 * @author Jakub Gniecki <kubuspl@onet.eu>
 * @copyright
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */


namespace DevLancer\MCPack;


use phpseclib\Net\SFTP;

/**
 * Class Logs
 * @package DevLancer\MCPack
 */
class Logs
{
    /**
     * @var SFTP
     */
    private SFTP $sftp;

    /**
     * @var string
     */
    private string $pathLogs;

    /**
     * @var string|null
     */
    private ?string $pathCache = null;

    /**
     * @var string|null
     */
    private ?string $logs = null;

    /**
     * Logs constructor.
     * @param SFTP $sftp
     * @param string $pathLogs
     * @param string|null $pathCache
     */
    public function __construct(SFTP $sftp, string $pathLogs, string $pathCache = null)
    {
        $this->sftp = $sftp;
        $this->pathLogs = $pathLogs;

        if ($pathCache)
            $this->pathCache = $pathCache;
    }

    /**
     * @return $this
     */
    public function update(): self
    {
        $sftp = $this->sftp;
        if (!$sftp->file_exists($this->pathLogs)) {
            $this->logs = "[Server] File $this->pathLogs not found.";
            return $this;
        }

        if ($this->pathCache) {
            if ($sftp->filemtime($this->pathLogs) != \filemtime($this->pathCache))
                $this->updateCache();

            return $this;
        }

        $this->logs = (string) $sftp->get($this->pathLogs);
        return $this;
    }

    public function updateCache(): void
    {
        if (!$this->pathCache)
            return;

        $sftp = $this->sftp;
        $sftp->get($this->pathLogs, $this->pathCache);

    }

    private function loadCache(): void
    {
        if (!$this->pathCache)
            return;

        if (!\file_exists($this->pathCache)) {
            $this->logs = "[Cache] File $this->pathCache not found.";
            return;
        }

        $this->logs = (string) \file_get_contents($this->pathCache);

    }

    /**
     * @param bool $update
     * @return string
     */
    public function getLogs(bool $update = false): string
    {
        if ($update)
            $this->update();

        if ($this->pathCache)
            $this->loadCache();

        return (string) $this->logs;
    }
}