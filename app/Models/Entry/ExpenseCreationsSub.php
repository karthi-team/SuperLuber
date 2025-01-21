<?php

namespace App\Models\Entry;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExpenseCreationsSub extends Model
{
    protected $table = 'expense_creations_sublist';
    protected $fillable = ['id','entry_date','visitor_sub_id','dealer_sub_id','market_sub_id','sales_expense_main_id','expense_id','to_loct','sub_expense_id','from_loct','total_amount','expense_amount','ta_amount','delete_status','created_at','updated_at','created_user_id','updated_user_id','created_ipaddress','updated_ipaddress','created_user_agent','updated_user_agent','mode_of_payment','travel','fuel','da_1','courier','lodging','phone','others','image_name'];
}
