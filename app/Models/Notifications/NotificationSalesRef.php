<?php

namespace App\Models\Notifications;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NotificationSalesRef extends Model
{
    protected $table = 'notification_sales_ref_creation';
    protected $fillable = ['id','group_id','item_id','status','upd_images','description','before_login_or_after_login'];
}
