<?php

require_once 'SeleniumRCToBehatMinkFormatter.php';
require_once 'SeleniumFileParser.php';

/**
 * Description of BehatFilesGenerator
 *
 * @author narek_vardzelyan
 */
class BehatFilesGenerator {
    protected $seleniumRCToBehatMinkFormatter;
    protected $seleniumFileParser;
    protected $SeleniumIdeParsedXML;
    
    public function __construct($filePath)
    {
        $this->seleniumFilePrser = new SeleniumFileParser($filePath);
        $this->seleniumIdeParsedXML = $this->seleniumFileParser->parse();
        $this->seleniumRCToBehatMinkFormatter = new SeleniumRCToBehatMinkFormatter(
            $this->seleniumIdeParsedXML
        );
    }
    
    protected function getTestMethodName()
    {
        
    }
    
    protected function getTestMethodContent()
    {
        
    }
    
    protected function getTestFeatureFileName()
    {
        
    }
    
    protected function generateContextFile()
    {
        $fileName = 'FeatureContext.php';
        $data = <<<FILE
<?php\n
use Behat\MinkExtension\Context\MinkContext;\n
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
    }\n
    {$this->seleniumRCToBehatMinkFormatter->format()}
FILE;
        file_put_contents($fileName, $data);
    }
    
    protected function generateFeatureFile()
    {
        $fileName = ;
        $data = <<<FILE
Feature: Search
    In order to see a word definition
    As a website user
    I need to be able to search for a word

    @javascript
    Scenario: Searching for a page with autocompletion
        Given I am on "/wiki/Main_Page"
FILE;
    }
    
    public function generate()
    {
        $this->generateContextFile();
        $this->generateFeatureFile();
    }
}
