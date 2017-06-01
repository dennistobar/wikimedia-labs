<?php

namespace route;

class stats
{
    public function index($f3)
    {
        $f3->set('title', 'Wiki Loves (Whatever)');
        $f3->set('output', 'stats/index.html');
        echo \Template::instance()->render('layout.html');
    }

    public function process($f3)
    {
        $category = str_replace(" ", "_", $f3->get('POST.category'));
        $stats = new \model\stats($category, $f3->get('POST.initial'), $f3->get('POST.final'), 0);

        $f3->set('totals', $stats->getTotals());
        $f3->set('users', $stats->getUsers());

        $f3->set('title', 'Wiki Loves (Whatever)');
        $f3->set('output', 'stats/dashboard.html');
        echo \Template::instance()->render('layout.html');
    }
}
