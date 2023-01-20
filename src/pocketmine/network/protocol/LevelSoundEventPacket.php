<?php

namespace pocketmine\network\protocol;

use pocketmine\network\multiversion\MultiversionEnums;

class LevelSoundEventPacket extends PEPacket {

	const NETWORK_ID = Info::LEVEL_SOUND_EVENT_PACKET;
	const PACKET_NAME = "LEVEL_SOUND_EVENT_PACKET";

	const SOUND_LARGE_BLAST = 'SOUND_LARGE_BLAST'; //for firework
	const SOUND_TWINKLE = 'SOUND_TWINKLE'; //for firework
	const SOUND_BLAST = 'SOUND_BLAST'; //for firework
	const SOUND_LAUNCH = 'SOUND_LAUNCH'; //for firework
	const SOUND_IGNITE = 'SOUND_IGNITE';
	const SOUND_HIT = 'SOUND_HIT';
	const SOUND_BREAK = 'SOUND_BREAK';
	const SOUND_PLACE = 'SOUND_PLACE';
	const SOUND_EAT = 'SOUND_EAT';
 	const SOUND_EXPLODE = 'SOUND_EXPLODE';
	const SOUND_BREAK_BLOCK = 'SOUND_BREAK_BLOCK';
 	const SOUND_CHEST_OPEN = 'SOUND_CHEST_OPEN';
 	const SOUND_CHEST_CLOSED = 'SOUND_CHEST_CLOSED';
	const SOUND_NOTE = 'SOUND_NOTE';
	const SOUND_BOW = 'SOUND_BOW';
	const SOUND_UNDEFINED = 'SOUND_UNDEFINED';
	const SOUND_LAND = 'SOUND_LAND';
	const SOUND_SPAWN = 'SOUND_SPAWN';
	const SOUND_FUSE = 'SOUND_FUSE';
	const SOUND_BOW_HIT = 'SOUND_BOW_HIT';
	const SOUND_SHULKERBOX_OPEN = 'SOUND_SHULKERBOX_OPEN';
	const SOUND_SHULKERBOX_CLOSED = 'SOUND_SHULKERBOX_CLOSED';
	const EVENT_SOUND_ITEMFRAME_ADD_ITEM = 'EVENT_SOUND_ITEMFRAME_ADD_ITEM';
	const EVENT_SOUND_ITEMFRAME_ROTATE_ITEM = 'EVENT_SOUND_ITEMFRAME_ROTATE_ITEM';
	const SOUND_FIZZ = "SOUND_FIZZ";
	const SOUND_LEVELUP = "SOUND_LEVEL_UP";

	public $sound;
	public $x;
	public $y;
	public $z;
	public $extraData = -1;
	public $pitch = 1;
	public $unknownBool = false;
	public $unknownBool2 = false;

	public function decode($playerProtocol){
	    $this->getHeader($playerProtocol);
		$this->sound = $this->getByte();
		$this->sound = MultiversionEnums::getLevelSoundEventName($playerProtocol, $this->sound);
		$this->getVector3f($this->x, $this->y, $this->z);
		$this->extraData = $this->getVarInt();
		$this->pitch = $this->getVarInt();
		$this->unknownBool = $this->getBool();
		$this->unknownBool2 = $this->getBool();
	}

	public function encode($playerProtocol){
		$this->reset($playerProtocol);
		$sound = MultiversionEnums::getLevelSoundEventId($playerProtocol, $this->sound);
		$this->putByte($sound);
		$this->putVector3f($this->x, $this->y, $this->z);
		$this->putVarInt($this->extraData);
		$this->putVarInt($this->pitch);
		$this->putBool($this->unknownBool);
		$this->putBool($this->unknownBool2);
	}
}