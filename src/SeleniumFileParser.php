<?php

namespace Fouraitch\SeleniumIdeFormatter;

use Exception;
use DOMDocument;

/**
 * The class parses Selenium Ide html test file
 * and returns array with appropriate fields.
 * @author narek_vardzelyan
 */
class SeleniumFileParser {
    protected $filePath;
    protected $xml;
    
    public function __construct($filePath)
    {
        $this->filePath = $filePath;
        $this->xml = new DOMDocument('1.0', 'UTF-8');
        $this->xml->load($filePath);
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
        $tableBodyElement = $this->xml->getElementsByTagName('tbody')->item(0);
        if ($tableBodyElement == null) {
            throw new Exception("Wrong Selenium Ide file format.\n Commands list absent.\n");
        }
        $trElements = $tableBodyElement->getElementsByTagName('tr');
        if ($trElements->length == 0) {
            throw new Exception("Wrong Selenium Ide file format.\n Commands list absent.\n");
        }
        $commandsList = [];
        for ($i = 0; $i < $trElements->length; ++$i) {
            $tdElements = $trElements->item($i)->getElementsByTagName('td');
            if ($tdElements->length < 3) {
                throw new Exception("Wrong Selenium Ide file format.\n Command isn't full.");
            }
            $commandsList[] = [
                'name' => $tdElements->item(0)->nodeValue,
                'target' => $tdElements->item(1)->nodeValue,
                'value' => $tdElements->item(2)->nodeValue
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
