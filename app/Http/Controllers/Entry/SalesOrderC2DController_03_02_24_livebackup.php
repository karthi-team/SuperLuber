<?php

namespace App\Http\Controllers\Entry;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Models\Entry\SalesOrderC2DMain;
use App\Models\Entry\SalesOrderC2DSub;
use App\Models\Entry\SalesOrderStockMain;
use App\Models\Entry\SalesOrderStockSub;
use App\Models\MarketCreation;
use App\Models\DealerCreation;
use App\Models\ItemCreation;
use App\Models\GroupCreation;
use App\Models\ItemLitersType;
use App\Models\ItemPropertiesType;
use App\Models\SalesRepCreation;
use Carbon\Carbon;
class SalesOrderC2DController extends Controller
{
    public function sales_order_c2d()
    {
        $order_no_list=SalesOrderC2DMain::select('order_no')->where(function($query){$query->where('delete_status', '0')->orWhereNull('delete_status');})->get();
        $sales_name = SalesRepCreation::select('id', 'sales_ref_name')->where(function ($query) {
            $query->where('delete_status', '0')->orWhereNull('delete_status');
        })->orderBy('sales_ref_name')->get();
        $dealer_creation=DealerCreation::select('id','dealer_name')->where(function($query){$query->where('delete_status', '0')->orWhereNull('delete_status');})->orderBy('dealer_name')->get();
        return view('Entry.sales_order_c2d.admin',['order_no_list'=>$order_no_list,'sales_name'=>$sales_name,'dealer_creation'=>$dealer_creation]);
    }
    public function retrieve($from_date_1,$to_date_1,$sales_exec_1,$dealer_creation_id_1,$order_no_1)
    {
        $cond="";
        if($from_date_1!=""){$cond.=" and order_date>='".$from_date_1."'";}
        if($to_date_1!=""){$cond.=" and order_date<='".$to_date_1."'";}
        if($order_no_1!=""){$cond.=" and order_no='".$order_no_1."'";}
        if($dealer_creation_id_1!=""){$cond.=" and dealer_creation_id=".$dealer_creation_id_1;}
        if($sales_exec_1!=""){$cond.=" and sales_exec=".$sales_exec_1;}
        $cond .= ' ORDER BY id DESC';
        $main_tb = (new SalesOrderC2DMain)->getTable();
        $sub_tb = (new SalesOrderC2DSub)->getTable();
        $DealerCreation_tb = (new DealerCreation)->getTable();
        $tb1=DB::select('select id,order_no,order_date,(select dealer_name from '.$DealerCreation_tb.' where id='.$main_tb.'.dealer_creation_id) as dealer_name,status,(select IF(count(*)>0,CONCAT(sum(order_quantity),";",sum(item_weights),";",sum(total_amount)),"0;0;0") from '.$sub_tb.' where sales_order_main_id='.$main_tb.'.id and (delete_status=0 or delete_status is null)) as total_sublist from '.$main_tb.' where (delete_status=0 or delete_status is null)'.$cond);

        $tb1=DB::select('select id,order_no,order_date,(select dealer_name from '.$DealerCreation_tb.' where id='.$main_tb.'.dealer_creation_id) as dealer_name,status,(select IF(count(*)>0,CONCAT(sum(order_quantity),";",sum(item_weights),";",sum(total_amount)),"0;0;0") from '.$sub_tb.' where sales_order_main_id='.$main_tb.'.id and (delete_status=0 or delete_status is null)) as total_sublist from '.$main_tb.' where (delete_status=0 or delete_status is null)'.$cond);

        return json_decode(json_encode($tb1), true);
    }
    public function retrieve_main($id)
    {
        return SalesOrderC2DMain::select('id','order_no','order_date','dealer_creation_id','status','description', 'sales_exec','address')->where('id',$id)->get()->first();
    }
    public function retrieve_sub($main_id,$sub_id)
    {
        if($sub_id=='')
        {
            $sub_tb = (new SalesOrderC2DSub)->getTable();
            $MarketCreation_tb = (new MarketCreation)->getTable();
            $GroupCreation_tb = (new GroupCreation)->getTable();
            $ItemCreation_tb = (new ItemCreation)->getTable();
            $ItemPropertiesType_tb = (new ItemPropertiesType)->getTable();
            $ItemLitersType_tb = (new ItemLitersType)->getTable();
            $tb1=DB::select('select id,order_date_sub,time_sub,(select area_name FROM '.$MarketCreation_tb.' where id='.$sub_tb.'.market_creation_id) as market_creation_id,(select item_name from ' . $ItemCreation_tb . ' where id=' . $sub_tb . '.item_creation_id) as item_creation_id,

            (select short_code from ' . $ItemCreation_tb . ' where id=' . $sub_tb . '.short_code_id) as short_code_id,

             (select group_name from ' . $GroupCreation_tb . ' where id=' . $sub_tb . '.group_creation_id) as group_creation_id,(select item_properties_type from '.$ItemPropertiesType_tb.' where id='.$sub_tb.'.item_property) as item_property,(select item_liters_type from '.$ItemLitersType_tb.' where id='.$sub_tb.'.item_weights) as item_weights,order_quantity,item_price,total_amount from '.$sub_tb.' where sales_order_main_id='.$main_id.' and (delete_status=0 or delete_status is null)');
            return json_decode(json_encode($tb1), true);
        }
        else
        {return SalesOrderC2DSub::where('id',$sub_id)->where(function($query){$query->where('delete_status', '0')->orWhereNull('delete_status');})->get(['id','order_date_sub','time_sub','market_creation_id','group_creation_id','item_creation_id','short_code_id','order_quantity','item_property','item_weights','item_price','total_amount'])->first();}
    }
    public function db_cmd(Request $request)
    {
        $action=$request->input('action');
        if($action=='insert')
        {
            $tb = new SalesOrderC2DMain();
            $tb->entry_date  = Carbon::now();
            $tb->order_no = $request->input('order_no');
            $tb->order_date = $request->input('order_date');
            $tb->dealer_creation_id = $request->input('dealer_creation_id');
            $tb->address = $request->input('dealer_address');
            $tb->status = $request->input('status');
            $tb->description = $request->input('description');
            $tb->sales_exec = $request->input('sales_exec');
            $tb->save();

        }
        else if($action=='update')
        {
            $tb = SalesOrderC2DMain::find($request->input('id'));
            $tb->order_no = $request->input('order_no');
            $tb->order_date = $request->input('order_date');
            $tb->dealer_creation_id = $request->input('dealer_creation_id');
            $tb->address = $request->input('dealer_address');
            $tb->status = $request->input('status');
            $tb->description = $request->input('description');
            $tb->sales_exec = $request->input('sales_exec');
            $tb->save();
        }
        else if($action=='delete')
        {
            $tb = SalesOrderC2DMain::find($request->input('id'));
            $tb->delete_status = "1";
            $tb->save();
        }
        else if($action=='insert_sub')
        {
            $main_id=$request->input('main_id');
            if($main_id==''){
                $main_id = SalesOrderC2DMain::insertGetId([
                    'entry_date' => Carbon::now(),
                    'order_no' => $request->input('order_no'),
                    'order_date' => $request->input('order_date'),
                    'dealer_creation_id' => $request->input('dealer_creation_id'),
                    'address' => $request->input('dealer_address'),
                    'status' => $request->input('status'),
                    'description' => $request->input('description'),
                    'sales_exec' => $request->input('sales_exec')

                ]);
            }
            $tb = new SalesOrderC2DSub();
            $tb->entry_date  = Carbon::now();
            $tb->sales_order_main_id = $main_id;
            $tb->order_date_sub = $request->input('order_date_sub');
            $tb->time_sub = $request->input('time_sub');
            $tb->group_creation_id = $request->input('group_creation_id');
            $tb->item_creation_id = $request->input('item_creation_id');
            $tb->short_code_id = $request->input('short_code_id');
            $tb->order_quantity = $request->input('order_quantity');
            $tb->balance_quantity = $request->input('order_quantity');
            $tb->item_property = $request->input('item_property');
            $tb->item_weights = $request->input('item_weights');
            $tb->item_price = $request->input('item_price');
            $tb->total_amount = $request->input('total_amount');
            $tb->save();

            return $main_id;
        }
        else if($action=='update_sub')
        {
            $tb = SalesOrderC2DSub::find($request->input('id'));
            $tb->order_date_sub = $request->input('order_date_sub');
            $tb->time_sub = $request->input('time_sub');
            $tb->group_creation_id = $request->input('group_creation_id');
            $tb->item_creation_id = $request->input('item_creation_id');
            $tb->short_code_id = $request->input('short_code_id');
            $tb->order_quantity = $request->input('order_quantity');
            $tb->balance_quantity = $request->input('order_quantity');
            $tb->item_property = $request->input('item_property');
            $tb->item_weights = $request->input('item_weights');
            $tb->item_price = $request->input('item_price');
            $tb->total_amount = $request->input('total_amount');
            $tb->save();
        }
        else if($action=='delete_sub')
        {
            $tb = SalesOrderC2DSub::find($request->input('id'));
            $tb->delete_status = "1";
            $tb->save();
        }
        else if($action=='retrieve')
        {
            $sales_order_c2d_main = $this->retrieve($request->input('from_date_1'),$request->input('to_date_1'),$request->input('sales_exec_1'),$request->input('dealer_creation_id_1'),$request->input('order_no_1'));
            return view('Entry.sales_order_c2d.list',['sales_order_c2d_main'=>$sales_order_c2d_main,'user_rights_edit_1'=>$request->input('user_rights_edit_1'),'user_rights_delete_1'=>$request->input('user_rights_delete_1')]);
        }
        else if($action=='create_form')
        {
            $main_tb = (new SalesOrderC2DMain)->getTable();
            $next_id = DB::select("SHOW TABLE STATUS LIKE '".$main_tb."'");
            $order_no="SALCD_".date("ym")."_".$next_id[0]->Auto_increment;
            $market_creation=MarketCreation::select('id','area_name')->where(function($query){$query->where('delete_status', '0')->orWhereNull('delete_status');})->orderBy('area_name')->get();
            $dealer_creation=DealerCreation::select('id','dealer_name')->where(function($query){$query->where('delete_status', '0')->orWhereNull('delete_status');})->orderBy('dealer_name')->get();
            $sales_name = SalesRepCreation::select('id', 'sales_ref_name')->where(function ($query) {
                $query->where('delete_status', '0')->orWhereNull('delete_status');
            })->orderBy('sales_ref_name')->get();
            return view('Entry.sales_order_c2d.create',['order_no'=>$order_no,'market_creation'=>$market_creation,'dealer_creation'=>$dealer_creation,"sales_name" => $sales_name]);
        }
        else if($action=='update_form')
        {

            $main_id_dealer = $request->input('id');

            $sales_exec = SalesOrderC2DMain::select('id', 'sales_exec')->where('id', $main_id_dealer)->get()->first();

            if($sales_exec){
                $sales_exec_value = $sales_exec->sales_exec;
            }else{
                $sales_exec_value = 0;
            }

            $sales_order_c2d_main=$this->retrieve_main($request->input('id'));
            $market_creation=MarketCreation::select('id','area_name')->where(function($query){$query->where('delete_status', '0')->orWhereNull('delete_status');})->orderBy('area_name')->get();
            $dealer_creation = DealerCreation::select('id', 'dealer_name')->where(function ($query) { $query->where('delete_status', '0')->orWhereNull('delete_status');
            })->where('sales_rep_id', '=', $sales_exec_value)->orderBy('dealer_name')->get();
            $sales_name = SalesRepCreation::select('id', 'sales_ref_name')->where(function ($query) {
                $query->where('delete_status', '0')->orWhereNull('delete_status');
            })->orderBy('sales_ref_name')->get();
            return view('Entry.sales_order_c2d.update',['sales_order_c2d_main'=>$sales_order_c2d_main,'market_creation'=>$market_creation,'dealer_creation'=>$dealer_creation,"sales_name" => $sales_name]);
        }
        else if($action=='form_sublist')
        {
            $main_id=$request->input('main_id');$sub_id=$request->input('sub_id');
            $sales_order_c2d_sub=null;if($sub_id!=""){$sales_order_c2d_sub = $this->retrieve_sub($main_id,$sub_id);}
            $sales_order_c2d_sub_list=[];if($main_id!=""){$sales_order_c2d_sub_list = $this->retrieve_sub($main_id,'');}
            $market_creation=MarketCreation::select('id','area_name')->where(function($query){$query->where('delete_status', '0')->orWhereNull('delete_status');})->orderBy('area_name')->get();
            $item_creation=ItemCreation::select('id','item_name','short_code','distributor_rate')->where(function($query){$query->where('delete_status', '0')->orWhereNull('delete_status');})->orderBy('item_name')->get();
            $group_creation = GroupCreation::select('id', 'group_name')->where(function ($query) {
                $query->where('delete_status', '0')->orWhereNull('delete_status');
            })->orderBy('group_name')->get();
            $item_properties_type=ItemPropertiesType::select('id','item_properties_type')->where('status1', '1')->where(function($query){$query->where('delete_status', '0')->orWhereNull('delete_status');})->orderBy('item_properties_type')->get();
            $item_liters_type=ItemLitersType::select('id','item_liters_type')->where('status1', '1')->where(function($query){$query->where('delete_status', '0')->orWhereNull('delete_status');})->orderBy('item_liters_type')->get();
            return view('Entry.sales_order_c2d.sublist',['sales_order_c2d_sub'=>$sales_order_c2d_sub,'sales_order_c2d_sub_list'=>$sales_order_c2d_sub_list,'main_id'=>$main_id,'sub_id'=>$sub_id,'market_creation'=>$market_creation,'item_creation'=>$item_creation,'group_creation'=>$group_creation,'item_properties_type'=>$item_properties_type,'item_liters_type'=>$item_liters_type,'user_rights_edit_1'=>$request->input('user_rights_edit_1'),'user_rights_delete_1'=>$request->input('user_rights_delete_1')]);
        }
        else if ($action == 'getdearlername') {

            $sales_exec = $request->input('sales_exec');

            $dealer_name = DealerCreation::select('id', 'dealer_name')
            ->where(function ($query) {
                $query->where('delete_status', '0')->orWhereNull('delete_status');
            })
            ->where('sales_rep_id', $sales_exec)
            ->get();

            return response()->json($dealer_name);
        }
        else if ($action == 'getmarket') {

            $dealer_creation_id = $request->input('dealer_creation_id');

            $dealer_address = DealerCreation::select('id', 'address')
            ->where(function ($query) {
                $query->where('delete_status', '0')->orWhereNull('delete_status');
            })
            ->where('id', $dealer_creation_id)
            ->get();

            $dealer_creation = DealerCreation::find($dealer_creation_id);
            $market_id = $dealer_creation->area_id;
            $market_ids = explode(",", $market_id);
            $area_names = [];
            $marketId_s = [];

            foreach ($market_ids as $marketId) {
                $area_name = MarketCreation::find($marketId);
                if ($area_name) {
                    $area_names[] = $area_name;
                    $marketId_s[] = $marketId;
                }
            }

            $data = [
                'dealer_address' => $dealer_address,
                'area_names' => $area_names,
            ];
            return response()->json($data);
        }
        else if ($action == 'getshortcode') {

            $item_creation_id = $request->input('item_creation_id');

            $short_code = ItemCreation::select('id', 'short_code')
            ->where(function ($query) {
                $query->where('delete_status', '0')->orWhereNull('delete_status');
            })
            ->where('id', $item_creation_id)
            ->get();

            return response()->json($short_code);
        }
        else if ($action == 'getitem_liters_type') {

            $item_creation_id = $request->input('item_creation_id');

            $item_liters_type = ItemCreation::select('item_liters_type.id','item_liters_type.item_liters_type')
            ->join('item_liters_type', 'item_liters_type.id', '=', 'item_creation.item_liters_type')
            ->where('item_creation.id', $item_creation_id)
            ->get();

            return response()->json($item_liters_type);
        }

        else if ($action == 'getitem_property') {
            $arr=[];
            $item_creation_id = $request->input('item_creation_id');

            //$item = ItemPropertiesType::find($item_creation_id);
           // $item_properties_type = $item->item_properties_type;
           // $id = $item->id;
           // $all_val=[
            //    'item_properties_type'=>//$item_properties_type,
            //    'id'=>$id
           // ];
           //     $arr[]=$all_val;
																										 $item_properties_type = ItemCreation::select('item_properties_type.id',
                                                                                                           'item_properties_type.item_properties_type')
            ->join('item_properties_type', 'item_properties_type.id', '=', 'item_creation.item_properties_type')
            ->where('item_creation.id', $item_creation_id)
            ->get();
			return response()->json($item_properties_type);

          //  return response()->json($arr);
        }
        else if ($action == 'getitemname') {

            $group_creation_id = $request->input('group_creation_id');

             $item_name = ItemCreation::select('id', 'item_name')
             ->where(function ($query) {
                $query->where('delete_status', '0')->orWhereNull('delete_status');
            })->orderBy('item_name')
            ->where('group_id', '=', $group_creation_id)
            ->get();

            return response()->json($item_name);
        }

        else if ($action == 'getdearlername_admin') {

            $sales_exec = $request->input('sales_exec');

            $dealer_name = DealerCreation::select('id', 'dealer_name')
            ->where('sales_rep_id', $sales_exec)
            ->get();

            return response()->json($dealer_name);
        }

        else if ($action == 'getordernumber_admin') {

            $dealer_creation_id_1 = $request->input('dealer_creation_id_1');

            $order_number = SalesOrderC2DMain::select('order_no')
            ->where(function ($query) {
                $query->where('delete_status', '0')->orWhereNull('delete_status');
            })
            ->where('dealer_creation_id', $dealer_creation_id_1)
            ->get();

            return response()->json($order_number);
        }

    }
}
