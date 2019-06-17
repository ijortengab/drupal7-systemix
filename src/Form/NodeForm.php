<?php

namespace Drupal\systemix\Form;

use Drupal\systemix\Node\Node;
use Symfony\Component\EventDispatcher\EventDispatcher;

/**
 * Seluruh property diberi prefix double underscore (__) untuk mencegah
 * kemiripan dengan nama field.
 */
class NodeForm
{

    protected $__elements = [];
    protected $__form;
    protected $__form_state;
    protected $__current_field;
    protected $__node;
    protected $__dispatcher;
    protected $__conditional = [];
    protected $__conditional_multilevel = false;
    protected $__embed = [];
    protected $__workflow = [];
    protected $__workflow_current_field;

    /**
     *
     */
    public function getForm()
    {
        return $this->__form;
    }

    /**
     *
     */
    public function getFormState()
    {
        return $this->__form_state;
    }

    /**
     *
     */
    public function setForm($form)
    {
        $this->__form = $form;
    }

    /**
     *
     */
    public function setFormState($form_state)
    {
        $this->__form_state = $form_state;
    }

    /**
     *
     */
    public function getDispatcher()
    {
        return $this->__dispatcher;
    }

    /**
     *
     */
    protected function getElement($field_name)
    {
        if (array_key_exists($field_name, $this->__elements)) {
            return $this->__elements[$field_name];
        }
        $this->__elements[$field_name] = new NodeFormElement($field_name, $this);
        return $this->__elements[$field_name];
    }

    /**
     *
     */
    public function addElement($element_name, $property)
    {
        $this->__form[$element_name] = $property;
        return $this;
    }


    /**
     *
     */
    protected function getEmbed($field_name)
    {
        if (array_key_exists($field_name, $this->__embed)) {
            return $this->__embed[$field_name];
        }
        $this->__embed[$field_name] = new NodeFormEmbed($field_name, $this);
        return $this->__embed[$field_name];
    }

    /**
     *
     */
    public function __construct(&$form, &$form_state) {
        $this->__form = &$form;
        $this->__form_state = &$form_state;
    }

    /**
     *
     */
    public function __get($field_name) {
        if (null !== $this->__current_field) {
            $current_field = $this->__current_field;
            $this->__current_field = null;
            // Ini berarti form embed.
            return $this->getEmbed($current_field)->{$field_name};
        }
        $this->__current_field = $field_name;
        return $this;
    }

    /**
     * Mencari tahu form init atau hasil rebuild, kita tidak melihat
     * pada key `values` melainkan pada key `input`. Karena module
     * entityconnect, ketika mereturn form parent, dia mengosongkan
     * `values`, sementara status form parent adalah non init.
     */
    public function isInitialize()
    {
        return empty($this->__form_state['input']);
    }

    /**
     *
     */
    public function isRebuild()
    {
        return !empty($this->__form_state['input']) && $this->__form_state['input']['form_build_id'] === $this->__form['form_build_id']['#value'];
    }

    /**
     *
     */
    public function show()
    {
        $current_field = $this->__current_field;
        $this->__current_field = null;
        $this->__form[$current_field]['#access'] = true;
        $language = isset($this->__form[$current_field]['#language']) ? $this->__form[$current_field]['#language'] : null;
        if (isset($this->__form[$current_field][$language])) {
            $this->__form[$current_field][$language]['#access'] = true;
        }
        // Perlu hide/show juga pada delta untuk field type `file`.
        $delta = -1;
        while (isset($this->__form[$current_field][$language][++$delta])) {
            $this->__form[$current_field][$language][$delta]['#access'] = true;
        }
    }

    /**
     *
     */
    public function hide()
    {
        $current_field = $this->__current_field;
        $this->__current_field = null;
        switch ($current_field) {
            case 'title':
                $this->__form[$current_field]['#access'] = false;
                $this->__form[$current_field]['#required'] = false;
                return;
        }

        // Attribute element jangan di-set #access = false, karena perlu
        // untuk menambahkan `prefix` dan `suffix` untuk kebutuhan ajax.
        // # $this->__form[$current_field]['#access'] = false;
        $language = isset($this->__form[$current_field]['#language']) ? $this->__form[$current_field]['#language'] : null;
        if (isset($this->__form[$current_field][$language])) {
            // Jika ada sub element
            // Required perlu dipaksa `false` agar konsisten dengan hide-nya.
            $this->__form[$current_field][$language]['#access'] = false;
            $this->__form[$current_field][$language]['#required'] = false;
        }
        // Perlu hide/show juga pada delta untuk field type `file`.
        $delta = -1;
        while (isset($this->__form[$current_field][$language][++$delta])) {
            $this->__form[$current_field][$language][$delta]['#access'] = false;
            $this->__form[$current_field][$language][$delta]['#required'] = false;
        }

    }

    /**
     *
     */
    public function addAjax($field_listener, $ajax_callback = null)
    {
        $current_field = $this->__current_field;

        $this->__current_field = null;
        $array = [
            'callback' => 'systemix_ajax_callback',
        ];
        if (is_callable($ajax_callback)) {
            $id = 'custom-wrapper-ajax-element-'.$field_listener;
            $this->{$field_listener}->addProperty('prefix', '<div id="'.$id.'">');
            $this->{$field_listener}->addProperty('suffix', '</div>');
            $array['wrapper'] = $id;
            $array['callback2'] = $ajax_callback;
        }
        else {
            $this->addProperty('prefix', '<div id="custom-wrapper-ajax">');
            $this->addProperty('suffix', '</div>');
            $array['wrapper'] = 'custom-wrapper-ajax';
            $array['callback2'] = [__CLASS__, 'ajaxCallbackReturnForm'];
        }
        $element = $this->__form[$current_field];
        $language = isset($this->__form[$current_field]['#language']) ? $this->__form[$current_field]['#language'] : null;
        // @todo, buat sebagai static atau constanct.
        $elements_available = ['radios', 'select', 'checkbox'];
        $type = isset($this->__form[$current_field][$language]['#type']) ? $this->__form[$current_field][$language]['#type'] : null;
        if (in_array($type, $elements_available)) {
            $this->__form[$current_field][$language]['#ajax'] = $array;
        }
        $waduh = $this->__form[$current_field][$language];

    }

    /**
     *
     */
    public function disabled()
    {
        $current_field = $this->__current_field;
        $this->__current_field = null;
        if ($current_field == 'title') {
            $this->__form['title']['#disabled'] = true;
            return;
        }
        $this->__form[$current_field]['#disabled'] = true;
        $language = isset($this->__form[$current_field]['#language']) ? $this->__form[$current_field]['#language'] : null;
        if (isset($this->__form[$current_field][$language])) {
            $this->__form[$current_field][$language]['#disabled'] = true;
        }
        // Perlu hide/show juga pada delta untuk field type `file`.
        $delta = -1;
        while (isset($this->__form[$current_field][$language][++$delta])) {
            $this->__form[$current_field][$language][$delta]['#disabled'] = true;
        }
    }

    /**
     *
     */
    public function addProperty($key, $value)
    {
        if ($this->__current_field === null) {
            $element = &$this->__form;
        }
        else {
            $current_field = $this->__current_field;
            $this->__current_field = null;
            $element = &$this->__form[$current_field];
        }
        if (is_array($value)) {
            array_key_exists('#'.$key, $element) or $element['#'.$key] = [];
            $element['#'.$key] = (array) $element['#'.$key];
            $element['#'.$key] = array_merge($element['#'.$key], $value);
        }
        else {
            $element['#'.$key] = $value;
        }
        // if ($this->__current_field === null) {
            // $this->__form['#'.$key] = $value;
        // }
        // else {
            // $current_field = $this->__current_field;
            // $this->__current_field = null;
            // $this->__form[$current_field]['#'.$key] = $value;
        // }
        return $this;
    }

    /**
     *
     */
    public function node()
    {
        if (null === $this->__node) {
            $this->__node = new Node($this->__form_state['node']);
        }
        return $this->__node;
    }

    /**
     * Get object.
     */
    public function author()
    {
        return $this->node()->author->getValue();
    }

    /**
     *
     */
    public function setValuexxx($value)
    {
        $current_field = $this->__current_field;
        $this->__current_field = null;
        $info = field_info_field($current_field);
        $language = isset($this->__form[$current_field]['#language']) ? $this->__form[$current_field]['#language'] : null;
        $delta = -1;
        while (isset($this->__form[$current_field][$language][++$delta])) {
            if (isset($this->__form[$current_field][$language][$delta]['#columns'])) {
                $columns = $this->__form[$current_field][$language][$delta]['#columns'];
                while ($column = array_shift($columns)) {
                    if (isset($this->__form[$current_field][$language][$delta][$column])) {
                        $type = isset($this->__form[$current_field][$language][$delta][$column]['#type']);
                        switch ($type) {
                            case 'textfield':
                                !is_string($value) or $this->__form[$current_field][$language][$delta][$column]['#value'] = $value;
                                break;
                        }
                    }
                }
            }
        }
    }

    /**
     *
     */
    public function setDefaultValue($value)
    {
        $current_field = $this->__current_field;
        $this->__current_field = null;
        $info = field_info_field($current_field);
        $language = isset($this->__form[$current_field]['#language']) ? $this->__form[$current_field]['#language'] : null;
        $delta = -1;
        while (isset($this->__form[$current_field][$language][++$delta])) {
            if (isset($this->__form[$current_field][$language][$delta]['#columns'])) {
                $columns = $this->__form[$current_field][$language][$delta]['#columns'];
                while ($column = array_shift($columns)) {
                    if (isset($this->__form[$current_field][$language][$delta][$column])) {
                        $type = isset($this->__form[$current_field][$language][$delta][$column]['#type']);
                        switch ($type) {
                            case 'textfield':
                                !is_string($value) or $this->__form[$current_field][$language][$delta][$column]['#default_value'] = $value;
                                break;
                        }
                    }
                }
            }
        }
    }

    /**
     *
     */
    public function attachJs($js)
    {
        $this->__form['#attached']['js'][] = $js;
        return $this;
    }

    /**
     *
     */
    protected function prepareDispatcher()
    {
        if (null === $this->__dispatcher) {
            $this->__dispatcher = new EventDispatcher;
        }
        return $this;
    }

    /**
     *
     */
    public function listen($element_name, $event, $callback, $ajax_callback = null)
    {
        $current_field = $this->__current_field;
        $this->__current_field = null;
        $this->prepareDispatcher();
        $namespace = $element_name . '.'. $event;
        $listen = [
            'namespace' => $namespace,
            'target_field' => $element_name,
            'event' => $event,
            'callback' => $callback,
            'ajax_callback' => $ajax_callback,
        ];
        $this->__conditional[$current_field]['listen'][$namespace] = $listen;
        $this->__conditional[$current_field]['target_field'][$element_name] = true;
        // Analisis.
        // Jika ada satu field yang melisten lebih dari satu field, maka
        // sudah cukup diduga bahwa form tersebut merupakan field berjenjang.
        // Sehingga #ajax callback akan mereturn keseluruhan form.
        // Tidak hanya satu element saja.
        if (count($this->__conditional[$current_field]['target_field']) > 1) {
            $this->__conditional_multilevel = true;
        }
        $this->__conditional[$element_name]['event'][$event] = true;
        return $this;
    }

    /**
     *
     */
    public function executeConditionalField()
    {
        $conditional = $this->__conditional;
        $conditional_multilevel = $this->__conditional_multilevel;

        // Prepare Ajax and Listener.
        // Rules: jika ada satu field yang me-listen lebih dari satu field
        // Maka ajax rombak seluruh form.
        //
        $add_file_js = false;
        foreach ($this->__conditional as $field => $info) {
            if (isset($info['listen'])) {
                if (field_info_field($field)['type'] == 'file') {
                    $add_file_js = true;
                };
                $list = $info['listen'];
                while ($listen = array_shift($list)) {
                    $element = $this->getElement($field);
                    $element->setProcess($listen['namespace'], $listen['callback']);
                    switch ($listen['event']) {
                        case 'on_value_changed':
                            $this->__dispatcher->addListener($listen['namespace'], [$element, 'onChanged']);
                            $this->{$listen['target_field']}->addAjax($field, $listen['ajax_callback']);
                            break;

                        case 'on_display_changed':
                            $this->__dispatcher->addListener($listen['namespace'], [$element, 'onChanged']);
                            break;
                    }
                }
            }
        }
        if ($add_file_js) {
            // kita perlu menambahkan javascript, karena conditional field
            // bisa menyebabkan field ber-type file ter-hide diawal sehingga
            // librarynya tidak terbawa saat initial render.
            $this->__form['#attached']['js'][] = drupal_get_path('module', 'file') . '/file.js';
        }
        foreach (module_invoke_all('systemix_conditional_attached_library') as $key => $info) {
            $this->__form['#attached']['library'][] = $info;
        }

        // Dispatch has value changed.
        foreach ($this->__conditional as $field => $info) {
            if (isset($info['event']['on_value_changed'])) {
                $element = $this->getElement($field);
                $event = new NodeFormConditionalEvent($element);
                $namespace = $field.'.on_value_changed';
                $event->setNameSpace($namespace);
                $this->__dispatcher->dispatch($namespace, $event);
            }
        }

        // Decision visibility.
        foreach ($this->__conditional as $field => $info) {
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

    /**
     *
     */
    public static function ajaxCallbackReturnForm($form, $form_state)
    {
        return $form;
    }

    public function hasValue()
    {
        $current_field = $this->__current_field;
        $this->__current_field = null;
        // return $this;
        return $this->getElement($current_field)->hasValue();

    }

    /**
     *
     */
    public function getValue($modifier = null)
    {
        $current_field = $this->__current_field;
        $this->__current_field = null;
        // return $this;
        return $this->getElement($current_field)->getValue($modifier);
    }

    /**
     *
     */
    public function setValueBy($modifier, $_value)
    {
        $current_field = $this->__current_field;
        $this->__current_field = null;
        return $this->getElement($current_field)->setValueBy($modifier, $_value);
    }

    /**
     *
     */
    public function setValue($value)
    {
        $current_field = $this->__current_field;
        $this->__current_field = null;
        switch ($current_field) {
            case 'title':
                $this->__form_state['values'][$current_field] = $value;
                break;

            default:
                return $this->getElement($current_field)->setValue($value);
        }
    }

    /**
     *
     */
    public function setWorkflow($field_name)
    {
        if (!array_key_exists($field_name, $this->__workflow)) {
            $this->__workflow[$field_name] = new NodeFormWorkflow($field_name, $this);
        }
        return $this->__workflow[$field_name];
    }

    /**
     *
     */
    public function setState($key, $value)
    {
        $this->__form_state[$key] = $value;
        return $this;
    }

    /**
     *
     */
    public function getState($key)
    {
        if (array_key_exists($key, $this->__form_state)) {
            return $this->__form_state[$key];
        }
    }
}
