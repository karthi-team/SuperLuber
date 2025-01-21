<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MachineCreation extends Model
{


    protected $table = 'machine_creation';
    protected $fillable = ['id','machine_id','machine_type','machine_name','model_number','purchase_date','description'];
}
