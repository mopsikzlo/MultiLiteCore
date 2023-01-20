<?php

namespace pocketmine\network\protocol;

class SetTitlePacket extends PEPacket {
	const NETWORK_ID = Info110::SET_TITLE_PACKET;
	const PACKET_NAME = "SET_TITLE_PACKET";

	const TYPE_CLEAR = 0;
	const TYPE_RESET = 1;
	const TYPE_TITLE = 2;
	const TYPE_SUB_TITLE = 3;
	const TYPE_ACTION_BAR = 4;
	const TYPE_TIMES = 5;

	public $type;
	public $title;
	public $fadeInDuration;
	public $duration;
	public $fadeOutDuration;

	public function decode($playerProtocol){}

	public function encode($playerProtocol){
		$this->reset($playerProtocol);
		$this->putVarInt($this->type);
		$this->putString($this->title);
		$this->putVarInt($this->fadeInDuration);
		$this->putVarInt($this->duration);
		$this->putVarInt($this->fadeOutDuration);
	}
}