<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AlertCreation extends Model
{
    use HasFactory;

    protected $table = 'alert_creation';
    protected $fillable = ['alert_title', 'alert_type', 'description', 'status'];
    public $timestamps = false;
}
