<?php

namespace model\desafio;

class dashboard {

    public static function ranking($name){
        $qRanking = "select rc_user_text user, count(distinct rc_title) articles
            , count(1) edits, sum(rc_size_abs) size
            from ediciones
            where desafio = ?
            group by 1
            order by articles desc, edits desc, size desc";
        $pRanking = [$name];
        return \model\database::instance('tools', \F3::get('db.user').'__desafio')->exec($qRanking, $pRanking);
    }


    public static function days($name){
        $qDays = "select substring(rc_timestamp, 1, 8) timestamp, count(1) edits, sum(rc_size_abs) size
            , count(distinct rc_title) articles
        from ediciones
        where desafio = ?
        group by 1;
        order by 1";
        $pDays = [$name];

        $rDays = \model\database::instance('tools', \F3::get('db.user').'__desafio')->exec($qDays, $pDays);

        array_walk($rDays, function(&$item){
            $item = array_merge($item, \helper\parsers::timestamp($item['timestamp']));
        });

        return $rDays;

    }

}
