<?php

declare(strict_types=1);

namespace musiclib;


use musiclib\nbs_decrypt\NBSFile;
use musiclib\nbs_decrypt\Song;
use pocketmine\plugin\Plugin;

use function intval;
use function floor;

class MusicManager
{
    /** @var Plugin  */
    public $plugin;
    /** @var MusicAttributes  */
    public $settings;
    /** @var MusicPlayerTask */
    private $task;

    public function __construct(Plugin $plugin, MusicAttributes $settings)
    {
        $this->plugin = $plugin;
        $this->settings = $settings;
    }

    public function onPlay(): void
    {
        if ($this->task instanceof MusicPlayerTask) {
            $this->onStop();
        }

        $song = NBSFile::parse($this->settings->path);
        if (!$song instanceof Song) {
            return;
        }
        $this->task = $this->plugin->getScheduler()->scheduleDelayedRepeatingTask(new MusicPlayerTask($this, $song), 20 * 3, intval(floor($song->getDelay())));
    }

    public function onReplay(): void
    {
        if (!$this->task instanceof MusicPlayerTask) {
            return;
        }

        $this->onStop();
        $this->onPlay();
    }

    public function onPause(bool $value): void
    {
        if (!$this->settings instanceof MusicAttributes) {
            return;
        }

        $this->settings->pause = $value;
    }

    public function onStop(): void
    {
        if (!$this->task instanceof MusicPlayerTask) {
            return;
        }

        $this->plugin->getScheduler()->cancelTask($this->task->getTaskId());
        $this->task = null;
    }
}