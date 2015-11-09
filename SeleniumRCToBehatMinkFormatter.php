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
    protected $seleniumIdeparsedXML;
    
    public function __construct($seleniumIdeparsedXML)
    {
        $this->seleniumIdeparsedXML = $seleniumIdeparsedXML;
        $this->minkContext = '';
    }
    
    protected function getCommandByName($command)
    {
        return CommandFactory::build($command['name'], $command);
    }
    
    public function format()
    {
        $parsedXML = $this->seleniumFileParser->parse();
        $commandsList = $parsedXML['commands_list'];
        foreach ($commandsList as $command) {
            $commandObj = $this->getCommandByName($command);
            $this->minkContext .= $commandObj->toBehatMink();
        }
        return $this->minkContext;
    }
}
