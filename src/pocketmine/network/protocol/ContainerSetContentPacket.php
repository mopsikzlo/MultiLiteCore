<?php

namespace pocketmine\network\protocol;

use pocketmine\item\Item;

class ContainerSetContentPacket extends PEPacket{
    
	const NETWORK_ID = Info::CONTAINER_SET_CONTENT_PACKET;
	const PACKET_NAME = "CONTAINER_SET_CONTENT_PACKET";

	const SPECIAL_INVENTORY = 0;
	const SPECIAL_OFFHAND = 0x77;
	const SPECIAL_ARMOR = 0x78;
	const SPECIAL_CREATIVE = 0x79;
	const SPECIAL_HOTBAR = 0x7a;
	const SPECIAL_FIXED_INVENTORY = 0x7b;

	public $windowid;
	public $targetEid;
	/** @var Item[] */
	public $slots = [];
	/** @var Item[] */
	public $hotbar = [];
	
	public function clean(){
		$this->slots = [];
		$this->hotbar = [];

		return parent::clean();
	}
	
	public function decode($playerProtocol){
	    $this->getHeader($playerProtocol);
		$this->windowid = $this->getUnsignedVarInt();
		$this->targetEid = $this->getEntityId();
		$count = $this->getUnsignedVarInt();
		for($s = 0; $s < $count and !$this->feof(); ++$s){
			$this->slots[$s] = $this->getSlot($playerProtocol);
		}
		if($this->windowid === self::SPECIAL_INVENTORY){
			$count = $this->getUnsignedVarInt();
			for($s = 0; $s < $count and !$this->feof(); ++$s){
				$this->hotbar[$s] = $this->getVarInt();
			}
		}
	}
	
	public function encode($playerProtocol){
		$this->reset($playerProtocol);
		$this->putUnsignedVarInt($this->windowid);
		$this->putEntityId($this->targetEid);
		$this->putUnsignedVarInt(count($this->slots));
		foreach($this->slots as $slot){
			$this->putSlot($slot, $playerProtocol);
		}
		if($this->windowid === self::SPECIAL_INVENTORY and count($this->hotbar) > 0){
			$this->putUnsignedVarInt(count($this->hotbar));
			foreach($this->hotbar as $slot){
				$this->putVarInt($slot);
			}
		}else{
			$this->putUnsignedVarInt(0);
		}
	}
}