<?php

namespace Drupal\systemix\Entity;

class Entity
{
    protected $entity_type;

    protected $entity_id;

    protected $entity_rev_id;

    protected $entity_bundle;

    protected $current_field_name;

    protected $entity_load;

    protected $entity_wrapper;

    protected $current_field_info = [];

    public static function getStaticInstance($entity_type, $entity_id)
    {

    }

    /**
     * @param $entity, integer or object
     *   Jika integer berarti entity id, jika object berarti hasil dari
     *   entity load.
     */
    function __construct($entity_type, $entity) {
        $this->entity_type = $entity_type;
        if (is_int($entity)) {
            $this->entity_id = $entity;
            $this->entity_load = entity_load($this->entity_type, [$this->entity_id]);
            list(, $this->entity_rev_id, $this->entity_bundle) = entity_extract_ids($this->entity_type, $this->entity_load);
        }
        else {
            $this->entity_load = $entity;
            list($this->entity_id, $this->entity_rev_id, $this->entity_bundle) = entity_extract_ids($this->entity_type, $entity);
        }
        $this->entity_wrapper = entity_metadata_wrapper($this->entity_type, $this->entity_load);
        return $this;
    }

    function getValue($modifier = null) {
        if ($modifier == 'machine_name') {
            return $this->release()->machine_name->getValue();
        }
        $value = null;
        if (null !== $this->current_field_name) {
            $current_field_name = $this->current_field_name;
            $info = $this->entity_wrapper->getPropertyInfo();
            if (isset($info[$current_field_name])) {
                $value = $this->entity_wrapper->{$current_field_name}->value();
            }
            $this->current_field_reset();
        }
        if (is_callable($modifier)) {
            $value = call_user_func_array($modifier, [$value]);
        }
        return $value;
    }

    public function setValueBy($modifier, $_value)
    {
        return $this;
    }

    public function setValue($value)
    {
        // $_value = 2743;
        $current_field_name = $this->current_field_name;
        $this->current_field_reset();
        $this->entity_wrapper->{$current_field_name} = $value;
        // die('op');

        return $this;
    }
    /**
     *
     */
    public function save()
    {
        $this->entity_wrapper->save();
        // return $this;
    }


    function release() {
        $this->current_field_load();
        switch ($this->current_field_info['field']['type']) {
                case 'entityreference':
                    // Get info, karena method ::value() akan mereset property
                    // $current_field_info;
                    $info = $this->current_field_info;
                    $entity_load = $this->getValue();
                    $entity_type = $info['field']['settings']['target_type'];
                    if ($entity_load === null) {
                        return new EntityEmpty($entity_type);
                    }
                    elseif(is_array($entity_load)){
                        return new ReferenceArray($entity_load, $info);
                    }

                    return (new static($entity_type, $entity_load));

                case '':
                    // Do something.
                    break;

                default:
                    // Do something.
                    break;
            }
    }

    function current_field_load() {
        $this->current_field_info['field'] = field_info_field($this->current_field_name);
        $this->current_field_info['instance'] = field_info_instance($this->entity_type, $this->current_field_name, $this->entity_bundle);
    }

    function current_field_reset() {
        $this->current_field_name = null;
        $this->current_field_info = [];
    }

    function __get($name) {
        $current_field_name = $this->current_field_name;
        if (null === $this->current_field_name) {
            $this->current_field_name = $name;
            return $this;
        }
        else {
            $this->current_field_load();
            // Kemungkinan adalah entityreference.
            // Periksa current field.
            switch ($this->current_field_info['field']['type']) {
                case 'entityreference':

                    // Get info, karena method ::value() akan mereset property
                    // $current_field_info;
                    $info = $this->current_field_info;
                    $entity_load = $this->getValue();
                    $entity_type = $info['field']['settings']['target_type'];

                    if ($entity_load === null || $entity_load === false) {
                        return new EntityEmpty($entity_type);
                    }
                    elseif(is_array($entity_load)){
                        return new ReferenceArray($entity_load);
                    }
                    // die('op');
                    return (new static($entity_type, $entity_load))->{$name};

                case '':
                    // Do something.
                    break;

                default:
                    // Do something.
                    break;
            }
            // $current_field = $this->current_field_name;
            // $field_info_field = field_info_field($current_field);



            return $this;
        }

    }
}
