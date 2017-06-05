<?php

namespace route;

class desafio extends main
{
    public function index(\Base $fat)
    {
        $fat->set('ranking', \model\desafio\dashboard::ranking());
        $fat->set('days', \model\desafio\dashboard::days());
        $fat->set('page.subtitle', 'Resumen');
        $fat->set('page.contents', 'desafio/dashboard.html');
    }

    public function user(\Base $fat)
    {
        $fat->set('contribs', \model\desafio\ediciones::user($fat->get('PARAMS.user')));
        $fat->set('stats', \model\desafio\ediciones::user_stats($fat->get('PARAMS.user')));
        $fat->set('page.subtitle', 'Contribuciones de usuario '.\helper\formaters::instance()->nounderline($fat->get('PARAMS.user')));
        $fat->set('page.contents', 'desafio/usercontrib.html');
    }

    public function day(\Base $fat)
    {
        $dia = \helper\parsers::timestamp($fat->get('PARAMS.day'));
        $fat->set('contribs', \model\desafio\ediciones::day($fat->get('PARAMS.day')));
        $fat->set('day', implode('-', [$dia['day'], $dia['month'], $dia['year']]));
        $fat->set('page.subtitle', 'Contribuciones del día '.$fat->get('day'));
        $fat->set('page.contents', 'desafio/day.html');
    }

    public function cronSQL(\Base $fat)
    {
        $origin = \model\database::instance('commonswiki', 'commonswiki');
        $dest = \model\database::instance('tools', \F3::get('db.user').'__desafio');

        $max = $dest->exec('select ifnull(max(rc_timestamp),0) ultimo from ediciones')[0]['ultimo'];

        $qData = 'select rc_id, rc_timestamp, rc_user, rc_user_text, rc_namespace, rc_title
            , abs(rc_old_len - rc_new_len) size, rc_comment, rc_this_oldid
        from eswiki_p.recentchanges
        where rc_id > 0 and
            rc_namespace in (0, 104) and
            lower(rc_comment) like :string and
            rc_timestamp between :max and 20170623000000 and
            rc_user > 5 and
            rc_new = 0 and
            rc_bot = 0;';

        $fecha = max([(int)$max+1, (int)(new \DateTime('-1 hour', new \DateTimeZone('UTC')))->format('YmdHis')]);

        $pData = ['max' => $fecha, 'string' => '%#calidad2017%'];

        $qInsert = "Insert into ediciones
        select :rc_id, :rc_timestamp, :rc_user, :rc_user_text, :rc_namespace, :rc_title, :size, :rc_comment, :rc_this_oldid from dual
        where not exists (select 1 from ediciones where rc_id = :rc_id) limit 1";
        $changes = $origin->exec($qData, $pData);
        foreach ($changes as $item) {
            $dest->exec($qInsert, $item);
        }
        header('Content-type: application/json');
        echo json_encode(['start' => $fecha, 'edits' => count($changes), 'max' => $max]);
    }

    public function cronCategories(\Base $fat)
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

    public function beforeroute(\Base $fat)
    {
        $fat->set('page.title', 'Desafío para mejorar la calidad de Wikipedia');
    }
}
