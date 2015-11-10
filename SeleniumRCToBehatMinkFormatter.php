<?php

require_once 'Commands.php';

/**
 * This class formats Selenium Ide commands to Behat Mink method calls.
 * The constructor recives array from SeleniumFileParser and
 * public method format returns method calls.
 */
class SeleniumRCToBehatMinkFormatter
{
    protected $minkContext;
    protected $seleniumIdeParsedXML;
    
    public function __construct($seleniumIdeParsedXML)
    {
        $this->seleniumIdeParsedXML = $seleniumIdeParsedXML;
        $this->minkContext = '';
    }
    
    protected function getCommandByName($command)
    {
        return CommandFactory::build($command['name'], $command);
    }
    
    public function format()
    {
        $commandsList = $this->seleniumIdeParsedXML['commands_list'];
        foreach ($commandsList as $command) {
            $commandObj = $this->getCommandByName($command);
            $this->minkContext .= $commandObj->toBehatMink();
        }
        return $this->minkContext;
    }
}
