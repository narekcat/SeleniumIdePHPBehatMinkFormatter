<?php

class SeleniumOneToSeleniumTwoConverter
{
    protected $commandsTableXMLIterator;
    
    public function __construct($filename)
    {
        $fileContent = file_get_contents($filename);
        $this->commandsTableXMLIterator = new SimpleXMLElementIterator($fileContent);
//        var_dump($this->commandsTable);
    }
    
    protected function getCommandsListTable()
    {
        while($this->commandsTableXMLIterator->current() !== null) {
            if ($this->commandsTableXMLIterator->current()->getName() === 'table') {
                return $this->commandsTableXMLIterator->current();
            }
            $this->commandsTableXMLIterator->current()->next();
        }
        return null;
    }
    
    protected function getCommandsList()
    {
        $commandsListTableXMLIterator = $this->getCommandsListTable();
        $commandsList = [];
        while ($commandsListTableXMLIterator->current() !== null) {
            if ($commandsListTableXMLIterator->current()->getName() === 'tr') {
                $childrens = $commandsListTableXMLIterator->current()->getChildren();
                $command = $childrens->__toString();
                $childrens->next();
                $target = $childrens->__toString();
                $childrens->next();
                $value = $childrens->__toString();
                $commandsList[] = [
                    $command,
                    $target,
                    $value
                ];
            }
        }
        return $commandsList;
    }
    
    public function convert()
    {
        $commandsList = $this->getCommandsList();
        $commandsCount = count($CommandsList);
        $commandsListInSeleniumTwo = [];
        for ($i = 0; $i < $commandsCount; ++$i) {
            $commandsListInSeleniumTwo[] = $this->appropriateCommandForSeleniumTwo($commandsList[$i]);
        }
    }
    
    protected function appropriateCommandForSeleniumTwo($command)
    {
        
    }
}

class SeleniumTwoToBehatMinkFormatter
{
    public function __construct()
    {
        
    }
    
    public function format()
    {
        
    }
}

$fileOne = '/Users/narek_vardzelyan/Downloads/10 hour long Training Session is created.xhtml';
$fileTwo = '/Users/narek_vardzelyan/Documents/search_wikipedia_html_testcase.html';

$converter = new SeleniumOneToSeleniumTwoConverter($fileOne);