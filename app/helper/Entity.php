<?php

namespace helper;

use Wikidata\Wikidata as WikidataWikidata;

class Entity extends WikidataWikidata
{

    /**
     * Obtiene la ciudadanía de un item de Wikidata
     *
     * @param integer $item
     * @return string
     */
    public function getCitizenship(int $item): string
    {
        $entity = $this->get('Q' . $item, 'es');
        $properties = $entity->properties->toArray();
        return $properties['P27']->value ?? '';
    }

}
