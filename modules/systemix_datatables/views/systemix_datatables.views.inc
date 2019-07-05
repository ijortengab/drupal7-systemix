<?php




/**
 * Implements hook_views_plugins().
 */
function systemix_datatables_views_plugins() {
  $path = drupal_get_path('module', 'systemix_datatables') . '/views';
  return array(
    'module' => 'systemix_datatables',
    'style' => array(
      'systemix_datatables' => array(
        'title' => t('Systemix DataTables'),
        'help' => t('Displays rows in a jQuery table.'),
        'path' => $path,
        'theme path' => $path,
        'handler' => 'systemix_datatables_style_plugin',
        'theme' => 'systemix_datatables_view',
        'uses row plugin' => FALSE,
        'uses row class' => TRUE,
        'uses fields' => TRUE,
        'uses options' => TRUE,
        'type' => 'normal',
      ),
    ),
  );
}
