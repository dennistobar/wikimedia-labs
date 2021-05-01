<?php

namespace model;

use helper\ApiHelper;
use helper\OresHelper;

class OresModel
{
    /**
     * Obtiene los artículos en función del usuario wiki
     *
     * @return mixed
     */
    public static function getArticles(string $user, string $wiki = 'es.wikipedia.org'): array
    {
        $parameters = ["action" => "query", "list" => "allrevisions",
            "arvprop" => "timestamp|oresscores|ids", 'arvuser' => $user, 'arvnamespace' => 0, 'arvlimit' => 50];
        $data = ApiHelper::createFromArray($parameters, $wiki)->getResults();
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

    public static function getFromRevId(int $revId, string $wiki = 'eswiki'): array
    {
        $data = OresHelper::createFromRevId($revId, $wiki)->getResults();

        return [];
    }
}
