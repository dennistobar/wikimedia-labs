<?php

namespace route;

use function GuzzleHttp\json_encode;
use helper\Entity;
use model\mujer\mujeres;
use model\mujer\origin;

class cron
{
    public static function cronSQL()
    {
        $origin = \model\database::instance('commonswiki', 'commonswiki');
        $dest = \model\database::instance('tools', \F3::get('db.user') . '__desafio');

        $max = $dest->exec('select ifnull(max(rc_timestamp),0) ultimo from ediciones')[0]['ultimo'];

        $desafio = new desafio;
        $timestamp = date('YmdHis');
        $i = 0;
        $desafios = $desafio->select('desafio_name, desafio_start, desafio_end', ['? between desafio_start and desafio_end', $timestamp]);
        $return = [];
        foreach ($desafios as $r_desafio) {
            $qData = 'select rc_id, rc_timestamp, rc_user, rc_user_text, rc_namespace, rc_title
                , abs(rc_old_len - rc_new_len) size, rc_comment, rc_this_oldid, :desafio as desafio
            from eswiki_p.recentchanges, eswiki_p.comment c
            where rc_id > 0 and
                rc_comment_id = c.comment_id and
                rc_namespace in (0, 104) and
                LOWER(CONVERT(comment_text USING utf8))  like :string and
                rc_timestamp between :max and :end and
                rc_user > 5 and
                rc_new = 0 and
                rc_bot = 0;';

            $fecha = min([(int) $max + 1, (int) (new \DateTime('-1 hour', new \DateTimeZone('UTC')))->format('YmdHis')]);
            $fecha = $fecha > $r_desafio['desafio_start'] ? $fecha : $r_desafio['desafio_start'];

            $pData = ['max' => $fecha, 'string' => '%#' . $r_desafio['desafio_name'] . '%', 'end' => $r_desafio['desafio_end'], 'desafio' => $r_desafio['desafio_name']];
            $qInsert = "Insert into ediciones
            select :rc_id, :rc_timestamp, :rc_user, :rc_user_text, :rc_namespace, :rc_title, :size, :rc_comment, :rc_this_oldid, :desafio from dual
            where not exists (select 1 from ediciones where rc_id = :rc_id) limit 1";
            $changes = $origin->exec($qData, $pData);
            foreach ($changes as $item) {
                $i++;
                $dest->exec($qInsert, $item);
            }
            $return[$r_desafio['desafio_name']] = ['min' => $fecha, 'edits' => count($changes), 'max' => $r_desafio['desafio_end']];
        }

        header('Content-type: application/json');
        echo json_encode($return);
    }

    public static function cronCategories()
    {
        $categorias = ['Wikipedia:Artículos sin contextualizar', 'Wikipedia:Artículos que necesitan referencias', 'Wikipedia:Wikificar'
            , 'Wikipedia:Traducción automática', 'Wikipedia:Traducciones para mejorar', 'Wikipedia:Artículos desactualizados', 'Wikipedia:Artículos que necesitan formato correcto de referencias'
            , 'Wikipedia:No neutral', 'Wikipedia:Categorizar', 'Wikipedia:Páginas huérfanas', 'Wikipedia:Páginas con referencias con parámetros obsoletos'
            , 'Wikipedia:Copyedit'];

        $query = ['action' => 'query', 'prop' => 'categoryinfo'];
        $query['titles'] = implode("|", array_map(function ($item) {
            return 'Category:' . $item;
        }, $categorias));

        $data = \helper\api::get($query);

        $Categorias = new \ArrayObject($data->query->pages);
        foreach ($Categorias as $Categoria) {
            $pCategoria = new \model\desafio\categoria;
            $params = ['cat_name' => $Categoria->title, 'cat_articulos' => $Categoria->categoryinfo->pages, 'cat_timestamp' => (new \DateTime('now', new \DateTimeZone('UTC')))->format('YmdHi') . '00'];
            $pCategoria->copyFrom($params);
            $pCategoria->save();
        }
    }

    public static function cronMujeres()
    {
        $dest = \model\database::instance('tools', \F3::get('db.user') . '__desafio');
        $origen = new origin;
        $origen->setParameters('20190301000000', '20190408000000');
        $changes = $origen->getData(null);

        $inserted = 0;
        foreach ($changes as $change) {
            $qInsert = "UPDATE mujeres
                SET wikidata_item = :wikidata_item, `timestamp` = :rev_timestamp
                WHERE page_id = :page_id;
            Insert into mujeres (page_id, wikidata_item, `timestamp`)
            select :page_id, :wikidata_item, :rev_timestamp from dual
            where not exists (select 1 from mujeres where page_id = :page_id) limit 1";
            $changes = $dest->exec($qInsert, $change);
            $inserted++;
        }

        header('Content-type: application/json');
        echo json_encode(['inserted' => $inserted]);
    }

    public static function populateMujeres()
    {
        $mujeres = mujeres::getWihtoutCitizenship();
        $wikidata = new Entity;
        $changed = 0;
        foreach ($mujeres as $mujer) {
            $property = $wikidata->getCitizenship($mujer->wikidata_item);
            if (!!$property) {
                $mujer->country = $property;
            }
            $changed += (int) $mujer->changed();
            $mujer->changed() ? $mujer->update() : '';
        }
        header('Content-type: application/json');
        echo json_encode(['selected' => count($mujeres), 'changed' => $changed]);
    }
}
