<?php

namespace pocketmine\network\protocol\v120;

use pocketmine\network\protocol\Info120;
use pocketmine\network\protocol\PEPacket;
use pocketmine\network\protocol\Info;

class InventoryContentPacket extends PEPacket {
	
	const NETWORK_ID = Info120::INVENTORY_CONTENT_PACKET;
	const PACKET_NAME = "INVENTORY_CONTENT_PACKET";
	
	public $inventoryID;
	public $items = [];
	
	public function decode($playerProtocol) {}

	public function encode($playerProtocol) {
		$this->reset($playerProtocol);
		$this->putUnsignedVarInt($this->inventoryID);
		$itemsNum = count($this->items);
		$this->putUnsignedVarInt($itemsNum);
		$index = 1;
		for ($i = 0; $i < $itemsNum; $i++) {
			$this->putSlot($this->items[$i], $playerProtocol);
		}
	}
}