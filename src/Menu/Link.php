<?php

namespace Drupal\systemix\Menu;

class Link
{
    protected $info;
    protected $path;
    /**
     *
     */
    public function __construct($path, $info)
    {
        $this->info = $info;
        $this->path = $path;
        return $this;


    }

    /**
     *
     */
    public function save()
    {

        // $this->info = $info;
        // $this->path = $path;
        $path = $this->path;
        $info = $this->info;


        // die('stop lah');

        // return $this;
    }

}

