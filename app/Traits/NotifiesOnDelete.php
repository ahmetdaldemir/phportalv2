<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Model;

trait NotifiesOnDelete
{

    public static function bootNotifiesOnDelete()
    {
        static::deleted(function (Model $model) {
            // Burada silme işlemi gerçekleştirildikten sonra bir bildirim oluşturabiliriz
            $notification = new \App\Models\Notification();
            $notification->subject = get_class($model);
            $notification->subject_id = $model->getKey();
            $notification->note = 'Silme işlemi gerçekleştirildi: ' . $_GET['note'];
            $notification->save();
        });
    }


}
