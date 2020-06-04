<?php

namespace route;

class ArchiveRoute extends MainRoute
{
    public function index(\Base $fat)
    {
        $fat->set('page.contents', 'archive/index.html');
    }

    public function process(\Base $fat)
    {
        $title = $fat->get('POST.article');
        $result = \model\archive::processArticle($title, 'es.wikipedia.org');
        $fat->mset(['result' => $result, 'title' => $title]);
        $fat->set('page.contents', 'archive/result.html');
    }

    public function beforeroute(\Base $fat)
    {
        $fat->set('page.title', 'Enviar URL a Internet Archive');
    }
}
