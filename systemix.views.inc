<?php

/**
 * Implements hook_views_data().
 */

function systemix_views_data() {
    $data = [];
    // Table node.
    $data['node']['systemix_bootstrap_button_dropdown'] = [
        'title' => t('Systemix - Bootstrap Button Dropdown'),
        'help' => t('Menyediakan bootstrap button dropdown.'),
        'field' => array(
          'title' => 'Operations',
          'handler' => 'systemix_handler_field_bootstrap_button_dropdown',
        ),
    ];
    return $data;
}
