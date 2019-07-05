<?php

// todo, default nya adalah no label.

class systemix_handler_field_systemix_span extends views_handler_field_entity {
    function render($values) {
        if ($entity = $this->get_value($values)) {
            // todo, pake options.
            list($id, $rid, $bundle) = entity_extract_ids('node', $entity);
            return $id;
            // return '<span class="systemix-entity" data-entity-type="node" data-entity-id="'.$id.'"></span>';
        }
    }
}
