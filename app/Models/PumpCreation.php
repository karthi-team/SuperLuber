<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PumpCreation extends Model
{
   protected $table = 'pump_creation';
   protected $fillable = ['id','operator','description','pumpstatus','status','date_time','duration','created_at','updated_at','deleted_at'];
}
