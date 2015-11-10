<?php

require_once 'Commands.php';

/**
 * This class formats Selenium Ide commands to Behat Mink method calls.
 * The constructor recives array from SeleniumFileParser and
 * public method format returns method calls.
 */
class SeleniumRCToBehatMinkFormatter
{
    protected $seleniumIdeTestData;
    
    public function __construct($seleniumIdeTestData)
    {
        $this->seleniumIdeTestData = $seleniumIdeTestData;
    }
    
    protected function getCommandByName($command)
    {
        return CommandFactory::build($command['name'], $command);
    }
    
    public function format()
    {
        $minkContext = '';
        $commandsList = $this->seleniumIdeTestData['commands_list'];
        foreach ($commandsList as $command) {
            $commandObj = $this->getCommandByName($command);
            $minkContext .= $commandObj->toBehatMink();
        }
        return $minkContext;
    }
}
