<?php

namespace model\mujer;

class origin
{

    private $conn = null;
    private $parameters = ['inicio' => null, 'fin' => null];

    public function __construct()
    {
        $this->conn = \model\database::instance('commonswiki', 'eswiki');
    }

    public function setParameters($inicio, $fin)
    {
        $this->parameters['inicio'] = $inicio;
        $this->parameters['fin'] = $fin;
    }

    public function getData($page = null)
    {
        $query = "select page_id, ips_item_id as wikidata_item, rev_timestamp
        from revision, categorylinks, page
        left join wikidatawiki_p.wb_items_per_site on ips_site_id = 'eswiki' and ips_site_page = replace(page_title, '_', ' ')
        where page_id = rev_page
        and rev_timestamp between :inicio and :fin
        and rev_parent_id = 0
        and page_namespace = 0
        and cl_from = page_id
        and cl_to = 'Mujeres'
        and cl_timestamp > :inicio
        AND page_id = ifnull(:page_id, page_id)";
        return $this->conn->exec($query, $this->parameters + ['page_id' => $page], 200);
    }
}
