<?php

namespace Drupal\systemix\Form;

use Symfony\Component\EventDispatcher\Event;

class NodeFormWorkflowEvent extends Event
{
    protected $element_changed;
    protected $element_listener;
    protected $namespace;

    /**
     *
     */
    public function __construct()
    {
        return $this;
    }


    /**
     *
     */
    public function setForm($form)
    {
        $this->form = $form;
    }


    /**
     *
     */
    public function getForm()
    {
        return $this->form;
    }

    /**
     *
     */
    public function getElementChanged()
    {
        return $this->element_changed;
    }

    /**
     *
     */
    public function getElementListener()
    {
        return $this->element_listener;
    }

    /**
     *
     */
    public function setElementListener(NodeFormElement $element)
    {
        $this->element_listener = $element;
        return $this;
    }

    /**
     *
     */
    public function setNameSpace($namespace)
    {
        $this->namespace = $namespace;
        return $this;
    }

    /**
     *
     */
    public function getNameSpace()
    {
        return $this->namespace;
    }
}
