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
     * @var array
     */
    private array $logs = [];

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
            $this->logs = ["[Server] File $this->pathLogs not found."];
            return $this;
        }

        if ($this->pathCache) {
            if ($sftp->filemtime($this->pathLogs) != filemtime($this->pathCache))
                $this->updateCache();

            return $this;
        }

        $this->logs = explode("\n", $sftp->get($this->pathLogs));
        return $this;
    }

    /**
     * @return $this
     */
    public function updateCache(): self
    {
        if (!$this->pathCache) {
            trigger_error("The \$pathCache parameter for " . __CLASS__ . " must be provided", E_USER_WARNING);
            return $this;
        }

        $sftp = $this->sftp;
        $sftp->get($this->pathLogs, $this->pathCache);
        return $this;

    }

    /**
     *
     */
    private function loadCache(): void
    {
        if (!$this->pathCache)
            return;

        if (!file_exists($this->pathCache)) {
            $this->logs = ["[Cache] File $this->pathCache not found."];
            return;
        }

        $this->logs = explode("\n", file_get_contents($this->pathCache));

    }

    /**
     * @param bool $update
     * @param int $offset
     * @param int $length
     * @param bool $reverse
     * @return array
     */
    public function getLogs(bool $update = false, int $offset = 0, int $length = -1, bool $reverse = false): array
    {
        if ($update)
            $this->update();

        if ($this->pathCache)
            $this->loadCache();

        $logs = $this->logs;
        $line = count($logs);

        if ($reverse)
            $logs = array_reverse($logs);

        for ($i = 0; $i < $offset; $i++)
            unset($logs[$i]);

        if ($logs === 0)
            return [];

        if ($length > -1) {
            for ($i = $line; $i >= ($line - $length); $i--)
                unset($logs[$i]);
        }

        if ($reverse)
            $logs = array_reverse($logs);

        return $logs;
    }
}