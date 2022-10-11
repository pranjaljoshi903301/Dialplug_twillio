<?php

namespace Corals\Foundation\Search;

class TermBuilder
{

    public static function terms($search, $config)
    {
        $search = trim(preg_replace('/[\/+\-><()~*\"@]+/', 'X', $search));
        
        $wildcards = $config['enable_wildcards'] ?? true;

        $terms = collect(preg_split('/[\s,]+/', $search));

        if ($wildcards === true) {
            $terms->each(function ($part) {
                return $part . '*';
            });
        }
        return $terms;
    }

}
