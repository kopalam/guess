<?php

$loader = new \Phalcon\Loader();

/**
 * We're a registering a set of directories taken from the configuration file
 */
$loader->registerDirs(
    [
        $config->application->controllersDir,
        $config->application->servicesDir,
        $config->application->libraryDir,
        $config->application->modelsDir,
        $config->application->certDir
    ]
)->register();
