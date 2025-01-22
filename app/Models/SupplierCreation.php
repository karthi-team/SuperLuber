<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SupplierCreation extends Model
{
    use HasFactory;
    protected $table = 'supplier_creation';
    protected $fillable = ['id','supplier_name','supplier_id','contact_person','contact_number','email_id','address','gst_number','next_review_date','creation_time','status','description'];
    public $timestamps = false; // Disable timestamps
}
