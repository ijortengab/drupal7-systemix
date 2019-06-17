<?php

namespace Drupal\systemix\Entity;

class EntityEmpty extends Entity
{
    protected $entity_empty = false;
    // protected $entity_type;
    // protected $current_field_name;

    /**
     * Mengembalikan object ini.
     */
    /* public function __get($name) {
        $current_field_name = $this->current_field_name;
        if (null === $this->current_field_name) {
            $this->current_field_name = $name;
            return $this;
        }
    } */

    /**
     *
     */
    public function __construct($entity_type)
    {
        $this->entity_type = $entity_type;
        return $this;
    }


    /**
     *
     */
    public function getValue($modifier = null)
    {
        return null;
    }

    /**
     *
     */


    /**
     *
     */
    // public function save()
    // {

        // return $this;
    // }

}
