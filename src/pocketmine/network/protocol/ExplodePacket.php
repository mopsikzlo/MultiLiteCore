<?php

namespace pocketmine\network\protocol;

class ExplodePacket extends PEPacket{
	const NETWORK_ID = Info::EXPLODE_PACKET;
	const PACKET_NAME = "EXPLODE_PACKET";

	public $x;
	public $y;
	public $z;
	public $radius;
	public $records = [];

	/**
	 * @return $this
	 */
	public function clean(){
		$this->records = [];
		
		return parent::clean();
	}

	public function decode($playerProtocol){}

	public function encode($playerProtocol){
		$this->reset($playerProtocol);
		$this->putVector3f($this->x, $this->y, $this->z);
		$this->putLFloat($this->radius);
		$this->putUnsignedVarInt(count($this->records));
		if(count($this->records) > 0){
			foreach($this->records as $record){
				$this->putBlockCoords((int) $record->x, (int) $record->y, (int) $record->z);
			}
		}
	}
}