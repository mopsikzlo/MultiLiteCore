<?php

namespace pocketmine\network\protocol;

class ClientToServerHandshakePacket extends PEPacket {
    
	const NETWORK_ID = Info::CLIENT_TO_SERVER_HANDSHAKE_PACKET;
	const PACKET_NAME = "CLIENT_TO_SERVER_HANDSHAKE_PACKET";
	
	public function canBeSentBeforeLogin() {
		return true;
	}

	public function decode($playerProtocol){}
	
	public function encode($playerProtocol){}
}