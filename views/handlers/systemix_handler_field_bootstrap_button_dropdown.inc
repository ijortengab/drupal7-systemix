<?php

class systemix_handler_field_bootstrap_button_dropdown extends views_handler_field_entity {
    function render($values) {
        $entity = $this->entities[$this->view->row_index];
        $ini = $this;
        $links = [];
        // Standard. View, Edit, Delete.
        // $context = [];
        // drupal_alter([]);
        // $context = array(
          // 'form' => $form,
          // 'field' => $field,
          // 'instance' => $instance,
          // 'langcode' => $langcode,
          // 'items' => $items,
          // 'delta' => $delta,
        // );
        // drupal_alter(array('field_widget_form', 'field_widget_' . $instance['widget']['type'] . '_form'), $element, $form_state, $context);


        return 'tempe';
        switch ($entity->type) {
            case 'nomor_seri_faktur_pajak':
                $lang = $entity->language;
                $kode_faktur_prefix = $entity->kode_faktur_prefix[$lang][0]['value'];
                $kode_faktur_serial_end = $entity->kode_faktur_serial_end[$lang][0]['value'];
                $kode_faktur_serial_current = $entity->kode_faktur_serial_current[$lang][0]['value'];
                $count = $kode_faktur_serial_end - $kode_faktur_serial_current + 1;
                return $count;
        }
    }
}
