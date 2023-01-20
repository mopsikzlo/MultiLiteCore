<?php

namespace pocketmine\network\protocol;

class EntityEventPacket extends PEPacket{
	const NETWORK_ID = Info::ENTITY_EVENT_PACKET;
	const PACKET_NAME = "ENTITY_EVENT_PACKET";

	const JUMP = 1;
	const HURT_ANIMATION = 2;
	const DEATH_ANIMATION = 3;

	const TAME_FAIL = 6;
	const TAME_SUCCESS = 7;
	const SHAKE_WET = 8;
	const USE_ITEM = 9;
	const EAT_GRASS_ANIMATION = 10;
	const FISH_HOOK_BUBBLE = 11;
	const FISH_HOOK_POSITION = 12;
	const FISH_HOOK_HOOK = 13;
	const FISH_HOOK_TEASE = 14;
	const SQUID_INK_CLOUD = 15;
	const AMBIENT_SOUND = 16;
	const RESPAWN = 17;

	//TODO add new events

	public $eid;
	public $event;
	public $data = 0;

	public function decode($playerProtocol){
	    $this->getHeader($playerProtocol);
		$this->eid = $this->getEntityId();
		$this->event = $this->getByte();
		$this->data = $this->getVarInt();
	}

	public function encode($playerProtocol){
		$this->reset($playerProtocol);
		$this->putEntityId($this->eid);
		$this->putByte($this->event);
		$this->putVarInt($this->data);
	}
}