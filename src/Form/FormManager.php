<?php

namespace Drupal\systemix\Form;

/**
 * Seluruh property diberi prefix double underscore (__) untuk mencegah
 * kemiripan dengan nama field.
 */
abstract class FormManager
{
    private static $validate_has_added = false;
    private static $submit_has_added = false;

    abstract public static function getClassName();

    /**
     *
     */
    public static function registerValidate(NodeForm $form, $method)
    {
        if (!self::$validate_has_added) {
            $form->addProperty('validate', ['systemix_form_validate_callback']);
            self::$validate_has_added = true;
        }
        $class = static::getClassName();
        if (method_exists($class, $method)) {
            $form->addProperty('validate2', [[$class, $method]]);
        }
    }

    /**
     *
     */
    public static function registerSubmit(NodeForm $form, $method)
    {
        if (!self::$submit_has_added) {
            $form->addProperty('submit', ['systemix_form_submit_callback']);
            self::$submit_has_added = true;
        }
        $class = static::getClassName();
        if (method_exists($class, $method)) {
            $form->addProperty('submit2', [[$class, $method]]);
        }
    }

    /**
     *
     */
    public static function redirect(NodeForm $form, $path)
    {
        // Sumber: https://gist.github.com/juampynr/660219418bde29b6d107#file-mymodule-php-L3
        $_form = $form->getForm();
        $_form_state = $form->getFormState();
        $_form['actions']['submit']['#submit'][] = 'systemix_form_actions_submit_redirect';
        $_form_state['systemix']['redirect'] = $path;
        $form->setForm($_form);
        $form->setFormState($_form_state);
    }

}
