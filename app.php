#!/usr/bin/env php
<?php
require_once __DIR__ . '/vendor/autoload.php';

use Symfony\Component\Console\Application;

use Console\RunCommand;

$app = new Application('Swarmon', 'v0.1.0');
$app->add(new RunCommand());
$app -> run();
