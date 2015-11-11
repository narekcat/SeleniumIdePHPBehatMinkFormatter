<?php

namespace Fouraitch;

/**
 * This class generates Behat Mink context file and feature file.
 * @author narek_vardzelyan
 */
class BehatTestGenerator {
    protected $seleniumRCToBehatMinkFormatter;
    protected $seleniumIdeTestData;
    
    public function __construct($filePath)
    {
        $seleniumFileParser = new SeleniumFileParser($filePath);
        $this->seleniumIdeTestData = $seleniumFileParser->parse();
        $this->seleniumRCToBehatMinkFormatter = new SeleniumRCToBehatMinkFormatter(
            $this->seleniumIdeTestData
        );
    }
    
    protected function getTestName()
    {
        return $this->seleniumIdeTestData['test_name'];
    }
    
    protected function getTestMethodName()
    {
        return str_replace(' ', '', ucwords($this->seleniumIdeTestData['test_name']));
    }
    
    protected function getTestMethodContent()
    {
        return $this->seleniumRCToBehatMinkFormattr->format();
    }
    
    protected function getTestFeatureFileName()
    {
        return str_replace(' ', '_', $this->seleniumIdeTestData['test_name']).'.feature';
    }
    
    protected function createFileStructure()
    {
        $filename = 'features/bootstrap/';
        if (!file_exists($filename)) {
            mkdir($filename, 0777,true);
        }
    }
    
    protected function generateContextFile()
    {
        $fileName = 'features/bootstrap/FeatureContext.php';
        $data = <<<FILE
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

    public function {$this->getTestMethodName()}()
    {
    {$this->seleniumRCToBehatMinkFormatter->format()}
    }
}
FILE;
        file_put_contents($fileName, $data);
    }
    
    protected function generateFeatureFile()
    {
        $fileName = 'features/' . $this->getTestFeatureFileName();
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
        $this->createFileStructure();
        $this->generateContextFile();
        $this->generateFeatureFile();
    }
}
