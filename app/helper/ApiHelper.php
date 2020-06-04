<?php

namespace helper;

class ApiHelper
{

    protected $domain = '';

    protected $queryString;

    /**
     * Constructor de clase
     */
    public function __construct(string $domain = 'es.wikipedia.org')
    {
        $this->domain = $domain;
    }

    /**
     * Genera el requerimiento de GET
     *
     * @param array|object $queryString
     */
    protected function get(array $queryString): self
    {
        $this->queryString = http_build_query($queryString);

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
        $url = sprintf('https://%s/w/api.php?%s&format=json', $this->domain, $this->queryString);

        return json_decode(\Web::instance()->request($url)['body']);
    }

    /**
     * Crea en forma estática el Helper
     */
    public static function createFromArray(array $queryString, string $domain = 'es.wikipedia.org'): ApiHelper
    {
        return (new self($domain))->get($queryString);
    }
}
