<?php
    
namespace Drupal\systemix\Form;

class ElementFormManager
{
    protected $element;
    protected $context;
    protected $form_state;
    protected $current_field;
    protected $current_behaviour;
    
    /**
     * 
     */
    public function __construct(&$element, &$form_state, $context) {
        $this->element =& $element;
        $this->form_state =& $form_state;
        $this->context = $context;
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
    
    /**
     * 
     */
    public function isChecked()
    {
        $field = $this->current_field;
        $this->current_field = null;
        $isInitialize = $this->isInitialize();
        $isRebuild = $this->isRebuild();
        // $debugname = 'isInitialize'; $debugfile = 'debug.html'; $debugvariable = '|||wakwaw|||'; if (array_key_exists($debugname, get_defined_vars())) { $debugvariable = $$debugname; } elseif (isset($this) && property_exists($this, $debugname)) { $debugvariable = $this->{$debugname}; $debugname = '$this->' . $debugname; } if ($debugvariable !== '|||wakwaw|||') { ob_start(); echo "\r\n<pre>" . basename(__FILE__ ). ":" . __LINE__ . " (Time: " . date('c') . ", Direktori: " . dirname(__FILE__) . ")\r\n". 'var_dump(' . $debugname . '): '; var_dump($debugvariable); echo "</pre>\r\n"; $debugoutput = ob_get_contents();ob_end_clean(); file_put_contents($debugfile, $debugoutput, FILE_APPEND); }
        // $debugname = 'isRebuild'; $debugfile = 'debug.html'; $debugvariable = '|||wakwaw|||'; if (array_key_exists($debugname, get_defined_vars())) { $debugvariable = $$debugname; } elseif (isset($this) && property_exists($this, $debugname)) { $debugvariable = $this->{$debugname}; $debugname = '$this->' . $debugname; } if ($debugvariable !== '|||wakwaw|||') { ob_start(); echo "\r\n<pre>" . basename(__FILE__ ). ":" . __LINE__ . " (Time: " . date('c') . ", Direktori: " . dirname(__FILE__) . ")\r\n". 'var_dump(' . $debugname . '): '; var_dump($debugvariable); echo "</pre>\r\n"; $debugoutput = ob_get_contents();ob_end_clean(); file_put_contents($debugfile, $debugoutput, FILE_APPEND); }
        // $debugname = 'field'; $debugfile = 'debug.html'; $debugvariable = '|||wakwaw|||'; if (array_key_exists($debugname, get_defined_vars())) { $debugvariable = $$debugname; } elseif (isset($this) && property_exists($this, $debugname)) { $debugvariable = $this->{$debugname}; $debugname = '$this->' . $debugname; } if ($debugvariable !== '|||wakwaw|||') { ob_start(); echo "\r\n<pre>" . basename(__FILE__ ). ":" . __LINE__ . " (Time: " . date('c') . ", Direktori: " . dirname(__FILE__) . ")\r\n". 'var_dump(' . $debugname . '): '; var_dump($debugvariable); echo "</pre>\r\n"; $debugoutput = ob_get_contents();ob_end_clean(); file_put_contents($debugfile, $debugoutput, FILE_APPEND); }
        
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
    public function isCurrentValueChecked()
    {
        $field = $this->current_field;
        $this->current_field = null;
        return ($this->form_state['values'][$field][LANGUAGE_NONE][0]['value'] == 1);
    }
    
    /**
     * 
     */
    public function isOriginalValueChecked()
    {
        $element = $this->element;
        // $debugname = 'element'; $debugfile = 'debug.html'; $debugvariable = '|||wakwaw|||'; if (array_key_exists($debugname, get_defined_vars())) { $debugvariable = $$debugname; } elseif (isset($this) && property_exists($this, $debugname)) { $debugvariable = $this->{$debugname}; $debugname = '$this->' . $debugname; } if ($debugvariable !== '|||wakwaw|||') { ob_start(); echo "\r\n<pre>" . basename(__FILE__ ). ":" . __LINE__ . " (Time: " . date('c') . ", Direktori: " . dirname(__FILE__) . ")\r\n". 'var_dump(' . $debugname . '): '; var_dump($debugvariable); echo "</pre>\r\n"; $debugoutput = ob_get_contents();ob_end_clean(); file_put_contents($debugfile, $debugoutput, FILE_APPEND); }
        
        $this->current_field = null;
        return ($this->element['#default_value'] == 1);
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
     * 
     */
    public function isRebuild()
    {
        return array_key_exists('input', $this->form_state) && !empty($this->form_state['input']); 
    }
    
    /**
     * 
     */
    public function isInitialize()
    {
        return array_key_exists('input', $this->form_state) && empty($this->form_state['input']); 
    }
    
    /**
     * 
     */
    public function show()
    {
        $this->current_field = null;
        $this->element['#access'] = true;
    }
    
    /**
     * 
     */
    public function hide()
    {
        $this->current_field = null;
        $this->element['#access'] = false;
    }
    
    /**
     * 
     */
    public function addAjax($array)
    {
        $this->form[$this->current_field][LANGUAGE_NONE]['#ajax'] = $array;
        $this->current_field = null;
    }
    
    
    
    
    
}

