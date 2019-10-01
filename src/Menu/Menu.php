<?php

namespace Drupal\systemix\Menu;

class Menu
{
    /**
     *
     */
    public static function autoCreate(&$items)
    {
        $paths = array_keys($items);
        $storage = [];
        // Cari path yang mengandung informasi auto create pada hook_menu().
        // Simpan informasi tersebut pada
        foreach ($paths as $path) {
            if (isset($items[$path]['systemix']['menu_links_auto_create']) && $items[$path]['systemix']['menu_links_auto_create']) {
                $storage[] = $path;
                $items[$path]['expanded'] = true;
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

    /**
     * Disabled berbagai route bawaan system.
     */
    public static function disabledDefaultRoute(&$items)
    {
        $items['node/add']['access callback'] = 'user_access';
        $items['node/add']['access arguments'] = ['administer site configuration'];
    }
    /**
     *
     */
    public static function forwardPageCallback(&$items)
    {
        $paths = array_keys($items);
        foreach ($paths as $path) {
            if (isset($items[$path]['page callback']) && is_array($items[$path]['page callback'])) {
                $items[$path]['options']['systemix_page_callback'] = $items[$path]['page callback'];
                $items[$path]['page callback'] = 'systemix_page_callback';
            }
        }
    }
}
