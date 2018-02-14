<?php

namespace model\desafio;

class resumen
{
    private $name;

    public function __construct($name)
    {
        $this->name = $name;
    }

    public function data()
    {
        $qCat = "
        select distinct concat('[[:',cat.cat_name,'|',ifnull(cat.cat_desc, substring(cat.cat_name, 21)),']]') as title
        , concat('{{PAGESINCATEGORY:',substring(cat.cat_name, 11),'}}') actual
        , tabla_valor
        , tabla_fecha
        from categoria_descripcion cat
        left join tabla_resumen
            on cat.cat_name = tabla_categoria
            and tabla_desafio = :desafio
        order by title, tabla_fecha";

        $qDates = "select min(tabla_fecha) minimo, max(tabla_fecha) maximo from tabla_resumen where tabla_desafio = :desafio";

        $rows = \model\database::instance('tools', \F3::get('db.user').'__desafio')->exec($qCat, ['desafio' => $this->name]);
        $dates = \model\database::instance('tools', \F3::get('db.user').'__desafio')->exec($qDates, ['desafio' => $this->name]);
        $date = array_pop($dates);
        $retorno = [];
        foreach ($rows as $row) {
            $retorno[$row['title']]['title'] = $row['title'];
            $retorno[$row['title']]['date_raw'][$row['tabla_fecha']] = is_numeric($row['tabla_valor']) ? (int)$row['tabla_valor'] : null;
            $retorno[$row['title']]['last'] = !is_null($row['tabla_valor']) ? (int)$row['tabla_valor'] : (int)$retorno[$row['title']]['last'];
            $retorno[$row['title']]['actual'] = $row['actual'];
        }
        return array_map(function ($f) use ($date) {
            $f['evolucion'] = $f['last'] - $f['date_raw'][$date['minimo']];
            return $f;
        }, $retorno);
    }

    public function days()
    {
        $qDays = "select distinct tabla_fecha from tabla_resumen where tabla_desafio = :desafio order by 1";
        $days = \model\database::instance('tools', \F3::get('db.user').'__desafio')->exec($qDays, ['desafio' => $this->name]);
        foreach ($days as &$day) {
            $day_parser = \helper\parsers::timestamp($day['tabla_fecha']);
            $day['name'] = (int)$day_parser['day'].' de '.\helper\parsers::mes((int)$day_parser['month']-1);
        }
        return $days;
    }
}
