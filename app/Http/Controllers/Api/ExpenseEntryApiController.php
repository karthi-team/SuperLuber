<?php
namespace App\Http\Controllers\Api;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Entry\ExpenseCreationsMain;
use App\Models\Entry\ExpenseCreationsSub;
use App\Models\MarketCreation;
use App\Models\DealerCreation;
use App\Models\ItemCreation;
use App\Models\ItemLitersType;
use App\Models\SalesRepCreation;
use App\Models\MarketManagerCreation;
use App\Models\ExpenseTypeCreation;
use App\Models\SubExpenseTypeCreation;
use App\Models\Entry\SalesOrderD2SMain;
use App\Models\Entry\VisitorCreation;
use App\Models\ItemPropertiesType;
use Carbon\Carbon;

class ExpenseEntryApiController extends Controller
{

    public function expense_order_number_api(Request $request)
    {
        $sales_executive_id = $request->input('sales_executive_id');
        $date = Carbon::now();
        $current_date = $date->format('Y-m-d');

        if (empty($sales_executive_id)) {
            return response()->json(['status' => 'FAILURE', 'message' => 'Sales Executive Id Not Found'], 404);
        }

        $expense_form = ExpenseCreationsMain::select('id', 'expense_no','expense_date', 'sales_rep_creation_id', 'dealer_creation_id' ,'status', 'description', 'mode_of_payment', 'market_manager_id')
        ->where('sales_rep_creation_id', $sales_executive_id)
        ->where('expense_date', $current_date)
        ->first();

        if ($expense_form != ''){

            $expense_form = ExpenseCreationsMain::select('id as expense_id', 'expense_no','expense_date', 'sales_rep_creation_id as sales_executive_id')
                ->where('sales_rep_creation_id', $sales_executive_id)
                ->where('expense_date', $current_date)
                ->first();

        } else {
            $expense_form = [];
            $expense_id = '';
            $main_tb = (new ExpenseCreationsMain)->getTable();
            $next_id = DB::select("SHOW TABLE STATUS LIKE '".$main_tb."'");
            $expense_no="SALEXP_".date("ym")."_".$next_id[0]->Auto_increment;
            $sales_executive_id = $request->input('sales_executive_id');
            $expense_form = [
                'expense_id' => $expense_id,
                'expense_no' => $expense_no,
                'expense_date' => $current_date,
                'sales_executive_id' => $sales_executive_id,
            ];
        }

        if ($expense_form != '') {
            return response()->json(['status' => 'SUCCESS', 'message' => 'Expense Form List Showed Successfully', 'expense_form' => $expense_form], 200);
        } else {
            return response()->json(['status' => 'FAILURE', 'message' => 'Expense Form List Not Found'], 404);
        }
    }

    public function expense_get_sales_executive_api(Request $request)
    {
        $entry_date = $request->input('expense_date');
        // $date = Carbon::createFromFormat('d-m-Y', $expense_date);
        // $entry_date = $date->format('Y-m-d');

        if($entry_date != ''){

            $SalesRepCreation_tb = (new SalesRepCreation)->getTable();
            $SalesOrderD2SMain_tb = (new SalesOrderD2SMain)->getTable();

            $sales_execs = SalesOrderD2SMain::select(
                $SalesRepCreation_tb . '.id',
                $SalesRepCreation_tb . '.sales_ref_name'
            )
            ->join($SalesRepCreation_tb, $SalesRepCreation_tb . '.id', '=', $SalesOrderD2SMain_tb . '.sales_exec')
            ->where($SalesOrderD2SMain_tb . '.entry_date', '=', $entry_date)
            ->where($SalesOrderD2SMain_tb . '.status', '=', '1')
            ->groupBy(
                $SalesRepCreation_tb . '.id',
                $SalesRepCreation_tb . '.sales_ref_name'
            )
            ->get();

            return response()->json(['status' => 'SUCCESS', 'message' => 'Sales Executive Name Showed Successfully', 'sales_executive_name' => $sales_execs], 200);
        }else{
            return response()->json(['status' => 'FAILURE', 'message' => 'Expense Number Not Found'], 404);
        }
    }

    public function expense_main_insert_api(Request $request)
    {

        $expense_no = $request->input('expense_number');
        $expense_date = $request->input('expense_date');
        $sales_rep_creation_id = $request->input('sales_executive_name');
        $market_manager_id = $request->input('market_manager_name');
        $status = '1';
        $description = $request->input('description');
        $mode_of_payment = $request->input('mode_of_payment');

        if (empty($expense_no)) {
            return response()->json(['status' => 'FAILURE', 'message' => 'Expense Number Not Found'], 404);
        }

        if (empty($expense_date)) {
            return response()->json(['status' => 'FAILURE', 'message' => 'Expense Date Not Found'], 404);
        }

        if (empty($sales_rep_creation_id)) {
            return response()->json(['status' => 'FAILURE', 'message' => 'Sales Executive Id Not Found'], 404);
        }

        if (empty($market_manager_id)) {
            return response()->json(['status' => 'FAILURE', 'message' => 'Manager Name Not Found'], 404);
        }

        if (empty($mode_of_payment)) {
            return response()->json(['status' => 'FAILURE', 'message' => 'Mode of Payment Not Found'], 404);
        }

        $tb = new ExpenseCreationsMain();
        $tb->entry_date  = Carbon::now();
        $tb->expense_no = $expense_no;
        $tb->expense_date = $expense_date;
        $tb->sales_rep_creation_id = $sales_rep_creation_id;
        $tb->market_manager_id = $market_manager_id;
        $tb->status = $status;
        $tb->description = $description;
        $tb->mode_of_payment = $mode_of_payment;
        $tb->save();

        $get_id =  ExpenseCreationsMain::select('id as expense_creation_main_id')
        ->orderBy('id', 'desc')
        ->first();

        return response()->json(['status' => 'SUCCESS', 'message' => 'Expense Creation Main List Inserted Successfully','Expense_main_id' => $get_id ], 200);
    }

    public function expense_sublist_insert_api(Request $request)
    {
        // main
        $main_id=$request->input('expense_main_id');
        $expense_no = $request->input('expense_number');
        $expense_date = $request->input('expense_date');
        $sales_rep_creation_id = $request->input('sales_executive_name');
        $market_manager_id = $request->input('market_manager_name');
        $status = '1';
        $description = $request->input('description');
        $mode_of_payment = $request->input('mode_of_payment');

        //sub
        $expense_id = $request->input('expense_name');
        $sub_expense_id = $request->input('sub_expense_name');
        $total_amount = $request->input('total_amount');
        $expense_amount = $request->input('expense_amount');

        $dealer_sub_id = $request->input('dealer_name');
        $visitor_sub_id = $request->input('visitor_name');
        $market_sub_id = $request->input('market_name');

        $travel = $request->input('travel');
        $fuel = $request->input('fuel');
        $da_1 = $request->input('da_1');
        $courier = $request->input('courier');
        $lodging = $request->input('lodging');
        $phone = $request->input('phone');
        $others = $request->input('others');





        if (empty($expense_no)) {
            return response()->json(['status' => 'FAILURE', 'message' => 'Expense Number Not Found'], 404);
        }
        if (empty($expense_date)) {
            return response()->json(['status' => 'FAILURE', 'message' => 'Expense Date Not Found'], 404);
        }
        if (empty($sales_rep_creation_id)) {
            return response()->json(['status' => 'FAILURE', 'message' => 'Sales Executive Id Not Found'], 404);
        }
        if (empty($market_manager_id)) {
            return response()->json(['status' => 'FAILURE', 'message' => 'Manager Name Not Found'], 404);
        }
        if (empty($mode_of_payment)) {
            return response()->json(['status' => 'FAILURE', 'message' => 'Mode of Payment Not Found'], 404);
        }


        if($main_id==''){
            $main_id = ExpenseCreationsMain::insertGetId([
                'entry_date' => Carbon::now(),
                'expense_no' => $expense_no,
                'expense_date' => $expense_date,
                'sales_rep_creation_id' => $sales_rep_creation_id,
                'market_manager_id' => $market_manager_id,
                'status' => $status,
                'description' => $description,
                'mode_of_payment' => $mode_of_payment

            ]);
        }

        $tb = new ExpenseCreationsSub();
        $tb->entry_date  = Carbon::now();
        $tb->sales_expense_main_id = $main_id;
        $tb->expense_id = $expense_id;
        $tb->sub_expense_id = $sub_expense_id;
        $tb->total_amount = $total_amount;
        $tb->expense_amount = $expense_amount;

        $tb->dealer_sub_id = $dealer_sub_id;
        $tb->visitor_sub_id = $visitor_sub_id;
        $tb->market_sub_id = $market_sub_id;
        $tb->travel = $travel;
        $tb->fuel = $fuel;
        $tb->da_1 = $da_1;
        $tb->courier = $courier;
        $tb->lodging = $lodging;
        $tb->phone = $phone;
        $tb->others = $others;
        if ($request->hasFile('image_name')) {
            $image = $request->file('image_name');
            $imgName = $image->getClientOriginalName();
            $image->storeAs('public/expense_img', $imgName);
            $tb->image_name = $imgName;
        }
        $tb->save();

        return response()->json(['status' => 'SUCCESS', 'message' => 'Expense Creation Sublist Inserted Successfully','Expense_main_id' => $main_id ], 200);
    }

    public function expense_get_dealer_api(Request $request)
    {
        $entry_date = $request->input('expense_date');
        // $date = Carbon::createFromFormat('d-m-Y', $expense_date);
        // $entry_date = $date->format('Y-m-d');

        $sales_rep_id = $request->input('sales_executive_name');

        if (empty($entry_date)) {
            return response()->json(['status' => 'FAILURE', 'message' => 'Entry Date Not Found'], 404);
        }

        if (empty($sales_rep_id)) {
            return response()->json(['status' => 'FAILURE', 'message' => 'Sales Executive Name Not Found'], 404);
        }

        $DealerCreation_tb = (new DealerCreation)->getTable();
        $SalesOrderD2SMain_tb = (new SalesOrderD2SMain)->getTable();

        $dealer_creation = SalesOrderD2SMain::select(
        $DealerCreation_tb . '.id',
        $DealerCreation_tb . '.dealer_name'
        )
        ->join($DealerCreation_tb, $DealerCreation_tb . '.id', '=', $SalesOrderD2SMain_tb . '.dealer_creation_id')
        ->where($SalesOrderD2SMain_tb . '.entry_date', '=', $entry_date)
        ->where($SalesOrderD2SMain_tb . '.sales_exec', '=', $sales_rep_id)
        ->where($DealerCreation_tb . '.status', '=', '1')
        ->groupBy(
        $DealerCreation_tb . '.id',
        $DealerCreation_tb . '.dealer_name'
        )
        ->get();

        if($dealer_creation != ''){

            return response()->json(['status' => 'SUCCESS', 'message' => 'Dealer Name Showed Successfully', 'dealer_name' => $dealer_creation], 200);
        }else{
            return response()->json(['status' => 'FAILURE', 'message' => 'Dealer Name Not Found'], 404);
        }
    }


    public function expense_get_visitor_api(Request $request)
    {
        $entry_date = $request->input('expense_date');
        // $date = Carbon::createFromFormat('d-m-Y', $expense_date);
        // $entry_date = $date->format('Y-m-d');

        $sales_rep_id = $request->input('sales_executive_name');

        if (empty($entry_date)) {
            return response()->json(['status' => 'FAILURE', 'message' => 'Entry Date Not Found'], 404);
        }

        if (empty($sales_rep_id)) {
            return response()->json(['status' => 'FAILURE', 'message' => 'Sales Executive Name Not Found'], 404);
        }

        $SalesOrderD2SMain_tb = (new SalesOrderD2SMain)->getTable();
        $VisitorCreation_tb = (new VisitorCreation)->getTable();

        $visitor_creation = SalesOrderD2SMain::select(
        $VisitorCreation_tb . '.id',
        $VisitorCreation_tb . '.visitor_name'
        )

        ->join($VisitorCreation_tb, $VisitorCreation_tb . '.d2s_id', '=', $SalesOrderD2SMain_tb . '.id')
        ->where($VisitorCreation_tb . '.order_date', '=', $entry_date)
        ->where($VisitorCreation_tb . '.sales_exec', '=', $sales_rep_id)

        ->groupBy(
        $VisitorCreation_tb . '.id',
        $VisitorCreation_tb . '.visitor_name'
        )
        ->get();

        if($visitor_creation != ''){

            return response()->json(['status' => 'SUCCESS', 'message' => 'Visitor Name Showed Successfully', 'visitor_creation' => $visitor_creation], 200);
        }else{
            return response()->json(['status' => 'FAILURE', 'message' => 'Visitor Name Not Found'], 404);
        }
    }

    public function expense_main_edit_api(Request $request)
    {

        $id = $request->input('expense_main_id');

        if (empty($id)) {
            return response()->json(['status' => 'FAILURE', 'message' => 'Expense Main Id Not Found'], 404);
        }

        $Expense_Main_Edit = ExpenseCreationsMain::select('id','expense_no','expense_date','sales_rep_creation_id','dealer_creation_id','status','description','mode_of_payment','market_manager_id')->where('id',$id)->get()->first();

        if ($Expense_Main_Edit != '') {
            return response()->json(['status' => 'SUCCESS', 'message' => 'Expense Main Edit List Showed Successfully', 'Expense_Main_Edit' => $Expense_Main_Edit], 200);
        } else {
            return response()->json(['status' => 'FAILURE', 'message' => 'Expense Main Edit List Not Found'], 404);
        }
    }

    public function expense_sublist_api(Request $request)
    {

        $main_id = $request->input('expense_main_id');

        if (empty($main_id)) {
            return response()->json(['status' => 'FAILURE', 'message' => 'Expense Main Id Not Found'], 404);
        }

        $sub_tb = (new ExpenseCreationsSub)->getTable();
        $ExpenseTypeCreation_tb = (new ExpenseTypeCreation)->getTable();
        $DealerCreation_tb = (new DealerCreation)->getTable();
        $MarketCreation_tb = (new MarketCreation)->getTable();
        $VisitorCreation_tb = (new VisitorCreation)->getTable();
        $ExpenseTypeCreation_tb = (new ExpenseTypeCreation)->getTable();
        $ItemPropertiesType_tb = (new ItemPropertiesType)->getTable();
        $SubExpenseTypeCreation_tb = (new SubExpenseTypeCreation)->getTable();
        $ItemLitersType_tb = (new ItemLitersType)->getTable();

        $tb1=DB::select('select id,(select expense_type from '.$ExpenseTypeCreation_tb.' where id='.$sub_tb.'.expense_id) as expense_name,(select area_name from '.$MarketCreation_tb.' where id='.$sub_tb.'.market_sub_id) as market_name,(select visitor_name from '.$VisitorCreation_tb.' where id='.$sub_tb.'.visitor_sub_id) as visitor_name,(select dealer_name from '.$DealerCreation_tb.' where id='.$sub_tb.'.dealer_sub_id) as dealer_name,(select sub_expense_type from '.$SubExpenseTypeCreation_tb.' where id='.$sub_tb.'.sub_expense_id) as sub_expense_name,(select item_liters_type from '.$ItemLitersType_tb.' where id='.$sub_tb.'.from_loct) as from_location,to_loct as to_location,expense_amount,total_amount from '.$sub_tb.' where sales_expense_main_id='.$main_id.' and (delete_status=0 or delete_status is null)');

        if ($tb1 != '') {
            return response()->json(['status' => 'SUCCESS', 'message' => 'Expense Sublist Showed Successfully', 'Expense_Sublist' => $tb1], 200);
        } else {
            return response()->json(['status' => 'FAILURE', 'message' => 'Expense Sublist Not Found'], 404);
        }
    }

    public function expense_sublist_edit_api(Request $request)
    {

        $id = $request->input('expense_sublist_id');

        if (empty($id)) {
            return response()->json(['status' => 'FAILURE', 'message' => 'Expense Sublist Id Not Found'], 404);
        }

        $Expense_Sublist_Edit = ExpenseCreationsSub::where('id',$id)->where(function($query){$query->where('delete_status', '0')->orWhereNull('delete_status');})->get(['id as expense_sublist_id','sales_expense_main_id as expense_main_id', 'visitor_sub_id as visitor_id','dealer_sub_id as dealer_id','market_sub_id as market_id','expense_id','sub_expense_id','from_loct as from_location','to_loct as to_location','total_amount','expense_amount'])->first();

        if ($Expense_Sublist_Edit != '') {
            return response()->json(['status' => 'SUCCESS', 'message' => 'Expense Sublist Edit List Showed Successfully', 'Expense_Sublist_Edit' => $Expense_Sublist_Edit], 200);
        } else {
            return response()->json(['status' => 'FAILURE', 'message' => 'Expense Sublist Edit List Not Found'], 404);
        }
    }

    public function expense_sublist_update_api(Request $request)
    {

        $id=$request->input('expense_sublist_id');
        $expense_id = $request->input('expense_name');
        $sub_expense_id = $request->input('sub_expense_name');
        $total_amount = $request->input('total_amount');
        $expense_amount = $request->input('expense_amount');
        $dealer_sub_id = $request->input('dealer_name');
        $visitor_sub_id = $request->input('visitor_name');
        $market_sub_id = $request->input('market_name');
        if (empty($id)) {
            return response()->json(['status' => 'FAILURE', 'message' => 'Expense Sublist Id Not Found'], 404);
        }
        $tb = ExpenseCreationsSub::find($id);
        $tb->expense_id = $expense_id;
        $tb->sub_expense_id = $sub_expense_id;
        $tb->expense_amount = $expense_amount;
        $tb->total_amount = $total_amount;
        $tb->dealer_sub_id = $dealer_sub_id;
        $tb->visitor_sub_id = $visitor_sub_id;
        $tb->market_sub_id = $market_sub_id;
        $tb->save();
        $get_expence_sublist = ExpenseCreationsSub::where('id',$id)->where(function($query){$query->where('delete_status', '0')->orWhereNull('delete_status');})->get(['id as expense_sublist_id','sales_expense_main_id as expense_main_id', 'visitor_sub_id as visitor_id','dealer_sub_id as dealer_id','market_sub_id as market_id','expense_id','sub_expense_id','from_loct as from_location','to_loct as to_location','total_amount','expense_amount'])->first();
        if($tb){
            return response()->json(['status' => 'SUCCESS', 'message' => 'Expense Sublist Updated Successfully','Expense_sublist_update' => $get_expence_sublist ], 200);
        }
        else
        {
            return response()->json(['status' => 'FAILURE', 'message' => 'Expense Sublist Id Not Found'], 404);
        }
    }

    public function expense_main_list_update_api(Request $request)
    {

        $id = $request->input('expense_main_id');
        $expense_no = $request->input('expense_number');
        $expense_date = $request->input('expense_date');
        $sales_rep_creation_id = $request->input('sales_executive_name');
        $market_manager_id = $request->input('market_manager_name');
        $status = '1';
        $description = $request->input('description');
        $mode_of_payment = $request->input('mode_of_payment');

        if (empty($expense_no)) {
            return response()->json(['status' => 'FAILURE', 'message' => 'Expense Number Not Found'], 404);
        }

        if (empty($expense_date)) {
            return response()->json(['status' => 'FAILURE', 'message' => 'Expense Date Not Found'], 404);
        }

        if (empty($sales_rep_creation_id)) {
            return response()->json(['status' => 'FAILURE', 'message' => 'Sales Executive Id Not Found'], 404);
        }

        if (empty($market_manager_id)) {
            return response()->json(['status' => 'FAILURE', 'message' => 'Manager Name Not Found'], 404);
        }

        if (empty($mode_of_payment)) {
            return response()->json(['status' => 'FAILURE', 'message' => 'Mode of Payment Not Found'], 404);
        }

        $tb = ExpenseCreationsMain::find($id);
        $tb->expense_no = $expense_no;
        $tb->expense_date = $expense_date;
        $tb->sales_rep_creation_id = $sales_rep_creation_id;
        $tb->market_manager_id = $market_manager_id;
        $tb->status = $status;
        $tb->description = $description;
        $tb->mode_of_payment = $mode_of_payment;
        $tb->save();


        return response()->json(['status' => 'SUCCESS', 'message' => 'Expense Creation Main List Updated Successfully','Expense_main_id' => $id ], 200);
    }

    public function expense_order_number_list_api(Request $request)
    {

        $Expense_order_no = ExpenseCreationsMain::select('expense_no')->where(function($query){$query->where('delete_status', '0')->orWhereNull('delete_status');})->get();

        if ($Expense_order_no != '') {
            return response()->json(['status' => 'SUCCESS', 'message' => 'Expense Order Number List Showed Successfully', 'Expense_order_no' => $Expense_order_no], 200);
        } else {
            return response()->json(['status' => 'FAILURE', 'message' => 'Expense Order Number List Not Found'], 404);
        }
    }


    public function expense_list_api(Request $request)
    {

        $from_date_1 = $request->input('from_date');
        $to_date_1 = $request->input('to_date');
        $expense_no_1 = $request->input('expense_number');
        $sales_rep_creation_id_1 = $request->input('sales_executive_id');
        $market_manager_id_1 = $request->input('market_manager_name');
        $status_1 = '1';
        $description_1 = $request->input('description');
        $mode_of_payment_1 = $request->input('mode_of_payment');

        $cond="";
        if($from_date_1!=""){$cond.=" and expense_date>='".$from_date_1."'";}
        if($to_date_1!=""){$cond.=" and expense_date<='".$to_date_1."'";}
        if($expense_no_1!=""){$cond.=" and expense_no='".$expense_no_1."'";}
        if($sales_rep_creation_id_1!=""){$cond.=" and sales_rep_creation_id=".$sales_rep_creation_id_1;}
        if($market_manager_id_1!=""){$cond.=" and market_manager_id=".$market_manager_id_1;}
        if($status_1!=""){$cond.=" and status=".$status_1;}
        if($description_1!=""){$cond.=" and description=".$description_1;}
        if($mode_of_payment_1!=""){$cond.=" and mode_of_payment="."'".$mode_of_payment_1."'";}

        $main_tb = (new ExpenseCreationsMain)->getTable();
        $sub_tb = (new ExpenseCreationsSub)->getTable();
        $MarketCreation_tb = (new MarketCreation)->getTable();
        $DealerCreation_tb = (new DealerCreation)->getTable();
        $SalesRepCreation_tb = (new SalesRepCreation)->getTable();
        $SalesOrderD2SMain_tb = (new SalesOrderD2SMain)->getTable();
        $MarketManagerCreation_tb = (new MarketManagerCreation)->getTable();

        // $tb1=DB::select('select id,expense_no,expense_date,mode_of_payment,(select area_name FROM '.$MarketCreation_tb.' where id='.$main_tb.'.sales_rep_creation_id) as sales_rep_creation_id,(select manager_name FROM '.$MarketManagerCreation_tb.' where id='.$main_tb.'.market_manager_id) as market_manager_id,(select dealer_name from '.$DealerCreation_tb.' where id='.$main_tb.'.dealer_creation_id) as dealer_name,status,(select IF(count(*)>0,CONCAT(sum(to_loct),";",sum(from_loct),";",sum(total_amount)),"0;0;0") from '.$sub_tb.' where sales_expense_main_id='.$main_tb.'.id and (delete_status=0 or delete_status is null)) as total_sublist from '.$main_tb.' where (delete_status=0 or delete_status is null)'.$cond);

        $tb1=DB::select('select id,expense_no,expense_date,mode_of_payment,(select sales_ref_name FROM '.$SalesRepCreation_tb.' where id='.$main_tb.'.sales_rep_creation_id) as sales_ref_name,(select manager_name FROM '.$MarketManagerCreation_tb.' where id='.$main_tb.'.market_manager_id) as market_manager_name,(select dealer_name from '.$DealerCreation_tb.' where id='.$main_tb.'.dealer_creation_id) as dealer_name,status,(select IF(count(*)>0,CONCAT(sum(to_loct),";",sum(from_loct),";",sum(total_amount)),"0;0;0") from '.$sub_tb.' where sales_expense_main_id='.$main_tb.'.id and (delete_status=0 or delete_status is null)) as total_sublist from '.$main_tb.' where (delete_status=0 or delete_status is null)'.$cond);


        if ($tb1 != '') {
            return response()->json(['status' => 'SUCCESS', 'message' => 'Expense Order Number List Showed Successfully', 'Expense_order_no' => $tb1], 200);
        } else {
            return response()->json(['status' => 'FAILURE', 'message' => 'Expense Order Number List Not Found'], 404);
        }
    }

}
