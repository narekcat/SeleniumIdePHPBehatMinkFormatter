<?php

/**
 * Description of SeleniumFileParser
 *
 * @author narek_vardzelyan
 */
class SeleniumFileParser {
    protected $testFileName;
    protected $xml;
    
    public function __construct($testFileName)
    {
        $this->testFileName = $testFileName;
        $this->xml = new DOMDocument('1.0', 'UTF-8');
        $this->xml->load($testFileName);
    }
    
    protected function getTestName()
    {
        $titleElements = $this->xml->getElementsByTagName('title');
        if ($titleElements->length == 0) {
            throw new Exception("Wrong Selenium Ide file format.\n Test name absent.\n");
        }
        return $titleElements->item(0)->nodeValue;
    }
    
    protected function getBaseUrl()
    {
        $linkElements = $this->xml->getElementsByTagName('link');
        if ($linkElements->length == 0) {
            throw new Exception("Wrong Selenium Ide file format.\n Base url absent.\n");
        }
        return $linkElements->item(0)->getAttribute('href');
    }
    
    protected function getCommandsList()
    {
        $trElements = $this->xml->getElementsByTagName('tr');
        if ($trElements->length == 0) {
            throw new Exception("Wrong Selenium Ide file format.\n Commands list absent.\n");
        }
        $commandsList = [];
        for ($i = 0; $i < $trElements->length; ++$i) {
            $tdElements = $trElements->item($i)->getElementsByTagName('td');
            if ($tdElements->length < 3) {
                throw new Exception("Wrong Selenium Ide file format.\n Command isn't full.");
            }
            $commandName = $tdElements->item(0);
            $commandTarget = $tdElements->item(1);
            $commandValue = $tdElements->item(2);
            $commandsList[] = [
                'name' => $commandName,
                'target' => $commandTarget,
                'value' => $commandValue
            ];
        }
        return $commandsList;
    }
    
    public function parse()
    {
        return [
            'test_name' => $this->getTestName(),
            'base_url' => $this->getBaseUrl(),
            'commands_list' => $this->getCommandsList()
        ];
    }
}
