<?php

namespace model;

class archive
{
    private static function checkUrl($array)
    {
        $headers = \Web::instance()->request($array['url'])['headers'];
        $status = !!array_filter($headers, function ($head) {
            return stripos($head, '200 OK');
        });
        $array['status'] = ['http' => $status];
        return $array;
    }

    private static function checkArchiveStatus($array)
    {
        $status = $array['status'];
        if (!!array_filter($array['status']) === true) {
            $request = \Web::instance()->request('http://archive.org/wayback/available?url=' . $array['url']);
            $api_archive = json_decode($request['body']);
            $status['archive'] = isset($api_archive->archived_snapshots->closest->timestamp) ?
            \DateTime::createFromFormat('YmdHis', $api_archive->archived_snapshots->closest->timestamp)
            : false;
        } else {
            $status['archive'] = false;
        }
        $array['status'] = $status;
        return $array;
    }

    private static function sendArchive($array)
    {
        $status = $array['status'];
        if ($status['http'] === true && ($status['archive'] === false || $status['archive']->diff(new \DateTime(), true)->days > 180)) {
            $headers = \Web::instance()->request('http://web.archive.org/save/' . $array['url'])['headers'];
            $array['status']['sent'] = !!array_filter($headers, function ($head) {
                return stripos($head, '200 OK');
            });
        } else {
            $array['status']['sent'] = false;
        }

        return $array;
    }

    public static function processArticle($title, $wiki = 'es.wikipedia.org')
    {
        $objetive = [];
        $data = \helper\api::get(["action" => "query", "prop" => "extlinks", "ellimit" => "max", 'titles' => $title], $wiki);
        $pages = new \ArrayObject($data->query->pages);
        foreach ($pages as $page) {
            $links = new \ArrayObject($page->extlinks);
            foreach ($links as $link) {
                $url = $link->{'*'};
                if (self::isLink($url) === false || self::isWikidata($url) === true || self::isInternettArchive($url) === true) {
                    continue;
                }
                $objetive[]['url'] = $url;
            }
        }
        $objetive = array_map([self::class, 'checkURL'], $objetive);
        $objetive = array_map([self::class, 'checkArchiveStatus'], $objetive);
        return array_map([self::class, 'sendArchive'], $objetive);
    }

    public static function isLink($url)
    {
        return filter_var($url, FILTER_VALIDATE_URL);
    }

    public static function isWikidata($url)
    {
        return stripos($url, '//www.wikidata.org/') !== false;
    }

    public static function isInternettArchive($url)
    {
        return stripos($url, 'web.archive.org/web/') !== false;
    }
}
