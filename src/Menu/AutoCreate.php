<?php

namespace Drupal\systemix\Menu;

class AutoCreate
{
    /**
     *
     */
    public static function menuAlter(&$items)
    {
        $paths = array_keys($items);
        $storage = [];
        // Cari path yang mengandung informasi auto create pada hook_menu().
        // Simpan informasi tersebut pada
        foreach ($paths as $path) {
            if (isset($items[$path]['systemix']['menu_links_auto_create']) && $items[$path]['systemix']['menu_links_auto_create']) {
                $storage[] = $path;
            }
        }
        $contains = [];
        // Cari child path dari path tersebut.
        foreach ($storage as $path) {
            $contains += array_filter($paths, function ($value) use ($path) {
                return (strpos($value, $path) !== false);
            });
        }
        foreach ($contains as $path) {
            // Bagi type yang bit == MENU_LOCAL_TASK,
            // maka tambahkan bit MENU_NORMAL_ITEM.
            if (isset($items[$path]['type']) && $items[$path]['type'] == MENU_LOCAL_TASK) {
                $items[$path]['type'] = $items[$path]['type'] | MENU_NORMAL_ITEM;
                $items[$path]['expanded'] = true;
            }
        }
    }
}
