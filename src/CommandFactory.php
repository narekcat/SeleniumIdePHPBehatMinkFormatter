<?php
/**
 * The file contains commands factory.
 */

namespace Fouraitch;

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
        $className = 'Fouraitch\\' . ucfirst($type);
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
        $selectorType = strstr($target, '=', true);
        $selector = str_replace("'", "\'", substr($target, strpos($target, '=') + 1));
        $result = '';
        if (!self::$isPageCreated) {
            $result = "\t\$page = \$this->getSession()->getPage();\n";
            self::$isPageCreated = true;
        }
        if ($selectorType === 'id') {
            return $result . "\t\$element = \$page->fileById('{$selector}');\n";
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
        if (strstr($selector, 'document.') === 0) {
            return "\t\$element = \$this->getSession()->evaluateScript('{$selector}');\n";
        }
        if (strstr($selector, '//') === 0) {
            return $result . "\t\$element = \$page->find('xpath', '{$selector}');\n";
        }
        return $result . "\t\$element = \$page->fileById('{$selector}');\n";
    }
}

class Open extends BaseCommand
{
    public function toBehatMink()
    {
        return "\t\$this->getSession()->visit(\$this->BaseUrl . \"{$this->command['target']}\");\n\n";
    }
}

class Type extends BaseCommand
{
    public function toBehatMink()
    {
        return $this->getElementByTarget($this->command['target'])
            . "\t\$element->setValue('{$this->command['value']}');\n\n";
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
        return "\t\$this->getSession()->selectOption(\"{$this->command['target']}\", "
        . "\"{$this->command['value']}\");\n\n";
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
