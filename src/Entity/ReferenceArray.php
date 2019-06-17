<?php

namespace Drupal\systemix\Entity;

class ReferenceArray implements \Iterator
{
    private $position = 0;
    protected $items;
    protected $field_info;

    function __construct(Array $array, $info) {
        $this->info = $info;
        $this->items = $array;
        return $this;
    }

    function __get($name) {
        return $this;
    }

    public function getValue() {
        return null;
    }

    public function each($callback) {
        $entity_type = $this->info['field']['settings']['target_type'];
        foreach ($this->items as $item) {
            call_user_func_array($callback, [new Entity($entity_type, $item)]);
        }
    }

    /**
     *
     */
    public function current()
    {
        return $this->items[$this->position];
    }

    /**
     *
     */
    public function key()
    {
        return $this->position;
    }
    /**
     *
     */
    public function next()
    {
        ++$this->position;
    }
    /**
     *
     */
    public function rewind()
    {
        $this->position = 0;
    }
    /**
     *
     */
    public function valid()
    {
        return isset($this->items[$this->position]);
    }

    /**
     *
     */
    public function shift()
    {
        $entity_type = $this->info['field']['settings']['target_type'];
        $item = array_shift($this->items);
        if ($item === null) {
            return new ReferenceNull;
        }
        return new Entity($entity_type, $item);
    }


}
