<?php 
//Custom NBT reader / writer designed for GamerTools

//Tags, but named types for easier recognition
$types = [
    "end" => 0,
    "byte" => 1,
    "short" => 2,
    "int" => 3,
    "long" => 4,
    "float" => 5,
    "double" => 6,
    "byte array" => 7,
    "varint array" => 7,
    "string" => 8,
    "list" => 9,
    "compound" => 10,
    "int array" => 11
];

class NbtReader {

    private $offset = 0;

    function setOffset($offset) {
        $this->offset = $offset;
    }
    function readByte($binary): int {
        $bytes = substr($binary, $this->offset, 1);
        $value = ord($bytes);
        $this->offset += 1;
        return $value;
    }
    function readShort($binary) {
        $value = unpack('n', $binary, $this->offset); 
        $this->offset += 2;   
        return $value[1];
    }
    function readInt($binary) {
        $value = unpack('N', $binary, $this->offset);
        $this->offset += 4;
        return $value[1];
    }
    function readLong($binary) {
        $value = unpack('J', $binary, $this->offset);
        $this->offset += 8;
        return $value[1];
    }
    function readFloat($binary) {
        $value = unpack('G', $binary, $this->offset);
        $this->offset += 8;
        return $value[1];
    }
    function readDouble($binary) {
        $value = unpack('E', $binary, $this->offset);
        $this->offset += 8;
        return $value[1];
    }
    function readByteArray($binary) {
        $byte_amount = $this->readInt($binary);
        $result = [];

        $i = 0;
        while($i < $byte_amount) {
            $value = $this->readByte($binary);
            array_push($result, $value);

            $i++;
        }
        return $result;
    }
    function readVarIntArray($binary) { //Is a VarInt array
        $byte_amount = $this->readInt($binary);
        $bytes = substr($binary, $this->offset, $byte_amount);
        $result = [];
        $i = 0;
        while ($i < $byte_amount) {
            $value = 0;
            $varint_length = 0;

            while (true) {
                $byte = ord(substr($binary, $this->offset + $i, 1));
                $value |= ($byte & 127) << ($varint_length++ * 7);
                if ($varint_length > 5) {
                    throw new RuntimeException("VarInt too big (probably corrupted data)");
                }
                if (($byte & 128) != 128) {
                    $i++;
                    array_push($result, $value);
                    break;
                }
                $i++;
            }
        }
        $this->offset += $byte_amount;
        return $result;
    }
    function readString($binary): string {
        $str_length = $this->readShort($binary);
        $bytes = substr($binary, $this->offset, $str_length);
        $value = mb_convert_encoding($bytes, 'ISO-8859-1', 'UTF-8');
        //$value = $bytes;
        $this->offset += strlen($value); //String length (in bytes)
        return $value;
    }
    function readList($binary) {
        $list_element_type = $this->readByte($binary);
        $type = $this->typeToString($list_element_type);
        $length = $this->readInt($binary);

        $result = [
            "types" => $type,
            "elements" => []
        ];
        $i = 0;
        for ($i = 0; $i < $length; $i++) {
            $result["elements"][] = $this->readEntry($binary, false, $type);
        }
        return $result;
    }
    function readCompound(string $binary): array {
        global $types;
        $value = [];

        while (true) {
            $entry = $this->readEntry($binary);
            $type = $entry["type"];

            if ($type === "end") { break; }

            if (is_null($entry["value"])) { continue; }

            $data = [
                "type" => $entry["type"],
                "name" => $entry["name"],
                "value" => $entry["value"]
            ];
            array_push($value, $data);
        }
        return $value;
    }
    function readIntArray($binary) {
        $length_bin = $this->readInt($binary);
        $result = [];
        $i = 0;
        while ($i < $length_bin) {
            $value = $this->readInt($binary);
            array_push($result, $value);
            $i++;
        }
        return $result;
    }
    function readTagData(string $binary): array {
        $type = $this->readByte($binary);
        $type = $this->typeToString($type);

        if ($type !== "end") {
            $tag_name = $this->readString($binary);
        } else {
            $tag_name = null;
        }

        return [
            "type" => $type,
            "name" => $tag_name
        ];
    }
    function typeToString($type): string {
        global $types;
        return array_search($type, $types);
    }
    function readEntry(string $binary, bool $readHeader = true, string $type = null) {
        if ($readHeader) {
            $tag_data = $this->readTagData($binary);
            $type = $tag_data["type"];
            $tag_name = $tag_data["name"];
            //echo "\nReading type " . $type . " with name $tag_name";
        } else {
            $tag_name = null;
        }

        // echo "\nTag data offset " . $tag_data["offset"];
        if ($type === "byte") {
            $value = $this->readByte($binary);
        } else if ($type === "short") {
            $value = $this->readShort($binary);
        } else if ($type === "int") {
            $value = $this->readInt($binary);
        } else if ($type === "long") {
            $value = $this->readLong($binary);
        } else if ($type === "float") { //NEEDS ROUNDING NUMBER
            $value = $this->readFloat($binary);
        } else if ($type === "double") { //REQUIRES TESTING
            $value = $this->readDouble($binary);
        } else if ($type === "byte array") {
            //Check if byte array is a varInt array
            $offset = $this->offset;
            try {
                $value = $this->readVarIntArray($binary);
            } catch (RuntimeException $e) {
                $this->setOffset($offset); //Reset offset (because previous function failed)
                $value = $this->readByteArray($binary);
            }
        } else if ($type === "int array") {
            $value = $this->readIntArray($binary);
        } else if ($type === "string") {
            $value = $this->readString($binary);
        } else if ($type === "list") {
            //Not done yet
            $value = $this->readList($binary);
        } else if ($type === "compound") {
            $value = $this->readCompound($binary);
        } else {
            $value = null;
        }
        return [
            "type" => $type,
            "name" => $tag_name,
            "value" => $value
        ];
    }
    function readData($binary) {
        $this->setOffset(0);
        $data = $this->readEntry($binary);
        return $data;
    }
    function NBTtoJSON($binary) {
        $data = $this->readData($binary);
        return json_encode($data, true);
    }
}









class NbtWriter {
    function unsignedRightShift($a, $b) {
        if($b == 0) return $a;
        return ($a >> $b) & ~(1<<(8*PHP_INT_SIZE-1)>>($b-1)); 
    }
    function writeByte($value) {
        return chr($value);
    }
    function writeVarInt($value) {
        $bytes = '';
        
        while (($value & -128) != 0) {
            $bytes .= $this->writeByte(($value & 0x7F) | 0x80);
            // Note: >>> means that the sign bit is shifted with the rest of the number rather than being left alone
            $value = $this->unsignedRightShift($value, 7);
        }
        $bytes .= $this->writeByte($value);
        
        return $bytes;
    }

    function writeShort($value) {
        return pack('n', $value);
    }
    function writeInt($value) {
        return pack('N', intval($value));
    }
    function writeLong($value) {
       return pack('J', $value);
    }
    function writeFloat($value) {
        return pack('G', $value);
    }
    function writeDouble($value) {
        return pack('E', (double)$value);
    }
    function writeString($value) {
        if (gettype($value) === "string") {
            $length = $this->writeShort(strlen($value));
            $binary = mb_convert_encoding($value,'UTF-8','ISO-8859-1');
            return $length . $binary;
        } else {
            throw new Exception('No string given for writeString()');
            exit;
        }
    }
    function writeByteArray($arr) {
        $bytes = pack("c*", ...$arr);
        $length_bin = $this->writeInt(strlen($bytes));
        return $length_bin . $bytes;
    }
    function writeVarIntArray($arr) { //Is a VarInt array
        if (is_array($arr)) {
            $bytes = '';
            foreach ($arr as $value) {
                $bytes .= $this->writeVarInt($value);
            }
            $length_bin = $this->writeInt(strlen($bytes));
            // $bytes = pack('C*', ...$arr);
            return $length_bin . $bytes;
        } else {
            throw new Exception('No valid array given for writeByteArray()');
            exit;
        }
    }
    function writeIntArray($arr) {
        if (is_array($arr)) {
            $length_bin = $this->writeInt(count($arr));
            $bin_ints = '';
            foreach($arr as $int) {
                $bin_ints .= $this->writeInt($int);
            }
            return $length_bin . $bin_ints;
        } else {
            throw new Exception('No valid array given for writeIntArray()');
            exit;
        }
    }
    function writeList($arr) {
        if (is_array($arr)) {
            //Type check
            $type_check = $arr[0]["type"];
            foreach($arr as $item) {
                if ($type_check !== $item["type"]) {
                    throw new Exception('Different tag types supplied in list.');
                    exit;
                }
            }

            //Writing
            $length_bin = $this->writeInt(count($arr));
            foreach($arr as $item) {
                
            }
        } else {
            throw new Exception('No valid array given for writeIntArray()');
            exit;
        }
    }
    function writeTagName($arr) { //Key
        global $types;
        $type = $this->writeByte($arr["type"]);
        if ($arr["type"] !== $types["end"]) {
            $binary = $this->writeString($arr["name"]);
            return $type . $binary;
        }
        return $type;
    }
    function writeObject($arr) {
        global $types;
        $bytes = "";

        if (is_array($arr)) {
            //Type handling
            if (is_string($arr["type"])) {
                if (array_key_exists($arr["type"], $types)) {
                    $arr["type"] = $types[$arr["type"]];
                } else {
                    throw new Exception("Type '".$arr["type"]."' not found");
                    exit();
                }
            }
            $bytes .= $this -> writeTagName($arr); //Write type (and key)
            $type = $arr["type"];
            if ($type === $types["byte"]) {
                $bytes .= $this->writeByte($arr["value"]);
            } else if ($type === $types["short"]) {
                $bytes .= $this->writeShort($arr["value"]);
            } else if ($type === $types["int"]) {
                $bytes .= $this->writeInt($arr["value"]);
            } else if ($type === $types["long"]) {
                $bytes .= $this->writeLong($arr["value"]);
            } else if ($type === $types["float"]) {
                $bytes .= $this->writeFloat($arr["value"]);
            } else if ($type === $types["double"]) {
                $bytes .= $this->writeDouble($arr["value"]);
            } else if ($type === $types["byte array"]) {
                $bytes .= $this->writeByteArray($arr["value"]);
            } else if ($type === $types["varint array"]) {
                $bytes .= $this->writeVarIntArray($arr["value"]);
            } else if ($type === $types["int array"]) {
                $bytes .= $this->writeIntArray($arr["value"]);
            } else if ($type === $types["string"]) {
                $bytes .= $this->writeString($arr["value"]);
            } else if ($type === $types["list"]) {
                $bytes .= $this->writeList($arr["value"]);
            } else if ($type === $types["compound"]) {
                foreach($arr["value"] as $array) {
                    $bytes .= $this->writeObject($array);
                }
                $bytes .= $this->writeObject(["type" => $types["end"]]);
            }
            return $bytes;
        } else {
            throw new Exception('No valid array given for writeObject()');
            exit;
        }
    }
    function JSONtoNBT($data) {
        if (is_string($data)) {
            try {
                $data = json_decode($data);
            } catch (Exception $e) {
                throw new Exception('No valid JSON string given at toNBT()');
            }
        }
        return $this->writeObject($data);
    }
}

function writeFile($data) {
    $file = fopen($_SERVER['DOCUMENT_ROOT'] . "/library/server/nbt/test.dat", "w") or die("Unable to open file!");
    fwrite($file, $data);
}