<?php

return [
    /**
     * 事件相关
     */
    'event' => [
        /**
         * 模型观察者.
         * 以下添加的模型都被ModelObserver监听和观察
         * 后续添加
         */
        'observers' => [
            \App\Models\User::class,
            \App\Models\Permission::class,
            \App\Models\Role::class,
            \App\Models\Site::class,
            \App\Models\Attachment::class,
            \App\Models\AttachmentRoup::class,

            //...
        ],
    ],
];
