<?php

declare(strict_types=1);

namespace musiclib;


use pocketmine\level\Level;

class MusicAttributes
{
    /** @var string */
    public $path;
    /** @var Level */
    public $level;
    /** @var bool */
    public $replay;
    /** @var bool  */
    public $pause = false;
}