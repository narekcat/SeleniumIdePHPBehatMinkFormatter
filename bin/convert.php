<?php
/**
 * This file is converts input file from SeleniumIde xml format to Behat php format
 */

require_once __DIR__ . '/vendor/autoload.php';

use Fouraitch\SeleniumIdeFormatter\BehatTestGenerator;

function setTextColor($color)
{
    $colors = [
        'red' => "\e[1;31m",
        'green' => "\e[1;32m"
    ];
    echo $colors[$color];
}

function resetTextColor()
{
    echo "\e[0m";
}

try {
    if ($_SERVER['argc'] < 2) {
        throw new Exception("There are not enough parameters.\n");
    }
    if ($_SERVER['argc'] > 2) {
        throw new Exception("There are too many parameters.\n");
    }
    
    $fileName = $_SERVER['argv'][1];
    if (preg_match('/^[0-9]/', $fileName)) {
        throw new Exception("Test file name must not start with number.\n");
    }
    $filePath = 'LearnshipSeleniumTests/TestCases/' . $fileName;
    if (!file_exists($filePath)) {
        throw new Exception("Wrong file name.\n");
    }
    $testGenerator = new BehatTestGenerator($filePath);
    $testGenerator->generate();
    setTextColor('green');
    echo "Your test converted successfully.\n"
    . "Now you can run \"bin/behat features/{$fileName}\"\n";
} catch (Exception $ex) {
    setTextColor('red');
    echo $ex->getMessage();
}
resetTextColor();
