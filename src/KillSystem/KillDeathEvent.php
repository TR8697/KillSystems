<?php

namespace KillSystem;

use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\entity\EntityDeathEvent;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerDeathEvent;
use pocketmine\player\Player;
use pocketmine\utils\TextFormat;

/**
 * Oyuncu öldürme ve ölüm olaylarını yöneten bir olay dinleyici sınıfı.
 */
class KillDeathEvent implements Listener
{
	/**
	 * Oyuncunun öldürme durumunda para kazanmasını sağlar.
	 *
	 * @param PlayerDeathEvent $event
	 */
	public function killmoney(PlayerDeathEvent $event)
	{
		$oyuncu = $event->getPlayer();
		$sonHasarNedeni = $oyuncu->getLastDamageCause();
		if ($sonHasarNedeni instanceof EntityDamageByEntityEvent) {
			$saldiran = $sonHasarNedeni->getDamager();
			if ($saldiran instanceof Player) {
				$oldurulenPara = Main::getInstance()->getConfig()->get("KillerMoney");
				if ($this->isEconomyAPIAvailable()) {
					\onebone\economyapi\EconomyAPI::getInstance()->addMoney($saldiran, $oldurulenPara);
					$saldiran->sendMessage(TextFormat::GREEN . "Bir oyuncu öldürdünüz, hesabınıza " . $oldurulenPara . " TL eklendi.");
					$saldiran->setHealth($saldiran->getMaxHealth());
				} else {
					Main::getInstance()->getLogger()->warning("EconomyAPI eklentisi bulunamadığından para eklenemedi.");
				}
			}
		}
	}

	/**
	 * Oyuncunun ölüm durumunda para kaybetmesini sağlar.
	 *
	 * @param PlayerDeathEvent $event
	 */
	public function deathmoney(PlayerDeathEvent $event)
	{
		$oyuncu = $event->getPlayer();
		$oldurulenPara = Main::getInstance()->getConfig()->get("DeathMoney");
		if ($this->isEconomyAPIAvailable()) {
			\onebone\economyapi\EconomyAPI::getInstance()->reduceMoney($oyuncu, $oldurulenPara);
			$oyuncu->sendMessage(TextFormat::RED . "Öldünüz, hesabınızdan " . $oldurulenPara . " TL kesildi.");
			$oyuncu->setHealth($oyuncu->getMaxHealth());
		} else {
			Main::getInstance()->getLogger()->warning("EconomyAPI eklentisi bulunamadığından para kesilemedi.");
		}
	}

	/**
	 * EconomyAPI eklentisinin yüklü olup olmadığını kontrol eder.
	 *
	 * @return bool
	 */
	private function isEconomyAPIAvailable(): bool
	{
		return class_exists(\onebone\economyapi\EconomyAPI::class);
	}
}