<?php

namespace pocketmine\utils;

use pocketmine\entity\Entity;
use pocketmine\network\protocol\Info;

class MetadataConvertor {

	private static $initialMeta = [];
	
	private static $diffEntityFlags110 = [
/*    	'DATA_FLAG_ONFIRE' => 0,
	    'DATA_FLAG_SNEAKING' => 1,
    	'DATA_FLAG_RIDING' => 2,
    	'DATA_FLAG_SPRINTING' => 3,
    	'DATA_FLAG_ACTION' => 4,
    	'DATA_FLAG_INVISIBLE' => 5,
	    'DATA_FLAG_TEMPTED' => 6,
	    'DATA_FLAG_INLOVE' => 7,
	    'DATA_FLAG_SADDLED' => 8,
	    'DATA_FLAG_POWERED' => 9,
	    'DATA_FLAG_IGNITED' => 10,
	    'DATA_FLAG_BABY' => 11,
	    'DATA_FLAG_CONVERTING' => 12,
	    'DATA_FLAG_CRITICAL' => 13,
	    'DATA_FLAG_CAN_SHOW_NAMETAG' => 14,
	    'DATA_FLAG_ALWAYS_SHOW_NAMETAG' => 15,
	    'DATA_FLAG_IMMOBILE' => 16,
	    'DATA_FLAG_NO_AI' => 16,
	    'DATA_FLAG_SILENT' => 17,
	    'DATA_FLAG_WALLCLIMBING' => 18,
	    'DATA_FLAG_CAN_CLIMB' => 19,
	    'DATA_FLAG_SWIMMER' => 20,
	    'DATA_FLAG_CAN_FLY' => 21,*/
		'DATA_FLAG_RESTING' => 22,
		'DATA_FLAG_SITTING' => 23,
		'DATA_FLAG_ANGRY' => 24,
		'DATA_FLAG_INTERESTED' => 25,
		'DATA_FLAG_CHARGED' => 26,
		'DATA_FLAG_TAMED' => 27,
		'DATA_FLAG_LEASHED' => 28,
		'DATA_FLAG_SHEARED' => 29,
		'DATA_FLAG_GLIDING' => 30,
		'DATA_FLAG_ELDER' => 31,
		'DATA_FLAG_MOVING' => 32,		
		'DATA_FLAG_BREATHING' => 33,
		'DATA_FLAG_CHESTED' => 34,
		'DATA_FLAG_STACKABLE' => 35,
	    /*'DATA_FLAG_SHOWBASE' => 36,
	    'DATA_FLAG_REARING' => 37,
	    'DATA_FLAG_VIBRATING' => 38,
	    'DATA_FLAG_IDLING' => 39,
	    'DATA_FLAG_EVOKER_SPELL' => 40,
	    'DATA_FLAG_CHARGE_ATTACK' => 41,
	    'DATA_FLAG_LINGER' => 45,*/
	];
	private static $diffEntityFlags120 = [
/*    	'DATA_FLAG_ONFIRE' => 0,
	    'DATA_FLAG_SNEAKING' => 1,
    	'DATA_FLAG_RIDING' => 2,
    	'DATA_FLAG_SPRINTING' => 3,
    	'DATA_FLAG_ACTION' => 4,
    	'DATA_FLAG_INVISIBLE' => 5,
	    'DATA_FLAG_TEMPTED' => 6,
	    'DATA_FLAG_INLOVE' => 7,
	    'DATA_FLAG_SADDLED' => 8,
	    'DATA_FLAG_POWERED' => 9,
	    'DATA_FLAG_IGNITED' => 10,
	    'DATA_FLAG_BABY' => 11,
	    'DATA_FLAG_CONVERTING' => 12,
	    'DATA_FLAG_CRITICAL' => 13,
	    'DATA_FLAG_CAN_SHOW_NAMETAG' => 14,
	    'DATA_FLAG_ALWAYS_SHOW_NAMETAG' => 15,
	    'DATA_FLAG_IMMOBILE' => 16,
	    'DATA_FLAG_NO_AI' => 16,
	    'DATA_FLAG_SILENT' => 17,
	    'DATA_FLAG_WALLCLIMBING' => 18,
	    'DATA_FLAG_CAN_CLIMB' => 19,
	    'DATA_FLAG_SWIMMER' => 20,
	    'DATA_FLAG_CAN_FLY' => 21,*/
		'DATA_FLAG_RESTING' => 22,
		'DATA_FLAG_SITTING' => 23,
		'DATA_FLAG_ANGRY' => 24,
		'DATA_FLAG_INTERESTED' => 25,
		'DATA_FLAG_CHARGED' => 26,
		'DATA_FLAG_TAMED' => 27,
		'DATA_FLAG_LEASHED' => 28,
		'DATA_FLAG_SHEARED' => 29,
		'DATA_FLAG_GLIDING' => 30,
		'DATA_FLAG_ELDER' => 31,
		'DATA_FLAG_MOVING' => 32,		
		'DATA_FLAG_BREATHING' => 33,
		'DATA_FLAG_CHESTED' => 34,
		'DATA_FLAG_STACKABLE' => 35,
/*	    'DATA_FLAG_SHOWBASE' => 36,
	    'DATA_FLAG_REARING' => 37,
	    'DATA_FLAG_VIBRATING' => 38,
	    'DATA_FLAG_IDLING' => 39,
	    'DATA_FLAG_EVOKER_SPELL' => 40,
	    'DATA_FLAG_CHARGE_ATTACK' => 41,
	    'DATA_FLAG_LINGER' => 45,*/
	];
	private static $entityFlags110 = [];
	private static $entityFlags120 = [];
	
	private static $diffEntityMetaIds110 = [
/*	    'DATA_FLAGS' => 0,
	    'DATA_HEALTH' => 1,
	    'DATA_VARIANT' => 2,
	    'DATA_COLOR' => 3,
	    'DATA_COLOUR' => 3,
	    'DATA_NAMETAG' => 4,
	    'DATA_OWNER_EID' => 5,
	    'DATA_TARGET_EID' => 6,
	    'DATA_AIR' => 7,
	    'DATA_POTION_COLOR' => 8,
	    'DATA_POTION_AMBIENT' => 9,
	    'DATA_HURT_TIME' => 11,
	    'DATA_HURT_DIRECTION' => 12,
	    'DATA_PADDLE_TIME_LEFT' => 13,
	    'DATA_PADDLE_TIME_RIGHT' => 14,
	    'DATA_EXPERIENCE_VALUE' => 15,
	    'DATA_MINECART_DISPLAY_BLOCK' => 16,
	    'DATA_MINECART_DISPLAY_OFFSET' => 17,
	    'DATA_MINECART_HAS_DISPLAY' => 18,
	    'DATA_ENDERMAN_HELD_ITEM_ID' => 23,
	    'DATA_ENDERMAN_HELD_ITEM_DAMAGE' => 24,
	    'DATA_ENTITY_AGE' => 25,
	    'DATA_FIREBALL_POWER_X' => 30,
	    'DATA_FIREBALL_POWER_Y' => 31,
	    'DATA_FIREBALL_POWER_Z' => 32,
	    'DATA_POTION_AUX_VALUE' => 37,
	    'DATA_LEAD_HOLDER_EID' => 38,
	    'DATA_SCALE' => 39,
	    'DATA_INTERACTIVE_TAG' => 40,
	    'DATA_NPC_SKIN_ID' => 41,
	    'DATA_URL_TAG' => 42,*/
/*	    'DATA_MAX_AIR' => 43,
	    'DATA_MARK_VARIANT' => 44,
	    'DATA_BLOCK_TARGET' => 48,
	    'DATA_WITHER_INVULNERABLE_TICKS' => 49,
	    'DATA_WITHER_TARGET_1' => 50,
	    'DATA_WITHER_TARGET_2' => 51,
	    'DATA_WITHER_TARGET_3' => 52,
	    'DATA_BOUNDING_BOX_WIDTH' => 54,
	    'DATA_BOUNDING_BOX_HEIGHT' => 55,
	    'DATA_FUSE_LENGTH' => 56,
	    'DATA_RIDER_SEAT_POSITION' => 57,
	    'DATA_RIDER_ROTATION_LOCKED' => 58,
	    'DATA_RIDER_MAX_ROTATION' => 59,
	    'DATA_RIDER_MIN_ROTATION' => 60,
	    'DATA_AREA_EFFECT_CLOUD_RADIUS' => 61,
	    'DATA_AREA_EFFECT_CLOUD_WAITING' => 62,
	    'DATA_AREA_EFFECT_CLOUD_PARTICLE_ID' => 63,
	    'DATA_SHULKER_ATTACH_FACE' => 65,
	    'DATA_SHULKER_ATTACH_POS' => 67,
	    'DATA_TRADING_PLAYER_EID' => 68,
	    'DATA_COMMAND_BLOCK_COMMAND' => 71,
	    'DATA_COMMAND_BLOCK_LAST_OUTPUT' => 72,
	    'DATA_COMMAND_BLOCK_TRACK_OUTPUT' => 73,
	    'DATA_CONTROLLING_RIDER_SEAT_NUMBER' => 74,
	    'DATA_STRENGTH' => 75,
	    'DATA_MAX_STRENGTH' => 76,
	    'DATA_ARMOR_STAND_POSE_INDEX' => 78,
	    'DATA_ENDER_CRYSTAL_TIME_OFFSET' => 79,
	    'DATA_FLAGS2' => 91,*/
	];
	private static $diffEntityMetaIds120 = [
/*	    'DATA_FLAGS' => 0,
	    'DATA_HEALTH' => 1,
	    'DATA_VARIANT' => 2,
	    'DATA_COLOR' => 3,
	    'DATA_COLOUR' => 3,
	    'DATA_NAMETAG' => 4,
	    'DATA_OWNER_EID' => 5,
	    'DATA_TARGET_EID' => 6,
	    'DATA_AIR' => 7,
	    'DATA_POTION_COLOR' => 8,
	    'DATA_POTION_AMBIENT' => 9,
	    'DATA_HURT_TIME' => 11,
	    'DATA_HURT_DIRECTION' => 12,
	    'DATA_PADDLE_TIME_LEFT' => 13,
	    'DATA_PADDLE_TIME_RIGHT' => 14,
	    'DATA_EXPERIENCE_VALUE' => 15,
	    'DATA_MINECART_DISPLAY_BLOCK' => 16,
	    'DATA_MINECART_DISPLAY_OFFSET' => 17,
	    'DATA_MINECART_HAS_DISPLAY' => 18,
	    'DATA_ENDERMAN_HELD_ITEM_ID' => 23,
	    'DATA_ENDERMAN_HELD_ITEM_DAMAGE' => 24,
	    'DATA_ENTITY_AGE' => 25,
	    'DATA_FIREBALL_POWER_X' => 30,
	    'DATA_FIREBALL_POWER_Y' => 31,
	    'DATA_FIREBALL_POWER_Z' => 32,
	    'DATA_POTION_AUX_VALUE' => 37,
	    'DATA_LEAD_HOLDER_EID' => 38,
	    'DATA_SCALE' => 39,
	    'DATA_INTERACTIVE_TAG' => 40,
	    'DATA_NPC_SKIN_ID' => 41,
	    'DATA_URL_TAG' => 42,*/
	    'DATA_MAX_AIR' => 43,
/*	    'DATA_MARK_VARIANT' => 44,
	    'DATA_BLOCK_TARGET' => 48,
	    'DATA_WITHER_INVULNERABLE_TICKS' => 49,
	    'DATA_WITHER_TARGET_1' => 50,
	    'DATA_WITHER_TARGET_2' => 51,
	    'DATA_WITHER_TARGET_3' => 52,
	    'DATA_BOUNDING_BOX_WIDTH' => 54,
	    'DATA_BOUNDING_BOX_HEIGHT' => 55,
	    'DATA_FUSE_LENGTH' => 56,
	    'DATA_RIDER_SEAT_POSITION' => 57,
	    'DATA_RIDER_ROTATION_LOCKED' => 58,
	    'DATA_RIDER_MAX_ROTATION' => 59,
	    'DATA_RIDER_MIN_ROTATION' => 60,
	    'DATA_AREA_EFFECT_CLOUD_RADIUS' => 61,
	    'DATA_AREA_EFFECT_CLOUD_WAITING' => 62,
	    'DATA_AREA_EFFECT_CLOUD_PARTICLE_ID' => 63,
	    'DATA_SHULKER_ATTACH_FACE' => 65,
	    'DATA_SHULKER_ATTACH_POS' => 67,
	    'DATA_TRADING_PLAYER_EID' => 68,
	    'DATA_COMMAND_BLOCK_COMMAND' => 71,
	    'DATA_COMMAND_BLOCK_LAST_OUTPUT' => 72,
	    'DATA_COMMAND_BLOCK_TRACK_OUTPUT' => 73,
	    'DATA_CONTROLLING_RIDER_SEAT_NUMBER' => 74,
	    'DATA_STRENGTH' => 75,
	    'DATA_MAX_STRENGTH' => 76,
	    'DATA_ARMOR_STAND_POSE_INDEX' => 78,
	    'DATA_ENDER_CRYSTAL_TIME_OFFSET' => 79,
	    'DATA_FLAGS2' => 91,*/
	];
	
	private static $entityMetaIds110 = [];
	private static $entityMetaIds120 = [];

	public static function init() {
		$oClass = new \ReflectionClass('pocketmine\entity\Entity');
		self::$initialMeta = $oClass->getConstants();

		foreach (self::$diffEntityFlags110 as $key => $value) {
			if (isset(self::$initialMeta[$key])) {
				self::$entityFlags110[self::$initialMeta[$key]] = $value;
			}
		}

		foreach (self::$diffEntityFlags120 as $key => $value) {
			if (isset(self::$initialMeta[$key])) {
				self::$entityFlags120[self::$initialMeta[$key]] = $value;
			}
		}
		
		foreach (self::$diffEntityMetaIds110 as $key => $value) {
			if (isset(self::$initialMeta[$key])) {
				self::$entityMetaIds110[self::$initialMeta[$key]] = $value;
			}
		}
		
		foreach (self::$diffEntityMetaIds120 as $key => $value) {
			if (isset(self::$initialMeta[$key])) {
				self::$entityMetaIds120[self::$initialMeta[$key]] = $value;
			}
		}
	}

	public static function updateMeta($meta, $protocol) {
		$meta = self::updateEntityFlags($meta, $protocol);
		$meta = self::updateMetaIds($meta, $protocol);
		return $meta;
	}

	private static function updateMetaIds($meta, $protocol) {
		switch ($protocol) {
			case Info::PROTOCOL_120:
				$protocolMeta = self::$entityMetaIds120;
				break;
			case Info::PROTOCOL_110:
				$protocolMeta = self::$entityMetaIds110;
				break;
			default:
				return $meta;
		}
		$newMeta = [];
		foreach ($meta as $key => $value) {
			if (isset($protocolMeta[$key])) {
				$newMeta[$protocolMeta[$key]] = $value;
			} else {
				$newMeta[$key] = $value;
			}
		}
		return $newMeta;
	}

	private static function updateEntityFlags($meta, $protocol) {
		if (!isset($meta[Entity::DATA_FLAGS])) {
			return $meta;
		}
		switch ($protocol) {
			case Info::PROTOCOL_120:
				$newflags = 1 << 19; //DATA_FLAG_CAN_CLIMBING
				$protocolFlags = self::$entityFlags120;
				break;
			case Info::PROTOCOL_110:
				$newflags = 1 << 19; //DATA_FLAG_CAN_CLIMBING
				$protocolFlags = self::$entityFlags110;
				break;
			default:
				return $meta;
		}
		
		$flags = strrev(decbin($meta[Entity::DATA_FLAGS][1]));
		$flagsLength = strlen($flags);
		for ($i = 0; $i < $flagsLength; $i++) {
			if ($flags[$i] === '1') {
				$newflags |= 1 << (isset($protocolFlags[$i]) ? $protocolFlags[$i] : $i);
			}
		}
		$meta[Entity::DATA_FLAGS][1] = $newflags;
		return $meta;
	}

}
