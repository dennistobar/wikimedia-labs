<?php

namespace route;

use \model\desafio\desafio;

class cron
{
    public static function cronSQL(\Base $fat)
    {
        $origin = \model\database::instance('commonswiki', 'commonswiki');
        $dest = \model\database::instance('tools', \F3::get('db.user').'__desafio');

        $max = $dest->exec('select ifnull(max(rc_timestamp),0) ultimo from ediciones')[0]['ultimo'];

        $desafio = new desafio;
        $timestamp = date('YmdHis');

        $desafios = $desafio->select('desafio_name, desafio_end', ['? between desafio_start and desafio_end', $timestamp]);
        $return = [];
        foreach ($desafios as $r_desafio) {
            $qData = 'select rc_id, rc_timestamp, rc_user, rc_user_text, rc_namespace, rc_title
                , abs(rc_old_len - rc_new_len) size, rc_comment, rc_this_oldid, :desafio as desafio
            from eswiki_p.recentchanges
            where rc_id > 0 and
                rc_namespace in (0, 104) and
                lower(rc_comment) like :string and
                rc_timestamp between :max and :end and
                rc_user > 5 and
                rc_new = 0 and
                rc_bot = 0;';

            $fecha = min([(int)$max+1, (int)(new \DateTime('-1 hour', new \DateTimeZone('UTC')))->format('YmdHis')]);

            $pData = ['max' => $fecha, 'string' => '%#'.$r_desafio['desafio_name'].'%', 'end' => $r_desafio['desafio_end'], 'desafio' => $r_desafio['desafio_name']];
            $qInsert = "Insert into ediciones
            select :rc_id, :rc_timestamp, :rc_user, :rc_user_text, :rc_namespace, :rc_title, :size, :rc_comment, :rc_this_oldid, :desafio from dual
            where not exists (select 1 from ediciones where rc_id = :rc_id) limit 1";
            $changes = $origin->exec($qData, $pData);
            foreach ($changes as $item) {
                $i++;
                $dest->exec($qInsert, $item);
            }
            $return[$r_desafio['desafio_name']] = ['start' => $fecha, 'edits' => count($changes), 'max' => $max];
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
            return 'Category:'.$item;
        }, $categorias));

        $data = \helper\api::get($query);

        $Categorias = new \ArrayObject($data->query->pages);
        foreach ($Categorias as $Categoria) {
            $pCategoria = new \model\desafio\categoria;
            $params = ['cat_name' => $Categoria->title, 'cat_articulos' => $Categoria->categoryinfo->pages, 'cat_timestamp' => (new \DateTime('now', new \DateTimeZone('UTC')))->format('YmdHi').'00'];
            $pCategoria->copyFrom($params);
            $pCategoria->save();
        }
    }
}
