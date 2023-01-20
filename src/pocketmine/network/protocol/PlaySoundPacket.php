<?php

namespace pocketmine\network\protocol;

class PlaySoundPacket extends PEPacket{
	const NETWORK_ID = Info110::PLAY_SOUND_PACKET;
	const PACKET_NAME = "PLAY_SOUND_PACKET";

	public $sound;
	public $x;
	public $y;
	public $z;
	public $volume;
	public $float;

	public function decode($playerProtocol){
	    $this->getHeader($playerProtocol);
		$this->sound = $this->getString();
		$this->getBlockPos($this->x, $this->y, $this->z);
		$this->volume = $this->getFloat();
		$this->float = $this->getFloat();
	}

	public function encode($playerProtocol){
		$this->reset($playerProtocol);
		$this->putString($this->sound);
		$this->putBlockPos($this->x, $this->y, $this->z);
		$this->putFloat($this->volume);
		$this->putFloat($this->float);
	}
}