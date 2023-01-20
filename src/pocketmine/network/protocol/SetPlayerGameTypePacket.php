<?php

namespace pocketmine\network\protocol;

class SetPlayerGameTypePacket extends PEPacket{
	const NETWORK_ID = Info::SET_PLAYER_GAMETYPE_PACKET;
	const PACKET_NAME = "SET_PLAYER_GAMETYPE_PACKET";

	public $gamemode;

	public function decode($playerProtocol){}

	public function encode($playerProtocol){
		$this->reset($playerProtocol);
		$this->putVarInt($this->gamemode);
	}
}