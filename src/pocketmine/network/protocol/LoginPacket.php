<?php

namespace pocketmine\network\protocol;

use pocketmine\utils\{BinaryStream, UUID, Binary, Utils};

class LoginPacket extends PEPacket {
	const NETWORK_ID = Info::LOGIN_PACKET;
	const PACKET_NAME = "LOGIN_PACKET";

	public $username; // Player nickname
	
	public $protocol1; // Protocol
	public $originalProtocol; // Protocol (original)
	public $isValidProtocol = true; // valid protocol
	
	/* 1.1 */
	public $gameEdition; // Unknown
	/* END */
	
	public $clientUUID; // UUID
	public $clientId; // CID (client ID)
	public $identityPublicKey; // public key
	public $serverAddress; // Server IP

    /* 1.2 */
	public $skin = ""; // SkinData
	public $skinId = ""; // SkinName or SkinId
	public $skinGeometryName = ""; // geometryName
	public $skinGeometryData = ""; // geometryData
	public $capeData = ""; // capeData
	
	public $xuid; // Xbox
	public $clientVersion; // MC Version
	public $platformChatId; // Unknown
	/* END */

	public $deviceModel; // Device
	public $deviceOS; // Device OS
	
	public $chainData = []; // clientData
	public $clientDataJwt; // clientData
	public $clientData = []; // clientData

	public function decode($playerProtocol){
	    $acceptedProtocols = Info::ACCEPTED_PROTOCOLS;
	    
		// header: protocolID, Subclient Sender, Subclient Receiver
		$this->getUnsignedVarInt(); // header: 1 byte for protocol < 280, 1-2 for 280
		$tmpData = Binary::readInt(substr($this->getBuffer(), $this->getOffset(), 4));
		if ($tmpData == 0) {
			$this->getShort();
		}
		
		$this->protocol1 = $this->getInt();
		if (!in_array($this->protocol1, $acceptedProtocols)) {
			$this->isValidProtocol = false;
			return;
		}
		
		if ($this->protocol1 < Info::PROTOCOL_120) {
			$this->gameEdition = $this->getByte();
		}
		
		$buffer = new BinaryStream($this->getString());
		$this->chainData = json_decode($buffer->get($buffer->getLInt()), true);

		$hasExtraData = false;
		
		foreach($this->chainData["chain"] as $chain){
			$webtoken = Utils::decodeJWT($chain);
			
			if(isset($webtoken["extraData"])){
				if($hasExtraData){
					throw new \Exception("Found 'extraData' multiple times in key chain");
				}
				
				$hasExtraData = true;
				
				if(isset($webtoken["extraData"]["displayName"])){
					$this->username = $webtoken["extraData"]["displayName"];
				}
				
				if(isset($webtoken["extraData"]["identity"])){
			    	$this->clientUUID = UUID::fromString($webtoken["extraData"]["identity"]);
				}
				
				$this->xuid = $webtoken["extraData"]["XUID"] ?? '';
			}
			
			$this->identityPublicKey = $webtoken["identityPublicKey"] ?? "";
		}

		$this->clientDataJwt = $buffer->get($buffer->getLInt());
		$this->clientData = Utils::decodeJWT($this->clientDataJwt);

		$this->clientId = $this->clientData["ClientRandomId"] ?? null;
		$this->serverAddress = $this->clientData["ServerAddress"] ?? null;
		$this->skinId = $this->clientData["SkinId"] ?? "CustomID";

		$this->skin = isset($this->clientData["SkinData"]) ? base64_decode($this->clientData["SkinData"]) : "";

		$this->skinGeometryName = $this->clientData['SkinGeometryName'] ?? ""; // 1.2
		
		if (isset($this->clientData['SkinGeometry'])) {
			$this->skinGeometryData = base64_decode($this->clientData['SkinGeometry']); // 1.2
		} elseif (isset($this->clientData['SkinGeometryData'])) {
			$this->skinGeometryData = base64_decode($this->clientData['SkinGeometryData']); // 1.2
			if (strpos($this->skinGeometryData, 'null') === 0) {
				$this->skinGeometryData = '';
			}
		}
		
		$this->capeData = isset($this->clientData['CapeData']) ? base64_decode($this->clientData['CapeData']) : ""; // 1.2
		
        $this->deviceOS = $this->clientData['DeviceOS'] ?? -1;
        $this->deviceModel = $this->clientData["DeviceModel"] ?? "";
		$this->clientVersion = $this->clientData['GameVersion'] ?? "unknown";
        $this->platformChatId = $this->clientData["PlatformOnlineId"] ?? "";
		
		$this->originalProtocol = $this->protocol1;
		$this->protocol1 = self::convertProtocol($this->protocol1);
	}

	public function encode($playerProtocol){}
}