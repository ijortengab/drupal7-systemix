<?php

namespace Drupal\systemix\Entity;

class Field
{
    /**
     *
     */
    public static function getColumnIdentifierByType($type)
    {
        switch ($type) {
            case 'entityreference':
                return 'target_id';

            case 'file':
                return 'fid';

            default:
                return 'value';
        }
    }

    /**
     *
     */
    public static function getIdentifierByMachineName($field_name, $machine_name)
    {
        $value = null;
        switch (field_info_field($field_name)['type']) {
            case 'entityreference':
                switch (field_info_field($field_name)['settings']['target_type']) {
                    case 'taxonomy_term':
                        $vocabularies = taxonomy_vocabulary_get_names();
                        $or = db_or();
                        foreach (field_info_field($field_name)['settings']['handler_settings']['target_bundles'] as $vocabulary_machine_name) {
                            $or->condition('vid', $vocabularies[$vocabulary_machine_name]->vid);
                        }
                        $query = db_select('taxonomy_term_data', 'n');
                        $query->fields('n', array('tid'));
                        $query->condition($or);
                        $query->condition('n.machine_name', $machine_name);
                        $result = $query->execute()->fetchAll();
                        // Hanya boleh hasil 1, jika lebih dari satu
                        // maka biarkan null. // todo support multivalue.
                        if (count($result) == 1) {
                            $result = array_shift($result);
                            $value = $result->tid;
                        }
                        break;
                }
                break;
        }
        return $value;
    }

}
