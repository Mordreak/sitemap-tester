<?php
/**
 * Created by PhpStorm.
 * User: theo
 * Date: 25/04/18
 * Time: 15:52
 */

require_once 'app/start.php';

use Sourcecode\CommandParser;

define('PWD', getcwd());

$parser = new CommandParser();
$parser->getInput()->parse($parser->getOutput());
