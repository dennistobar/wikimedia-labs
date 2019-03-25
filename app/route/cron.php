<?php

namespace route;

use \model\desafio\desafio;

class cron
{
    public static function cronSQL(\Base $fat)
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

    public static function cronCategories(\Base $fat)
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

    public static function cronMujeres(\Base $fat)
    {
        $origin = \model\database::instance('commonswiki', 'eswiki');
        $dest = \model\database::instance('tools', \F3::get('db.user') . '__desafio');
        $query = "select page_id, ips_item_id as wikidata_item
            from revision, categorylinks, page
            left join wikidatawiki_p.wb_items_per_site on ips_site_id = 'eswiki' and ips_site_page = replace(page_title, '_', ' ')
            where page_id = rev_page
            and rev_timestamp between '20190307000000' and '20190409000000'
            and rev_parent_id = 0
            and page_namespace = 0
            and cl_from = page_id
            and cl_to = 'Mujeres'
            and cl_timestamp > '20190307000000'";

        $changes = $origin->exec($query);
        $i = 0;
        foreach ($changes as $change) {
            $qInsert = "Insert into mujeres
            select :page_id, :wikidata_item from dual
            where not exists (select 1 from mujeres where page_id = :page_id) limit 1";
            $changes = $origin->exec($qInsert, $change);
            $i++;
        }
        header('Content-type: application/json');
        echo json_encode([$i]);
    }
}
