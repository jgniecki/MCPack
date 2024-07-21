<?php declare(strict_types=1);
/**
 * @author Jakub Gniecki <kubuspl@onet.eu>
 * @copyright
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */


namespace DevLancer\MCPack;


use DevLancer\MCPack\Loader\PropertiesLoader;
use DevLancer\MCPack\Locator\RemoteFileLocator;
use DevLancer\MCPack\Properties\PropertyNameTrait;
use DevLancer\MCPack\Properties\ServerProperties;
use DevLancer\MCPack\Serialization\PropertiesEncoder;
use DevLancer\MCPack\Serialization\PropertiesNormalizer;
use Exception;
use phpseclib\Net\SFTP;
use ReflectionClass;
use Symfony\Component\Config\Exception\FileLocatorFileNotFoundException;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\SerializerInterface;

/**
 * Class Properties
 * @package DevLancer\MCPack
 */
class Properties
{
    use PropertyNameTrait;

    private string $path;
    private ?SFTP $sftp;
    private LoaderInterface $loader;
    private SerializerInterface $serializer;
    private ServerProperties $serverProperties;

    /**
     * @throws FileLocatorFileNotFoundException
     * @throws Exception No SFTP connection
     */
    public function __construct(?SFTP $sftp, string $path, ?LoaderInterface $loader = null, ?SerializerInterface $serializer = null)
    {
        if (!$sftp->isConnected())
            throw new Exception("No SFTP connection");

        $this->sftp = $sftp;
        $this->path = $path;

        $locator = ($sftp)? new RemoteFileLocator($sftp) : new FileLocator();

        if (!$serializer) {
            $normalizers = [new PropertiesNormalizer()];
            $encoders = [new PropertiesEncoder(), new JsonEncoder()];
            $serializer = new Serializer($normalizers, $encoders);
        }

        if (!$loader) {
            $loader = new PropertiesLoader($locator, $serializer);
        }

        $this->loader = $loader;
        $this->serializer = $serializer;
        $this->serverProperties = $loader->load($path)[0];
    }

    /**
     * @param string $name
     * @param string|int|bool|float $value
     * @return self
     */
    public function setProperty(string $name, $value): self
    {
        $class = new ReflectionClass(ServerProperties::class);
        $property = $this->getPropertyBySerializedName($class, $name);
        if (!$property)
            return $this;

        $setter = 'set' . ucfirst($property->getName());
        call_user_func_array([$this->serverProperties, $setter], [$value]);

        return $this;
    }

    public function hasProperty(string $name):bool
    {
        $class = new ReflectionClass(ServerProperties::class);
        $property = $this->getPropertyBySerializedName($class, $name);
        return (bool) $property;
    }

    public function getProperty(string $name)
    {
        $class = new ReflectionClass(ServerProperties::class);
        $property = $this->getPropertyBySerializedName($class, $name);
        if (!$property)
            return null;

        return $property->getValue($this->serverProperties);
    }

    public function getProperties():array
    {
        return []; //todo
    }

    public function save(): bool
    {
        $properties = $this->serializer->serialize($this->serverProperties, ServerProperties::class);

        if ($this->loader->getLocator() instanceof RemoteFileLocator)
            return $this->sftp->put($this->path, $properties);

        return (file_put_contents($this->path, $properties) !== false);
    }
}
