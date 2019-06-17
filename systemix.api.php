<?php


/**
 * Hook ini berguna bagi Conditional Field.
 * Yang mungkin tidak ter-load library jika terjadi hide element.
 * @see: Drupal\systemix\Form\NodeForm::executeConditionalField().
 */
function hook_systemix_conditional_attached_library() {
   return [
        'sbadmin2_helper' => ['sbadmin2_helper', 'sbadmin2.managed_file'],
    ];
}

