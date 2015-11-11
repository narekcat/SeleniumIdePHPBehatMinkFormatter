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
        return "\t\$this->getSession()->fillFields(\"{$this->command['target']}\", "
        . "\"{$this->command['value']}\");\n";
    }
}

class Click extends BaseCommand
{
    public function toBehatMink()
    {
        return "\t\$this->getSession()->clicLink(\"{$this->command['target']}\");\n";
    }
}

class ClickAndWait extends BaseCommand
{
    public function toBehatMink()
    {
        return "\t\$this->getSession()->pressButton(\"{$this->command['target']}\");\n"
        . "\t\$this->getSession()->wait();\n";
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
        return "\t\$this->getSession()->fillFields(\"{$this->command['target']}\", "
        . "\"{$this->command['value']}\");\n";
    }
}

class WaitForElementPresent extends BaseCommand
{
    public function toBehatMink()
    {
        return "\t\$this->getSession()->wait('target'0000, 'document.getElementById"
        . "(\"{$this->command['target']}\")')";
    }
}
