<?php

namespace helper;

class OresHelper
{

    protected $wiki = '';

    protected $revId;

    /**
     * Constructor de clase
     */
    public function __construct(string $wiki = 'eswiki')
    {
        $this->wiki = $wiki;
    }

    /**
     * Envía el número de ID a obtener
     */
    protected function getRevId(int $revId): self
    {
        $this->revId = intval($revId);

        return $this;
    }

    /**
     * Obtiene los resultados a partir de la consulta
     *
     * @return mixed
     */
    public function getResults()
    {
        ini_set("user_agent", "Mediawiki-php-consumer 0.1");
        $url = sprintf('https://ores.wikimedia.org/v3/scores/%s/%s', $this->wiki, $this->revId);

        return json_decode(\Web::instance()->request($url)['body']);
    }

    /**
     * Crea en forma estática el Helper
     */
    public static function createFromRevId(int $revId, string $wiki = 'eswiki'): OresHelper
    {
        return (new self($wiki))->getRevId($revId);
    }
}
