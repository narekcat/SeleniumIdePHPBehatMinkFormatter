<?php

class CommandFactory
{
    public function __construct()
    {
        
    }
    
    public static function build($type)
    {
        if ($type == '') {
            throw new Exception('Invalid command type.');
        } else {
            $className = ucfirst($type);
            if (class_exists($className)) {
                return new $className();
            } else {
                throw new Exception('Command type not found.');
            }
        }
    }
}

interface CommandInterface
{
    public function toBehatMink();
}

class Open implements CommandInterface
{
    public function toBehatMink()
    {
        
    }
}

class Type implements CommandInterface
{
    public function toBehatMink()
    {
        
    }
}

class ClickAndWait implements CommandInterface
{
    public function toBehatMink()
    {
        
    }
}

class Select implements CommandInterface
{
    public function toBehatMink()
    {
        
    }
}

class SendKeys implements CommandInterface
{
    public function toBehatMink()
    {
        
    }
}

class WaitForElementPresent implements CommandInterface
{
    public function toBehatMink()
    {
        
    }
}