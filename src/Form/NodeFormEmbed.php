<?php

namespace Drupal\systemix\Form;

use Drupal\systemix\Entity\Field;

class NodeFormEmbed
{
    protected $__field_name;
    protected $__parent;
    protected $__current_field;

    /**
     *
     */
    public function __construct($field_name, NodeForm $parent) {
        $this->__field_name = $field_name;
        $this->__parent = $parent;
    }

    /**
     *
     */
    public function getValue($modifier)
    {
        $current_field = $this->__current_field;
        $this->__current_field = null;
        $field_name = $this->__field_name;

        if (field_info_field($field_name)['cardinality'] != 1) {
            return; // Belum support.
        }
        if (field_info_field($current_field)['cardinality'] != 1) {
            return; // Belum support.
        }
        $column = Field::getColumnIdentifierByType(field_info_field($field_name)['type']);
        $language = null;
        if (isset($this->__parent->getForm()[$field_name]['#language'])) {
            $language = $this->__parent->getForm()[$field_name]['#language'];
        }
        $bundle_name = $this->__parent->node()->getType();
        $value = null;
        switch (field_info_instance('node', $field_name, $bundle_name)['widget']['type']) {
            case 'inline_entity_form_single':
                if (isset($this->__parent->getFormState()['values'][$field_name][$language]['form'][$current_field][$language][0][$column])) {
                    $value = $this->__parent->getFormState()['values'][$field_name][$language]['form'][$current_field][$language][0][$column];
                }
                break;
        }
        switch ($modifier) {
            case 'machine_name':
                switch (field_info_field($current_field)['type']) {
                    case 'entityreference':
                        switch (field_info_field($current_field)['settings']['target_type']) {
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
    public function __get($field_name)
    {
        $this->__current_field = $field_name;
        return $this;
    }


    /**
     *
     */
    public function setValueBy($modifier, $_value)
    {
        $current_field = $this->__current_field;
        $this->__current_field = null;
        $field_name = $this->__field_name;
        $value = null;
        switch ($modifier) {
            case 'machine_name':
                $value = Field::getIdentifierByMachineName($current_field, $_value);
                break;
        }
        return $this->{$current_field}->setValue($value);
    }

    /**
     *
     */
    public function setValue($value)
    {
        $current_field = $this->__current_field;
        $this->__current_field = null;
        $field_name = $this->__field_name;
        $parent_field_info_field = field_info_field($field_name);
        $child_field_info_field = field_info_field($current_field);

        if (field_info_field($field_name)['cardinality'] != 1) {
            return; // Belum support.
        }
        if (field_info_field($current_field)['cardinality'] != 1) {
            return; // Belum support.
        }
        $column = Field::getColumnIdentifierByType(field_info_field($field_name)['type']);
        $language = null;
        if (isset($this->__parent->getForm()[$field_name]['#language'])) {
            $language = $this->__parent->getForm()[$field_name]['#language'];
        }
        $bundle_name = $this->__parent->node()->getType();
        $form = $this->__parent->getForm();
        $form_state = $this->__parent->getFormState();

        switch (field_info_instance('node', $field_name, $bundle_name)['widget']['type']) {
            case 'inline_entity_form_single':
                 switch ($parent_field_info_field['type']) {
                    case 'entityreference':
                        switch ($parent_field_info_field['settings']['handler']) {
                            case 'base': // Simple selection.
                                $entity_type_target = $parent_field_info_field['settings']['target_type'];
                                $entity_bundles_target = $parent_field_info_field['settings']['handler_settings']['target_bundles'];
                                $child_bundles = $child_field_info_field['bundles'][$entity_type_target];
                                $bundles = array_intersect($child_bundles, $entity_bundles_target);
                                if (is_array($bundles)) {
                                    // Harusnya cuma satu, kalo tidak satu, berarti tidak
                                    // sesuai konvensi.
                                    $child_bundle_name = array_shift($bundles);
                                    // $tahu = field_info_instance($entity_type_target, $current_field, $child_bundle_name)['widget']['type'];

                                    switch (field_info_instance($entity_type_target, $current_field, $child_bundle_name)['widget']['type']) {
                                        case 'options_select':
                                            $form[$field_name][$language]['form'][$current_field][$language]['#value'] = $value;
                                            $form_state['values'][$field_name][$language]['form'][$current_field][$language][0][$column] = $value;
                                            break;

                                        case 'entityreference_autocomplete':
                                            // Do something.
                                            $form[$field_name][$language]['form'][$current_field][$language][0][$column]['#value'] = 'Dummy ('. $value . ')';
                                            $form_state['values'][$field_name][$language]['form'][$current_field][$language][0][$column] = $value;
                                            break;
                                        case 'entityreference_autocomplete_tags':
                                            // todo.
                                            break;
                                    }
                                }
                                break;
                        }
                        break;
                }
                break;
        }
        $this->__parent->setForm($form);
        $this->__parent->setFormState($form_state);
        // todo, juga form state.
    }

    /**
     *
     */
    public function hide()
    {
        $current_field = $this->__current_field;
        $this->__current_field = null;
        $field_name = $this->__field_name;

        if (field_info_field($field_name)['cardinality'] != 1) {
            return; // Belum support.
        }
        $column = Field::getColumnIdentifierByType(field_info_field($field_name)['type']);
        $language = null;
        if (isset($this->__parent->getForm()[$field_name]['#language'])) {
            $language = $this->__parent->getForm()[$field_name]['#language'];
        }
        $form = $this->__parent->getForm();
        // Spesific field.
        switch ($current_field) {
            case 'title':
                $form[$field_name][$language]['form'][$current_field]['#access'] = false;
                $form[$field_name][$language]['form'][$current_field]['#required'] = false;
                $this->__parent->setForm($form);
                return;
        }
        // Other...
        $bundle_name = $this->__parent->node()->getType();
        switch (field_info_instance('node', $field_name, $bundle_name)['widget']['type']) {
            case 'inline_entity_form_single':
                $form[$field_name][$language]['form'][$current_field][$language]['#access'] = false;
                $form[$field_name][$language]['form'][$current_field][$language]['#required'] = false;
                $delta = -1;
                while (isset($form[$field_name][$language]['form'][$current_field][$language][++$delta])) {
                    $form[$field_name][$language]['form'][$current_field][$language][$delta]['#access'] = false;
                    $form[$field_name][$language]['form'][$current_field][$language][$delta]['#required'] = false;
                }
                break;
        }
        $this->__parent->setForm($form);
    }

}


