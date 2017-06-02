<?php

namespace route;

class main
{
    public function afterroute(\Base $fat)
    {
        if ($fat->get('AJAX') === false) {
            echo \Template::instance()->render('layout.html');
        }
    }
}
