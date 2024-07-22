<?php declare(strict_types=1);
/**
 * @author Jakub Gniecki <kubuspl@onet.eu>
 * @copyright
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */


namespace DevLancer\MCPack\Serialization;

use Symfony\Component\Serializer\Encoder\DecoderInterface;
use Symfony\Component\Serializer\Encoder\EncoderInterface;
use DevLancer\MCPack\Properties\ServerProperties;

class PropertiesEncoder implements EncoderInterface, DecoderInterface
{
    public const FORMAT = ServerProperties::class;

    public function encode($data, string $format, array $context = []): string
    {
        return $data;
    }

    public function decode(string $data, string $format, array $context = []): string
    {
        return $data;
    }

    public function supportsEncoding(string $format): bool
    {
        return $format === self::FORMAT;
    }

    public function supportsDecoding(string $format): bool
    {
        return $format === self::FORMAT;
    }
}