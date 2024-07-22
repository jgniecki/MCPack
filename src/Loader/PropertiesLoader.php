<?php declare(strict_types=1);
/**
 * @author Jakub Gniecki <kubuspl@onet.eu>
 * @copyright
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */


namespace DevLancer\MCPack\Loader;

use DevLancer\MCPack\Locator\RemoteFileLocator;
use DevLancer\MCPack\Properties\ServerProperties;
use InvalidArgumentException;
use Symfony\Component\Config\Exception\FileLocatorFileNotFoundException;
use Symfony\Component\Config\FileLocatorInterface;
use Symfony\Component\Config\Loader\FileLoader;
use Symfony\Component\Serializer\SerializerInterface;

class PropertiesLoader extends FileLoader
{
    private SerializerInterface $serializer;
    public function __construct(FileLocatorInterface $locator, SerializerInterface $serializer, ?string $env = null)
    {
        $this->serializer = $serializer;
        parent::__construct($locator, $env);
    }

    /**
     * @inheritDoc
     * @param string $resource
     * @return ServerProperties[]
     * @throws FileLocatorFileNotFoundException
     * @throws \Exception No content received
     */
    public function load($resource, ?string $type = null): array
    {
        if (!$this->supports($resource, $type)) {
            throw new InvalidArgumentException(sprintf('Argument 1 passed to DevLancer\MCPack\PropertiesLoader::load() must be an instance of string, %s given', get_debug_type($resource)));
        }

        if (is_string($locate = $this->locator->locate($resource))) {
            return [$this->serializer->deserialize($this->content($locate), ServerProperties::class, ServerProperties::class)];
        }

        $result = [];
        foreach ($this->locator->locate($resource) as $locate) {
            $result[] = $this->serializer->deserialize($this->content($locate), ServerProperties::class, ServerProperties::class);
        }

        return $result;
    }

    /**
     * @throws \Exception
     */
    private function content(string $locate): string
    {
        if ($this->locator instanceof RemoteFileLocator) {
            $sftp = $this->locator->getSftp();
            $content = $sftp->get($locate);
        } else {
            $content = file_get_contents($locate);
        }

        if ($content === false) {
            throw new \Exception("No content received from $locate");
        }

        return $content;
    }

    /**
     * @inheritDoc
     */
    public function supports($resource, ?string $type = null): bool
    {
        return is_string($resource) && strlen($resource) > 0;
    }
}