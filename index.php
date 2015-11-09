<?php

class SeleniumOneToBehatMinkFormatter
{
    protected $fileContent;
    protected $testCaseXMLIterator;
    protected $contextFileContent;
    protected $featureFileContent;
    
    public function __construct($filename)
    {
        $this->fileContent = file_get_contents($filename);
        $this->testCaseXMLDOM = new DOMDocument($this->fileContent);
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
        $elements = $this->testCaseXMLDOM->getElementsByTagName('table');
        if (count($elements) !== 0) {
            return $elements[0];
        }
        return null;
    }
    
    protected function getCommandsList()
    {
        $commandsListTableXMLIterator = new SimpleXMLIterator($this->getCommandsListTable()->saveXML());
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
            $commandsListInSeleniumTwo[] = $this->toBehatMink($commandsList[$i]);
        }
    }
    
    protected function toBehatMink($command)
    {
        $minkCommands = '';
        try {
            switch($command[0]) {
                case 'open':
                    $minkCommands .= "\t\$this->getSession()->visit({$command[1]});\n";
                    break;
                case 'type':
                    $minkCommands .= "\t\$this->getSession()->fillFields({$command[1]}, {$command[2]});\n";
                    break;
                case 'clickAndWait':
                    $minkCommands .= "\t\$this->getSession()->pressButton({$command[1]});\n";
                    $minkCommands .= "\t\$this->getSession()->wait();\n";
                    break;
                case 'select':
                    $minkCommands .= "\t\$this->getSession()->selectOption({$command[1]}, {$command[2]});\n";
                    break;
                case 'sendKeys':
                    $minkCommands .= "\t\$this->getSession()->fillFields({$command[1]}, {$command[2]});\n";
                    break;
                case 'waitForElementPresent':
                    $minkCommands .= "\t\$this->getSession()->wait(10000, 'document.getElementById('{$command[1]}')')";
                    break;
                default:
                    throw new Exception('There are not command with name '.$command[0]);
            }
        } catch (Exception $ex) {
            die($e->getMessage());
        }
        return $minkCommands;
    }
    
    public function format()
    {
        $testName = str_replace(' ', '', ucwords(
            $this->getTestName()
        ));
        $this->contextFileContent .= "\n    public function {$testName}()\n    {";
        $commands = $this->getCommandsList();
        foreach ($commands as $command) {
            $this->contextFileContent .= $this->toBehatMink($command);
        }
        var_dump($this->contextFileContent);
    }
}

$fileOne = '/Users/narek_vardzelyan/Documents/10 hour long Training Session is created.xhtml';
$fileTwo = '/Users/narek_vardzelyan/Documents/search_wikipedia_html_testcase.html';

$formatter = new SeleniumOneToBehatMinkFormatter($fileOne);
$formatter->format();