<?php

class CommandFactory
{
    public function __construct()
    {
        
    }
    
    public static function build($type, $command)
    {
        if ($type == '') {
            throw new Exception('Invalid command type.');
        } else {
            $className = ucfirst($type);
            if (class_exists($className)) {
                return new $className($command);
            } else {
                throw new Exception('Command type not found.');
            }
        }
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
        return "\t\$this->getSession()->visit({$this->command[1]});\n";
    }
}

class Type extends BaseCommand
{
    public function toBehatMink()
    {
        return "\t\$this->getSession()->fillFields({$this->command[1]}, "
        . "{$this->command[2]});\n";
    }
}

class ClickAndWait extends BaseCommand
{
    public function toBehatMink()
    {
        return "\t\$this->getSession()->pressButton({$this->command[1]});\n"
        . "\t\$this->getSession()->wait();\n";
    }
}

class Select extends BaseCommand
{
    public function toBehatMink()
    {
        return "\t\$this->getSession()->selectOption({$this->commandcommand[1]}, "
        . "{$this->commandcommand[2]});\n";
    }
}

class SendKeys extends BaseCommand
{
    public function toBehatMink()
    {
        return "\t\$this->getSession()->fillFields({$this->commandcommand[1]}, "
        . "{$this->commandcommand[2]});\n";
    }
}

class WaitForElementPresent extends BaseCommand
{
    public function toBehatMink()
    {
        return "\t\$this->getSession()->wait(10000, 'document.getElementById"
        . "('{$this->commandcommand[1]}')')";
    }
}