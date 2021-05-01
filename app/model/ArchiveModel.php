<?php

namespace model;

use ArrayObject;
use Exception;
use helper\ApiHelper;

class ArchiveModel
{
    /**
     * Procesa un artículo y lo sube a Internet Archive
     */
    public static function processArticle(string $title, string $wiki = 'es.wikipedia.org'): array
    {
        $objetive = [];
        $parameters = ["action" => "query", "prop" => "extlinks", "ellimit" => "max", 'titles' => $title];
        $data = ApiHelper::createFromArray($parameters, $wiki)->getResults();
        $pages = new ArrayObject($data->query->pages);
        foreach ($pages as $page) {
            if (!property_exists($page, 'extlinks')) {
                continue;
            }
            $links = new ArrayObject($page->extlinks);
            foreach ($links as $link) {
                $url = $link->{'*'};
                try {
                    $objetive[] = UrlModel::create($url)->sendArchive();
                } catch (Exception $e) {
                    continue;
                }
            }
        }
        return $objetive;
    }
}
