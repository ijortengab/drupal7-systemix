<?php

namespace Drupal\systemix\Form;

use Symfony\Component\EventDispatcher\EventDispatcher;

/**
 * Seluruh property diberi prefix double underscore (__) untuk mencegah
 * kemiripan dengan nama field.
 */
class NodeFormWorkflow
{
    protected $field_name;
    protected $parent;
    protected $dispatcher;
    protected $change_state = [];
    protected $on_state_change = [];

    /**
     *
     */
    public function __construct($field_name, $parent)
    {
        $this->field_name = $field_name;
        $this->parent = $parent;
        return $this;
    }

    /**
     *
     */
    public function onCreated($state)
    {
        return $this->changeState($state, function ($event) {
            return $event->getForm()->node()->isNew();
        });
        // $this->
        // $ortu = $this->parent;
        // if ($this->parent->node()->isNew()) {
            // $field_name = $this->field_name;
            // $this->parent->{$field_name}->setValueBy('machine_name', $state);
        // }
    }

    /**
     *
     */
    public function onStateChange($state, $callback, $state_before = null)
    {
        $this->on_state_change[$state][] = [
            'state_before' => $state_before,
            'callback' => $callback,
        ];
    }

    /**
     * Change to a new state.
     */
    public function changeState($state, $callback, $state_before = null)
    {
        $this->change_state[$state][] = [
            'state_before' => $state_before,
            'callback' => $callback,
        ];


        // First condition.
        // if (null !== $state_before) {
            // $field_name = $this->field_name;
            // if ($this->parent->{$field_name}->getValue('machine_name') != $state_before) {
                // return;
            // }
        // }

    }

    /**
     * @todo: support for priority which is default as 0.
     */
    public function execute()
    {
        $this->dispatcher = new EventDispatcher;



        // Listen berbagai event.
        // $log = 'Mulai membaca berbagai event yang terdaftar';

        foreach ($this->on_state_change as $state => $list) {
            $namespace = $state.'.changed';
            while ($each = array_shift($list)) {
                $this->dispatcher->addListener($namespace, [$this, 'onChanged']);
            }
        }
        // $log = 'Selesai.';


        // Ubah.
        // $log = 'Mulai eksekusi event perubahan state.';

        foreach ($this->change_state as $state => $list) {
            while ($each = array_shift($list)) {
                $execute = true;
                $field_name = $this->field_name;
                // $execute1 = $execute;
                // $state_before = $this->parent->node()->{$field_name}->getValue('machine_name');
                // $state_before = $each['state_before'];
                if (!empty($each['state_before']) && $this->parent->node()->{$field_name}->getValue('machine_name') != $each['state_before']) {
                    $execute = false;
                }
                // $execute2 = $execute;

                if ($execute) {
                    $event = new NodeFormWorkflowEvent();
                    $event->setForm($this->parent);
                    $execute = call_user_func_array($each['callback'], [$event]);
                }
                if ($execute) {
                    $this->parent->{$field_name}->setValueBy('machine_name', $state);
                    // $log = 'Status berubah menjadi '.$state.', sekarang mainkan event dispatcher';
                    $namespace = $state.'.changed';
                    $event = new NodeFormWorkflowEvent();
                    $event->setNameSpace($namespace);
                    $this->dispatcher->dispatch($namespace, $event);
                }
            }
        }

        // $log = 'Selesai.';


        // $change_state = $this->change_state;
        // $on_state_change = $this->on_state_change;

    }

    /**
     *
     */
    public function onChanged($event)
    {
        // $log = __METHOD__;
        // $namespace = $event->getNameSpace();

        foreach ($this->on_state_change as $state => $list) {
            $namespace = $state.'.changed';
            if ($event->getNameSpace() == $namespace) {
                while ($each = array_shift($list)) {
                    $execute = true;
                    $field_name = $this->field_name;
                    if (!empty($each['state_before']) && $this->parent->node()->{$field_name}->getValue('machine_name') != $each['state_before']) {
                        $execute = false;
                    }
                    if ($execute) {
                        $event = new NodeFormWorkflowEvent();
                        $event->setForm($this->parent);
                        call_user_func_array($each['callback'], [$event]);
                    }
                }
            }
        }
    }
}
