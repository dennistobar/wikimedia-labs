<?php

namespace model;

class archive
{
    public static function processArticle($title, $wiki = 'es.wikipedia.org')
    {
        $objetive = [];
        $data = \helper\api::get(["action" => "query", "prop" => "extlinks", "ellimit" => "max", 'titles' => $title], $wiki);

        $pages = new \ArrayObject($data->query->pages);
        foreach ($pages as $page) {
            $links = new \ArrayObject($page->extlinks);
            foreach ($links as $link) {
                $url = $link->{'*'};
                try {
                    $objetive[] = Url::create($url)->sendArchive();
                } catch (\Exception $e) {
                    continue;
                }
            }
        }
        return $objetive;
    }
}
