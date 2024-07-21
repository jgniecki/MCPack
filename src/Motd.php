<?php declare(strict_types=1);
/**
 * @author Jakub Gniecki
 * @copyright Jakub Gniecki <kubuspl@onet.eu>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */


namespace DevLancer\MCPack;

use DevLancer\MinecraftMotdParser\Collection\MotdItemCollection;
use DevLancer\MinecraftMotdParser\Generator\HtmlGenerator;
use DevLancer\MinecraftMotdParser\Generator\TextGenerator;
use DevLancer\MinecraftMotdParser\Parser\ArrayParser;
use DevLancer\MinecraftMotdParser\Parser\TextParser;

/**
 * Class Motd
 * @package DevLancer\MCPack
 * @deprecated since dev-lancer/mc-pack 2.2, use dev-lancer/minecraft-motd-parser package
 */
class Motd
{
    const REQUEST_RAW = "text";
    const REQUEST_EXTRA = "extra";
    const RESPONSE_RAW = 0;
    const RESPONSE_TEXT = 1;
    const RESPONSE_HTML = 2;
    const SYMBOL = "(\?|\\u00A7|§)";
    const FORMAT_CODE = [
        "0" => "color: #000000;",
        "1" => "color: #0000AA;",
        "2" => "color: #00AA00;",
        "3" => "color: #00AAAA;",
        "4" => "color: #AA0000;",
        "5" => "color: #AA00AA;",
        "6" => "color: #FFAA00;",
        "7" => "color: #AAAAAA;",
        "8" => "color: #555555;",
        "9" => "color: #5555FF;",
        "a" => "color: #55FF55;",
        "b" => "color: #55FFFF;",
        "c" => "color: #FF5555;",
        "d" => "color: #FF55FF;",
        "e" => "color: #FFFF55;",
        "f" => "color: #FFFFFF;",
        "k" => "",
        "l" => "font-weight: bold;",
        "m" => "text-decoration: line-through;",
        "n" => "text-decoration: underline;",
        "o" => "font-style: italic;"
    ];
    const FORMAT_NAME = [
        "black" => "0",
        "dark_blue" => "1",
        "dark_green" => "2",
        "dark_aqua" => "3",
        "dark_red" => "4",
        "dark_purple" => "5",
        "gold" => "6",
        "gray" => "7",
        "dark_gray" => "8",
        "blue" => "9",
        "green" => "a",
        "aqua" => "b",
        "red" => "c",
        "light_purple" => "d",
        "yellow" => "e",
        "white" => "f",
        "obfuscated" => "k",
        "bold" => "l",
        "strikethrough" => "m",
        "underline" => "n",
        "italic" => "o"
    ];
    const INFO_QUERY = Query::class;
    const INFO_PING = Ping::class;
    private ServerInfo $info;

    /**
     * @var null|string|array
     */
    private $response;

    public function __construct(ServerInfo $info)
    {
        $this->info = $info;
    }

    public function sendRequest(?string $type = null): bool
    {
        if (!$this->info->isConnected())
            return false;

        $this->response = $this->info->getMotd();
        return (bool) $this->response;

    }

    public function getResponse(int $type = self::RESPONSE_RAW, ?array $format = null, string $symbol = '§')
    {
        if ($type == self::RESPONSE_RAW)
            return $this->response;

        $motd = $this->response;

        if (json_validate($this->response)) {
            $motd = json_decode($this->response, true);
            $parser = new ArrayParser();
        } else {
            $parser = new TextParser(null, null, $symbol);
        }

        $collection = $parser->parse($motd, new MotdItemCollection());
        $generator = ($type == self::RESPONSE_HTML)? new HTMLGenerator() : new TextGenerator();
        return $generator->generate($collection);
    }
}
