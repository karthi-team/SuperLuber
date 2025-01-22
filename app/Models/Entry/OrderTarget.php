<?php

namespace App\Models\Entry;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderTarget extends Model
{
    protected $table = 'order_target';
    protected $fillable = ['id','entry_date','shift_type','state_id','shift_type1','district_id','area_id','manager_name','order_target_type','shift_type','category_type','description','check','delete_status','created_at','updated_at','created_user_id','updated_user_id','created_ipaddress','updated_ipaddress','created_user_agent','updated_user_agent'];
}
