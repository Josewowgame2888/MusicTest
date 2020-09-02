<?php

declare(strict_types=1);
namespace test;


use musiclib\MusicAttributes;
use musiclib\MusicManager;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerChatEvent;
use pocketmine\plugin\PluginBase;

class Loader extends PluginBase implements Listener
{
    /** @var MusicManager */
    private $music;

    public function onEnable()
    {
        @mkdir($this->getDataFolder());

        $this->getServer()->getPluginManager()->registerEvents($this, $this);

        $attributes = new MusicAttributes();
        $attributes->replay = true;
        $attributes->path = $this->getDataFolder() . 'test.nbs';
        $attributes->level = $this->getServer()->getDefaultLevel();
        $this->music = new MusicManager($this, $attributes);
    }

    public function onChat(PlayerChatEvent $event): void
    {
        if ($event->getMessage() === 'start') {
            $this->music->onPlay();
        }
        if ($event->getMessage() === 'pause') {
            $this->music->onPause(true);
        }
        if ($event->getMessage() === 'continue') {
            $this->music->onPause(false);
        }
        if ($event->getMessage() === 'replay') {
            $this->music->onReplay();
        }
        if ($event->getMessage() === 'stop') {
            $this->music->onStop();
        }
    }
}