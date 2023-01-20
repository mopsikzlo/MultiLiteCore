<?php

namespace pocketmine\network\protocol;

use pocketmine\network\multiversion\MultiversionEnums;

class TextPacket extends PEPacket {
	const NETWORK_ID = Info::TEXT_PACKET;
	const PACKET_NAME = "TEXT_PACKET";

	const TYPE_RAW = "TYPE_RAW";
	const TYPE_CHAT = "TYPE_CHAT";
	const TYPE_TRANSLATION = "TYPE_TRANSLATION";
	const TYPE_POPUP = "TYPE_POPUP";
	const TYPE_JUKEBOX_POPUP = "TYPE_JUKEBOX_POPUP";
	const TYPE_TIP = "TYPE_TIP";
	const TYPE_SYSTEM = "TYPE_SYSTEM";
	const TYPE_WHISPER = "TYPE_WHISPER";
	const TYPE_ANNOUNCEMENT = "TYPE_ANNOUNCEMENT";

	public $type;
	public $source;
	public $message;
	public $parameters = [];
	public $isLocalize = false;
	public $xuid = "";

	public function decode($playerProtocol){
	    $this->getHeader($playerProtocol);
		$this->type = $this->getByte();
		$this->type = MultiversionEnums::getMessageType($playerProtocol, $this->type);
		
		if ($playerProtocol >= Info::PROTOCOL_120) {
			$this->isLocalize = $this->getBool();
		}
		
		switch($this->type) {
			case self::TYPE_CHAT:
			case self::TYPE_WHISPER:
			case self::TYPE_ANNOUNCEMENT:
				$this->source = $this->getString();
				$this->message = $this->getString();
				break;
			case self::TYPE_RAW:
			case self::TYPE_TIP:
			case self::TYPE_SYSTEM:
				$this->message = $this->getString();
				break;
			case self::TYPE_TRANSLATION:
			case self::TYPE_POPUP:
				$this->message = $this->getString();
				$paramCount = $this->getUnsignedVarInt();
				for($i = 0; $i < $paramCount; $i++) {
					$this->parameters[] = $this->getString();
				}
				break;
		}
		
		if($playerProtocol >= Info::PROTOCOL_120) {
			$this->xuid = $this->getString();
		}
	}

	public function encode($playerProtocol){
		$this->reset($playerProtocol);
		$typeId = MultiversionEnums::getMessageTypeId($playerProtocol, $this->type);
		$this->putByte($typeId);
		
		if($playerProtocol >= Info::PROTOCOL_120) {
			$this->putBool($this->isLocalize);
		}
		
		switch($this->type) {
			case self::TYPE_CHAT:
			case self::TYPE_WHISPER:
			case self::TYPE_ANNOUNCEMENT:
				$this->putString($this->source);
				$this->putString($this->message);
				break;
			case self::TYPE_RAW:
			case self::TYPE_TIP:
			case self::TYPE_SYSTEM:
				$this->putString($this->message);
				break;
			case self::TYPE_TRANSLATION:
			case self::TYPE_POPUP:
			case self::TYPE_JUKEBOX_POPUP:
				$this->putString($this->message);
				$this->putUnsignedVarInt(count($this->parameters));
				foreach ($this->parameters as $p) {
					$this->putString($p);
				}
				break;
		}
		
		if ($playerProtocol >= Info::PROTOCOL_120) {
			$this->putString($this->xuid);
		}
	}
}