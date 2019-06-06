<?php

namespace helper;

class api
{
    public static function get($queryString, $domain = 'es.wikipedia.org')
    {
        ini_set("user_agent", "Mediawiki-php-consumer 0.1");

        $url = sprintf('https://%s/w/api.php?%s&format=json', $domain, http_build_query($queryString));

        return json_decode(\Web::instance()->request($url)['body']);
    }
}
