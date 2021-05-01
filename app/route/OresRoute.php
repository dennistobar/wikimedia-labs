<?php

namespace route;

use model\OresModel;

class OresRoute extends MainRoute
{
    public function index(\Base $fat)
    {
        $fat->set('page.contents', 'ores/index.html');
    }

    public function process(\Base $fat)
    {
        $username = $fat->get('POST.username');
        $results = OresModel::getArticles($username, 'es.wikipedia.org');

        $fat->mset(['results' => $results, 'title' => $username]);
        $fat->set('page.contents', 'ores/result.html');
    }

    public function getFromRevId(\Base $fat)
    {
        $revId = $fat->get('PARAMS.revid');
        $results = OresModel::getFromRevId($revId, 'eswiki');
    }

    public function beforeroute(\Base $fat)
    {
        $fat->set('page.title', 'Comprobar ediciones en ORES');
    }
}
