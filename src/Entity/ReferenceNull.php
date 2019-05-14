<?php

namespace Drupal\systemix\Entity;

class ReferenceNull
{
    /**
     * Mengembalikan object ini.
     */
    function __get($name) {
        return $this;
    }

    /**
     *
     */
    public function value(){
        return null;
    }
}
