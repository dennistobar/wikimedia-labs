<?php

namespace model\desafio;

class categoria extends \DB\SQL\Mapper
{
    public function __construct()
    {
        parent::__construct(\model\database::instance('tools', \F3::get('db.user').'__desafio'), 'categorias');
    }

    public static function resumen()
    {
        $qCat = "
        select distinct concat('[[:',cat.cat_name,'|',ifnull(cat2.cat_desc, substring(cat.cat_name, 21)),']]') as title
        , concat('{{PAGESINCATEGORY:',substring(cat.cat_name, 11),'}}') actual
        , (select cat_articulos from categorias cat2 where cat.cat_name = cat2.cat_name and substring(cat2.cat_timestamp, 1, 11) = 20170605000) '20170605'
        , (select cat_articulos from categorias cat2 where cat.cat_name = cat2.cat_name and substring(cat2.cat_timestamp, 1, 11) = 20170610000) '20170610'
        , (select cat_articulos from categorias cat2 where cat.cat_name = cat2.cat_name and substring(cat2.cat_timestamp, 1, 11) = 20170615000) '20170615'
        , (select cat_articulos from categorias cat2 where cat.cat_name = cat2.cat_name and substring(cat2.cat_timestamp, 1, 11) = 20170620000) '20170620'
        , (select cat_articulos from categorias cat2 where cat.cat_name = cat2.cat_name and substring(cat2.cat_timestamp, 1, 11) = 20170623233) '20170623'
        from categorias cat
        left join categoria_descripcion cat2
	       on cat.cat_name = cat2.cat_name
        order by 1";

        $data = \model\database::instance('tools', \F3::get('db.user').'__desafio')->exec($qCat);

        return array_map(function ($f) use ($data) {
            $f['evolucion'] = $f[max(array_keys(array_filter($f)))] - $f['20170605'];
            return $f;
        }, $data);
    }
}
