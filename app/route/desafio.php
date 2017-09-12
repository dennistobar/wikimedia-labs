<?php

namespace route;

class desafio extends main
{
    public function index(\Base $fat)
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
        $data = \model\desafio\categoria::resumen();
        $fat->set('data', $data);
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
        $fat->set('desafio', $desafio);

        $fat->set('page.title', $desafio['desafio_title']);
    }
}
