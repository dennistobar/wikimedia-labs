<?php

namespace route;

class stats extends main
{
    public function index($fat)
    {
        $fat->set('page.contents', 'stats/index.html');
    }

    public function process($fat)
    {
        $category = str_replace(" ", "_", $fat->get('POST.category'));
        $stats = new \model\stats($category, $fat->get('POST.initial'), $fat->get('POST.final'), 0);

        $fat->set('totals', $stats->getTotals());
        $fat->set('users', $stats->getUsers());

        $fat->set('page.contents', 'stats/dashboard.html');
    }

    public function beforeroute(\Base $fat)
    {
        $fat->set('page.title', 'Wiki Loves (Whatever)');
    }
}
