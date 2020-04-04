<?php


namespace App\Observer;

use App\Events\DefaultLoggable;
use Illuminate\Support\Arr;

class ModelObserver
{

    /**
     * 模型新建后
     */
    public function created($model)
    {
        $attributes = $model->getAttributes();
        $attributes = Arr::except($attributes, ['created_at', 'updated_at']);

        $baseModelName = get_class($model);
        $title = "新增记录";
        $content = $attributes;

        event(new DefaultLoggable($title, $content, null, $model));
    }

    /**
     * 只有确定更新后才记录日志
     */
    public function updated($model)
    {
        $dirty = $model->getDirty();
        $original = $model->getOriginal();

        // 有时候可能只要监控某些字段
        if (method_exists($model, 'limitObservedFields')) {
            $fields = $model->limitObservedFields();
            $dirty = Arr::only($dirty, $fields);
            $original = Arr::only($original, array_keys($dirty));
        } else {
            $dirty = Arr::except($dirty, ['updated_at']);
            $original = Arr::only($original, array_keys($dirty));
        }

        if (count($dirty)) {
            $baseModelName = get_class($model);
            $title = "修改记录";
            $content = $dirty;

            event(new DefaultLoggable($title, $content, null, $model));
        }
    }

    /**
     * 模型删除后
     */
    public function deleted($model)
    {
        $attributes = $model->getAttributes();

        $baseModelName = get_class($model);
        $title = "删除记录";
        $content = $attributes;

        event(new DefaultLoggable($title, $content, null, $model));
    }

}
