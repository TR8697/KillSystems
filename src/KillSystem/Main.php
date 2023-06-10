<?php

namespace KillSystem;

use pocketmine\plugin\PluginBase;
use pocketmine\utils\Config;

class Main extends PluginBase
{
	private static Main $instance;
	private static Config $config;

	public static function getInstance(): Main
	{
		return self::$instance;
	}

	public function onLoad(): void
	{
		self::$instance = $this;
		$this->saveResource("config.yml");
		self::$config = new Config($this->getDataFolder() . "config.yml", Config::YAML);
	}

	public function onEnable(): void
	{
		$this->getLogger()->info("Plugin aktif!");
		$this->getServer()->getPluginManager()->registerEvents(new KillDeathEvent(), $this);
	}

	public function onDisable(): void
	{
		$this->getLogger()->info("Plugin deaktif");
	}

	public function getConfig(): Config
	{
		return self::$config;
	}
}