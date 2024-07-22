<?php declare(strict_types=1);
/**
 * @author Jakub Gniecki <kubuspl@onet.eu>
 * @copyright
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */


namespace DevLancer\MCPack\Properties;

use ReflectionClass;
use ReflectionProperty;

trait PropertyNameTrait
{
    private function getSerializedName(ReflectionProperty $property): string
    {
        $docComment = $property->getDocComment();

        if ($docComment !== false) {
            if (preg_match('/@SerializedName\("([^"]+)"\)/', $docComment, $matches)) {
                return $matches[1];
            }
        }

        return $this->convertCamelCaseToSnakeCase($property->getName());
    }

    private function getPropertyBySerializedName(ReflectionClass $class, string $name): ?ReflectionProperty
    {
        foreach ($class->getProperties(ReflectionProperty::IS_PRIVATE) as $property) {
            $serializedName = $this->getSerializedName($property);
            if ($serializedName === $name) {
                return $property;
            }
        }

        return null;
    }

    private function convertCamelCaseToSnakeCase(string $input): string
    {
        return strtolower(preg_replace('/[A-Z]/', '_$0', lcfirst($input)));
    }
}