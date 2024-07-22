<?php declare(strict_types=1);
/**
 * @author Jakub Gniecki <kubuspl@onet.eu>
 * @copyright
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace DevLancer\MCPack\Manager;

use DevLancer\MCPack\Exception\SshConnectionException;
use DevLancer\MCPack\Loader\PropertiesLoader;
use DevLancer\MCPack\Locator\RemoteFileLocator;
use DevLancer\MCPack\Properties\ServerProperties;
use DevLancer\MCPack\Serialization\PropertiesEncoder;
use DevLancer\MCPack\Serialization\PropertiesNormalizer;
use DevLancer\MCPack\Sftp\SftpInterface;
use phpseclib\Net\SFTP;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ArrayDenormalizer;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\SerializerInterface;

class PropertiesManager
{
    private string $path;
    private ?SftpInterface $sftp;
    private LoaderInterface $loader;
    private SerializerInterface $serializer;
    private ?ServerProperties $serverProperties = null;
    private int $filemtime = -1;

    /**
     * @throws SshConnectionException
     */
    public function __construct(string $path, ?SftpInterface $sftp = null, ?LoaderInterface $loader = null, ?SerializerInterface $serializer = null)
    {
        if ($sftp && !$sftp->isConnected())
            throw new SshConnectionException("No SFTP connection");

        $this->sftp = $sftp;
        $this->path = $path;


        if (!$serializer) {
            $normalizers = [new PropertiesNormalizer(), new ArrayDenormalizer(), new ObjectNormalizer()];
            $encoders = [new PropertiesEncoder(), new JsonEncoder()];
            $serializer = new Serializer($normalizers, $encoders);
        }

        if (!$loader) {
            $locator = ($sftp)? new RemoteFileLocator($sftp) : new FileLocator();
            $loader = new PropertiesLoader($locator, $serializer);
        }

        $this->loader = $loader;
        $this->serializer = $serializer;
        $this->serverProperties = $loader->load($path)[0];
    }

    /**
     * @throws \Exception
     */
    public function getProperties(): ServerProperties
    {
        /**
         * @var SFTP $sftp;
         */
        $sftp = $this->sftp;

        if (!$this->serverProperties) {
            $this->serverProperties = $this->loader->load($this->path)[0];
            $this->filemtime = ($this->loader->getLocator() instanceof RemoteFileLocator)? $sftp->filemtime($this->path) : filemtime($this->path);
            return $this->serverProperties;
        }

        $filemtime = ($this->loader->getLocator() instanceof RemoteFileLocator)? $this->sftp->filemtime($this->path) : filemtime($this->path);
        if ($filemtime !== $this->filemtime) {
            $this->serverProperties = null;
            return $this->getProperties();
        }

        return $this->serverProperties;
    }

    public function saveProperties(ServerProperties $properties): bool
    {
        $properties = $this->serializer->serialize($this->serverProperties, ServerProperties::class);

        if ($this->loader->getLocator() instanceof RemoteFileLocator)
            return $this->sftp->put($this->path, $properties);

        return (file_put_contents($this->path, $properties) !== false);
    }

}