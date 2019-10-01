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
     * Implements of hook_node_access().
     */
    public static function disabledFormCreateDefault($content_type, $node, $op)
    {
        switch ($op) {
            case 'create':
                $type = is_string($node) ? $node : (isset($node->type) ? $node->type : null);
                if ($type == $content_type) {
                    $path = 'node/add/' . str_replace('_', '-', $content_type);
                    if (strpos(current_path(), $path) === 0) {
                        return NODE_ACCESS_DENY;
                    }
                }
                break;
        }
    }
    

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
