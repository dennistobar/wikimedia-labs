<?php

namespace route;

use \model\desafio\desafio as DesafioModel;

class desafio extends main
{
    public function index(\Base $fat)
    {
        $fat->set('Desafios', DesafioModel::instance()->find([], ['order' => 'desafio_start']));
        $fat->set('page.title', 'Desafíos de edición en Wikipedia en español');
        $fat->set('page.subtitle', 'Visión global');
        $fat->set('page.contents', 'desafio/listado.html');
    }

    public function home(\Base $fat)
    {
        $fat->set('ranking', \model\desafio\dashboard::ranking($fat->get('PARAMS.name')));
        $fat->set('days', \model\desafio\dashboard::days($fat->get('PARAMS.name')));
        $fat->set('page.subtitle', 'Resumen');
        $fat->set('page.contents', 'desafio/dashboard.html');
    }

    public function user(\Base $fat)
    {
        $fat->set('contribs', \model\desafio\ediciones::user($fat->get('PARAMS.name'), $fat->get('PARAMS.user')));
        $fat->set('stats', \model\desafio\ediciones::user_stats($fat->get('PARAMS.name'), $fat->get('PARAMS.user')));
        $fat->set('page.subtitle', 'Contribuciones de usuario '.\helper\formaters::instance()->nounderline($fat->get('PARAMS.user')));
        $fat->set('page.contents', 'desafio/usercontrib.html');
    }

    public function day(\Base $fat)
    {
        $dia = \helper\parsers::timestamp($fat->get('PARAMS.day'));
        $fat->set('contribs', \model\desafio\ediciones::day($fat->get('PARAMS.name'), $fat->get('PARAMS.day'), $fat->get('PARAMS.user')));
        $fat->set('day', implode('-', [$dia['day'], $dia['month'], $dia['year']]));
        $fat->set('page.subtitle', 'Contribuciones del día '.$fat->get('day'));
        $fat->set('page.contents', 'desafio/day.html');
    }

    public function resume(\Base $fat)
    {
        $Resumen = new \model\desafio\resumen($fat->get('PARAMS.name'));
        $fat->set('data', $Resumen->data());
        $fat->set('days', $Resumen->days());
        $fat->set('page.subtitle', 'Tabla resumen para Wikipedia');
        $fat->set('page.contents', 'desafio/resume.html');
    }

    public function beforeroute(\Base $fat)
    {
        $name = $fat->get('PARAMS.name');
        $desafio = \model\desafio\desafio::getByName($name);
        if (is_array($desafio)) {
            $desafio = array_pop($desafio);
        } else {
            die('Error, desafío no encontrado');
        }
        $fat->set('page.title', $desafio['desafio_title']);
        if ($fat->exists('PARAMS.name')) {
            $fat->set('desafio', $desafio);
            if ($desafio->has_resumen()) {
                $menu = [
                    ['href' => $fat->get('BASE').'/desafio/'.$name, 'txt' => 'General'],
                    ['href' => $fat->get('BASE').'/desafio/'.$name.'/resumen', 'txt' => 'Tabla de categoría'],
                ];
                $menu = array_map(function ($f) use ($fat) {
                    $f['classCSS'] = $fat->get('PATH') === $f['href'] ? 'is-active' : '';
                    return $f;
                }, $menu);
                $fat->set('page.menu_footer', $menu);
            }
        }
    }
}
