<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInit7f506ece7e5278d8f9b68d7eaefdd425
{
    public static $fallbackDirsPsr0 = array (
        0 => __DIR__ . '/../..' . '/src',
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->fallbackDirsPsr0 = ComposerStaticInit7f506ece7e5278d8f9b68d7eaefdd425::$fallbackDirsPsr0;

        }, null, ClassLoader::class);
    }
}
