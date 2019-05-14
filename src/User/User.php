<?php

namespace Drupal\systemix\User;

use Drupal\systemix\Entity\Entity;

class User
{
    protected $user;
    protected $entity;

    /**
     *
     */
    public function __construct($userObject)
    {
        $this->user = $userObject;
        return $this;
    }

    /**
     *
     */
    public function __get($field)
    {
        if (null == $this->entity) {
            $this->entity = new Entity('user', $this->user);
        }
        return $this->entity->{$field};
    }
}
