<?php

namespace Drupal\systemix\Form;

class NodeFormElement
{
    protected $field_name;
    protected $parent;
    protected $callback;
    protected $visibility = [];

    /**
     *
     */
    public function __construct($field_name, NodeForm $parent) {
        $this->field_name = $field_name;
        $this->parent = $parent;
    }

    /**
     *
     */
    public function onChanged(NodeFormEvent $event) {
        $event->setElementListener($this);
        $namespace = $event->getNameSpace();
        return call_user_func($this->callback[$namespace], $event);
    }

    /**
     *
     */
    public function setProcess($namespace, Callable $callback)
    {
        $this->callback[$namespace] = $callback;
    }

    /**
     *
     */
    public function getValue($modifier = null)
    {
        $isRebuild = $this->parent->isRebuild();
        $isNew = $this->parent->node()->isNew();
        $isExists = $this->parent->node()->isExists();
        $field_name = $this->field_name;
        if ($this->parent->isInitialize() && $this->parent->node()->isExists()) {
            return $this->parent->node()->$field_name->value($modifier);
        }

        // dapatkan informasi column dan field type
        $info = field_info_field($this->field_name);
        $cardinality = $info['cardinality'];
        if ($cardinality != 1) {
            return; // Belum support.
        }
        $column = null;
        switch ($info['type']) {
            case 'entityreference':
                $column = 'target_id';
                break;
        }
        $language = null;
        if (isset($this->parent->getForm()[$this->field_name]['#language'])) {
            $language = $this->parent->getForm()[$this->field_name]['#language'];
        }
        $value = null;
        if (isset($this->parent->getFormState()['values'][$this->field_name][$language][0][$column])) {
            $value = $this->parent->getFormState()['values'][$this->field_name][$language][0][$column];
        }
        switch ($modifier) {
            case 'machine_name':
                switch ($info['type']) {
                    case 'entityreference':
                        switch ($info['settings']['target_type']) {
                            case 'taxonomy_term':
                                $conditions = array('tid' => ($value));
                                $result = entity_load('taxonomy_term', false, $conditions); // Return Array.
                                $result = array_shift($result);
                                if (isset($result->machine_name)) {
                                    $value = $result->machine_name;
                                }
                                break;
                        }
                        break;
                }
                break;
        }
        return $value;
    }
    /**
     *
     */
    public function hasValue()
    {
        // dapatkan informasi column dan field type
        $info = field_info_field($this->field_name);
        $cardinality = $info['cardinality'];
        if ($cardinality != 1) {
            return; // Belum support.
        }
        $column = null;
        switch ($info['type']) {
            case 'entityreference':
                $column = 'target_id';
                break;
        }
        $language = null;
        if (isset($this->parent->getForm()[$this->field_name]['#language'])) {
            $language = $this->parent->getForm()[$this->field_name]['#language'];
        }
        $value = null;
        if (isset($this->parent->getFormState()['values'][$this->field_name][$language][0][$column])) {
            $value = $this->parent->getFormState()['values'][$this->field_name][$language][0][$column];
        }
        return ($value !== null);
    }

    /**
     *
     */
    public function show()
    {
        $this->visibility[] = 'show';
        $this->onDisplayChanged();
    }

    /**
     *
     */
    public function hide()
    {
        $this->visibility[] = 'hide';
        // $this->onDisplayChanged();
    }

    /**
     *
     */
    protected function onDisplayChanged()
    {
        $delfi = true;
        $event = new NodeFormEvent($this);
        $namespace = $this->field_name.'.on_display_changed';
        $event->setNameSpace($namespace);
        $this->parent->getDispatcher()->dispatch($namespace, $event);
    }

    /**
     *
     */
    public function isVisible()
    {
        $visibility = $this->visibility;
        if (in_array('hide', $this->visibility)) {
            return false;
        }
        elseif (in_array('show', $this->visibility)) {
            return true;
        }
        return false;
        // return $this;
    }






}

