<?php

require_once 'Commands.php';
require_once 'SeleniumFileParser.php';

class SeleniumRCToBehatMinkFormatter
{
    protected $contextFileContent;
    protected $minkContext;
    protected $seleniumFileParser;
    
    public function __construct($fileName)
    {
        $this->seleniumFileParser = new SeleniumFileParser($fileName);
        $this->minkContext = <<<FILE
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
    
    protected function getCommandByName($command)
    {
        return CommandFactory::build($command['name'], $command);
    }
    
    public function generate($commandsList)
    {
        foreach ($commandsList as $command) {
            $commandObj = $this->getCommandByName($command);
            $this->minkContext .= $commandObj->toBehatMink();
        }
    }
    
    public function format()
    {
        $parsedXML = $this->seleniumFileParser->parse();
        $this->generate($parsedXML['commands_list']);
    }
}
