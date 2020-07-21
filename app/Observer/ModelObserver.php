<?php


namespace App\Observer;

use App\Models\Activitylog;
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
        $title = ($model->desc?'【'.$model->desc.'】':'')." 新增记录[ID:".$attributes['id'].']';
        Activitylog::addlog([
            'model' => $model,
            'title' => $title,
            'content' => $attributes,
        ]);

    }

    /**
     * 只有确定更新后才记录日志
     */
    public function updated($model)
    {
        $dirty = $model->getDirty();
        $original = $model->getOriginal();
        $attributes = $model->getAttributes();

        // 有时候可能只要监控某些字段
        if (method_exists($model, 'limitObservedFields')) {
            $fields = $model->limitObservedFields();
            $dirty = Arr::only($dirty, $fields);
            $original = Arr::only($original, array_keys($dirty));
        } else {
            $dirty = Arr::except($dirty, ['updated_at']);
            $original = Arr::only($original, array_keys($dirty));
        }

        if (count($dirty) > 0) {
            $title =  ($model->desc?'【'.$model->desc.'】':'')." 修改记录[ID:".$attributes['id'].']';
            Activitylog::addlog([
                'model' => $model,
                'title' => $title,
                'content' => $dirty,
            ]);
        }
    }

    public function restored($model)
    {
        $attributes = $model->getAttributes();
        $title =  ($model->desc?'【'.$model->desc.'】':'')." 从回收站恢复记录[ID:".$attributes['id'].']';
        Activitylog::addlog([
            'model' => $model,
            'title' => $title,
            'content' => $attributes,
        ]);
    }


    /**
     * 模型删除后
     */
    public function deleted($model)
    {
        $attributes = $model->getAttributes();
        $title =  ($model->desc?'【'.$model->desc.'】':'')." 删除记录[ID:".$attributes['id'].']';
        Activitylog::addlog([
            'model' => $model,
            'title' => $title,
            'content' => $attributes,
        ]);
    }

    public function forceDeleted($model)
    {
        $attributes = $model->getAttributes();
        $title =  ($model->desc?'【'.$model->desc.'】':'')." 在回收站中删除记录[ID:".$attributes['id'].']';
        Activitylog::addlog([
            'model' => $model,
            'title' => $title,
            'content' => $attributes,
        ]);
    }

}
