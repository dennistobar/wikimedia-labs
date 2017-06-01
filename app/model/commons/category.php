<?php

namespace model\commons;

class category
{
    private $name;
    private $search;
    private $user;

    public function __construct($name)
    {
        $this->name = str_replace("_", " ", $name);
        $this->search = str_replace(" ", "_", $name);
    }

    public function users()
    {
        $qUsers = "select ifnull(oi_user_text, img_user_text) as user, count(distinct img_name) as number
        from page, image
        left join oldimage o1
            on oi_name = img_name
            and oi_timestamp = (select min(o2.oi_timestamp) from oldimage o2 where o1.oi_name = o2.oi_name)
        , categorylinks
        where page_title = img_name
            and cl_from = page_id
            and cl_to = :cat
            and page_namespace = 6
        group by 1
        order by 2 DESC";
        $pUsers = ['cat' => $this->search];
        return \model\database::instance('commonswiki', 'commonswiki')->exec($qUsers, $pUsers);
    }

    public function details_user($user)
    {
        $qDetails = '
            select img_name, img_size, img_metadata, img_timestamp, img_width, img_height,
                (select count(distinct oi_timestamp) from oldimage where oi_name = img_name) as revs
            from page, image
            left join oldimage o1
                on oi_name = img_name
                and oi_timestamp = (select min(o2.oi_timestamp) from oldimage o2 where o1.oi_name = o2.oi_name)
            , categorylinks
            where page_title = img_name
                and cl_from = page_id
                and cl_to = :cat
                and page_namespace = 6
                and ifnull(oi_user_text , img_user_text) = :user
                order by img_timestamp DESC;';
        $pDetails = ['cat'=>$this->search, 'user'=>$user];
        $this->user = $user;
        return \model\database::instance('commonswiki', 'commonswiki')->exec($qDetails, $pDetails);
    }

    public function parameters()
    {
        return ['search' => $this->search, 'name' => $this->name, 'user' => $this->user];
    }

    public static function load($name)
    {
        return new self($name);
    }
}
