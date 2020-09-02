<?php

declare(strict_types=1);

namespace musiclib\nbs_decrypt;


use pocketmine\utils\BinaryStream;

class NBSBinaryStream extends BinaryStream
{
    public function getString(): string
    {
        return $this->get(\unpack("I", $this->get(4))[1]);
    }
}