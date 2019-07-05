<?php

/**
 * Implements hook_views_data().
 */

function systemix_views_data() {
    $data = [];
    // Table node.
    // $data['node']['systemix_bootstrap_button_dropdown'] = [
        // 'title' => t('Systemix - Bootstrap Button Dropdown'),
        // 'help' => t('Menyediakan bootstrap button dropdown.'),
        // 'field' => array(
          // 'title' => 'Operations',
          // 'handler' => 'systemix_handler_field_bootstrap_button_dropdown',
        // ),
    // ];
    //  yang didalamnya terdapat informasi attribute yang bisa digunakan nantinya oleh javascript untuk merender element.
    // $data['views']['systemix_span'] = [
        // 'title' => t('Systemix: Entity Span'),
        // 'help' => t('Menyediakan informasi entity didalam element HTML &lt;span&gt;.'),
        // 'field' => array(
          // 'handler' => 'systemix_handler_field_systemix_span',
        // ),
    // ];

    return $data;
}
