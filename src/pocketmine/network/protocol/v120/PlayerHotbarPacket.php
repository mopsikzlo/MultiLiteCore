<?php

namespace pocketmine\network\protocol\v120;

use pocketmine\network\protocol\Info;
use pocketmine\network\protocol\Info120;
use pocketmine\network\protocol\PEPacket;

class PlayerHotbarPacket extends PEPacket {
	
	const NETWORK_ID = Info120::PLAYER_HOTBAR_PACKET;
	const PACKET_NAME = "PLAYER_HOTBAR_PACKET";
	
	public $selectedSlot;
	public $slotsLink;
	
	public function decode($playerProtocol) {}

	public function encode($playerProtocol) {
		$this->reset($playerProtocol);
		$this->putUnsignedVarInt($this->selectedSlot);
		$this->putByte(0); // container ID, 0 - player inventory
		$slotsNum = count($this->slotsLink);
		$this->putUnsignedVarInt($slotsNum);
		
		for ($i = 0; $i < $slotsNum; $i++) {
			$this->putUnsignedVarInt($this->slotsLink[$i]);
		}
		
		$this->putBool(false); // Should select slot (don't know how it works)
	}
}