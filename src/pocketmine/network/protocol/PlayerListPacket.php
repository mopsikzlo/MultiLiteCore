<?php

namespace pocketmine\network\protocol;

class PlayerListPacket extends PEPacket{
	const NETWORK_ID = Info::PLAYER_LIST_PACKET;
	const PACKET_NAME = "PLAYER_LIST_PACKET";

	const TYPE_ADD = 0;
	const TYPE_REMOVE = 1;

	/**
	 * Each entry is array
	 * 0 - UUID
	 * 1 - Player ID
	 * 2 - Player Name
	 * 3 - Skin ID
	 * 4 - Skin Data
	 * 5 - Cape Data
	 * 6 - Skin Geometry Name
	 * 7 - Skin Geometry Data
	 * 8 - XUID
	 */
	/** @var array[] */
	public $entries = [];
	public $type;

	/**
	 * @return $this
	 */
	public function clean(){
		$this->entries = [];
		return parent::clean();
	}

	public function decode($playerProtocol){}

	public function encode($playerProtocol){
		$this->reset($playerProtocol);
		$this->putByte($this->type);
		$this->putUnsignedVarInt(count($this->entries));
		switch ($this->type) {
			case self::TYPE_ADD:
				foreach ($this->entries as $d) {
			    	$this->putUUID($d[0]);
			    	$this->putEntityId($d[1]);
			    	$this->putString($d[2]);
				    
			    	if ($playerProtocol >= Info::PROTOCOL_120) {
				        $this->putString($d[3]); // Skin ID
				        $skinData = !empty($d[4]) ? $d[4] : $emptySkin;
				    	$this->putString($skinData); // Skin Data
				    	$capeData = isset($d[5]) ? $d[5] : '';
				     	$this->putString($capeData); // Cape Data
				    	$this->putString(isset($d[6]) ? $d[6] : ''); // Skin Geometry Name
				    	$this->putString(isset($d[7]) ? $this->prepareGeometryDataForOld($d[7]) : ''); // Skin Geometry Data
				    	$this->putString(isset($d[8]) ? $d[8] : ''); // XUID
			    	} else {
				        $this->putString("Standard_Custom");
				        $skinData = !empty($d[4]) ? $d[4] : $emptySkin; // Skin Data
				        $this->putString($skinData);
			    	}
				}
				
				break;
			case self::TYPE_REMOVE:
				foreach ($this->entries as $d) {
					$this->putUUID($d[0]);
				}
				
				break;
		}
	}
}