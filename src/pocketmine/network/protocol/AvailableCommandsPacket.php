<?php

namespace pocketmine\network\protocol;

use pocketmine\network\multiversion\MultiversionEnums;
use pocketmine\utils\BinaryStream;

class AvailableCommandsPacket extends PEPacket {
	const NETWORK_ID = Info::AVAILABLE_COMMANDS_PACKET;
	const PACKET_NAME = "AVAILABLE_COMMANDS_PACKET";

	static private $commandsBuffer = [];
	static private $commandsBufferDefault = "";

	public $commands;

	public function decode($playerProtocol){}

	public function encode($playerProtocol){
		$this->reset($playerProtocol);
		foreach (self::$commandsBuffer as $protocol => $data) {
			if ($playerProtocol >= $protocol) {
				$this->put($data);
				return;
			}
		}
		$this->putString(self::$commandsBufferDefault);
	}
	
	const ARG_FLAG_VALID = 0x100000;
	const ARG_FLAG_ENUM = 0x200000;

	const ARG_TYPE_INT = "ARG_TYPE_INT";
	const ARG_TYPE_FLOAT = "ARG_TYPE_FLOAT";
	const ARG_TYPE_VALUE = "ARG_TYPE_VALUE";
	const ARG_TYPE_TARGET = "ARG_TYPE_TARGET";
	const ARG_TYPE_STRING = "ARG_TYPE_STRING";
	const ARG_TYPE_POSITION = "ARG_TYPE_POSITION";
	const ARG_TYPE_RAWTEXT = "ARG_TYPE_RAWTEXT";
	const ARG_TYPE_TEXT = "ARG_TYPE_TEXT";
	const ARG_TYPE_JSON = "ARG_TYPE_JSON";
	const ARG_TYPE_COMMAND = "ARG_TYPE_COMMAND";
	
	public static function prepareCommands($commands) {
		self::$commandsBufferDefault = json_encode($commands);

		$enumValues = [];
		$enumValuesCount = 0;
		$enums = [];
		$enumsCount = 0;
		$commandsStreams = [
			Info::PROTOCOL_120 => new BinaryStream(),
		];

		foreach ($commands as $commandName => &$commandData) { // Replace &$commandData with $commandData when alises fix for 1.2 won't be needed anymore
			$commandsStream = new BinaryStream();
			$commandsStream->putString($commandName);
			$commandsStream->putString($commandData['versions'][0]['description']);
		    foreach ($commandsStreams as $protocol => $unused) {
			    /** @IMPORTANT $commandsStream doesn't should use after this line */
				$commandsStreams[$protocol]->put($commandsStream->getBuffer());
				$commandsStreams[$protocol]->putByte(0); // flags
			    $permission = AdventureSettingsPacket::COMMAND_PERMISSION_LEVEL_ANY;
			    switch ($commandData['versions'][0]['permission']) {
				    case "staff":
					    $permission = AdventureSettingsPacket::COMMAND_PERMISSION_LEVEL_GAME_MASTERS;
					    default;
			    }
			    $commandsStreams[$protocol]->putByte($permission); // permission level
			    if (isset($commandData['versions'][0]['aliases']) && !empty($commandData['versions'][0]['aliases'])) {
				    foreach ($commandData['versions'][0]['aliases'] as $alias) {
					    $aliasAsCommand = $commandData;
					    $aliasAsCommand['versions'][0]['aliases'] = [];
					    $commands[$alias] = $aliasAsCommand;
				    }
				    $commandData['versions'][0]['aliases'] = [];
			    }
			    $aliasesEnumId = -1; // temp aliases fix for 1.2
			    $commandsStreams[$protocol]->putLInt($aliasesEnumId);
			    $commandsStreams[$protocol]->putUnsignedVarInt(count($commandData['versions'][0]['overloads'])); // overloads
			}
			foreach ($commandData['versions'][0]['overloads'] as $overloadData) {
				$paramNum = count($overloadData['input']['parameters']);
				foreach ($commandsStreams as $protocol => $unused) {
					$commandsStreams[$protocol]->putUnsignedVarInt($paramNum);
				}
				foreach ($overloadData['input']['parameters'] as $paramData) {
					// rawtext type cause problems on some types of clients
					$isParamOneAndOptional = ($paramNum == 1 && isset($paramData['optional']) && $paramData['optional']);
					if ($paramData['type'] == "rawtext" && ($paramNum > 1 || $isParamOneAndOptional)) {
						$paramData['type'] = "string";
					}
					if ($paramData['type'] == "stringenum") {
						 $enums[$enumsCount]['name'] = $paramData['name'];
						 $enums[$enumsCount]['data'] = [];
						 foreach ($paramData['enum_values'] as $enumElem) {
							 $enumValues[$enumValuesCount] = $enumElem;
							 $enums[$enumsCount]['data'][] = $enumValuesCount;
							 $enumValuesCount++;
						 }
						 $enumsCount++;
                    }
					foreach ($commandsStreams as $protocol => $unused) {
						$commandsStreams[$protocol]->putString($paramData['name']);
						 if ($paramData['type'] == "stringenum") {
                            $commandsStreams[$protocol]->putLInt(self::ARG_FLAG_VALID | self::ARG_FLAG_ENUM | ($enumsCount - 1));
                        } else {
							$commandsStreams[$protocol]->putLInt(self::ARG_FLAG_VALID | self::getFlag($paramData['type'], $protocol));
                        }
						$commandsStreams[$protocol]->putByte(isset($paramData['optional']) && $paramData['optional']);
					}
				}
			}
		}

		$additionalDataStream = new BinaryStream();
		$additionalDataStream->putUnsignedVarInt($enumValuesCount);
		for ($i = 0; $i < $enumValuesCount; $i++) {
			$additionalDataStream->putString($enumValues[$i]);
		}
		$additionalDataStream->putUnsignedVarInt(0);
		$additionalDataStream->putUnsignedVarInt($enumsCount);
		for ($i = 0; $i < $enumsCount; $i++) {
			$additionalDataStream->putString($enums[$i]['name']);
			$dataCount = count($enums[$i]['data']);
			$additionalDataStream->putUnsignedVarInt($dataCount);
			for ($j = 0; $j < $dataCount; $j++) {
				if ($enumValuesCount < 256) {
					$additionalDataStream->putByte($enums[$i]['data'][$j]);
				} else if ($enumValuesCount < 65536) {
					$additionalDataStream->putLShort($enums[$i]['data'][$j]);
				} else {
					$additionalDataStream->putLInt($enums[$i]['data'][$j]);
				}
			}
		}
		$additionalDataStream->putUnsignedVarInt(count($commands));

		foreach ($commandsStreams as $protocol => $commandsStream) {
			self::$commandsBuffer[$protocol] = $additionalDataStream->getBuffer() . $commandsStream->getBuffer();
		}

		krsort(self::$commandsBuffer);
	}

	/**
	 * @param string $paramName
	 * @return int
	 */
    private static function getFlag($paramName, $protocol){
		// new in 1.6
		// 05 - operator
	    $typeName = "";
	    switch ($paramName){
		    case "int":
				$typeName = self::ARG_TYPE_INT;
			    break;
		    case "float":
			    $typeName = self::ARG_TYPE_FLOAT;
			    break;
		    case "mixed":
		    case "value":
			    $typeName = self::ARG_TYPE_VALUE;
			    break;
		    case "target":
			    $typeName = self::ARG_TYPE_TARGET;
			    break;
		    case "string":
			    $typeName = self::ARG_TYPE_STRING;
			    break;
		    case "xyz":
		    case "x y z":
			    $typeName = self::ARG_TYPE_POSITION;
			    break;
		    case "rawtext":
		    case "message":
			    $typeName = self::ARG_TYPE_RAWTEXT;
			    break;
		    case "text":
			    $typeName = self::ARG_TYPE_TEXT;
			    break;
		    case "json":
			    $typeName = self::ARG_TYPE_JSON;
			    break;
		    case "command":
			    $typeName = self::ARG_TYPE_COMMAND;
			    break;
		    default:
			    return 0;
	    }
	    return MultiversionEnums::getCommandArgType($typeName, $protocol);
    }
}