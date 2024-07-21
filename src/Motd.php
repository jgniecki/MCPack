<?php declare(strict_types=1);
/**
 * @author Jakub Gniecki
 * @copyright Jakub Gniecki <kubuspl@onet.eu>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */


namespace DevLancer\MCPack;

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


    /**
     * @var ServerInfo
     */
    private ServerInfo $info;

    /**
     * @var null|string|array
     */
    private $response;

    /**
     * Motd constructor.
     * @param ServerInfo $info
     */
    public function __construct(ServerInfo $info)
    {
        $this->info = $info;
    }

    /**
     * @param string $type
     * @return bool
     */
    public function sendRequest(string $type = self::REQUEST_RAW): bool
    {
        if (!$this->info->isConnected())
            return false;

        if (get_class($this->info) == self::INFO_QUERY)
            $this->response = (string) $this->info->getInfo()['HostName'];
        else
            $this->response = $this->info->getInfo()['description'][$type];

        return (bool) $this->response;

    }

    /**
     * @param int $type
     * @param array|string[] $format
     * @return array|string|null
     */
    public function getResponse(int $type = self::RESPONSE_RAW, array $format = self::FORMAT_CODE)
    {
        if ($type == self::RESPONSE_RAW)
            return $this->response;

        if (get_class($this->info) == self::INFO_PING) {
            if ($type == self::RESPONSE_TEXT)
                return $this->generateTextMotd();

            if ($type == self::RESPONSE_HTML)
                return $this->generateHtmlMotd($format);
        }

        if (get_class($this->info) == self::INFO_QUERY) {
            if ($type == self::RESPONSE_TEXT)
                return (string) preg_replace('/' . self::SYMBOL . '[0-9a-fk-or]{1}/', '', $this->response);

            if ($type == self::RESPONSE_HTML)
                return (string) $this->parseMotd($format);
        }

        return $this->response;
    }

    /**
     * @param array $format
     * @return string
     */
    private function generateHtmlMotd(array $format): string
    {
        $result = "";

        foreach ($this->response as $item) {
            $style = "";

            foreach ($item as $key => $value) {

                if (isset(self::FORMAT_NAME[$key]) && (bool) $value)
                    $style .= " " . $format[self::FORMAT_NAME[$key]];

                if ($key == "color" && isset(self::FORMAT_NAME[$value]))
                    $style .= " " . $format[self::FORMAT_NAME[$value]];
            }

            $text = "<span style=\"$style\">" . $item['text'] . "</span>";
            $result .= $text;
        }

        return str_replace("\n", "<br />\n", $result);
    }

    /**
     * @return string
     */
    private function generateTextMotd(): string
    {
        $result = "";

        foreach ($this->response as $item)
            $result .= $item['text'];

        return $result;
    }

    /**
     * @param array $format
     * @return string
     */
    private function parseMotd(array $format): string
    {
        $text = preg_split('/' . self::SYMBOL . '[0-9a-fk-or]{1}/', $this->response);

        preg_match_all('/' . self::SYMBOL . '[0-9a-fk-or]{1}/', $this->response, $codes);
        $codes = $codes[0];

        $isOpenColor = false;
        $formatText = 0;

        foreach ($text as $id => $item) {

            if (!isset($codes[$id]) || preg_match('/' . self::SYMBOL . '[r]{1}/', $codes[$id])) {
                for ($i = 1; $formatText >= $i; $i++)
                    $item .= "</span>";

                $formatText = 0;

                if ($isOpenColor) {
                    $item .= "</span>";
                    $isOpenColor = false;
                }

                $text[$id] = $item;

                if (isset($codes[$id]))
                    continue;

                break;
            }

            $code = $codes[$id];

            if (!preg_match('/' . self::SYMBOL . '[0-9a-fk-o]{1}/', $code))
                continue;

            if (preg_match('/' . self::SYMBOL . '[0-9a-f]{1}/', $code)) {
                if ($isOpenColor)
                    $item .= "</span>";

                $isOpenColor = true;
            } else {
                $formatText++;
            }

            $key = $code[strlen($code) - 1];
            $text[$id] = $item . "<span style=\"" . $format[$key] . "\">";

            continue;
        }

        $result = implode("", $text);
        return str_replace("\n", "<br />\n", $result);
    }
}