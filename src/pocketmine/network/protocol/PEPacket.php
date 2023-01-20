<?php

namespace pocketmine\network\protocol;

abstract class PEPacket extends DataPacket {

	const CLIENT_ID_MAIN_PLAYER = 0;
	const CLIENT_ID_SERVER = 0;

	public $senderSubClientID = self::CLIENT_ID_SERVER;

	public $targetSubClientID = self::CLIENT_ID_MAIN_PLAYER;

	abstract public function encode($playerProtocol);

	abstract public function decode($playerProtocol);

	/**
	 * !IMPORTANT! Should be called at first line in decode
	 * @param integer $playerProtocol
	 */
	protected function getHeader($playerProtocol = 0) {
		$d = $this->getByte(); // packetID
		if ($playerProtocol >= Info::PROTOCOL_120) {
			$this->senderSubClientID = $this->getByte();
			$this->targetSubClientID = $this->getByte();
			if ($this->senderSubClientID > 4 || $this->targetSubClientID > 4) {
				throw new \Exception(get_class($this) . ": Packet decode headers error");
			}
		}
	}

	/**
	 * !IMPORTANT! Should be called at first line in encode
	 * @param integer $playerProtocol
	 */
	public function reset($playerProtocol = 0) {
		parent::reset();
		
		if (empty(self::$packetsIds)) {
		    self::initPackets();
		}
		
		$this->putByte(self::$packetsIds[$playerProtocol][$this::PACKET_NAME]);
		if ($playerProtocol >= Info::PROTOCOL_120) {
			$this->putByte($this->senderSubClientID);
			$this->putByte($this->targetSubClientID);
		}
	}

	public final static function convertProtocol($protocol) {
		switch ($protocol) {
		    case Info::PROTOCOL_136:
		    case Info::PROTOCOL_135:
		    case Info::PROTOCOL_134:
		    case Info::PROTOCOL_133:
		    case Info::PROTOCOL_132:
		    case Info::PROTOCOL_131:
		    case Info::PROTOCOL_130:
		    case Info::PROTOCOL_121:
		    case Info::PROTOCOL_120:
				return Info::PROTOCOL_120;
			default:
				return Info::PROTOCOL_110;
		}
	}
	
	public static function convertChunkProtocol($playerProtocol){
		switch ($playerProtocol) {
			case Info::PROTOCOL_120:
				return Info::PROTOCOL_120;
			default:
				return Info::PROTOCOL_110;
		}
	}
}