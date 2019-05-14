<?php

namespace Drupal\systemix\Node;

use Drupal\systemix\Entity\Entity;
use Drupal\systemix\User\User;

class Node
{

    protected $node;
    protected $entity;
    protected $author;


    /**
     *
     */
    public function __construct($nodeObject)
    {
        $this->node = $nodeObject;
        return $this;
    }

    /**
     *
     */
    public function isNew()
    {
        return empty($this->node->nid);
    }

    /**
     *
     */
    public function isExists()
    {
        return !empty($this->node->nid);
    }

    /**
     *
     */
    public function __get($field)
    {
        if (null == $this->entity) {
            $this->entity = new Entity('node', $this->node);
        }
        return $this->entity->{$field};
    }



    /**
     *
     */
    public function author()
    {
        if (null === $this->author) {
            $this->author = new User($this->node->uid);
        }
        return $this->author;
    }

}
