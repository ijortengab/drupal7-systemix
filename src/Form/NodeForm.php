<?php

namespace Drupal\systemix\Form;

use Drupal\systemix\Node\Node;
use Symfony\Component\EventDispatcher\EventDispatcher;

class NodeForm
{

    protected $elements = [];
    protected $form_id;
    protected $form;
    protected $form_state;
    protected $current_field;
    protected $current_behaviour;
    protected $node;
    protected $dispatcher;
    protected $event;
    protected $conditional = [];

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
    public function getFormState()
    {
        return $this->form_state;
    }

    /**
     *
     */
    public function getDispatcher()
    {
        return $this->dispatcher;
    }

    /**
     *
     */
    protected function getElement($field_name)
    {
        if (array_key_exists($field_name, $this->elements)) {
            return $this->elements[$field_name];
        }
        $this->elements[$field_name] = new NodeFormElement($field_name, $this);
        return $this->elements[$field_name];
    }

    /**
     *
     */
    public function __construct($form_id, &$form, &$form_state) {
        $this->form_id = $form_id;
        $this->form =& $form;
        $this->form_state =& $form_state;
    }

    /**
     *
     */
    public function __get($name) {
        $this->current_field = $name;
        return $this;
    }

    /**
     *
     */
    public function isValue($value)
    {
        $field = $this->current_field;
        $this->current_field = null;
        return (
            $this->isInitialize() && $this->$field->isOriginalValue($value) ||
            $this->isRebuild() && $this->$field->isCurrentValue($value)
        );
    }

    /**
     *
     */
    public function isFilled()
    {
        $field = $this->current_field;
        $this->current_field = null;
        return (
            $this->isInitialize() && $this->$field->isOriginalValueFilled() ||
            $this->isRebuild() && $this->$field->isCurrentValueFilled()
        );
    }

    public function isChecked()
    {
        $field = $this->current_field;
        $this->current_field = null;
        return (
            $this->isInitialize() && $this->$field->isOriginalValueChecked() ||
            $this->isRebuild() && $this->$field->isCurrentValueChecked()
        );
    }

    /**
     *
     */
    public function isCurrentValue($value)
    {
        $this->modifyValue($value);
        $form_state = $this->form_state;
        $field = $this->current_field;
        $this->current_field = null;
        return (
            isset($form_state['input'][$field][LANGUAGE_NONE]) &&
            $form_state['input'][$field][LANGUAGE_NONE] == $value
        ) ||
        (
            isset($form_state['values'][$field][LANGUAGE_NONE][0]['target_id']) &&
            $form_state['values'][$field][LANGUAGE_NONE][0]['target_id'] == $value
        );
    }

    /**
     *
     */
    public function isOriginalValue($value)
    {
        $this->modifyValue($value);
        $form_state = $this->form_state;
        $field = $this->current_field;
        $this->current_field = null;
        return isset($form_state['node']->$field[LANGUAGE_NONE]) &&
        $form_state['node']->$field[LANGUAGE_NONE][0]['target_id'] == $value;
    }

    /**
     *
     */
    public function isCurrentValueFilled()
    {
        $form_state = $this->form_state;
        $field = $this->current_field;
        $this->current_field = null;
        return
            isset($form_state['input'][$field][LANGUAGE_NONE]) ||
            isset($form_state['values'][$field][LANGUAGE_NONE][0]['target_id']);
    }

    /**
     *
     */
    public function isOriginalValueFilled()
    {
        $form_state = $this->form_state;
        $field = $this->current_field;
        $this->current_field = null;
        return isset($form_state['node']->$field[LANGUAGE_NONE]);
    }

    /**
     *
     */
    public function isCurrentValueChecked()
    {
        $form_state = $this->form_state;
        $field = $this->current_field;
        $this->current_field = null;
        return
            (
                (isset($form_state['input'][$field][LANGUAGE_NONE][0]['value']) && $form_state['input'][$field][LANGUAGE_NONE][0]['value'] == 1) ||
                (isset($form_state['values'][$field][LANGUAGE_NONE][0]['value']) && $form_state['values'][$field][LANGUAGE_NONE][0]['value'] == 1)
            );
    }

    /**
     *
     */
    public function isOriginalValueChecked()
    {
        $form_state = $this->form_state;
        $field = $this->current_field;
        $this->current_field = null;
        return (
            isset($form_state['node']->$field[LANGUAGE_NONE][0]['value']) &&
            $form_state['node']->$field[LANGUAGE_NONE][0]['value'] == 1);
    }

    /**
     *
     */
    public function setBehaviourValue($behaviour)
    {
        $this->current_behaviour = $behaviour;
    }

    /**
     *
     */
    public function resetBehaviourValue()
    {
        $this->current_behaviour = null;
    }

    /**
     *
     */
    public function modifyValue(&$value)
    {
        switch ($this->current_behaviour) {
            case 'taxonomy':
                $conditions = array('machine_name' => trim($value));
                $result = entity_load('taxonomy_term', false, $conditions);
                if (empty($result)) {
                    return;
                }
                $result = array_shift($result);
                $value = $result->tid;
                break;

            case '':
                // Do something.
                break;

            default:
                // Do something.
                break;
        }
        // return $this;
    }


    /**
     * Mencari tahu form init atau hasil rebuild, kita tidak melihat
     * pada key `values` melainkan pada key `input`. Karena module
     * entityconnect, ketika mereturn form parent, dia mengosongkan
     * `values`, sementara status form parent adalah non init.
     */
    public function isInitialize()
    {
        return empty($this->form_state['input']);
    }

    /**
     *
     */
    public function isRebuild()
    {
        return !empty($this->form_state['input']);
    }

    /**
     *
     */
    public function show()
    {
        $this->form[$this->current_field]['#access'] = true;
        $language = isset($this->form[$this->current_field]['#language']) ? $this->form[$this->current_field]['#language'] : null;
        if (isset($this->form[$this->current_field][$language])) {
            $this->form[$this->current_field][$language]['#access'] = true;
        }
        // Perlu hide/show juga pada delta untuk field type `file`.
        $delta = -1;
        while (isset($this->form[$this->current_field][$language][++$delta])) {
            $this->form[$this->current_field][$language][$delta]['#access'] = true;
        }
        $this->current_field = null;
    }

    /**
     *
     */
    public function hide()
    {
        // Required perlu dipaksa `false` agar konsisten dengan hide-nya.
        $this->form[$this->current_field]['#access'] = false;
        $language = isset($this->form[$this->current_field]['#language']) ? $this->form[$this->current_field]['#language'] : null;
        if (isset($this->form[$this->current_field][$language])) {
            $this->form[$this->current_field][$language]['#access'] = false;
            $this->form[$this->current_field][$language]['#required'] = false;
        }
        // Perlu hide/show juga pada delta untuk field type `file`.
        $delta = -1;
        while (isset($this->form[$this->current_field][$language][++$delta])) {
            $this->form[$this->current_field][$language][$delta]['#access'] = false;
            $this->form[$this->current_field][$language][$delta]['#required'] = false;
        }
        $this->current_field = null;
    }

    /**
     *
     */
    public function addAjax($array)
    {
        $element = $this->form[$this->current_field];
        $language = isset($this->form[$this->current_field]['#language']) ? $this->form[$this->current_field]['#language'] : null;
        $elements = ['radios'];
        if (isset($this->form[$this->current_field][$language]['#type']) && in_array($this->form[$this->current_field][$language]['#type'], $elements)) {
            $this->form[$this->current_field][$language]['#ajax'] = $array;
        }
        $this->current_field = null;
    }

    /**
     *
     */
    public function disabled()
    {
        $field = $this->current_field;
        if ($field == 'title') {
            $this->form['title']['#disabled'] = true;
            return;
        }
        $this->form[$this->current_field]['#disabled'] = true;
        $language = isset($this->form[$this->current_field]['#language']) ? $this->form[$this->current_field]['#language'] : null;
        if (isset($this->form[$this->current_field][$language])) {
            $this->form[$this->current_field][$language]['#disabled'] = true;
        }
        // Perlu hide/show juga pada delta untuk field type `file`.
        $delta = -1;
        while (isset($this->form[$this->current_field][$language][++$delta])) {
            $this->form[$this->current_field][$language][$delta]['#disabled'] = true;
        }
        $this->current_field = null;
    }

    /**
     *
     */
    public function setFormAttribute($key, $value)
    {
        $this->form['#'.$key] = $value;
        return $this;
    }

    /**
     *
     */
    public function node()
    {
        if (null === $this->node) {
            $this->node = new Node($this->form_state['node']);
        }
        return $this->node;
    }

    /**
     *
     */
    public function setFixedValue($value)
    {
        $field = $this->current_field;
        $element = $this->form[$field];
        $info = field_info_field($field);
        $language = isset($this->form[$this->current_field]['#language']) ? $this->form[$this->current_field]['#language'] : null;
        $delta = -1;
        while (isset($this->form[$this->current_field][$language][++$delta])) {
            if (isset($this->form[$this->current_field][$language][$delta]['#columns'])) {
                $columns = $this->form[$this->current_field][$language][$delta]['#columns'];
                while ($column = array_shift($columns)) {
                    if (isset($this->form[$this->current_field][$language][$delta][$column])) {
                        $type = isset($this->form[$this->current_field][$language][$delta][$column]['#type']);
                        switch ($type) {
                            case 'textfield':
                                !is_string($value) or $this->form[$this->current_field][$language][$delta][$column]['#value'] = $value;
                                break;
                        }
                    }
                }
            }
        }
        $this->current_field = null;
    }

    /**
     *
     */
    protected function prepareDispatcher()
    {
        if (null === $this->dispatcher) {
            $this->dispatcher = new EventDispatcher;
        }
        return $this;
    }

    /**
     *
     */
    public function modifyField()
    {

        // return $this;
    }

    /**
     *
     */
    public function listen($element_name, $event, $callback)
    {
        $this->prepareDispatcher();
        $field = $this->current_field;
        // $parts = explode('.', $namespace);
        $namespace = $element_name . '.'. $event;
        $info = [
            'namespace' => $namespace,
            'target_field' => $element_name,
            'event' => $event,
            'callback' => $callback,
        ];
        $this->conditional[$this->current_field]['listen'][$namespace] = $info;
        $this->conditional[$element_name]['event'][$event] = $info;
        if (isset($parts[0]) && isset($parts[1])) {
            $info = [
                'namespace' => $namespace,
                'target_field' => $parts[0],
                'event' => $parts[1],
                'callback' => $callback,
            ];
        }
        return $this;
    }

    /**
     *
     */
    public function executeConditionalField()
    {
        $c = $this->conditional;

        $ajax = [
            'callback' => 'custom_submission_return_form',
            'wrapper' => 'custom_submission--submission-node-form',
        ];
        $listener = [];
        $target_fields = [];
        // Prepare Ajax and Listener.
        foreach ($this->conditional as $field => $info) {
            if (isset($info['listen'])) {
                $list = $info['listen'];
                while ($listen = array_shift($list)) {
                    // $elemet = new NodeFormElement($field, $this);
                    $element = $this->getElement($field);
                    $element->setProcess($listen['namespace'], $listen['callback']);
                    switch ($listen['event']) {
                        case 'on_value_changed':
                            $this->dispatcher->addListener($listen['namespace'], [$element, 'onChanged']);
                            $this->{$listen['target_field']}->addAjax($ajax);
                            break;

                        case 'on_display_changed':
                            $this->dispatcher->addListener($listen['namespace'], [$element, 'onChanged']);
                            break;
                    }
                }
            }
        }

        // Dispatch has value changed.
        foreach ($this->conditional as $field => $info) {
            if (isset($info['event']['on_value_changed'])) {
                $element = $this->getElement($field);
                $event = new NodeFormEvent($element);
                $namespace = $field.'.on_value_changed';
                $event->setNameSpace($namespace);
                $this->dispatcher->dispatch($namespace, $event);
            }
        }

        // Decision visibility.
        foreach ($this->conditional as $field => $info) {
            if (isset($info['listen'])) {
                $element = $this->getElement($field);
                if ($element->isVisible()) {
                    $this->{$field}->show();
                }
                else {
                    $this->{$field}->hide();
                }
            }
        }

    }


}
