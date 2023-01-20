<?php

namespace pocketmine\network\protocol;

class CraftingEventPacket extends PEPacket{
	const NETWORK_ID = Info::CRAFTING_EVENT_PACKET;
	const PACKET_NAME = "CRAFTING_EVENT_PACKET";

	public $windowId;
	public $type;
	public $id;
	public $input = [];
	public $output = [];

	/**
	 * @return $this
	 */
	public function clean(){
		$this->input = [];
		$this->output = [];

		return parent::clean();
	}

	public function decode($playerProtocol){
	    $this->getHeader($playerProtocol);
		$this->windowId = $this->getByte();
		$this->type = $this->getVarInt();
		$this->id = $this->getUUID();

		$size = $this->getUnsignedVarInt();
		for($i = 0; $i < $size and $i < 128; ++$i){
			$this->input[] = $this->getSlot($playerProtocol);
		}

		$size = $this->getUnsignedVarInt();
		for($i = 0; $i < $size and $i < 128; ++$i){
			$this->output[] = $this->getSlot($playerProtocol);
		}
	}

	public function encode($playerProtocol){}
}