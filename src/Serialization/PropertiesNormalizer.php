<?php declare(strict_types=1);
/**
 * @author Jakub Gniecki <kubuspl@onet.eu>
 * @copyright
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */


namespace DevLancer\MCPack\Serialization;

use DevLancer\MCPack\Properties\PropertyNameTrait;
use DevLancer\MCPack\Properties\ServerProperties;
use ReflectionClass;
use ReflectionProperty;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class PropertiesNormalizer implements DenormalizerInterface, NormalizerInterface
{
    use PropertyNameTrait;

    /**
     * @throws \ReflectionException
     */
    public function normalize($object, string $format = null, array $context = []): string
    {
        if (!$this->supportsNormalization($object, $format, $context)) {
            throw new \InvalidArgumentException('Unsupported object type');
        }

        $reflectionClass = new ReflectionClass($object);
        $properties = $reflectionClass->getProperties(ReflectionProperty::IS_PRIVATE);
        $output = '';
        foreach ($properties as $property) {
            $value = $property->getValue($object);
            if ($value === null) {
                continue;
            }

            $serializedName = $this->getSerializedName($property);
            $output .= $serializedName . '=' . $this->normalizeValue($value) . "\n";
        }

        return $output;
    }

    /**
     * @throws \ReflectionException
     */
    public function denormalize($data, string $type, string $format = null, array $context = [])
    {
        if (!$this->supportsDenormalization($data, $type, $format)) {
            throw new \InvalidArgumentException('Unsupported data type');
        }

        $properties = [];
        $lines = explode("\n", $data);
        $reflectionClass = new ReflectionClass($type);

        foreach ($lines as $line) {
            if (!str_contains($line, '=')) {
                continue;
            }

            list($key, $value) = explode('=', $line, 2);
            $key = trim($key);
            $property = $this->getPropertyBySerializedName($reflectionClass, $key);

            if ($property) {
                $propertyName = $property->getName();
                $properties[$propertyName] = $this->denormalizeValue(trim($value));
            }
        }

        $constructorParameters = $reflectionClass->getConstructor()->getParameters();
        $constructorArguments = [];

        foreach ($constructorParameters as $parameter) {
            $name = $parameter->getName();
            if (array_key_exists($name, $properties)) {
                $constructorArguments[$name] = $properties[$name];
            } else {
                $constructorArguments[$name] = null;
            }
        }

        return $reflectionClass->newInstanceArgs($constructorArguments);
    }

    public function supportsNormalization($data, string $format = null): bool
    {
        return $data instanceof ServerProperties;
    }

    public function supportsDenormalization($data, string $type, string $format = null): bool
    {
        return $type === ServerProperties::class;
    }

    private function normalizeValue($value): string
    {
        if (is_bool($value)) {
            return $value ? 'true' : 'false';
        }

        return (string) $value;
    }

    /**
     * @param string $value
     * @return bool|float|int|string
     */
    private function denormalizeValue(string $value)
    {
        if ($value === 'true') {
            return true;
        }
        if ($value === 'false') {
            return false;
        }
        if (is_numeric($value)) {
            return $value + 0;
        }
        return $value;
    }
}