<?php

namespace model\mujer;

class mujeres extends \DB\SQL\Mapper
{
    public function __construct()
    {
        parent::__construct(\model\database::instance('tools', \F3::get('db.user') . '__desafio'), 'mujeres');
    }

    public static function instance()
    {
        return (new self);
    }

    public static function getWihtoutWikidata()
    {
        return self::instance()->find(['wikidata_item is null']);
    }

    public static function getWihtoutCitizenship()
    {
        return self::instance()->find(['country is null', 'wikidata_item is not null'], ['limit' => 100, 'order' => 'rand()']);
    }
}
