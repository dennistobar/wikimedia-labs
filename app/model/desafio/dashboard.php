<?php

namespace model\desafio;

class dashboard {

    public static function ranking(){
        $qRanking = "select rc_user_text user, count(distinct rc_title) articles
            , count(1) edits, sum(rc_size_abs) size
            from ediciones
            group by 1
            order by articles desc, edits desc, size desc";

        return \model\database::instance('tools', \F3::get('db.user').'__desafio')->exec($qRanking);
    }


    public static function days(){
        $qDays = "select substring(rc_timestamp, 1, 8) day, count(1) edits, sum(rc_size_abs) size
            , count(distinct rc_title) articles
        from ediciones
        group by 1;
        order by 1";

        $rDays = \model\database::instance('tools', \F3::get('db.user').'__desafio')->exec($qDays);

        array_walk($rDays, function(&$item){
            $item['year'] = substr($item['day'], 0, 4);
            $item['month'] = substr($item['day'], 4, 2);
            $item['day'] = substr($item['day'], 6, 2);
        });

        return $rDays;

    }

}
