<?php declare(strict_types=1);
/**
 * @author Jakub Gniecki <kubuspl@onet.eu>
 * @copyright
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */


namespace DevLancer\MCPack\Locator;

use phpseclib\Net\SFTP;
use Symfony\Component\Config\Exception\FileLocatorFileNotFoundException;
use Symfony\Component\Config\FileLocatorInterface;

class RemoteFileLocator implements FileLocatorInterface
{
    private SFTP $sftp;
    private array $paths;

    /**
     * @param SFTP $sftp
     * @param string[]|string $paths
     */
    public function __construct(SFTP $sftp, $paths = [])
    {
        $this->sftp = $sftp;
        $this->paths = (array) $paths;
    }

    public function getSftp(): SFTP
    {
        return $this->sftp;
    }

    /**
     * @return string|string[]
     *
     * @psalm-return ($first is true ? string : string[])
     */
    public function locate(string $name, ?string $currentPath = null, bool $first = true)
    {
        if ('' === $name) {
            throw new \InvalidArgumentException('An empty file name is not valid to be located.');
        }

        if ($this->isAbsolutePath($name)) {
            if (!$this->sftp->file_exists($name)) {
                throw new FileLocatorFileNotFoundException(sprintf('The file "%s" does not exist.', $name), 0, null, [$name]);
            }

            return $name;
        }

        $paths = $this->paths;

        if (null !== $currentPath) {
            array_unshift($paths, $currentPath);
        }

        $paths = array_unique($paths);
        $filepaths = $notfound = [];

        foreach ($paths as $path) {
            if (@$this->sftp->file_exists($file = $path.\DIRECTORY_SEPARATOR.$name)) {
                if (true === $first) {
                    return $file;
                }
                $filepaths[] = $file;
            } else {
                $notfound[] = $file;
            }
        }

        if (!$filepaths) {
            throw new FileLocatorFileNotFoundException(sprintf('The file "%s" does not exist (in: "%s").', $name, implode('", "', $paths)), 0, null, $notfound);
        }

        return $filepaths;
    }

    /**
     * Returns whether the file path is an absolute path.
     */
    private function isAbsolutePath(string $file): bool
    {
        if ('/' === $file[0] || '\\' === $file[0]
            || (\strlen($file) > 3 && ctype_alpha($file[0])
                && ':' === $file[1]
                && ('\\' === $file[2] || '/' === $file[2])
            )
            || null !== parse_url($file, \PHP_URL_SCHEME)
        ) {
            return true;
        }

        return false;
    }
}