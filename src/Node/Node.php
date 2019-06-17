<?php

namespace Drupal\systemix\Node;

use Drupal\systemix\Entity\Entity;
use Drupal\systemix\User\User;

class Node extends Entity
{

    // protected $node;
    // protected $entity;
    protected $author;


    /**
     *
     */
    public function __construct($nodeObject)
    {
        parent::__construct('node', $nodeObject);
        return $this;
    }

    /**
     *
     */
    public function isNew()
    {
        return empty($this->entity_load->nid);
    }

    /**
     *
     */
    public function isExists()
    {
        return !empty($this->entity_load->nid);
    }

    /**
     *
     */
    // public function __get($field)
    // {
        // if (null == $this->entity) {
            // $this->entity = new Entity('node', $this->node);
        // }
        // return $this->entity->{$field};
    // }

    /**
     *
     */
    public function author()
    {
        if (null === $this->author) {
            $this->author = new User($this->entity_load->uid);
        }
        return $this->author;
    }

    /**
     *
     */
    public function getType()
    {
        return $this->entity_load->type;
    }

    /**
     *
     */
    /* public function save()
    {
        if (null == $this->entity) {
            $this->entity = new Entity('node', $this->node);
        }

        $this->entity->save();
        // return $this;
    } */


}
