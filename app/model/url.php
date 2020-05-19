<?php

namespace model;

use DateTime;

class Url
{

    const MIN_DAYS = 100;

    private $url;
    private $status = [];

    /**
     * Constructor de clase
     *
     * @param string $url
     */
    public function __construct(string $url)
    {
        if (filter_var($url, FILTER_VALIDATE_URL) === false) {
            throw new \Exception(sprintf('%s no es una URL válida', $url));
        }
        $this->url = $url;
        if ($this->isInternetAchive() || $this->isWikidata()) {
            throw new \Exception(sprintf('%s no será enviada a Internet Archive', $url));
        }
    }

    /**
     * Constructor de clase en factory
     *
     * @param string $url
     * @return void
     */
    public static function create(string $url)
    {
        return new self($url);
    }

    public function sendArchive(): array
    {
        $this->checkArchiveStatus();
        if ($this->getStatus('archive') === false) {
            return [false, true, $this->url, 'Ya está en el Internet Archive'];
        }
        $this->checkUrlStatus();
        if ($this->getStatus('http') === false) {
            return [false, false, $this->url, 'La URL no está disponible'];
        }
        $this->uploadArchive();
        if ($this->getStatus('upload') === false) {
            return [false, false, $this->url, 'No se pudo subir al Internet Archive'];
        }
        return [true, true, $this->url, 'Subido!'];
    }

    /**
     * Chequea si el enlace es Wikidata
     *
     * @return boolean
     */
    private function isWikidata(): bool
    {
        return stripos($this->url, 'wikidata.org') !== false;
    }

    /**
     * Chequea el enlace si es Internet Archive
     *
     * @return boolean
     */
    private function isInternetAchive(): bool
    {
        return stripos($this->url, 'web.archive.org/web/') !== false;
    }

    private function checkArchiveStatus()
    {
        $request = \Web::instance()->request('http://archive.org/wayback/available?url=' . $this->url);
        $apiArchive = json_decode($request['body']);
        $timestamp = $apiArchive->archived_snapshots->closest->timestamp;
        if (isset($timestamp) === false) {
            $this->setStatus('archive', false);
            return;
        }
        $date = date_create_from_format('YmdHis', $apiArchive->archived_snapshots->closest->timestamp);
        $this->setStatus('archive', $date->diff(new DateTime())->days > self::MIN_DAYS);
    }

    private function checkUrlStatus()
    {
        $headers = \Web::instance()->request($this->url)['headers'];
        $statusURL = !!array_filter($headers, function ($head) {
            return stripos($head, '200 OK') !== false || stripos($head, 'HTTP/2 200') !== false;
        });
        $this->setStatus('http', $statusURL);
    }

    private function uploadArchive()
    {
        $headers = \Web::instance()->request('http://web.archive.org/save/' . $this->url)['headers'];
        $sent = !!array_filter($headers, function ($head) {
            return stripos($head, '200 OK');
        });
        $this->setStatus('upload', $sent);
    }

    /**
     * Setea el estado de una comprobación
     *
     * @param string $key
     * @param boolean $value
     * @return void
     */
    private function setStatus(string $key, bool $value)
    {
        $this->status[$key] = $value;
    }

    /**
     * Obtiene el estado de una comprobación. Si no existe la comprobación, asume falso
     *
     * @param string $key
     * @return boolean
     */
    private function getStatus(string $key): bool
    {
        return $this->status[$key] ?? false;
    }
}