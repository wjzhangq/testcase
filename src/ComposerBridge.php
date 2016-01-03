<?php 
/**
 * composer 桥接类
 */
namespace Cola\Warper;
use Composer\Script\Event;
use Composer\Installer\PackageEvent;

class ComposerBridge{

	public static function Init(Event $event)
    {
        $vendor_dir = $event->getComposer()->getConfig()->get('vendor-dir');

        $root_dir = dirname($vendor_dir);

        WarperBuild::buildDirTree($root_dir);

    }
}