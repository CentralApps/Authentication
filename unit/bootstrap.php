<?php
require_once(__DIR__.'/splClassLoader.php');
$classLoader = new SplClassLoader('CentralApps\Authentication', __DIR__ . '/../' );
$classLoader->register();