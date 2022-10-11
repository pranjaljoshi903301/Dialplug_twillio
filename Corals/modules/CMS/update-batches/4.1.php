<?php


\DB::table('settings')->insert([
    [
        'code' => 'cms_comments_allow_guest',
        'type' => 'BOOLEAN',
        'category' => 'CMS',
        'label' => 'Comments Allow Guest ',
        'value' => 'false',
        'editable' => 1,
        'hidden' => 0,
        'created_at' => now(),
        'updated_at' => now(),
    ]
]);
