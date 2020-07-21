<?php
return [
    'operation_log' => [
        'enable' => true,
        'except' => [],
    ],

    'DatabaseBackup' => [
        'path' => storage_path('app/database/'),
        'part' => 20971520,
        'compress' => 1, //1:启用压缩 , 0:不启用
        'level' => 9, //1:普通 , 4:一般 , 9:最高
    ]
];
