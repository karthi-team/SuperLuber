<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SalesExecutiveTimelogs extends Model
{
    protected $table = 'sales_executive_timelogs';
    protected $fillable = ['id','date','time','sales_executive_id','latitude','langititude','current_status','created_at','updated_at'];
}
