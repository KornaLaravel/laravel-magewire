<?php
/**
 * Livewire copyright © Caleb Porzio (https://github.com/livewire/livewire).
 * Magewire copyright © Willem Poortman 2024-present.
 * All rights reserved.
 *
 * Please read the README and LICENSE files for more
 * details on copyrights and license information.
 */
namespace Magewirephp\Magewire\Features\SupportAttributes;

use Magewirephp\Magewire\Component;
abstract class Attribute
{
    protected Component $component;
    protected $subTarget;
    protected $subName;
    protected AttributeLevel $level;
    protected $levelName;
    function __boot($component, AttributeLevel $level, $name = null, $subName = null, $subTarget = null)
    {
        $this->component = $component;
        $this->subName = $subName;
        $this->subTarget = $subTarget;
        $this->level = $level;
        $this->levelName = $name;
    }
    function getComponent()
    {
        return $this->component;
    }
    function getSubTarget()
    {
        return $this->subTarget;
    }
    function getSubName()
    {
        return $this->subName;
    }
    function getLevel()
    {
        return $this->level;
    }
    function getName()
    {
        return $this->levelName;
    }
    function getValue()
    {
        if ($this->level !== AttributeLevel::PROPERTY) {
            throw new \Exception('Can\'t set the value of a non-property attribute.');
        }
        return data_get($this->component->all(), $this->levelName);
    }
    function setValue($value, ?bool $nullable = false)
    {
        if ($this->level !== AttributeLevel::PROPERTY) {
            throw new \Exception('Can\'t set the value of a non-property attribute.');
        }
        if ($enum = $this->tryingToSetStringOrIntegerToEnum($value)) {
            if ($nullable) {
                $value = $enum::tryFrom($value);
            } else {
                $value = $enum::from($value);
            }
        }
        data_set($this->component, $this->levelName, $value);
    }
    protected function tryingToSetStringOrIntegerToEnum($subject)
    {
        if (!is_string($subject) && !is_int($subject)) {
            return;
        }
        $target = $this->subTarget ?? $this->component;
        $name = $this->subName ?? $this->levelName;
        $property = str($name)->before('.')->toString();
        $reflection = new \ReflectionProperty($target, $property);
        $type = $reflection->getType();
        // If the type is available, display its name
        if ($type instanceof \ReflectionNamedType) {
            $name = $type->getName();
            // If the type is a BackedEnum then return it's name
            if (is_subclass_of($name, \BackedEnum::class)) {
                return $name;
            }
        }
        return false;
    }
}