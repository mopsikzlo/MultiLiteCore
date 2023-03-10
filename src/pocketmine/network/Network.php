<?php

/*
 *
 *  ____            _        _   __  __ _                  __  __ ____
 * |  _ \ ___   ___| | _____| |_|  \/  (_)_ __   ___      |  \/  |  _ \
 * | |_) / _ \ / __| |/ / _ \ __| |\/| | | '_ \ / _ \_____| |\/| | |_) |
 * |  __/ (_) | (__|   <  __/ |_| |  | | | | | |  __/_____| |  | |  __/
 * |_|   \___/ \___|_|\_\___|\__|_|  |_|_|_| |_|\___|     |_|  |_|_|
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * @author PocketMine Team
 * @link http://www.pocketmine.net/
 *
 *
*/

/**
 * Network-related classes
 */

namespace pocketmine\network;

use pocketmine\network\protocol\AddEntityPacket;
use pocketmine\network\protocol\AddItemEntityPacket;
use pocketmine\network\protocol\AddPaintingPacket;
use pocketmine\network\protocol\AddPlayerPacket;
use pocketmine\network\protocol\AdventureSettingsPacket;
use pocketmine\network\protocol\AnimatePacket;
use pocketmine\network\protocol\ContainerClosePacket;
use pocketmine\network\protocol\ContainerOpenPacket;
use pocketmine\network\protocol\ContainerSetContentPacket;
use pocketmine\network\protocol\ContainerSetDataPacket;
use pocketmine\network\protocol\ContainerSetSlotPacket;
use pocketmine\network\protocol\CraftingDataPacket;
use pocketmine\network\protocol\CraftingEventPacket;
use pocketmine\network\protocol\DataPacket;
use pocketmine\network\protocol\DropItemPacket;
use pocketmine\network\protocol\FullChunkDataPacket;
use pocketmine\network\protocol\Info;
use pocketmine\network\protocol\{MapInfoRequestPacket, MapItemDataPacket, ChangeDimensionPacket};
use pocketmine\network\protocol\SetEntityLinkPacket;
use pocketmine\network\protocol\SpawnExperienceOrbPacket;
use pocketmine\network\protocol\TileEntityDataPacket;
use pocketmine\network\protocol\EntityEventPacket;
use pocketmine\network\protocol\ExplodePacket;
use pocketmine\network\protocol\HurtArmorPacket;
use pocketmine\network\protocol\Info110 as ProtocolInfo110;
use pocketmine\network\protocol\Info120 as ProtocolInfo120;
use pocketmine\network\protocol\InteractPacket;
use pocketmine\network\protocol\LevelEventPacket;
use pocketmine\network\protocol\LevelSoundEventPacket;
use pocketmine\network\protocol\DisconnectPacket;
use pocketmine\network\protocol\LoginPacket;
use pocketmine\network\protocol\PlayStatusPacket;
use pocketmine\network\protocol\PlaySoundPacket;
use pocketmine\network\protocol\TextPacket;
use pocketmine\network\protocol\MoveEntityPacket;
use pocketmine\network\protocol\MovePlayerPacket;
use pocketmine\network\protocol\PlayerActionPacket;
use pocketmine\network\protocol\MobArmorEquipmentPacket;
use pocketmine\network\protocol\MobEquipmentPacket;
use pocketmine\network\protocol\RemoveBlockPacket;
use pocketmine\network\protocol\RemoveEntityPacket;
use pocketmine\network\protocol\RespawnPacket;
use pocketmine\network\protocol\SetDifficultyPacket;
use pocketmine\network\protocol\SetEntityDataPacket;
use pocketmine\network\protocol\SetEntityMotionPacket;
use pocketmine\network\protocol\SetSpawnPositionPacket;
use pocketmine\network\protocol\SetTimePacket;
use pocketmine\network\protocol\StartGamePacket;
use pocketmine\network\protocol\TakeItemEntityPacket;
use pocketmine\network\protocol\TileEventPacket;
use pocketmine\network\protocol\TransferPacket;
use pocketmine\network\protocol\UpdateBlockPacket;
use pocketmine\network\protocol\UseItemPacket;
use pocketmine\network\protocol\PlayerListPacket;
use pocketmine\network\protocol\ClientToServerHandshakePacket;
use pocketmine\Player;
use pocketmine\Server;
use pocketmine\utils\{MainLogger, BinaryStream};
use pocketmine\network\protocol\ItemFrameDropItemPacket;
use pocketmine\network\protocol\ChunkRadiusUpdatePacket;
use pocketmine\network\protocol\RequestChunkRadiusPacket;
use pocketmine\network\protocol\SetCommandsEnabledPacket;
use pocketmine\network\protocol\AvailableCommandsPacket;
use pocketmine\network\protocol\CommandStepPacket;
use pocketmine\network\protocol\ResourcePackDataInfoPacket;
use pocketmine\network\protocol\ResourcePacksInfoPacket;
use pocketmine\network\protocol\ResourcePackClientResponsePacket;
use pocketmine\network\protocol\ResourcePackChunkRequestPacket;
use pocketmine\network\protocol\PlayerInputPacket;
use pocketmine\network\protocol\v120\CommandRequestPacket;
use pocketmine\network\protocol\v120\InventoryContentPacket;
use pocketmine\network\protocol\v120\InventoryTransactionPacket;
use pocketmine\network\protocol\v120\ModalFormResponsePacket;
use pocketmine\network\protocol\v120\PlayerHotbarPacket;
use pocketmine\network\protocol\v120\PurchaseReceiptPacket;
use pocketmine\network\protocol\v120\ServerSettingsRequestPacket;
use pocketmine\network\protocol\v120\SubClientLoginPacket;
use pocketmine\network\protocol\v120\BossEventPacket;
use pocketmine\network\protocol\v120\PlayerSkinPacket;

class Network {

	public static $BATCH_THRESHOLD = 512;

	/** @var \SplFixedArray */
	private $packetPool110;
	/** @var \SplFixedArray */
	private $packetPool120;

	/** @var Server */
	private $server;

	/** @var SourceInterface[] */
	private $interfaces = [];

	/** @var AdvancedSourceInterface[] */
	private $advancedInterfaces = [];

	private $upload = 0;
	private $download = 0;

	private $name;

	/**
	 * Network constructor.
	 *
	 * @param Server $server
	 */
	public function __construct(Server $server){

		$this->registerPackets110();
		$this->registerPackets120();

		$this->server = $server;
	}

	/**
	 * @param $upload
	 * @param $download
	 */
	public function addStatistics($upload, $download){
		$this->upload += $upload;
		$this->download += $download;
	}

	/**
	 * @return int
	 */
	public function getUpload(){
		return $this->upload;
	}

	/**
	 * @return int
	 */
	public function getDownload(){
		return $this->download;
	}

	public function resetStatistics(){
		$this->upload = 0;
		$this->download = 0;
	}

	/**
	 * @return SourceInterface[]
	 */
	public function getInterfaces(){
		return $this->interfaces;
	}

	public function processInterfaces(){
		foreach($this->interfaces as $interface){
			try{
				$interface->process();
			}catch(\Throwable $e){
				$logger = $this->server->getLogger();
				if(\pocketmine\DEBUG > 1){
					if($logger instanceof MainLogger){
						$logger->logException($e);
					}
				}

				$interface->emergencyShutdown();
				$this->unregisterInterface($interface);
				$logger->critical($this->server->getLanguage()->translateString("pocketmine.server.networkError", [get_class($interface), $e->getMessage()]));
			}
		}
	}

	/**
	 * @param SourceInterface $interface
	 */
	public function registerInterface(SourceInterface $interface){
		$this->interfaces[$hash = spl_object_hash($interface)] = $interface;
		if($interface instanceof AdvancedSourceInterface){
			$this->advancedInterfaces[$hash] = $interface;
			$interface->setNetwork($this);
		}
		$interface->setName($this->name);
	}

	/**
	 * @param SourceInterface $interface
	 */
	public function unregisterInterface(SourceInterface $interface){
		unset($this->interfaces[$hash = spl_object_hash($interface)],
			$this->advancedInterfaces[$hash]);
	}

	/**
	 * Sets the server name shown on each interface Query
	 *
	 * @param string $name
	 */
	public function setName($name){
		$this->name = (string) $name;
		foreach($this->interfaces as $interface){
			$interface->setName($this->name);
		}
	}

	public function getName(){
		return $this->name;
	}

	public function updateName(){
		foreach($this->interfaces as $interface){
			$interface->setName($this->name);
		}
	}

	/**
	 * @param int        $id 0-255
	 * @param DataPacket $class
	 */
	public function registerPacket110($id, $class){
		$this->packetPool110[$id] = new $class;
	}
	
	/**
	 * @param int        $id 0-255
	 * @param DataPacket $class
	 */
	public function registerPacket120($id, $class){
		$this->packetPool120[$id] = new $class;
	}

	/**
	 * @return Server
	 */
	public function getServer(){
		return $this->server;
	}

	/**
	 * @param $id
	 *
	 * @return DataPacket
	 */
	public function getPacket($id, $playerProtocol){
		/** @var DataPacket $class */
		switch ($playerProtocol) {
			case Info::PROTOCOL_120:
				$class = $this->packetPool120[$id];
				break;
			default:
				$class = $this->packetPool110[$id];
				break;
		}
		
		if($class !== null){
			return clone $class;
		}
		
		return null;
	}


	/**
	 * @param string $address
	 * @param int    $port
	 * @param string $payload
	 */
	public function sendPacket($address, $port, $payload){
		foreach($this->advancedInterfaces as $interface){
			$interface->sendRawPacket($address, $port, $payload);
		}
	}

	/**
	 * Blocks an IP address from the main interface. Setting timeout to -1 will block it forever
	 *
	 * @param string $address
	 * @param int    $timeout
	 */
	public function blockAddress($address, $timeout = 300){
		foreach($this->advancedInterfaces as $interface){
			$interface->blockAddress($address, $timeout);
		}
	}

	/**
	 * Unblocks an IP address from the main interface.
	 *
	 * @param string $address
	 */
	public function unblockAddress($address){
		foreach($this->advancedInterfaces as $interface){
			$interface->unblockAddress($address);
		}
	}

	private function registerPackets110(){
		$this->packetPool110 = new \SplFixedArray(256);
		$this->registerPacket110(ProtocolInfo110::CHANGE_DIMENSION_PACKET, ChangeDimensionPacket::class);
		$this->registerPacket110(ProtocolInfo110::CLIENTBOUND_MAP_ITEM_DATA_PACKET, MapItemDataPacket::class);
		$this->registerPacket110(ProtocolInfo110::LOGIN_PACKET, LoginPacket::class);
		$this->registerPacket110(ProtocolInfo110::PLAY_STATUS_PACKET, PlayStatusPacket::class);
		$this->registerPacket110(ProtocolInfo110::DISCONNECT_PACKET, DisconnectPacket::class);
		$this->registerPacket110(ProtocolInfo110::TEXT_PACKET, TextPacket::class);
		$this->registerPacket110(ProtocolInfo110::SET_TIME_PACKET, SetTimePacket::class);
		$this->registerPacket110(ProtocolInfo110::START_GAME_PACKET, StartGamePacket::class);
		$this->registerPacket110(ProtocolInfo110::ADD_PLAYER_PACKET, AddPlayerPacket::class);
		$this->registerPacket110(ProtocolInfo110::ADD_ENTITY_PACKET, AddEntityPacket::class);
		$this->registerPacket110(ProtocolInfo110::REMOVE_ENTITY_PACKET, RemoveEntityPacket::class);
		$this->registerPacket110(ProtocolInfo110::ADD_ITEM_ENTITY_PACKET, AddItemEntityPacket::class);
		$this->registerPacket110(ProtocolInfo110::TAKE_ITEM_ENTITY_PACKET, TakeItemEntityPacket::class);
		$this->registerPacket110(ProtocolInfo110::MOVE_ENTITY_PACKET, MoveEntityPacket::class);
		$this->registerPacket110(ProtocolInfo110::MOVE_PLAYER_PACKET, MovePlayerPacket::class);
		$this->registerPacket110(ProtocolInfo110::REMOVE_BLOCK_PACKET, RemoveBlockPacket::class);
		$this->registerPacket110(ProtocolInfo110::UPDATE_BLOCK_PACKET, UpdateBlockPacket::class);
		$this->registerPacket110(ProtocolInfo110::ADD_PAINTING_PACKET, AddPaintingPacket::class);
		$this->registerPacket110(ProtocolInfo110::EXPLODE_PACKET, ExplodePacket::class);
		$this->registerPacket110(ProtocolInfo110::LEVEL_EVENT_PACKET, LevelEventPacket::class);
		$this->registerPacket110(ProtocolInfo110::LEVEL_SOUND_EVENT_PACKET, LevelSoundEventPacket::class);
		$this->registerPacket110(ProtocolInfo110::TILE_EVENT_PACKET, TileEventPacket::class);
		$this->registerPacket110(ProtocolInfo110::ENTITY_EVENT_PACKET, EntityEventPacket::class);
		$this->registerPacket110(ProtocolInfo110::MOB_EQUIPMENT_PACKET, MobEquipmentPacket::class);
		$this->registerPacket110(ProtocolInfo110::MOB_ARMOR_EQUIPMENT_PACKET, MobArmorEquipmentPacket::class);
		$this->registerPacket110(ProtocolInfo110::INTERACT_PACKET, InteractPacket::class);
		$this->registerPacket110(ProtocolInfo110::USE_ITEM_PACKET, UseItemPacket::class);
		$this->registerPacket110(ProtocolInfo110::PLAYER_ACTION_PACKET, PlayerActionPacket::class);
		$this->registerPacket110(ProtocolInfo110::HURT_ARMOR_PACKET, HurtArmorPacket::class);
		$this->registerPacket110(ProtocolInfo110::SET_ENTITY_DATA_PACKET, SetEntityDataPacket::class);
		$this->registerPacket110(ProtocolInfo110::SET_ENTITY_MOTION_PACKET, SetEntityMotionPacket::class);
		$this->registerPacket110(ProtocolInfo110::SET_ENTITY_LINK_PACKET, SetEntityLinkPacket::class);
		$this->registerPacket110(ProtocolInfo110::SET_SPAWN_POSITION_PACKET, SetSpawnPositionPacket::class);
		$this->registerPacket110(ProtocolInfo110::ANIMATE_PACKET, AnimatePacket::class);
		$this->registerPacket110(ProtocolInfo110::RESPAWN_PACKET, RespawnPacket::class);
		$this->registerPacket110(ProtocolInfo110::DROP_ITEM_PACKET, DropItemPacket::class);
		$this->registerPacket110(ProtocolInfo110::CONTAINER_OPEN_PACKET, ContainerOpenPacket::class);
		$this->registerPacket110(ProtocolInfo110::CONTAINER_CLOSE_PACKET, ContainerClosePacket::class);
		$this->registerPacket110(ProtocolInfo110::CONTAINER_SET_SLOT_PACKET, ContainerSetSlotPacket::class);
		$this->registerPacket110(ProtocolInfo110::CONTAINER_SET_DATA_PACKET, ContainerSetDataPacket::class);
		$this->registerPacket110(ProtocolInfo110::CONTAINER_SET_CONTENT_PACKET, ContainerSetContentPacket::class);
		$this->registerPacket110(ProtocolInfo110::CRAFTING_DATA_PACKET, CraftingDataPacket::class);
		$this->registerPacket110(ProtocolInfo110::CRAFTING_EVENT_PACKET, CraftingEventPacket::class);
		$this->registerPacket110(ProtocolInfo110::ADVENTURE_SETTINGS_PACKET, AdventureSettingsPacket::class);
		$this->registerPacket110(ProtocolInfo110::TILE_ENTITY_DATA_PACKET, TileEntityDataPacket::class);
		$this->registerPacket110(ProtocolInfo110::FULL_CHUNK_DATA_PACKET, FullChunkDataPacket::class);
		$this->registerPacket110(ProtocolInfo110::SET_COMMANDS_ENABLED_PACKET, SetCommandsEnabledPacket::class);
		$this->registerPacket110(ProtocolInfo110::SET_DIFFICULTY_PACKET, SetDifficultyPacket::class);
		$this->registerPacket110(ProtocolInfo110::PLAYER_LIST_PACKET, PlayerListPacket::class);
		$this->registerPacket110(ProtocolInfo110::REQUEST_CHUNK_RADIUS_PACKET, RequestChunkRadiusPacket::class);
		$this->registerPacket110(ProtocolInfo110::CHUNK_RADIUS_UPDATE_PACKET, ChunkRadiusUpdatePacket::class);
		$this->registerPacket110(ProtocolInfo110::AVAILABLE_COMMANDS_PACKET, AvailableCommandsPacket::class);
		$this->registerPacket110(ProtocolInfo110::COMMAND_STEP_PACKET, CommandStepPacket::class);
		$this->registerPacket110(ProtocolInfo110::TRANSFER_PACKET, TransferPacket::class);
		$this->registerPacket110(ProtocolInfo110::CLIENT_TO_SERVER_HANDSHAKE_PACKET, ClientToServerHandshakePacket::class);
		$this->registerPacket110(ProtocolInfo110::RESOURCE_PACK_DATA_INFO_PACKET, ResourcePackDataInfoPacket::class);
		$this->registerPacket110(ProtocolInfo110::RESOURCE_PACKS_INFO_PACKET, ResourcePacksInfoPacket::class);
		$this->registerPacket110(ProtocolInfo110::RESOURCE_PACKS_CLIENT_RESPONSE_PACKET, ResourcePackClientResponsePacket::class);
		$this->registerPacket110(ProtocolInfo110::RESOURCE_PACK_CHUNK_REQUEST_PACKET, ResourcePackChunkRequestPacket::class);
		$this->registerPacket110(ProtocolInfo110::PLAYER_INPUT_PACKET, PlayerInputPacket::class);
    	$this->registerPacket110(ProtocolInfo110::PLAY_SOUND_PACKET, PlaySoundPacket::class);
    	$this->registerPacket110(ProtocolInfo110::ITEM_FRAME_DROP_ITEM_PACKET, ItemFrameDropItemPacket::class);
    	$this->registerPacket110(ProtocolInfo110::BOSS_EVENT_PACKET, BossEventPacket::class);
		$this->registerPacket110(ProtocolInfo110::MAP_INFO_REQUEST_PACKET, MapInfoRequestPacket::class);
		$this->registerPacket110(ProtocolInfo110::SPAWN_EXPERIENCE_ORB_PACKET, SpawnExperienceOrbPacket::class);
	}
	
	private function registerPackets120() {
		$this->packetPool120 = new \SplFixedArray(256);
		$this->registerPacket120(ProtocolInfo120::SPAWN_EXPERIENCE_ORB_PACKET, SpawnExperienceOrbPacket::class);
		$this->registerPacket120(ProtocolInfo120::CHANGE_DIMENSION_PACKET, ChangeDimensionPacket::class);
		$this->registerPacket120(ProtocolInfo120::CLIENTBOUND_MAP_ITEM_DATA_PACKET, MapItemDataPacket::class);
		$this->registerPacket120(ProtocolInfo120::PLAY_STATUS_PACKET, PlayStatusPacket::class);
		$this->registerPacket120(ProtocolInfo120::DISCONNECT_PACKET, DisconnectPacket::class);
		$this->registerPacket120(ProtocolInfo120::TEXT_PACKET, TextPacket::class);
		$this->registerPacket120(ProtocolInfo120::SET_TIME_PACKET, SetTimePacket::class);
		$this->registerPacket120(ProtocolInfo120::START_GAME_PACKET, StartGamePacket::class);
		$this->registerPacket120(ProtocolInfo120::ADD_PLAYER_PACKET, AddPlayerPacket::class);
		$this->registerPacket120(ProtocolInfo120::ADD_ENTITY_PACKET, AddEntityPacket::class);
		$this->registerPacket120(ProtocolInfo120::REMOVE_ENTITY_PACKET, RemoveEntityPacket::class);
		$this->registerPacket120(ProtocolInfo120::ADD_ITEM_ENTITY_PACKET, AddItemEntityPacket::class);
		$this->registerPacket120(ProtocolInfo120::TAKE_ITEM_ENTITY_PACKET, TakeItemEntityPacket::class);
		$this->registerPacket120(ProtocolInfo120::MOVE_ENTITY_PACKET, MoveEntityPacket::class);
		$this->registerPacket120(ProtocolInfo120::MOVE_PLAYER_PACKET, MovePlayerPacket::class);
		$this->registerPacket120(ProtocolInfo120::UPDATE_BLOCK_PACKET, UpdateBlockPacket::class);
		$this->registerPacket120(ProtocolInfo120::ADD_PAINTING_PACKET, AddPaintingPacket::class);
		$this->registerPacket120(ProtocolInfo120::EXPLODE_PACKET, ExplodePacket::class);
		$this->registerPacket120(ProtocolInfo120::LEVEL_EVENT_PACKET, LevelEventPacket::class);
		$this->registerPacket120(ProtocolInfo120::LEVEL_SOUND_EVENT_PACKET, LevelSoundEventPacket::class);
		$this->registerPacket120(ProtocolInfo120::TILE_EVENT_PACKET, TileEventPacket::class);
		$this->registerPacket120(ProtocolInfo120::ENTITY_EVENT_PACKET, EntityEventPacket::class);
		$this->registerPacket120(ProtocolInfo120::MOB_EQUIPMENT_PACKET, MobEquipmentPacket::class);
		$this->registerPacket120(ProtocolInfo120::MOB_ARMOR_EQUIPMENT_PACKET, MobArmorEquipmentPacket::class);
		$this->registerPacket120(ProtocolInfo120::INTERACT_PACKET, InteractPacket::class);
		$this->registerPacket120(ProtocolInfo120::PLAYER_ACTION_PACKET, PlayerActionPacket::class);
		$this->registerPacket120(ProtocolInfo120::HURT_ARMOR_PACKET, HurtArmorPacket::class);
		$this->registerPacket120(ProtocolInfo120::SET_ENTITY_DATA_PACKET, SetEntityDataPacket::class);
		$this->registerPacket120(ProtocolInfo120::SET_ENTITY_MOTION_PACKET, SetEntityMotionPacket::class);
		$this->registerPacket120(ProtocolInfo120::SET_ENTITY_LINK_PACKET, SetEntityLinkPacket::class);
		$this->registerPacket120(ProtocolInfo120::SET_SPAWN_POSITION_PACKET, SetSpawnPositionPacket::class);
		$this->registerPacket120(ProtocolInfo120::ANIMATE_PACKET, AnimatePacket::class);
		$this->registerPacket120(ProtocolInfo120::RESPAWN_PACKET, RespawnPacket::class);
		$this->registerPacket120(ProtocolInfo120::CONTAINER_OPEN_PACKET, ContainerOpenPacket::class);
		$this->registerPacket120(ProtocolInfo120::CONTAINER_CLOSE_PACKET, ContainerClosePacket::class);
		$this->registerPacket120(ProtocolInfo120::CONTAINER_SET_DATA_PACKET, ContainerSetDataPacket::class);
		$this->registerPacket120(ProtocolInfo120::CRAFTING_DATA_PACKET, CraftingDataPacket::class);
		$this->registerPacket120(ProtocolInfo120::CRAFTING_EVENT_PACKET, CraftingEventPacket::class);
		$this->registerPacket120(ProtocolInfo120::ADVENTURE_SETTINGS_PACKET, AdventureSettingsPacket::class);
		$this->registerPacket120(ProtocolInfo120::TILE_ENTITY_DATA_PACKET, TileEntityDataPacket::class);
		$this->registerPacket120(ProtocolInfo120::FULL_CHUNK_DATA_PACKET, FullChunkDataPacket::class);
		$this->registerPacket120(ProtocolInfo120::SET_COMMANDS_ENABLED_PACKET, SetCommandsEnabledPacket::class);
		$this->registerPacket120(ProtocolInfo120::SET_DIFFICULTY_PACKET, SetDifficultyPacket::class);
		$this->registerPacket120(ProtocolInfo120::PLAYER_LIST_PACKET, PlayerListPacket::class);
		$this->registerPacket120(ProtocolInfo120::REQUEST_CHUNK_RADIUS_PACKET, RequestChunkRadiusPacket::class);
		$this->registerPacket120(ProtocolInfo120::CHUNK_RADIUS_UPDATE_PACKET, ChunkRadiusUpdatePacket::class);
		$this->registerPacket120(ProtocolInfo120::AVAILABLE_COMMANDS_PACKET, AvailableCommandsPacket::class);
		$this->registerPacket120(ProtocolInfo120::TRANSFER_PACKET, TransferPacket::class);
		$this->registerPacket120(ProtocolInfo120::CLIENT_TO_SERVER_HANDSHAKE_PACKET, ClientToServerHandshakePacket::class);
		$this->registerPacket120(ProtocolInfo120::RESOURCE_PACK_DATA_INFO_PACKET, ResourcePackDataInfoPacket::class);
		$this->registerPacket120(ProtocolInfo120::RESOURCE_PACKS_INFO_PACKET, ResourcePackDataInfoPacket::class);
		$this->registerPacket120(ProtocolInfo120::RESOURCE_PACKS_CLIENT_RESPONSE_PACKET, ResourcePackClientResponsePacket::class);
		$this->registerPacket120(ProtocolInfo120::RESOURCE_PACK_CHUNK_REQUEST_PACKET, ResourcePackChunkRequestPacket::class);
		$this->registerPacket120(ProtocolInfo120::PLAYER_INPUT_PACKET, PlayerInputPacket::class);
		$this->registerPacket120(ProtocolInfo120::MAP_INFO_REQUEST_PACKET, MapInfoRequestPacket::class);
		// new
		$this->registerPacket120(ProtocolInfo120::INVENTORY_TRANSACTION_PACKET, InventoryTransactionPacket::class);
		$this->registerPacket120(ProtocolInfo120::INVENTORY_CONTENT_PACKET, InventoryContentPacket::class);
		$this->registerPacket120(ProtocolInfo120::PLAYER_HOTBAR_PACKET, PlayerHotbarPacket::class);
		$this->registerPacket120(ProtocolInfo120::COMMAND_REQUEST_PACKET, CommandRequestPacket::class);
		$this->registerPacket120(ProtocolInfo120::PLAYER_SKIN_PACKET, PlayerSkinPacket::class);
		$this->registerPacket120(ProtocolInfo120::MODAL_FORM_RESPONSE_PACKET, ModalFormResponsePacket::class);
		$this->registerPacket120(ProtocolInfo120::SERVER_SETTINGS_REQUEST_PACKET, ServerSettingsRequestPacket::class);
		$this->registerPacket120(ProtocolInfo120::PURCHASE_RECEIPT_PACKET, PurchaseReceiptPacket::class);
		$this->registerPacket120(ProtocolInfo120::SUB_CLIENT_LOGIN_PACKET, SubClientLoginPacket::class);		
    	$this->registerPacket120(ProtocolInfo120::PLAY_SOUND_PACKET, PlaySoundPacket::class);
    	$this->registerPacket120(ProtocolInfo120::ITEM_FRAME_DROP_ITEM_PACKET, ItemFrameDropItemPacket::class);
    	$this->registerPacket120(ProtocolInfo120::BOSS_EVENT_PACKET, BossEventPacket::class);
	}
}