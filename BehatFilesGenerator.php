<?php

require_once 'SeleniumRCToBehatMinkFormatter.php';
require_once 'SeleniumFileParser.php';

/**
 * This class generates Behat Mink context file and feature file.
 * @author narek_vardzelyan
 */
class BehatFilesGenerator {
    protected $seleniumRCToBehatMinkFormatter;
    protected $seleniumFileParser;
    protected $seleniumIdeParsedXML;
    
    public function __construct($filePath)
    {
        $this->seleniumFileParser = new SeleniumFileParser($filePath);
        $this->seleniumIdeParsedXML = $this->seleniumFileParser->parse();
        $this->seleniumRCToBehatMinkFormatter = new SeleniumRCToBehatMinkFormatter(
            $this->seleniumIdeParsedXML
        );
    }
    
    protected function getTestName()
    {
        return $this->seleniumIdeParsedXML['test_name'];
    }
    
    protected function getTestMethodName()
    {
        return 'somedumytestMethodName';
        //return $this->seleniumIdeParsedXML['test_name'];
    }
    
    protected function getTestMethodContent()
    {
        return $this->seleniumRCToBehatMinkFormattr->format();
    }
    
    protected function getTestFeatureFileName()
    {
        return str_replace(' ', '_', $this->seleniumIdeParsedXML['test_name']);
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
class FeatureContext extends MinkContext\n{
    /**
     * Initializes context.
     * Every scenario gets it's own context object.
     *
     * @param array \$parameters context parameters (set them up through behat.yml)
     */
    public function __construct(array \$parameters)\n\t{
        // Initialize your context here\n\t}\n
    public function {$this->getTestMethodName()}()
    {\n{$this->seleniumRCToBehatMinkFormatter->format()}\n\t}\n}
FILE;
        file_put_contents($fileName, $data);
    }
    
    protected function generateFeatureFile()
    {
        $fileName = $this->getTestFeatureFileName();
        $data = <<<FILE
Feature: {$this->getTestName()}
    Feature description

    @javascript
    Scenario: {$this->getTestName()}
        {$this->getTestName()}
FILE;
        file_put_contents($fileName, $data);
    }
    
    public function generate()
    {
        $this->generateContextFile();
        $this->generateFeatureFile();
    }
}
