<?php

namespace model;

class ores
{

    public static function getArticles(string $user, $wiki = 'es.wikipedia.org')
    {
        $data = \helper\api::get(["action" => "query", "list" => "allrevisions",
            "arvprop" => "timestamp|oresscores|ids", 'arvuser' => $user, 'arvnamespace' => 0, 'arvlimit' => 50], $wiki);
        $revisions = [];
        foreach (new \ArrayObject($data->query->allrevisions) as $revision) {
            foreach ($revision->revisions as $minorRevision) {
                $localRevision = ['id' => $minorRevision->revid, 'title' => $revision->title,
                    'timestamp' => $minorRevision->timestamp, 'ores' => $minorRevision->oresscores];
                if (empty($minorRevision->oresscores) === false) {
                    array_push($revisions, $localRevision);
                }
            }
        }
        array_multisort(array_column($revisions, 'timestamp'), SORT_DESC, $revisions);
        return $revisions;
    }
}
