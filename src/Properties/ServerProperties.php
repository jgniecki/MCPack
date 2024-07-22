<?php declare(strict_types=1);
/**
 * @author Jakub Gniecki <kubuspl@onet.eu>
 * @copyright
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace DevLancer\MCPack\Properties;

use ReflectionClass;
use Symfony\Component\Serializer\Annotation\SerializedName;

class ServerProperties
{
    use PropertyNameTrait;

    /**
     * @SerializedName("accepts-transfers")
     * @var ?bool
     */
    private ?bool $acceptsTransfers;

    /**
     * @SerializedName("allow-flight")
     * @var ?bool
     */
    private ?bool $allowFlight;

    /**
     * @SerializedName("allow-nether")
     * @var ?bool
     */
    private ?bool $allowNether;

    /**
     * @SerializedName("broadcast-console-to-ops")
     * @var ?bool
     */
    private ?bool $broadcastConsoleToOps;

    /**
     * @SerializedName("broadcast-rcon-to-ops")
     * @var ?bool
     */
    private ?bool $broadcastRconToOps;

    /**
     * @SerializedName("bug-report-link")
     * @var ?string
     */
    private ?string $bugReportLink;

    /**
     * @SerializedName("difficulty")
     * @var ?string
     */
    private ?string $difficulty;

    // private ?DifficultyEnum $difficultyEnum = DifficultyEnum::easy;

    /**
     * @SerializedName("enable-command-block")
     * @var ?bool
     */
    private ?bool $enableCommandBlock;

    /**
     * @SerializedName("enable-jmx-monitoring")
     * @var ?bool
     */
    private ?bool $enableJmxMonitoring;

    /**
     * @SerializedName("enable-query")
     * @var ?bool
     */
    private ?bool $enableQuery;

    /**
     * @SerializedName("enable-rcon")
     * @var ?bool
     */
    private ?bool $enableRcon;

    /**
     * @SerializedName("enable-status")
     * @var ?bool
     */
    private ?bool $enableStatus;

    /**
     * @SerializedName("enforce-secure-profile")
     * @var ?bool
     */
    private ?bool $enforceSecureProfile;

    /**
     * @SerializedName("enforce-whitelist")
     * @var ?bool
     */
    private ?bool $enforceWhitelist;

    /**
     * @SerializedName("entity-broadcast-range-percentage")
     * @var ?int
     */
    private ?int $entityBroadcastRangePercentage;

    /**
     * @SerializedName("force-gamemode")
     * @var ?bool
     */
    private ?bool $forceGamemode;

    /**
     * @SerializedName("function-permission-level")
     * @var ?int
     */
    private ?int $functionPermissionLevel;

    /**
     * @SerializedName("gamemode")
     * @var ?string
     */
    private ?string $gamemode;

    // private ?GamemodeEnum $gamemodeEnum = GamemodeEnum::survival;

    /**
     * @SerializedName("generate-structures")
     * @var ?bool
     */
    private ?bool $generateStructures;

    /**
     * @SerializedName("generator-settings")
     * @var ?string
     */
    private ?string $generatorSettings;

    /**
     * @SerializedName("hardcore")
     * @var ?bool
     */
    private ?bool $hardcore;

    /**
     * @SerializedName("hide-online-players")
     * @var ?bool
     */
    private ?bool $hideOnlinePlayers;

    /**
     * @SerializedName("initial-disabled-packs")
     * @var ?string
     */
    private ?string $initialDisabledPacks;

    /**
     * @SerializedName("initial-enabled-packs")
     * @var ?string
     */
    private ?string $initialEnabledPacks;

    /**
     * @SerializedName("level-name")
     * @var ?string
     */
    private ?string $levelName;

    /**
     * @SerializedName("level-seed")
     * @var ?string
     */
    private ?string $levelSeed;

    /**
     * @SerializedName("level-type")
     * @var ?string
     */
    private ?string $levelType;

    /**
     * @SerializedName("log-ips")
     * @var ?bool
     */
    private ?bool $logIps;

    /**
     * @SerializedName("max-chained-neighbor-updates")
     * @var ?int
     */
    private ?int $maxChainedNeighborUpdates;

    /**
     * @SerializedName("max-players")
     * @var ?int
     */
    private ?int $maxPlayers;

    /**
     * @SerializedName("max-tick-time")
     * @var ?int
     */
    private ?int $maxTickTime;

    /**
     * @SerializedName("max-world-size")
     * @var ?int
     */
    private ?int $maxWorldSize;

    /**
     * @SerializedName("motd")
     * @var ?string
     */
    private ?string $motd;

    /**
     * @SerializedName("network-compression-threshold")
     * @var ?int
     */
    private ?int $networkCompressionThreshold;

    /**
     * @SerializedName("online-mode")
     * @var ?bool
     */
    private ?bool $onlineMode;

    /**
     * @SerializedName("op-permission-level")
     * @var ?int
     */
    private ?int $opPermissionLevel;

    /**
     * @SerializedName("player-idle-timeout")
     * @var ?int
     */
    private ?int $playerIdleTimeout;

    /**
     * @SerializedName("prevent-proxy-connections")
     * @var ?bool
     */
    private ?bool $preventProxyConnections;

    /**
     * @SerializedName("pvp")
     * @var ?bool
     */
    private ?bool $pvp;

    /**
     * @SerializedName("query.port")
     * @var ?int
     */
    private ?int $queryPort;

    /**
     * @SerializedName("rate-limit")
     * @var ?int
     */
    private ?int $rateLimit;

    /**
     * @SerializedName("rcon.password")
     * @var ?string
     */
    private ?string $rconPassword;

    /**
     * @SerializedName("rcon.port")
     * @var ?int
     */
    private ?int $rconPort;

    /**
     * @SerializedName("region-file-compression")
     * @var ?string
     */
    private ?string $regionFileCompression;

    /**
     * @SerializedName("require-resource-pack")
     * @var ?bool
     */
    private ?bool $requireResourcePack;

    /**
     * @SerializedName("resource-pack")
     * @var ?string
     */
    private ?string $resourcePack;

    /**
     * @SerializedName("resource-pack-id")
     * @var ?string
     */
    private ?string $resourcePackId;

    /**
     * @SerializedName("resource-pack-prompt")
     * @var ?string
     */
    private ?string $resourcePackPrompt;

    /**
     * @SerializedName("resource-pack-sha1")
     * @var ?string
     */
    private ?string $resourcePackSha1;

    /**
     * @SerializedName("server-ip")
     * @var ?string
     */
    private ?string $serverIp;

    /**
     * @SerializedName("server-port")
     * @var ?int
     */
    private ?int $serverPort;

    /**
     * @SerializedName("simulation-distance")
     * @var ?int
     */
    private ?int $simulationDistance;

    /**
     * @SerializedName("spawn-animals")
     * @var ?bool
     */
    private ?bool $spawnAnimals;

    /**
     * @SerializedName("spawn-monsters")
     * @var ?bool
     */
    private ?bool $spawnMonsters;

    /**
     * @SerializedName("spawn-npcs")
     * @var ?bool
     */
    private ?bool $spawnNpcs;

    /**
     * @SerializedName("spawn-protection")
     * @var ?int
     */
    private ?int $spawnProtection;

    /**
     * @SerializedName("sync-chunk-writes")
     * @var ?bool
     */
    private ?bool $syncChunkWrites;

    /**
     * @SerializedName("text-filtering-config")
     * @var ?string
     */
    private ?string $textFilteringConfig;

    /**
     * @SerializedName("use-native-transport")
     * @var ?bool
     */
    private ?bool $useNativeTransport;

    /**
     * @SerializedName("view-distance")
     * @var ?int
     */
    private ?int $viewDistance;

    /**
     * @SerializedName("white-list")
     * @var ?bool
     */
    private ?bool $whiteList;

    public function __construct(
        ?bool $acceptsTransfers = false,
        ?bool $allowFlight = false,
        ?bool $allowNether = true,
        ?bool $broadcastConsoleToOps = true,
        ?bool $broadcastRconToOps = true,
        ?string $bugReportLink = "",
        ?string $difficulty = "easy",
        ?bool $enableCommandBlock = false,
        ?bool $enableJmxMonitoring = false,
        ?bool $enableQuery = false,
        ?bool $enableRcon = false,
        ?bool $enableStatus = true,
        ?bool $enforceSecureProfile = true,
        ?bool $enforceWhitelist = false,
        ?int $entityBroadcastRangePercentage = 100,
        ?bool $forceGamemode = false,
        ?int $functionPermissionLevel = 2,
        ?string $gamemode = "survival",
        ?bool $generateStructures = true,
        ?string $generatorSettings = "{}",
        ?bool $hardcore = false,
        ?bool $hideOnlinePlayers = false,
        ?string $initialDisabledPacks = "",
        ?string $initialEnabledPacks = "vanilla",
        ?string $levelName = "world",
        ?string $levelSeed = "",
        ?string $levelType = "minecraft:normal",
        ?bool $logIps = true,
        ?int $maxChainedNeighborUpdates = 1000000,
        ?int $maxPlayers = 20,
        ?int $maxTickTime = 60000,
        ?int $maxWorldSize = 29999984,
        ?string $motd = "A Minecraft Server",
        ?int $networkCompressionThreshold = 256,
        ?bool $onlineMode = true,
        ?int $opPermissionLevel = 4,
        ?int $playerIdleTimeout = 0,
        ?bool $preventProxyConnections = false,
        ?bool $pvp = true,
        ?int $queryPort = 25565,
        ?int $rateLimit = 0,
        ?string $rconPassword = "",
        ?int $rconPort = 25575,
        ?string $regionFileCompression = "deflate",
        ?bool $requireResourcePack = false,
        ?string $resourcePack = "",
        ?string $resourcePackId = "",
        ?string $resourcePackPrompt = "",
        ?string $resourcePackSha1 = "",
        ?string $serverIp = "",
        ?int $serverPort = 25565,
        ?int $simulationDistance = 10,
        ?bool $spawnAnimals = true,
        ?bool $spawnMonsters = true,
        ?bool $spawnNpcs = true,
        ?int $spawnProtection = 16,
        ?bool $syncChunkWrites = true,
        ?string $textFilteringConfig = "",
        ?bool $useNativeTransport = true,
        ?int $viewDistance = 10,
        ?bool $whiteList = false
    ) {
        $this->acceptsTransfers = $acceptsTransfers;
        $this->allowFlight = $allowFlight;
        $this->allowNether = $allowNether;
        $this->broadcastConsoleToOps = $broadcastConsoleToOps;
        $this->broadcastRconToOps = $broadcastRconToOps;
        $this->bugReportLink = $bugReportLink;
        $this->difficulty = $difficulty;
        $this->enableCommandBlock = $enableCommandBlock;
        $this->enableJmxMonitoring = $enableJmxMonitoring;
        $this->enableQuery = $enableQuery;
        $this->enableRcon = $enableRcon;
        $this->enableStatus = $enableStatus;
        $this->enforceSecureProfile = $enforceSecureProfile;
        $this->enforceWhitelist = $enforceWhitelist;
        $this->entityBroadcastRangePercentage = $entityBroadcastRangePercentage;
        $this->forceGamemode = $forceGamemode;
        $this->functionPermissionLevel = $functionPermissionLevel;
        $this->gamemode = $gamemode;
        $this->generateStructures = $generateStructures;
        $this->generatorSettings = $generatorSettings;
        $this->hardcore = $hardcore;
        $this->hideOnlinePlayers = $hideOnlinePlayers;
        $this->initialDisabledPacks = $initialDisabledPacks;
        $this->initialEnabledPacks = $initialEnabledPacks;
        $this->levelName = $levelName;
        $this->levelSeed = $levelSeed;
        $this->levelType = $levelType;
        $this->logIps = $logIps;
        $this->maxChainedNeighborUpdates = $maxChainedNeighborUpdates;
        $this->maxPlayers = $maxPlayers;
        $this->maxTickTime = $maxTickTime;
        $this->maxWorldSize = $maxWorldSize;
        $this->motd = $motd;
        $this->networkCompressionThreshold = $networkCompressionThreshold;
        $this->onlineMode = $onlineMode;
        $this->opPermissionLevel = $opPermissionLevel;
        $this->playerIdleTimeout = $playerIdleTimeout;
        $this->preventProxyConnections = $preventProxyConnections;
        $this->pvp = $pvp;
        $this->queryPort = $queryPort;
        $this->rateLimit = $rateLimit;
        $this->rconPassword = $rconPassword;
        $this->rconPort = $rconPort;
        $this->regionFileCompression = $regionFileCompression;
        $this->requireResourcePack = $requireResourcePack;
        $this->resourcePack = $resourcePack;
        $this->resourcePackId = $resourcePackId;
        $this->resourcePackPrompt = $resourcePackPrompt;
        $this->resourcePackSha1 = $resourcePackSha1;
        $this->serverIp = $serverIp;
        $this->serverPort = $serverPort;
        $this->simulationDistance = $simulationDistance;
        $this->spawnAnimals = $spawnAnimals;
        $this->spawnMonsters = $spawnMonsters;
        $this->spawnNpcs = $spawnNpcs;
        $this->spawnProtection = $spawnProtection;
        $this->syncChunkWrites = $syncChunkWrites;
        $this->textFilteringConfig = $textFilteringConfig;
        $this->useNativeTransport = $useNativeTransport;
        $this->viewDistance = $viewDistance;
        $this->whiteList = $whiteList;
    }

    public function toArray(): array
    {
        $result = [];

        $class = new ReflectionClass(ServerProperties::class);
        $properties = $class->getProperties(\ReflectionProperty::IS_PRIVATE);
        foreach ($properties as $property) {
            $property->setAccessible(true);
            if ($property->getValue($this) === null)
                continue;

            $result[$this->getSerializedName($property)] = $property->getValue($this);
        }

        return $result;
    }

    public function getAcceptsTransfers(): ?bool
    {
        return $this->acceptsTransfers;
    }

    public function setAcceptsTransfers(?bool $acceptsTransfers): void
    {
        $this->acceptsTransfers = $acceptsTransfers;
    }

    public function getAllowFlight(): ?bool
    {
        return $this->allowFlight;
    }

    public function setAllowFlight(?bool $allowFlight): void
    {
        $this->allowFlight = $allowFlight;
    }

    public function getAllowNether(): ?bool
    {
        return $this->allowNether;
    }

    public function setAllowNether(?bool $allowNether): void
    {
        $this->allowNether = $allowNether;
    }

    public function getBroadcastConsoleToOps(): ?bool
    {
        return $this->broadcastConsoleToOps;
    }

    public function setBroadcastConsoleToOps(?bool $broadcastConsoleToOps): void
    {
        $this->broadcastConsoleToOps = $broadcastConsoleToOps;
    }

    public function getBroadcastRconToOps(): ?bool
    {
        return $this->broadcastRconToOps;
    }

    public function setBroadcastRconToOps(?bool $broadcastRconToOps): void
    {
        $this->broadcastRconToOps = $broadcastRconToOps;
    }

    public function getBugReportLink(): ?string
    {
        return $this->bugReportLink;
    }

    public function setBugReportLink(?string $bugReportLink): void
    {
        $this->bugReportLink = $bugReportLink;
    }

    public function getDifficulty(): ?string
    {
        return $this->difficulty;
    }

    public function setDifficulty(?string $difficulty): void
    {
        $this->difficulty = $difficulty;
    }

    public function getEnableCommandBlock(): ?bool
    {
        return $this->enableCommandBlock;
    }

    public function setEnableCommandBlock(?bool $enableCommandBlock): void
    {
        $this->enableCommandBlock = $enableCommandBlock;
    }

    public function getEnableJmxMonitoring(): ?bool
    {
        return $this->enableJmxMonitoring;
    }

    public function setEnableJmxMonitoring(?bool $enableJmxMonitoring): void
    {
        $this->enableJmxMonitoring = $enableJmxMonitoring;
    }

    public function getEnableQuery(): ?bool
    {
        return $this->enableQuery;
    }

    public function setEnableQuery(?bool $enableQuery): void
    {
        $this->enableQuery = $enableQuery;
    }

    public function getEnableRcon(): ?bool
    {
        return $this->enableRcon;
    }

    public function setEnableRcon(?bool $enableRcon): void
    {
        $this->enableRcon = $enableRcon;
    }

    public function getEnableStatus(): ?bool
    {
        return $this->enableStatus;
    }

    public function setEnableStatus(?bool $enableStatus): void
    {
        $this->enableStatus = $enableStatus;
    }

    public function getEnforceSecureProfile(): ?bool
    {
        return $this->enforceSecureProfile;
    }

    public function setEnforceSecureProfile(?bool $enforceSecureProfile): void
    {
        $this->enforceSecureProfile = $enforceSecureProfile;
    }

    public function getEnforceWhitelist(): ?bool
    {
        return $this->enforceWhitelist;
    }

    public function setEnforceWhitelist(?bool $enforceWhitelist): void
    {
        $this->enforceWhitelist = $enforceWhitelist;
    }

    public function getEntityBroadcastRangePercentage(): ?int
    {
        return $this->entityBroadcastRangePercentage;
    }

    public function setEntityBroadcastRangePercentage(?int $entityBroadcastRangePercentage): void
    {
        $this->entityBroadcastRangePercentage = $entityBroadcastRangePercentage;
    }

    public function getForceGamemode(): ?bool
    {
        return $this->forceGamemode;
    }

    public function setForceGamemode(?bool $forceGamemode): void
    {
        $this->forceGamemode = $forceGamemode;
    }

    public function getFunctionPermissionLevel(): ?int
    {
        return $this->functionPermissionLevel;
    }

    public function setFunctionPermissionLevel(?int $functionPermissionLevel): void
    {
        $this->functionPermissionLevel = $functionPermissionLevel;
    }

    public function getGamemode(): ?string
    {
        return $this->gamemode;
    }

    public function setGamemode(?string $gamemode): void
    {
        $this->gamemode = $gamemode;
    }

    public function getGenerateStructures(): ?bool
    {
        return $this->generateStructures;
    }

    public function setGenerateStructures(?bool $generateStructures): void
    {
        $this->generateStructures = $generateStructures;
    }

    public function getGeneratorSettings(): ?string
    {
        return $this->generatorSettings;
    }

    public function setGeneratorSettings(?string $generatorSettings): void
    {
        $this->generatorSettings = $generatorSettings;
    }

    public function getHardcore(): ?bool
    {
        return $this->hardcore;
    }

    public function setHardcore(?bool $hardcore): void
    {
        $this->hardcore = $hardcore;
    }

    public function getHideOnlinePlayers(): ?bool
    {
        return $this->hideOnlinePlayers;
    }

    public function setHideOnlinePlayers(?bool $hideOnlinePlayers): void
    {
        $this->hideOnlinePlayers = $hideOnlinePlayers;
    }

    public function getInitialDisabledPacks(): ?string
    {
        return $this->initialDisabledPacks;
    }

    public function setInitialDisabledPacks(?string $initialDisabledPacks): void
    {
        $this->initialDisabledPacks = $initialDisabledPacks;
    }

    public function getInitialEnabledPacks(): ?string
    {
        return $this->initialEnabledPacks;
    }

    public function setInitialEnabledPacks(?string $initialEnabledPacks): void
    {
        $this->initialEnabledPacks = $initialEnabledPacks;
    }

    public function getLevelName(): ?string
    {
        return $this->levelName;
    }

    public function setLevelName(?string $levelName): void
    {
        $this->levelName = $levelName;
    }

    public function getLevelSeed(): ?string
    {
        return $this->levelSeed;
    }

    public function setLevelSeed(?string $levelSeed): void
    {
        $this->levelSeed = $levelSeed;
    }

    public function getLevelType(): ?string
    {
        return $this->levelType;
    }

    public function setLevelType(?string $levelType): void
    {
        $this->levelType = $levelType;
    }

    public function getLogIps(): ?bool
    {
        return $this->logIps;
    }

    public function setLogIps(?bool $logIps): void
    {
        $this->logIps = $logIps;
    }

    public function getMaxChainedNeighborUpdates(): ?int
    {
        return $this->maxChainedNeighborUpdates;
    }

    public function setMaxChainedNeighborUpdates(?int $maxChainedNeighborUpdates): void
    {
        $this->maxChainedNeighborUpdates = $maxChainedNeighborUpdates;
    }

    public function getMaxPlayers(): ?int
    {
        return $this->maxPlayers;
    }

    public function setMaxPlayers(?int $maxPlayers): void
    {
        $this->maxPlayers = $maxPlayers;
    }

    public function getMaxTickTime(): ?int
    {
        return $this->maxTickTime;
    }

    public function setMaxTickTime(?int $maxTickTime): void
    {
        $this->maxTickTime = $maxTickTime;
    }

    public function getMaxWorldSize(): ?int
    {
        return $this->maxWorldSize;
    }

    public function setMaxWorldSize(?int $maxWorldSize): void
    {
        $this->maxWorldSize = $maxWorldSize;
    }

    public function getMotd(): ?string
    {
        return $this->motd;
    }

    public function setMotd(?string $motd): void
    {
        $this->motd = $motd;
    }

    public function getNetworkCompressionThreshold(): ?int
    {
        return $this->networkCompressionThreshold;
    }

    public function setNetworkCompressionThreshold(?int $networkCompressionThreshold): void
    {
        $this->networkCompressionThreshold = $networkCompressionThreshold;
    }

    public function getOnlineMode(): ?bool
    {
        return $this->onlineMode;
    }

    public function setOnlineMode(?bool $onlineMode): void
    {
        $this->onlineMode = $onlineMode;
    }

    public function getOpPermissionLevel(): ?int
    {
        return $this->opPermissionLevel;
    }

    public function setOpPermissionLevel(?int $opPermissionLevel): void
    {
        $this->opPermissionLevel = $opPermissionLevel;
    }

    public function getPlayerIdleTimeout(): ?int
    {
        return $this->playerIdleTimeout;
    }

    public function setPlayerIdleTimeout(?int $playerIdleTimeout): void
    {
        $this->playerIdleTimeout = $playerIdleTimeout;
    }

    public function getPreventProxyConnections(): ?bool
    {
        return $this->preventProxyConnections;
    }

    public function setPreventProxyConnections(?bool $preventProxyConnections): void
    {
        $this->preventProxyConnections = $preventProxyConnections;
    }

    public function getPvp(): ?bool
    {
        return $this->pvp;
    }

    public function setPvp(?bool $pvp): void
    {
        $this->pvp = $pvp;
    }

    public function getQueryPort(): ?int
    {
        return $this->queryPort;
    }

    public function setQueryPort(?int $queryPort): void
    {
        $this->queryPort = $queryPort;
    }

    public function getRateLimit(): ?int
    {
        return $this->rateLimit;
    }

    public function setRateLimit(?int $rateLimit): void
    {
        $this->rateLimit = $rateLimit;
    }

    public function getRconPassword(): ?string
    {
        return $this->rconPassword;
    }

    public function setRconPassword(?string $rconPassword): void
    {
        $this->rconPassword = $rconPassword;
    }

    public function getRconPort(): ?int
    {
        return $this->rconPort;
    }

    public function setRconPort(?int $rconPort): void
    {
        $this->rconPort = $rconPort;
    }

    public function getRegionFileCompression(): ?string
    {
        return $this->regionFileCompression;
    }

    public function setRegionFileCompression(?string $regionFileCompression): void
    {
        $this->regionFileCompression = $regionFileCompression;
    }

    public function getRequireResourcePack(): ?bool
    {
        return $this->requireResourcePack;
    }

    public function setRequireResourcePack(?bool $requireResourcePack): void
    {
        $this->requireResourcePack = $requireResourcePack;
    }

    public function getResourcePack(): ?string
    {
        return $this->resourcePack;
    }

    public function setResourcePack(?string $resourcePack): void
    {
        $this->resourcePack = $resourcePack;
    }

    public function getResourcePackId(): ?string
    {
        return $this->resourcePackId;
    }

    public function setResourcePackId(?string $resourcePackId): void
    {
        $this->resourcePackId = $resourcePackId;
    }

    public function getResourcePackPrompt(): ?string
    {
        return $this->resourcePackPrompt;
    }

    public function setResourcePackPrompt(?string $resourcePackPrompt): void
    {
        $this->resourcePackPrompt = $resourcePackPrompt;
    }

    public function getResourcePackSha1(): ?string
    {
        return $this->resourcePackSha1;
    }

    public function setResourcePackSha1(?string $resourcePackSha1): void
    {
        $this->resourcePackSha1 = $resourcePackSha1;
    }

    public function getServerIp(): ?string
    {
        return $this->serverIp;
    }

    public function setServerIp(?string $serverIp): void
    {
        $this->serverIp = $serverIp;
    }

    public function getServerPort(): ?int
    {
        return $this->serverPort;
    }

    public function setServerPort(?int $serverPort): void
    {
        $this->serverPort = $serverPort;
    }

    public function getSimulationDistance(): ?int
    {
        return $this->simulationDistance;
    }

    public function setSimulationDistance(?int $simulationDistance): void
    {
        $this->simulationDistance = $simulationDistance;
    }

    public function getSpawnAnimals(): ?bool
    {
        return $this->spawnAnimals;
    }

    public function setSpawnAnimals(?bool $spawnAnimals): void
    {
        $this->spawnAnimals = $spawnAnimals;
    }

    public function getSpawnMonsters(): ?bool
    {
        return $this->spawnMonsters;
    }

    public function setSpawnMonsters(?bool $spawnMonsters): void
    {
        $this->spawnMonsters = $spawnMonsters;
    }

    public function getSpawnNpcs(): ?bool
    {
        return $this->spawnNpcs;
    }

    public function setSpawnNpcs(?bool $spawnNpcs): void
    {
        $this->spawnNpcs = $spawnNpcs;
    }

    public function getSpawnProtection(): ?int
    {
        return $this->spawnProtection;
    }

    public function setSpawnProtection(?int $spawnProtection): void
    {
        $this->spawnProtection = $spawnProtection;
    }

    public function getSyncChunkWrites(): ?bool
    {
        return $this->syncChunkWrites;
    }

    public function setSyncChunkWrites(?bool $syncChunkWrites): void
    {
        $this->syncChunkWrites = $syncChunkWrites;
    }

    public function getTextFilteringConfig(): ?string
    {
        return $this->textFilteringConfig;
    }

    public function setTextFilteringConfig(?string $textFilteringConfig): void
    {
        $this->textFilteringConfig = $textFilteringConfig;
    }

    public function getUseNativeTransport(): ?bool
    {
        return $this->useNativeTransport;
    }

    public function setUseNativeTransport(?bool $useNativeTransport): void
    {
        $this->useNativeTransport = $useNativeTransport;
    }

    public function getViewDistance(): ?int
    {
        return $this->viewDistance;
    }

    public function setViewDistance(?int $viewDistance): void
    {
        $this->viewDistance = $viewDistance;
    }

    public function getWhiteList(): ?bool
    {
        return $this->whiteList;
    }

    public function setWhiteList(?bool $whiteList): void
    {
        $this->whiteList = $whiteList;
    }
}
