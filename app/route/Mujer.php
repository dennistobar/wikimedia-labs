<?php

namespace route;

class Mujer extends main
{

    public function index(\Base $fat)
    {

        $fat->set('page.contents', 'mujer/dashboard.html');
    }

}
