<?php

namespace model;

use helper\api;

class archive
{
    /**
     * Procesa un artículo y lo sube a Internet Archive
     */
    public static function processArticle(string $title, string $wiki = 'es.wikipedia.org'): array
    {
        $objetive = [];
        $parameters = ["action" => "query", "prop" => "extlinks", "ellimit" => "max", 'titles' => $title];
        $data = api::get($parameters, $wiki);

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
