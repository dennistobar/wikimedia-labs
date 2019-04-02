<?php

namespace model\mujer;

class metadata extends \DB\SQL\Mapper
{
    public function __construct()
    {
        parent::__construct(\model\database::instance('tools', \F3::get('db.user') . '__desafio'), 'mujeres_metadata');
    }

    public static function instance()
    {
        return (new self);
    }

    public static function addMetadata($page, $metadata, $value)
    {
        $exists = self::instance()->find(["page_id = $page AND metadata = '$metadata' AND value = $value"]);
        if (!!$exists === false) {
            $self = new self;
            $self->page_id = $page;
            $self->metadata = $metadata;
            $self->value = $value;
            $self->save();
        }
    }

}
