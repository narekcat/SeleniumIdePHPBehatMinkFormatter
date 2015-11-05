<?php

class SeleniumOneToSeleniumTwoConverter
{
    protected $fileContent;
    protected $commandsTableXMLIterator;
    
    public function __construct($filename)
    {
        $this->fileContent = file_get_contents($filename);
        $this->commandsTableXMLIterator = new SimpleXMLIterator($this->fileContent);
    }
    
    public function getTestName()
    {
        $domNode = new DOMDocument('1.0', 'UTF-8');
        $domNode->loadXML($this->fileContent);
        $title = $domNode->getElementsByTagName('title')[0];
        return $title->nodeValue;
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
        $commandsCount = count($commandsList);
        $commandsListInSeleniumTwo = [];
        for ($i = 0; $i < $commandsCount; ++$i) {
            $commandsListInSeleniumTwo[] = $this->appropriateCommandForSeleniumTwo($commandsList[$i]);
        }
    }
    
    protected function appropriateCommandForSeleniumTwo($command)
    {
        
    }
    
    protected function open()
    {
        
    }
}

class SeleniumTwoToBehatMinkFormatter
{
    protected $contextFileContent;
    protected $featureFileContent;
    protected $converter;
    
    public function __construct($fileName)
    {
        $this->contextFileContent = <<<FILE
<?php

use Behat\MinkExtension\Context\MinkContext;

/**
 * Features context.
 */
class FeatureContext extends MinkContext
{
    /**
     * Initializes context.
     * Every scenario gets it's own context object.
     *
     * @param array \$parameters context parameters (set them up through behat.yml)
     */
    public function __construct(array \$parameters)
    {
        // Initialize your context here
    }

FILE;
        $this->featureFileContent = <<<FILE
Feature: Search
    In order to see a word definition
    As a website user
    I need to be able to search for a word

    @javascript
    Scenario: Searching for a page with autocompletion
        Given I am on "/wiki/Main_Page"
FILE;
        $this->converter = new SeleniumOneToSeleniumTwoConverter($fileName);
    }
    
    public function format()
    {
        $testName = str_replace(' ', '', ucwords(
            $this->converter->getTestName()
        ));
        $this->contextFileContent .= "public function {$testName}()\n{";
        var_dump($this->contextFileContent);
    }
}

$fileOne = '/Users/narek_vardzelyan/Downloads/10 hour long Training Session is created.xhtml';
$fileTwo = '/Users/narek_vardzelyan/Documents/search_wikipedia_html_testcase.html';

$formatter = new SeleniumTwoToBehatMinkFormatter($fileOne);
$formatter->format();