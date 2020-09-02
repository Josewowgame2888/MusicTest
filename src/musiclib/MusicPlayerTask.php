<?php

declare(strict_types=1);

namespace musiclib;


use musiclib\nbs_decrypt\Layer;
use musiclib\nbs_decrypt\NBSFile;
use musiclib\nbs_decrypt\Song;
use pocketmine\network\mcpe\protocol\PlaySoundPacket;
use pocketmine\Player;
use pocketmine\scheduler\Task;

class MusicPlayerTask extends Task
{
    public $song = null;
    /** @var bool */
    protected $playing = false;
    /** @var int */
    private $tick = -1;
    /** @var MusicManager */
    private $main;

    public function __construct(MusicManager $main, Song $song)
    {
        $this->main = $main;
        $this->song = $song;
        $this->playing = true;
    }


    public function onRun(int $currentTick)
    {
        if ($this->main->settings->pause === false) {
            if (!$this->playing) {
                return;
            }
            if ($this->tick > $this->song->getLength()) {
                $this->tick = -1;
                $this->playing = false;
                //finish
                if ($this->main->settings->replay) {
                    $this->main->onReplay();
                    return;
                } else {
                    $this->main->onStop();
                }
            }
            $this->tick++;
            foreach ($this->main->settings->level->getPlayers() as $player)
            {
                $this->playTick($player, $this->tick);
            }
        }
    }

    public function playTick(Player $player, int $tick): void
    {
        $playerVolume = 50;

        /** @var Layer $layer */
        foreach ($this->song->getLayerHashMap()->values()->toArray() as $layer) {
            $note = $layer->getNote($tick);
            if ($note === null) {
                continue;
            }

            $volume = ($layer->getVolume() * $playerVolume) / 100;
            $pitch = 2 ** (($note->getKey() - 45) / 12);
            $sound = NBSFile::MAPPING[$note->instrument] ?? NBSFile::MAPPING[NBSFile::INSTRUMENT_PIANO];
            $pk = new PlaySoundPacket();
            $pk->soundName = $sound;
            $pk->pitch = $pitch;
            $pk->volume = $volume;
            $vector = $player->asVector3();
            $pk->x = $vector->x;
            $pk->y = $vector->y + $player->getEyeHeight();
            $pk->z = $vector->z;
            $player->dataPacket($pk);
            unset($add, $pk, $vector, $note);
        }
    }
}