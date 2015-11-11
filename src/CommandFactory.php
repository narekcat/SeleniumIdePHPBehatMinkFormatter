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
    public function __construct($command)
    {
        $this->command = $command;
    }
    abstract public function toBehatMink();
}

class Open extends BaseCommand
{
    public function toBehatMink()
    {
        return "\t\$this->getSession()->visit(\"{$this->command['target']}\");\n";
    }
}

class Type extends BaseCommand
{
    public function toBehatMink()
    {
        $selectorType = strstr($this->command['target'], '=', true);
        $selector = substr(
            $this->command['target'],
            strpos($this->command['target'], '=') + 1
        );
        if ($selectorType === 'id') {
            return "\t\$this->getSession()->fillField(\"{$selector}\", "
            . "\"{$this->command['value']}\");\n";
        }
    }
}

class Click extends BaseCommand
{
    public function toBehatMink()
    {
        $selectorType = strstr($this->command['target'], '=', true);
        $selector = substr(
            $this->command['target'],
            strpos($this->command['target'], '=') + 1
        );
        if ($selectorType === 'id' || $selectorType === 'title' ||
            $selectorType === 'alt' || $selectorType === 'text'
        ) {
            return "\t\$this->getSession()->clicLink(\"{$selector}\");\n";
        }
        if ($selectorType === 'css') {
            
        }
        if ($selectorType === 'xpath') {
            
        }
    }
}

class ClickAndWait extends BaseCommand
{
    public function toBehatMink()
    {
        return "\t\$this->getSession()->pressButton(\"{$this->command['target']}\");\n"
        . "\t\$this->getSession()->wait(5000);\n";
    }
}

class Select extends BaseCommand
{
    public function toBehatMink()
    {
        return "\t\$this->getSession()->selectOption(\"{$this->command['target']}\", "
        . "\"{$this->command['value']}\");\n";
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
            . "'{$this->command['value']}');\n";
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
            . "(\"{$selector}\")');\n";
        }
    }
}
