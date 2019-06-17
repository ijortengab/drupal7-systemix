<?php

namespace Drupal\systemix\Form;

use Drupal\systemix\Entity\Field;

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
    public function onChanged(NodeFormConditionalEvent $event) {
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
        $field_name = $this->field_name;
        if ($this->parent->isInitialize() && $this->parent->node()->isExists()) {
            return $this->parent->node()->$field_name->getValue($modifier);
        }

        // dapatkan informasi column dan field type.
        $info = field_info_field($field_name);
        if (null === $info) {
            // kemungkinan pseudo element.
            $value = null;
            switch ($modifier) {
                case 'raw':
                    $form_state = $this->parent->getFormState();
                    if (isset($form_state['input'][$field_name])) {
                        $value = $form_state['input'][$field_name];
                    }
                    break;
            }
            return $value;
        }

        $cardinality = $info['cardinality'];
        if ($cardinality != 1) {
            return; // Belum support.
        }
        $column = Field::getColumnIdentifierByType($info['type']);
        $language = null;
        if (isset($this->parent->getForm()[$this->field_name]['#language'])) {
            $language = $this->parent->getForm()[$this->field_name]['#language'];
        }
        $value = null;
        if (isset($this->parent->getFormState()['values'][$this->field_name][$language][0][$column])) {
            $value = $this->parent->getFormState()['values'][$this->field_name][$language][0][$column];
        }
        switch ($modifier) {
            case 'column':
                if (isset($this->parent->getFormState()['values'][$this->field_name][$language][0])) {
                    $value = $this->parent->getFormState()['values'][$this->field_name][$language][0];
                }
                break;
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
        $field_name = $this->field_name;
        if ($this->parent->isInitialize() && $this->parent->node()->isExists()) {
            $value_init = $this->parent->node()->$field_name->getValue();
            if ($this->parent->node()->$field_name->getValue()) {
                return true;
            }
            else {
                return false;
            };
        }

        // dapatkan informasi column dan field type
        $info = field_info_field($field_name);
        $bundle_name = $this->parent->node()->getType();
        $field_type = $info['type'];
        $widget_type = field_info_instance('node', $field_name, $bundle_name)['widget']['type'];
        $cardinality = $info['cardinality'];
        if ($cardinality != 1) {
            return; // Belum support.
        }
        $column = Field::getColumnIdentifierByType($info['type']);
        $language = null;
        if (isset($this->parent->getForm()[$this->field_name]['#language'])) {
            $language = $this->parent->getForm()[$this->field_name]['#language'];
        }
        $value = null;
        if (isset($this->parent->getFormState()['values'][$this->field_name][$language][0][$column])) {
            $value = $this->parent->getFormState()['values'][$this->field_name][$language][0][$column];
            // Jika type = checkbox dan $value = 0, maka itu artinya tidak ada value.
            if ($value === 0 && $field_type == 'list_boolean' && $widget_type == 'options_onoff') {
                $value = null;
            }
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
        $this->onDisplayChanged();
    }

    /**
     *
     */
    protected function onDisplayChanged()
    {
        $delfi = true;
        $event = new NodeFormConditionalEvent($this);
        $namespace = $this->field_name.'.on_display_changed';
        $event->setNameSpace($namespace);
        $this->parent->getDispatcher()->dispatch($namespace, $event);
    }

    /**
     *
     */
    public function isVisible()
    {
        // $visibility = $this->visibility;

        if (in_array('hide', $this->visibility)) {
            return false;
        }
        elseif (in_array('show', $this->visibility)) {
            return true;
        }
        return false;
        // return $this;
    }


    /**
     *
     */
    public function setValueBy($modifier, $_value)
    {
        $field_name = $this->field_name;
        $value = null;
        switch ($modifier) {
            case 'machine_name':
                $value = Field::getIdentifierByMachineName($field_name, $_value);
                break;
        }
        return $this->setValue($value);
    }

    /**
     *
     */
    public function setValue($value)
    {
        $field_name = $this->field_name;
        if (field_info_field($field_name)['cardinality'] != 1) {
            return; // Belum support.
        }
        $column = Field::getColumnIdentifierByType(field_info_field($field_name)['type']);
        $language = null;
        if (isset($this->parent->getForm()[$field_name]['#language'])) {
            $language = $this->parent->getForm()[$field_name]['#language'];
        }
        $bundle_name = $this->parent->node()->getType();
        $anu = field_info_instance('node', $field_name, $bundle_name)['widget']['type'];
        $form_state = $this->parent->getFormState();
        $form = $this->parent->getForm();
        switch (field_info_instance('node', $field_name, $bundle_name)['widget']['type']) {
            case 'options_select':
                $form_state['values'][$field_name][$language][0][$column] = $value;
                $form[$field_name][$language]['#value'] = $value;
                break;

            case 'text_textfield':
                $form_state['values'][$field_name][$language][0][$column] = $value;
                $form[$field_name][$language][0][$column]['#value'] = $value;
                break;
        }
        $this->parent->setFormState($form_state);
        $this->parent->setForm($form);
    }






}

