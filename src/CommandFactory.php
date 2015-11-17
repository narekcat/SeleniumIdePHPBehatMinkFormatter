<?php
/**
 * The file contains commands factory.
 */

namespace Fouraitch\SeleniumIdeFormatter;

use Exception;

class CommandFactory
{
    public function __construct()
    {
        
    }
    
    public static function build($type, $command)
    {
        if ($type == '') {
            throw new Exception('Invalid command type.');
        }
        $className = 'Fouraitch\\SeleniumIdeFormatter\\' . ucfirst($type);
        if (!class_exists($className)) {
            throw new Exception("Command type({$className}) not found.");
        }
        return new $className($command);
    }
}

abstract class BaseCommand
{
    protected $command;
    protected static $isPageCreated = false;
    public function __construct($command)
    {
        $this->command = $command;
    }
    abstract public function toBehatMink();
    protected function getElementByTarget($target)
    {
        $result = '';
        if (!self::$isPageCreated) {
            $result = "\t\$page = \$this->getSession()->getPage();\n";
            self::$isPageCreated = true;
        }
        if (preg_match('/^(id|css|link|name|xpath)/', $target, $out)) {
            $selectorType = $out[0];
            $selector = str_replace("'", "\'", substr($target, strpos($target, '=') + 1));
            if ($selectorType === 'id') {
                return $result . "\t\$element = \$page->findById('{$selector}');\n";
            }
            if ($selectorType === 'css') {
                return $result . "\t\$element = \$page->find('css', '{$selector}');\n";
            }
            if ($selectorType === 'link') {
                return $result . "\t\$escapedValue = \$this->getSession()->getSelectorsHandler()->xpathLiteral('{$selector}');\n"
                        . "\t\$element = \$page->find('named', array('link', \$escapedValue));\n";
            }
            if ($selectorType === 'name') {
                return $result . "\t\$escapedValue = \$this->getSession()->getSelectorsHandler()->xpathLiteral('{$selector}');\n"
                        . "\t\$element = \$page->find('named', array('id_or_name', \$escapedValue));\n";
            }
            if ($selectorType === 'xpath') {
                return $result . "\t\$element = \$page->find('xpath', '{$selector}');\n";
            }
        }
        if (strpos($target, 'document.') === 0) {
            return "\t\$element = \$this->getSession()->evaluateScript('{$target}');\n";
        }
        if (strpos($target, '//') === 0) {
            return $result . "\t\$element = \$page->find('xpath', \"{$target}\");\n";
        }
        return $result . "\t\$element = \$page->fileById('{$target}');\n";
    }
}

class Open extends BaseCommand
{
    public function toBehatMink()
    {
        return "\t\$this->getSession()->visit(\$this->BaseUrl . \"{$this->command['target']}\");\n\n";
    }
}

//class Type extends BaseCommand
//{
//    public function toBehatMink()
//    {
//        $result = $this->getElementByTarget($this->command['target'])
////                . "\t\$element->sendKeys('{$this->command['value']}');\n";
////            . "\t\$element->setValue();\n"
//            . "\t\$element->focus();\n";
//        $valueAsArray = str_split($this->command['value']);
//        foreach ($valueAsArray as $inputChar) {
//            $asciiCodeOfInputChar = ord($inputChar);
//            $jsToEvaluate = "keyboardEvent = document.createEvent('KeyboardEvent');
//initMethod = typeof keyboardEvent.initKeyboardEvent !== 'undefined' ? 'initKeyboardEvent' : 'initKeyEvent';
//keyboardEvent[initMethod](
//    'keypress', // event type : keydown, keyup, keypress
//     true, // bubbles
//     true, // cancelable
//     window, // viewArg: should be window
//     false, // ctrlKeyArg
//     false, // altKeyArg
//     false, // shiftKeyArg
//     false, // metaKeyArg
//     {$asciiCodeOfInputChar}, // keyCodeArg : unsigned long the virtual key code, else 0
//     0 // charCodeArgs : unsigned long the Unicode character associated with the depressed key, else 0
//);
//document.dispatchEvent(keyboardEvent);";
//            $result .= "\t\$this->getSession()->evaluateScript(\"{$jsToEvaluate}\");\n";
//        }
//        return $result;
//    }
//}

class Type extends BaseCommand
{
    public function toBehatMink()
    {
        return $this->getElementByTarget($this->command['target']).
                "\t\$element->setValue('{$this->command['value']}');\n\n";
    }
}

class TypeAndWait extends BaseCommand
{
    public function toBehatMink()
    {
        return $this->getElementByTarget($this->command['target']).
                "\t\$element->setValue('{$this->command['value']}');\n"
                . "\t\$this->getSession()->wait(5000);\n\n";
    }
}

class Click extends BaseCommand
{
    public function toBehatMink()
    {
        return $this->getElementByTarget($this->command['target']).
                "\t\$element->click();\n\n";
    }
}

class ClickAndWait extends BaseCommand
{
    public function toBehatMink()
    {
        return $this->getElementByTarget($this->command['target'])
            ."\t\$element->click();\n"
            . "\t\$this->getSession()->wait(5000);\n\n";
    }
}

class Select extends BaseCommand
{
    public function toBehatMink()
    {
        $optionValueOrText = substr(
            $this->command['value'],
            strpos($this->command['value'], '=') + 1
        );
        return $this->getElementByTarget($this->command['target'])
            . "\t\$element->selectOption('{$optionValueOrText}');\n\n";
    }
}

class SelectAndWait extends BaseCommand
{
    public function toBehatMink()
    {
        $optionValueOrText = substr(
            $this->command['value'],
            strpos($this->command['value'], '=') + 1
        );
        return $this->getElementByTarget($this->command['target'])
            . "\t\$element->selectOption('{$optionValueOrText}');\n"
            . "\t\$this->getSession()->wait(5000);\n\n";
    }
}

class SendKeys extends BaseCommand
{
    public function toBehatMink()
    {
        $selectorType = strstr($this->command['target'], '=', true);
        $selector = substr(
            $this->command['target'],
            strpos($this->command['target'], '=') + 1
        );
        if ($selectorType === 'id' || $selectorType === 'css') {
            return "\t\$this->getSession()->fillField('{$selector}', "
            . "'{$this->command['value']}');\n\n";
        }
    }
}

class WaitForElementPresent extends BaseCommand
{
    public function toBehatMink()
    {
        $selectorType = strstr($this->command['target'], '=', true);
        $selector = substr(
            $this->command['target'],
            strpos($this->command['target'], '=') + 1
        );
        $selector = str_replace('"','\"' , $selector);
        $selector = str_replace("'","\'" , $selector);
        if ($selectorType === 'id' || $selectorType === 'css') {
            return "\t\$this->getSession()->wait(5000, 'document.getElementById"
            . "(\"{$selector}\")');\n\n";
        }
    }
}
