<?php

namespace App\Http\Controllers\Entry;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Models\Entry\SalesReturnD2CMain;
use App\Models\Entry\SalesReturnD2CSub;
use App\Models\Entry\SalesOrderC2DMain;
use App\Models\Entry\SalesOrderC2DSub;
use App\Models\Entry\SalesOrderD2SMain;
use App\Models\Entry\SalesOrderD2SSub;
use App\Models\Entry\ReceiptEntry;
use App\Models\Entry\ReceiptEntryTallySublist;
use App\Models\Entry\ReceiptEntrySub;
use App\Models\Entry\SalesOrderDeliveryMain;
use App\Models\Entry\SalesOrderDeliverySub;
use App\Models\Entry\SalesOrderStockMain;
use App\Models\Entry\SalesOrderStockSub;
use App\Models\MarketCreation;
use App\Models\DealerCreation;
use App\Models\ShopCreation;
use App\Models\ReturnCreation;
use App\Models\GroupCreation;
use App\Models\ItemCreation;
use App\Models\ItemLitersType;
use App\Models\ItemPropertiesType;
use App\Models\SalesRepCreation;
use Carbon\Carbon;
class SalesReturnD2CController extends Controller
{
    public function sales_return_d2c()
    {
        $order_no_list=SalesReturnD2CMain::select('order_no')->where(function($query){$query->where('delete_status', '0')->orWhereNull('delete_status');})->get();
        $market_creation=MarketCreation::select('id','area_name')->where(function($query){$query->where('delete_status', '0')->orWhereNull('delete_status');})->orderBy('area_name')->get();
        $dealer_creation=DealerCreation::select('id','dealer_name')->where(function($query){$query->where('delete_status', '0')->orWhereNull('delete_status');})->orderBy('dealer_name')->get();
        $sales_name = SalesRepCreation::select('id', 'sales_ref_name')->where(function ($query) {
            $query->where('delete_status', '0')->orWhereNull('delete_status');
        })->orderBy('sales_ref_name')->get();
        return view('Entry.sales_return_d2c.admin',['order_no_list'=>$order_no_list,'market_creation'=>$market_creation,'dealer_creation'=>$dealer_creation,"sales_name" => $sales_name]);
    }
    public function retrieve($from_date_1,$to_date_1,$order_no_1,$dealer_creation_id_1,$status_1,$description_1,$sales_exec_1)
    {
        $cond="";
        if($from_date_1!=""){$cond.=" and order_date>='".$from_date_1."'";}
        if($to_date_1!=""){$cond.=" and order_date<='".$to_date_1."'";}
        if($sales_exec_1!=""){$cond.=" and sales_exec=".$sales_exec_1;}
        if($dealer_creation_id_1!=""){$cond.=" and dealer_creation_id=".$dealer_creation_id_1;}
        if($order_no_1!=""){$cond.=" and order_no='".$order_no_1."'";}
        $main_tb = (new SalesReturnD2CMain)->getTable();
        $sub_tb = (new SalesReturnD2CSub)->getTable();
        $MarketCreation_tb = (new MarketCreation)->getTable();
        $DealerCreation_tb = (new DealerCreation)->getTable();
        $SalesRepCreation_tb = (new SalesRepCreation)->getTable();

        $tb1=DB::select('select id,order_no,order_date,
        (select dealer_name from '.$DealerCreation_tb.' where id='.$main_tb.'.dealer_creation_id) as dealer_name,
        (select sales_ref_name FROM '.$SalesRepCreation_tb.' where id='.$main_tb.'.sales_exec) as sales_exec,
        status,credit_note_no,(select IF(count(*)>0,CONCAT(sum(order_quantity),";",sum(item_weights),";",sum(total_amount)),"0;0;0") from '.$sub_tb.' where sales_order_main_id='.$main_tb.'.id and (delete_status=0 or delete_status is null)) as total_sublist from '.$main_tb.' where (delete_status=0 or delete_status is null)'.$cond);

        $tb1=DB::select('select id,order_no,order_date,(select dealer_name from '.$DealerCreation_tb.' where id='.$main_tb.'.dealer_creation_id) as dealer_name,(select sales_ref_name FROM '.$SalesRepCreation_tb.' where id='.$main_tb.'.sales_exec) as sales_exec,status,credit_note_no,(select IF(count(*)>0,CONCAT(sum(order_quantity),";",sum(item_weights),";",sum(total_amount)),"0;0;0") from '.$sub_tb.' where sales_order_main_id='.$main_tb.'.id and (delete_status=0 or delete_status is null)) as total_sublist from '.$main_tb.' where (delete_status=0 or delete_status is null)'.$cond);
        return json_decode(json_encode($tb1), true);
    }
    public function retrieve_main($id)
    {
        return SalesReturnD2CMain::select('id','order_no','order_date','dealer_creation_id','dealer_address','mop','status','credit_note_no','description', 'sales_exec')->where('id',$id)->get()->first();
    }
    public function retrieve_sub($main_id,$sub_id)
    {
        if($sub_id=='')
        {
            $sub_tb = (new SalesReturnD2CSub)->getTable();
            $MarketCreation_tb = (new MarketCreation)->getTable();
            $ItemCreation_tb = (new ItemCreation)->getTable();
            $GroupCreation_tb = (new GroupCreation)->getTable();
            $ItemPropertiesType_tb = (new ItemPropertiesType)->getTable();
            $ItemLitersType_tb = (new ItemLitersType)->getTable();
            $ShopCreation_tb = (new ShopCreation)->getTable();
            $ReturnCreation_tb = (new ReturnCreation)->getTable();
            $SalesOrderDeliveryMain_tb = (new SalesOrderDeliveryMain)->getTable();
            $SalesOrderDeliverySub_tb = (new SalesOrderDeliverySub)->getTable();
            $SalesOrderC2DMain_tb = (new SalesOrderC2DMain)->getTable();

            $tb1=DB::select('
            SELECT
                id,
                order_date_sub,
                time_sub,
                (SELECT tally_no FROM ' . $SalesOrderDeliveryMain_tb . ' WHERE id=' . $sub_tb . '.tally_no) AS tally_no,
                batch_no,
                (SELECT description FROM ' . $SalesOrderC2DMain_tb . ' WHERE id=' . $sub_tb . '.desc) AS `desc`,
                (SELECT return_type FROM ' . $ReturnCreation_tb . ' WHERE id=' . $sub_tb . '.return_type_id) AS return_type_id,
                (SELECT item_name FROM ' . $ItemCreation_tb . ' WHERE id=' . $sub_tb . '.item_creation_id) AS item_creation_id,
                (SELECT short_code FROM ' . $ItemCreation_tb . ' WHERE id=' . $sub_tb . '.short_code_id) AS short_code_id,
                (SELECT group_name FROM ' . $GroupCreation_tb . ' WHERE id=' . $sub_tb . '.group_creation_id) AS group_creation_id,
                (SELECT item_properties_type FROM '.$ItemPropertiesType_tb.' WHERE id='.$sub_tb.'.item_property) AS item_property,
                (SELECT item_liters_type FROM '.$ItemLitersType_tb.' WHERE id='.$sub_tb.'.item_weights) AS item_weights,
                order_quantity,
                pieces_quantity,
                item_price,
                total_amount
            FROM '.$sub_tb.'
            WHERE sales_order_main_id='.$main_id.' AND (delete_status=0 OR delete_status IS NULL)
        ');

            return json_decode(json_encode($tb1), true);
        }
        else
        {return SalesReturnD2CSub::where('id',$sub_id)->where(function($query){$query->where('delete_status', '0')->orWhereNull('delete_status');})->get(['id','order_date_sub','time_sub','tally_no','batch_no','return_type_id','desc','group_creation_id','item_creation_id','order_quantity','pieces_quantity','item_property','item_weights','item_price','total_amount'])->first();}
    }
    public function db_cmd(Request $request)
    {
        $action=$request->input('action');
        if($action=='insert')
        {
            $tb = new SalesReturnD2CMain();
            $tb->entry_date  = Carbon::now();
            $tb->order_no = $request->input('order_no');
            $tb->order_date = $request->input('order_date');
            $tb->dealer_creation_id = $request->input('dealer_creation_id');
            $tb->dealer_address = $request->input('dealer_address');
            $tb->mop = $request->input('mop');
            $tb->status = $request->input('status');
            $tb->credit_note_no = $request->input('credit_note_no');
            $tb->description = $request->input('description');
            $tb->sales_exec = $request->input('sales_exec');
            $tb->save();

        }
        else if($action=='update')
        {
            $tb = SalesReturnD2CMain::find($request->input('id'));
            $tb->order_no = $request->input('order_no');
            $tb->order_date = $request->input('order_date');
            $tb->dealer_creation_id = $request->input('dealer_creation_id');
            $tb->dealer_address = $request->input('dealer_address');
            $tb->mop = $request->input('mop');
            $tb->status = $request->input('status');
            $tb->credit_note_no = $request->input('credit_note_no');
            $tb->description = $request->input('description');
            $tb->sales_exec = $request->input('sales_exec');
            $tb->save();
        }
        else if($action=='delete')
        {
            if ($request->input('id')) {
                $sales_order_main_id = $request->input('id');

                $sales_return_d2c_sub = SalesReturnD2CSub::where('sales_order_main_id', $sales_order_main_id)->where(function ($query) {
                    $query->where('delete_status', '0')->orWhereNull('delete_status');
                })->get();

                foreach ($sales_return_d2c_sub as $sub) {
                    $sales_return_d2c_main = SalesReturnD2CMain::find($sales_order_main_id);

                    $item_creation_id = $sub->item_creation_id;
                    $item_property = $sub->item_property;
                    $item_weights = $sub->item_weights;
                    $order_quantity = $sub->order_quantity;

                    $order_date = $sales_return_d2c_main->order_date;
                    $dealer_creation_id = $sales_return_d2c_main->dealer_creation_id;

                    $SalesOrderStockMain_tb = (new SalesOrderStockMain)->getTable();
                    $SalesOrderStockSub_tb = (new SalesOrderStockSub)->getTable();

                    $current_stock = SalesOrderStockMain::select($SalesOrderStockMain_tb . '.id', $SalesOrderStockMain_tb . '.stock_entry_date', $SalesOrderStockSub_tb . '.current_stock')
                    ->join($SalesOrderStockSub_tb, $SalesOrderStockSub_tb . '.sales_order_main_id', '=', $SalesOrderStockMain_tb . '.id')
                    ->where($SalesOrderStockMain_tb . '.dealer_creation_id', $dealer_creation_id)
                    ->where(function ($query) use ($SalesOrderStockMain_tb) {
                        $query->where($SalesOrderStockMain_tb . '.delete_status', '0')
                            ->orWhereNull($SalesOrderStockMain_tb . '.delete_status');
                    })
                    ->where($SalesOrderStockMain_tb . '.status', '1')
                    ->where($SalesOrderStockMain_tb . '.stock_entry_date', '<=', $order_date)
                    ->where($SalesOrderStockSub_tb . '.item_creation_id', $item_creation_id)
                    ->where($SalesOrderStockSub_tb . '.item_property', $item_property)
                    ->where($SalesOrderStockSub_tb . '.item_weights', $item_weights)
                    ->orderBy($SalesOrderStockMain_tb . '.stock_entry_date', 'desc')
                    ->first();

                    $total_current_stock = $current_stock ? $current_stock->current_stock + $order_quantity : $order_quantity;

                    $sales_order_stock_id = SalesOrderStockMain::join('sales_order_stock_sublist', 'sales_order_stock_sublist.sales_order_main_id', '=', 'sales_order_stock_main.id')
                    ->where('sales_order_stock_main.dealer_creation_id', $dealer_creation_id)
                    ->where(function ($query) {
                        $query->where('sales_order_stock_main.delete_status', '0')
                            ->orWhereNull('sales_order_stock_main.delete_status');
                    })
                    ->where('sales_order_stock_main.status', '1')
                    ->where('sales_order_stock_main.stock_entry_date', '<=', $order_date)
                    ->where('sales_order_stock_sublist.item_creation_id', $item_creation_id)
                    ->where('sales_order_stock_sublist.item_property', $item_property)
                    ->where('sales_order_stock_sublist.item_weights', $item_weights)
                    ->orderBy('sales_order_stock_main.stock_entry_date', 'desc')
                    ->select('sales_order_stock_main.id as stock_main_id', 'sales_order_stock_sublist.id as stock_sub_id')
                    ->first();

                    if($sales_order_stock_id){
                        $sales_order_stock_main_max_id = $sales_order_stock_id -> stock_main_id;
                        $sales_order_stock_sub_max_id = $sales_order_stock_id -> stock_sub_id;
                    }else{
                        $sales_order_stock_main_max_id = '0';
                        $sales_order_stock_sub_max_id = '0';
                    }

                    SalesOrderStockSub::join('sales_order_stock_main as sm', 'sales_order_stock_sublist.sales_order_main_id', '=', 'sm.id')
                    ->where('sm.dealer_creation_id', $dealer_creation_id)
                    ->where('sales_order_stock_sublist.item_creation_id', $item_creation_id)
                    ->where('sales_order_stock_sublist.item_property', $item_property)
                    ->where('sales_order_stock_sublist.item_weights', $item_weights)
                    ->where('sm.id', $sales_order_stock_main_max_id)
                    ->where('sales_order_stock_sublist.id', $sales_order_stock_sub_max_id)
                    ->update(['sales_order_stock_sublist.current_stock' => $total_current_stock]);
                }
            }

            $tb = SalesReturnD2CMain::find($request->input('id'));
            $tb->delete_status = "1";
            $tb->save();
        }
        else if($action=='insert_sub')
        {
            $main_id=$request->input('main_id');
            if($main_id==''){
                $main_id = SalesReturnD2CMain::insertGetId([
                    'entry_date' => Carbon::now(),
                    'order_no' => $request->input('order_no'),
                    'order_date' => $request->input('order_date'),
                    'dealer_creation_id' => $request->input('dealer_creation_id'),
                    'dealer_address' => $request->input('dealer_address'),
                    'mop' => $request->input('mop'),
                    'status' => $request->input('status'),
                    'credit_note_no' => $request->input('credit_note_no'),
                    'description' => $request->input('description'),
                    'sales_exec' => $request->input('sales_exec')

                ]);
            }
            $tb = new SalesReturnD2CSub();
            $tb->entry_date  = Carbon::now();
            $tb->sales_order_main_id = $main_id;
            $tb->order_date_sub = $request->input('order_date_sub');
            $tb->time_sub = $request->input('time_sub');
            $tb->tally_no = $request->input('tally_no');
            $tb->desc = $request->input('desc');
            $tb->batch_no = $request->input('batch_no');
            $tb->return_type_id = $request->input('return_type_id');
            $tb->group_creation_id = $request->input('group_creation_id');
            $tb->item_creation_id = $request->input('item_creation_id');
            $tb->short_code_id = $request->input('short_code_id');
            $tb->order_quantity = $request->input('order_quantity');
            $tb->pieces_quantity = $request->input('pieces_quantity');
            $tb->item_property = $request->input('item_property');
            $tb->item_weights = $request->input('item_weights');
            $tb->item_price = $request->input('item_price');
            $tb->total_amount = $request->input('total_amount');
            $tb->save();

            $order_date_sub = $request->input('order_date_sub');
            if($request->input('dealer_creation_id')){
                $order_date = $request->input('order_date');
                $dealer_creation_id = $request->input('dealer_creation_id');
            }else{
                $sales_return_d2c_main = SalesReturnD2CMain::find($main_id);
                $order_date = $sales_return_d2c_main->order_date;
                $dealer_creation_id = $sales_return_d2c_main->dealer_creation_id;
            }
            $tally_no = $request->input('tally_no');
            $desc = $request->input('desc');
            $item_creation_id = $request->input('item_creation_id');
            $item_property = $request->input('item_property');
            $item_weights = $request->input('item_weights');
            $order_quantity = $request->input('order_quantity');
            $total_amount = $request->input('total_amount');

            $SalesOrderDeliveryMain_tb = (new SalesOrderDeliveryMain)->getTable();
            $SalesOrderDeliverySub_tb = (new SalesOrderDeliverySub)->getTable();

            $SalesOrderDeliveryMain = SalesOrderDeliveryMain::select($SalesOrderDeliverySub_tb . '.total_amount_1', $SalesOrderDeliverySub_tb . '.bal_amount')
            ->join($SalesOrderDeliverySub_tb, $SalesOrderDeliverySub_tb . '.sales_order_main_id', '=', $SalesOrderDeliveryMain_tb . '.id')
            ->where($SalesOrderDeliveryMain_tb . '.dealer_creation_id', '=', $dealer_creation_id)
            ->where($SalesOrderDeliveryMain_tb . '.id', '=', $tally_no)
            ->where(function ($query) use ($SalesOrderDeliveryMain_tb) {
                $query->where($SalesOrderDeliveryMain_tb . '.delete_status', '0')
                    ->orWhereNull($SalesOrderDeliveryMain_tb . '.delete_status');
            })
            ->where(function ($query) use ($SalesOrderDeliverySub_tb) {
                $query->where($SalesOrderDeliverySub_tb . '.delete_status', '0')
                    ->orWhereNull($SalesOrderDeliverySub_tb . '.delete_status');
            })
            ->where($SalesOrderDeliverySub_tb . '.item_creation_id', '=', $item_creation_id)
            ->where($SalesOrderDeliverySub_tb . '.item_property', '=', $item_property)
            ->where($SalesOrderDeliverySub_tb . '.item_weights', '=', $item_weights)
            ->first();

            if ($SalesOrderDeliveryMain) {
                $total_amount_1 = $SalesOrderDeliveryMain->total_amount_1;
                $bal_amount = $SalesOrderDeliveryMain->bal_amount;
                $final_total_amount = $total_amount_1 - $total_amount;
                $final_bal_amount = $bal_amount - $total_amount;
            }else{
                $final_total_amount = 0;
                $final_bal_amount = 0;
            }

            SalesOrderDeliverySub::join('sales_order_delivery_main_c as sm', 'sales_order_delivery_sublist_c.sales_order_main_id', '=', 'sm.id')
            ->where('sm.dealer_creation_id', $dealer_creation_id)
            ->where('sm.id', $tally_no)
            ->where('sales_order_delivery_sublist_c.item_creation_id', $item_creation_id)
            ->where('sales_order_delivery_sublist_c.item_property', $item_property)
            ->where('sales_order_delivery_sublist_c.item_weights', $item_weights)
            ->update(['sales_order_delivery_sublist_c.total_amount_1' => $final_total_amount , 'sales_order_delivery_sublist_c.bal_amount' => $final_bal_amount]);

            $ReceiptEntry_tb = (new ReceiptEntry)->getTable();
            $ReceiptEntryTallySublist_tb = (new ReceiptEntryTallySublist)->getTable();

            $ReceiptEntry = ReceiptEntry::select($ReceiptEntryTallySublist_tb . '.id', $ReceiptEntryTallySublist_tb . '.total_amount', $ReceiptEntryTallySublist_tb . '.bal_amount')
                ->join($ReceiptEntryTallySublist_tb, $ReceiptEntryTallySublist_tb . '.receipt_entry_main_id', '=', $ReceiptEntry_tb . '.id')
                ->where($ReceiptEntry_tb . '.dealer_creation_id', '=', $dealer_creation_id)
                ->where($ReceiptEntry_tb . '.tally_no', '=', $tally_no)
                ->where(function ($query) use ($ReceiptEntry_tb) {
                    $query->where($ReceiptEntry_tb . '.delete_status', '0')
                        ->orWhereNull($ReceiptEntry_tb . '.delete_status');
                })
                ->where(function ($query) use ($ReceiptEntryTallySublist_tb) {
                    $query->where($ReceiptEntryTallySublist_tb . '.delete_status', '0')
                        ->orWhereNull($ReceiptEntryTallySublist_tb . '.delete_status');
                })
                ->get();

            foreach ($ReceiptEntry as $ReceiptEntry_1) {
                if ($ReceiptEntry_1) {
                    $id_2 = $ReceiptEntry_1->id;
                    $total_amount_2 = $ReceiptEntry_1->total_amount;
                    $bal_amount_2 = $ReceiptEntry_1->bal_amount;
                    $final_total_amount_1 = $total_amount_2 - $total_amount;
                    $final_bal_amount_1 = $bal_amount_2 - $total_amount;

                    ReceiptEntryTallySublist::join('receipt_entry as sm', 'receipt_entry_tally_sublist.receipt_entry_main_id', '=', 'sm.id')
                        ->where('sm.dealer_creation_id', $dealer_creation_id)
                        ->where('sm.tally_no', $tally_no)
                        ->where('sm.id', $id_2)  // Use 'sm.id' instead of 'sm.tally_no'
                        ->update([
                            'receipt_entry_tally_sublist.total_amount' => $final_total_amount_1,
                            'receipt_entry_tally_sublist.bal_amount' => $final_bal_amount_1
                        ]);
                } else {
                    $final_total_amount_1 = 0;
                    $final_bal_amount_1 = 0;
                }
            }

            if($item_creation_id && $order_quantity){
                $SalesOrderStockMain_tb = (new SalesOrderStockMain)->getTable();
                $SalesOrderStockSub_tb = (new SalesOrderStockSub)->getTable();

                $current_stock = SalesOrderStockMain::select($SalesOrderStockMain_tb . '.id', $SalesOrderStockMain_tb . '.stock_entry_date', $SalesOrderStockSub_tb . '.current_stock')
                ->join($SalesOrderStockSub_tb, $SalesOrderStockSub_tb . '.sales_order_main_id', '=', $SalesOrderStockMain_tb . '.id')
                ->where($SalesOrderStockMain_tb . '.dealer_creation_id', $dealer_creation_id)
                ->where(function ($query) use ($SalesOrderStockMain_tb) {
                    $query->where($SalesOrderStockMain_tb . '.delete_status', '0')
                        ->orWhereNull($SalesOrderStockMain_tb . '.delete_status');
                })
                ->where($SalesOrderStockMain_tb . '.status', '1')
                ->where($SalesOrderStockMain_tb . '.stock_entry_date', '<=', $order_date)
                ->where($SalesOrderStockSub_tb . '.item_creation_id', $item_creation_id)
                ->where($SalesOrderStockSub_tb . '.item_property', $item_property)
                ->where($SalesOrderStockSub_tb . '.item_weights', $item_weights)
                ->orderBy($SalesOrderStockMain_tb . '.stock_entry_date', 'desc')
                ->first();

                if ($current_stock) {
                    $current_stock_value = $current_stock->current_stock;
                    $total_current_stock = $current_stock_value - $order_quantity;
                }else{
                    $total_current_stock = 0;
                }
            }

            $sales_order_stock_id = SalesOrderStockMain::join('sales_order_stock_sublist', 'sales_order_stock_sublist.sales_order_main_id', '=', 'sales_order_stock_main.id')
            ->where('sales_order_stock_main.dealer_creation_id', $dealer_creation_id)
            ->where(function ($query) {
                $query->where('sales_order_stock_main.delete_status', '0')
                    ->orWhereNull('sales_order_stock_main.delete_status');
            })
            ->where('sales_order_stock_main.status', '1')
            ->where('sales_order_stock_main.stock_entry_date', '<=', $order_date)
            ->where('sales_order_stock_sublist.item_creation_id', $item_creation_id)
            ->where('sales_order_stock_sublist.item_property', $item_property)
            ->where('sales_order_stock_sublist.item_weights', $item_weights)
            ->orderBy('sales_order_stock_main.stock_entry_date', 'desc')
            ->select('sales_order_stock_main.id as stock_main_id', 'sales_order_stock_sublist.id as stock_sub_id')
            ->first();

            if($sales_order_stock_id){
                $sales_order_stock_main_max_id = $sales_order_stock_id -> stock_main_id;
                $sales_order_stock_sub_max_id = $sales_order_stock_id -> stock_sub_id;
            }else{
                $sales_order_stock_main_max_id = '0';
                $sales_order_stock_sub_max_id = '0';
            }

            SalesOrderStockSub::join('sales_order_stock_main as sm', 'sales_order_stock_sublist.sales_order_main_id', '=', 'sm.id')
            ->where('sm.dealer_creation_id', $dealer_creation_id)
            ->where('sales_order_stock_sublist.item_creation_id', $item_creation_id)
            ->where('sales_order_stock_sublist.item_property', $item_property)
            ->where('sales_order_stock_sublist.item_weights', $item_weights)
            ->where('sm.id', $sales_order_stock_main_max_id)
            ->where('sales_order_stock_sublist.id', $sales_order_stock_sub_max_id)
            ->update(['sales_order_stock_sublist.current_stock' => $total_current_stock]);

            return $main_id;
        }
        else if($action=='update_sub')
        {
            if($request->input('id')){
                $sales_return_d2c_sub = SalesReturnD2CSub::find($request->input('id'));
                $main_id = $sales_return_d2c_sub->sales_order_main_id;
                $order_quantity_sub = $sales_return_d2c_sub->order_quantity;
                $sales_return_d2c_main = SalesReturnD2CMain::find($main_id);
                $order_date = $sales_return_d2c_main->order_date;
                $dealer_creation_id = $sales_return_d2c_main->dealer_creation_id;
            }

            $item_creation_id = $request->input('item_creation_id');
            $item_property = $request->input('item_property');
            $item_weights = $request->input('item_weights');

            $SalesOrderStockMain_tb = (new SalesOrderStockMain)->getTable();
            $SalesOrderStockSub_tb = (new SalesOrderStockSub)->getTable();

            $current_stock = SalesOrderStockMain::select($SalesOrderStockMain_tb . '.id', $SalesOrderStockMain_tb . '.stock_entry_date', $SalesOrderStockSub_tb . '.current_stock')
            ->join($SalesOrderStockSub_tb, $SalesOrderStockSub_tb . '.sales_order_main_id', '=', $SalesOrderStockMain_tb . '.id')
            ->where($SalesOrderStockMain_tb . '.dealer_creation_id', $dealer_creation_id)
            ->where(function ($query) use ($SalesOrderStockMain_tb) {
                $query->where($SalesOrderStockMain_tb . '.delete_status', '0')
                    ->orWhereNull($SalesOrderStockMain_tb . '.delete_status');
            })
            ->where($SalesOrderStockMain_tb . '.status', '1')
            ->where($SalesOrderStockMain_tb . '.stock_entry_date', '<=', $order_date)
            ->where($SalesOrderStockSub_tb . '.item_creation_id', $item_creation_id)
            ->where($SalesOrderStockSub_tb . '.item_property', $item_property)
            ->where($SalesOrderStockSub_tb . '.item_weights', $item_weights)
            ->orderBy($SalesOrderStockMain_tb . '.stock_entry_date', 'desc')
            ->first();

            if ($current_stock) {
                $current_stock = $current_stock->current_stock;
                $total_current_stock = $current_stock + $order_quantity_sub;
            }else{
                $total_current_stock = $current_stock->current_stock;
            }

            $sales_order_stock_id = SalesOrderStockMain::join('sales_order_stock_sublist', 'sales_order_stock_sublist.sales_order_main_id', '=', 'sales_order_stock_main.id')
            ->where('sales_order_stock_main.dealer_creation_id', $dealer_creation_id)
            ->where(function ($query) {
                $query->where('sales_order_stock_main.delete_status', '0')
                    ->orWhereNull('sales_order_stock_main.delete_status');
            })
            ->where('sales_order_stock_main.status', '1')
            ->where('sales_order_stock_main.stock_entry_date', '<=', $order_date)
            ->where('sales_order_stock_sublist.item_creation_id', $item_creation_id)
            ->where('sales_order_stock_sublist.item_property', $item_property)
            ->where('sales_order_stock_sublist.item_weights', $item_weights)
            ->orderBy('sales_order_stock_main.stock_entry_date', 'desc')
            ->select('sales_order_stock_main.id as stock_main_id', 'sales_order_stock_sublist.id as stock_sub_id')
            ->first();

            if($sales_order_stock_id){
                $sales_order_stock_main_max_id = $sales_order_stock_id -> stock_main_id;
                $sales_order_stock_sub_max_id = $sales_order_stock_id -> stock_sub_id;
            }else{
                $sales_order_stock_main_max_id = '0';
                $sales_order_stock_sub_max_id = '0';
            }

            SalesOrderStockSub::join('sales_order_stock_main as sm', 'sales_order_stock_sublist.sales_order_main_id', '=', 'sm.id')
            ->where('sm.dealer_creation_id', $dealer_creation_id)
            ->where('sales_order_stock_sublist.item_creation_id', $item_creation_id)
            ->where('sales_order_stock_sublist.item_property', $item_property)
            ->where('sales_order_stock_sublist.item_weights', $item_weights)
            ->where('sm.id', $sales_order_stock_main_max_id)
            ->where('sales_order_stock_sublist.id', $sales_order_stock_sub_max_id)
            ->update(['sales_order_stock_sublist.current_stock' => $total_current_stock]);

            $tb = SalesReturnD2CSub::find($request->input('id'));
            $tb->order_date_sub = $request->input('order_date_sub');
            $tb->time_sub = $request->input('time_sub');
            $tb->tally_no = $request->input('tally_no');
            $tb->batch_no = $request->input('batch_no');
            $tb->return_type_id = $request->input('return_type_id');
            $tb->group_creation_id = $request->input('group_creation_id');
            $tb->item_creation_id = $request->input('item_creation_id');
            $tb->short_code_id = $request->input('short_code_id');
            $tb->order_quantity = $request->input('order_quantity');
            $tb->pieces_quantity = $request->input('pieces_quantity');
            $tb->item_property = $request->input('item_property');
            $tb->item_weights = $request->input('item_weights');
            $tb->item_price = $request->input('item_price');
            $tb->total_amount = $request->input('total_amount');
            $tb->save();

            $order_date_sub = $request->input('order_date_sub');
            if($request->input('dealer_creation_id')){
                $dealer_creation_id = $request->input('dealer_creation_id');
            }else{
                $sales_return_d2c_main = SalesReturnD2CMain::find($main_id);
                $dealer_creation_id = $sales_return_d2c_main->dealer_creation_id;
            }
            $item_creation_id = $request->input('item_creation_id');
            $item_property = $request->input('item_property');
            $item_weights = $request->input('item_weights');
            $order_quantity = $request->input('order_quantity');

            if($item_creation_id && $order_quantity){
                $SalesOrderStockMain_tb = (new SalesOrderStockMain)->getTable();
                $SalesOrderStockSub_tb = (new SalesOrderStockSub)->getTable();

                $current_stock = SalesOrderStockMain::select($SalesOrderStockMain_tb . '.id', $SalesOrderStockMain_tb . '.stock_entry_date', $SalesOrderStockSub_tb . '.current_stock')
                ->join($SalesOrderStockSub_tb, $SalesOrderStockSub_tb . '.sales_order_main_id', '=', $SalesOrderStockMain_tb . '.id')
                ->where($SalesOrderStockMain_tb . '.dealer_creation_id', $dealer_creation_id)
                ->where(function ($query) use ($SalesOrderStockMain_tb) {
                    $query->where($SalesOrderStockMain_tb . '.delete_status', '0')
                        ->orWhereNull($SalesOrderStockMain_tb . '.delete_status');
                })
                ->where($SalesOrderStockMain_tb . '.status', '1')
                ->where($SalesOrderStockMain_tb . '.stock_entry_date', '<=', $order_date)
                ->where($SalesOrderStockSub_tb . '.item_creation_id', $item_creation_id)
                ->where($SalesOrderStockSub_tb . '.item_property', $item_property)
                ->where($SalesOrderStockSub_tb . '.item_weights', $item_weights)
                ->orderBy($SalesOrderStockMain_tb . '.stock_entry_date', 'desc')
                ->first();

                if ($current_stock) {
                    $current_stock_value = $current_stock->current_stock;
                    $total_current_stock = $current_stock_value - $order_quantity;
                }
            }

            $sales_order_stock_id = SalesOrderStockMain::join('sales_order_stock_sublist', 'sales_order_stock_sublist.sales_order_main_id', '=', 'sales_order_stock_main.id')
            ->where('sales_order_stock_main.dealer_creation_id', $dealer_creation_id)
            ->where(function ($query) {
                $query->where('sales_order_stock_main.delete_status', '0')
                    ->orWhereNull('sales_order_stock_main.delete_status');
            })
            ->where('sales_order_stock_main.status', '1')
            ->where('sales_order_stock_main.stock_entry_date', '<=', $order_date)
            ->where('sales_order_stock_sublist.item_creation_id', $item_creation_id)
            ->where('sales_order_stock_sublist.item_property', $item_property)
            ->where('sales_order_stock_sublist.item_weights', $item_weights)
            ->orderBy('sales_order_stock_main.stock_entry_date', 'desc')
            ->select('sales_order_stock_main.id as stock_main_id', 'sales_order_stock_sublist.id as stock_sub_id')
            ->first();

            if($sales_order_stock_id){
                $sales_order_stock_main_max_id = $sales_order_stock_id -> stock_main_id;
                $sales_order_stock_sub_max_id = $sales_order_stock_id -> stock_sub_id;
            }else{
                $sales_order_stock_main_max_id = '0';
                $sales_order_stock_sub_max_id = '0';
            }

            SalesOrderStockSub::join('sales_order_stock_main as sm', 'sales_order_stock_sublist.sales_order_main_id', '=', 'sm.id')
            ->where('sm.dealer_creation_id', $dealer_creation_id)
            ->where('sales_order_stock_sublist.item_creation_id', $item_creation_id)
            ->where('sales_order_stock_sublist.item_property', $item_property)
            ->where('sales_order_stock_sublist.item_weights', $item_weights)
            ->where('sm.id', $sales_order_stock_main_max_id)
            ->where('sales_order_stock_sublist.id', $sales_order_stock_sub_max_id)
            ->update(['sales_order_stock_sublist.current_stock' => $total_current_stock]);

        }
        else if($action=='delete_sub')
        {
            if($request->input('id')){
                $sales_return_d2c_sub = SalesReturnD2CSub::find($request->input('id'));
                $main_id = $sales_return_d2c_sub->sales_order_main_id;
                $order_quantity_sub = $sales_return_d2c_sub->order_quantity;
                $item_creation_id = $sales_return_d2c_sub->item_creation_id;
                $item_property = $sales_return_d2c_sub->item_property;
                $item_weights = $sales_return_d2c_sub->item_weights;
                $sales_return_d2c_main = SalesReturnD2CMain::find($main_id);
                $order_date = $sales_return_d2c_main->order_date;
                $dealer_creation_id = $sales_return_d2c_main->dealer_creation_id;
            }
            $SalesOrderStockMain_tb = (new SalesOrderStockMain)->getTable();
            $SalesOrderStockSub_tb = (new SalesOrderStockSub)->getTable();

            $current_stock = SalesOrderStockMain::select($SalesOrderStockMain_tb . '.id', $SalesOrderStockMain_tb . '.stock_entry_date', $SalesOrderStockSub_tb . '.current_stock')
            ->join($SalesOrderStockSub_tb, $SalesOrderStockSub_tb . '.sales_order_main_id', '=', $SalesOrderStockMain_tb . '.id')
            ->where($SalesOrderStockMain_tb . '.dealer_creation_id', $dealer_creation_id)
            ->where(function ($query) use ($SalesOrderStockMain_tb) {
                $query->where($SalesOrderStockMain_tb . '.delete_status', '0')
                    ->orWhereNull($SalesOrderStockMain_tb . '.delete_status');
            })
            ->where($SalesOrderStockMain_tb . '.status', '1')
            ->where($SalesOrderStockMain_tb . '.stock_entry_date', '<=', $order_date)
            ->where($SalesOrderStockSub_tb . '.item_creation_id', $item_creation_id)
            ->where($SalesOrderStockSub_tb . '.item_property', $item_property)
            ->where($SalesOrderStockSub_tb . '.item_weights', $item_weights)
            ->orderBy($SalesOrderStockMain_tb . '.stock_entry_date', 'desc')
            ->first();

            if ($current_stock) {
                $current_stock = $current_stock->current_stock;
                $total_current_stock = $current_stock + $order_quantity_sub;
            }else{
                $total_current_stock = $current_stock->current_stock;
            }

            $sales_order_stock_id = SalesOrderStockMain::join('sales_order_stock_sublist', 'sales_order_stock_sublist.sales_order_main_id', '=', 'sales_order_stock_main.id')
            ->where('sales_order_stock_main.dealer_creation_id', $dealer_creation_id)
            ->where(function ($query) {
                $query->where('sales_order_stock_main.delete_status', '0')
                    ->orWhereNull('sales_order_stock_main.delete_status');
            })
            ->where('sales_order_stock_main.status', '1')
            ->where('sales_order_stock_main.stock_entry_date', '<=', $order_date)
            ->where('sales_order_stock_sublist.item_creation_id', $item_creation_id)
            ->where('sales_order_stock_sublist.item_property', $item_property)
            ->where('sales_order_stock_sublist.item_weights', $item_weights)
            ->orderBy('sales_order_stock_main.stock_entry_date', 'desc')
            ->select('sales_order_stock_main.id as stock_main_id', 'sales_order_stock_sublist.id as stock_sub_id')
            ->first();

            if($sales_order_stock_id){
                $sales_order_stock_main_max_id = $sales_order_stock_id -> stock_main_id;
                $sales_order_stock_sub_max_id = $sales_order_stock_id -> stock_sub_id;
            }else{
                $sales_order_stock_main_max_id = '0';
                $sales_order_stock_sub_max_id = '0';
            }

            SalesOrderStockSub::join('sales_order_stock_main as sm', 'sales_order_stock_sublist.sales_order_main_id', '=', 'sm.id')
            ->where('sm.dealer_creation_id', $dealer_creation_id)
            ->where('sales_order_stock_sublist.item_creation_id', $item_creation_id)
            ->where('sales_order_stock_sublist.item_property', $item_property)
            ->where('sales_order_stock_sublist.item_weights', $item_weights)
            ->where('sm.id', $sales_order_stock_main_max_id)
            ->where('sales_order_stock_sublist.id', $sales_order_stock_sub_max_id)
            ->update(['sales_order_stock_sublist.current_stock' => $total_current_stock]);

            $tb = SalesReturnD2CSub::find($request->input('id'));
            $tb->delete_status = "1";
            $tb->save();
        }
        else if($action=='retrieve')
        {
            $sales_return_d2c_main = $this->retrieve($request->input('from_date_1'),$request->input('to_date_1'),$request->input('order_no_1'),
            $request->input('dealer_creation_id_1'),
            $request->input('status_1'),$request->input('description'), $request->input('sales_exec_1'));
            // return $sales_return_d2c_main;
            return view('Entry.sales_return_d2c.list',['sales_return_d2c_main'=>$sales_return_d2c_main,'user_rights_edit_1'=>$request->input('user_rights_edit_1'),'user_rights_delete_1'=>$request->input('user_rights_delete_1')]);
        }
        else if($action=='create_form')
        {
            $main_tb = (new SalesReturnD2CMain)->getTable();
            $next_id = DB::select("SHOW TABLE STATUS LIKE '".$main_tb."'");
            $order_no="RETURN_".date("ym")."_".$next_id[0]->Auto_increment;
            $market_creation=MarketCreation::select('id','area_name')->where(function($query){$query->where('delete_status', '0')->orWhereNull('delete_status');})->orderBy('area_name')->get();
            $dealer_creation=DealerCreation::select('id','dealer_name')->where(function($query){$query->where('delete_status', '0')->orWhereNull('delete_status');})->orderBy('dealer_name')->get();
            $sales_name = SalesRepCreation::select('id', 'sales_ref_name')->where(function ($query) {
                $query->where('delete_status', '0')->orWhereNull('delete_status');
            })->orderBy('sales_ref_name')->get();
            return view('Entry.sales_return_d2c.create',['order_no'=>$order_no,'market_creation'=>$market_creation,'dealer_creation'=>$dealer_creation,"sales_name" => $sales_name]);
        }
        else if($action=='update_form')
        {
            $sales_return_d2c_main=$this->retrieve_main($request->input('id'));
            $market_creation=MarketCreation::select('id','area_name')->where(function($query){$query->where('delete_status', '0')->orWhereNull('delete_status');})->orderBy('area_name')->get();
            $dealer_creation=DealerCreation::select('id','dealer_name')->where(function($query){$query->where('delete_status', '0')->orWhereNull('delete_status');})->orderBy('dealer_name')->get();
            $sales_name = SalesRepCreation::select('id', 'sales_ref_name')->where(function ($query) {
                $query->where('delete_status', '0')->orWhereNull('delete_status');
            })->orderBy('sales_ref_name')->get();
            return view('Entry.sales_return_d2c.update',['sales_return_d2c_main'=>$sales_return_d2c_main,'market_creation'=>$market_creation,'dealer_creation'=>$dealer_creation,"sales_name" => $sales_name]);
        }
        else if($action=='form_sublist')
        {
            $main_id=$request->input('main_id');$sub_id=$request->input('sub_id');
            $sales_return_d2c_sub=null;if($sub_id!=""){$sales_return_d2c_sub = $this->retrieve_sub($main_id,$sub_id);}
            $sales_return_d2c_sub_list=[];if($main_id!=""){$sales_return_d2c_sub_list = $this->retrieve_sub($main_id,'');}
            $market_creation=MarketCreation::select('id','area_name')->where(function($query){$query->where('delete_status', '0')->orWhereNull('delete_status');})->orderBy('area_name')->get();
            $group_creation = GroupCreation::select('id', 'group_name')->where(function ($query) {
                $query->where('delete_status', '0')->orWhereNull('delete_status');
            })->orderBy('group_name')->get();

            $tally_no_main = SalesOrderDeliveryMain::select('id', 'tally_no')->where(function ($query) {
                $query->where('delete_status', '0')->orWhereNull('delete_status');
            })->orderBy('id')->get();

            $item_creation=ItemCreation::select('id','item_name','short_code','distributor_rate','piece')->where(function($query){$query->where('delete_status', '0')->orWhereNull('delete_status');})->orderBy('item_name')->get();
            $item_properties_type=ItemPropertiesType::select('id','item_properties_type')->where('status1', '1')->where(function($query){$query->where('delete_status', '0')->orWhereNull('delete_status');})->orderBy('item_properties_type')->get();
            $item_liters_type=ItemLitersType::select('id','item_liters_type')->where('status1', '1')->where(function($query){$query->where('delete_status', '0')->orWhereNull('delete_status');})->orderBy('item_liters_type')->get();
            $return_creation=ReturnCreation::select('id','return_type')->where('status', '1')->where(function($query){$query->where('delete_status', '0')->orWhereNull('delete_status');})->orderBy('return_type')->get();

            return view('Entry.sales_return_d2c.sublist',['sales_return_d2c_sub'=>$sales_return_d2c_sub,'sales_return_d2c_sub_list'=>$sales_return_d2c_sub_list,'main_id'=>$main_id,'sub_id'=>$sub_id,'market_creation'=>$market_creation,'group_creation'=>$group_creation,'tally_no_main'=>$tally_no_main,'item_creation'=>$item_creation,'item_properties_type'=>$item_properties_type,'return_creation'=>$return_creation,'item_liters_type'=>$item_liters_type,'user_rights_edit_1'=>$request->input('user_rights_edit_1'),'user_rights_delete_1'=>$request->input('user_rights_delete_1')]);
        }
        else if ($action == 'gettallydescno') {
            $tally_no = $request->input('tally_no');

            // $descriptions = SalesOrderC2DMain::select('sales_order_c2d_main.id', 'sales_order_c2d_main.description')
            // ->leftJoin('sales_order_delivery_main_c', 'sales_order_c2d_main.desc', '=', 'sales_order_delivery_main_c.id')
            // ->where('sales_order_c2d_main.id', $tally_no)
            // ->where(function ($query) {
            //     $query->where('sales_order_delivery_main_c.delete_status', '0')->orWhereNull('sales_order_delivery_main_c.delete_status');
            // })
            // ->orderBy('sales_order_c2d_main.id')
            // ->get();

            $descriptions = SalesOrderDeliveryMain::select('sales_order_c2d_main.id', 'sales_order_c2d_main.description')
            ->leftJoin('sales_order_c2d_main', 'sales_order_delivery_main_c.desc', '=', 'sales_order_c2d_main.id')
                ->where('sales_order_delivery_main_c.id', $tally_no)
                ->where(function ($query) {
                    $query->where('sales_order_c2d_main.delete_status', '0')->orWhereNull('sales_order_c2d_main.delete_status');
                })
                ->orderBy('id')
                ->get();

            return response()->json($descriptions);
        }


        else if ($action == 'getdearlername1') {

            $sales_exec = $request->input('sales_exec');

            $dealer_name = DealerCreation::select('id', 'dealer_name')
            ->where('sales_rep_id', $sales_exec)
            ->get();

            return response()->json($dealer_name);
        }
        else if ($action == 'getdearlername') {

            $sales_exec = $request->input('sales_exec');

            $dealer_name = DealerCreation::select('id', 'dealer_name')
            ->where('sales_rep_id', $sales_exec)
            ->get();

            return response()->json($dealer_name);
        }

        else if ($action == 'gettally_no') {

            $sales_exec = $request->input('sales_exec');
            $dealer_creation_id = $request->input('dealer_creation_id');

            $SalesOrderDeliveryMain_tb = (new SalesOrderDeliveryMain)->getTable();
            $SalesOrderDeliverySub_tb = (new SalesOrderDeliverySub)->getTable();

            $tally_no = SalesOrderDeliveryMain::select($SalesOrderDeliveryMain_tb . '.id', $SalesOrderDeliveryMain_tb . '.tally_no')
            ->join($SalesOrderDeliverySub_tb, $SalesOrderDeliverySub_tb . '.sales_order_main_id', '=', $SalesOrderDeliveryMain_tb . '.id')
            ->where($SalesOrderDeliveryMain_tb . '.dealer_creation_id', $dealer_creation_id)
            ->where($SalesOrderDeliverySub_tb . '.bal_amount', '!=', 0)
            ->where(function ($query) use ($SalesOrderDeliveryMain_tb) {
                $query->where($SalesOrderDeliveryMain_tb . '.delete_status', '0')
                    ->orWhereNull($SalesOrderDeliveryMain_tb . '.delete_status');
            })
            ->where(function ($query) use ($SalesOrderDeliverySub_tb) {
                $query->where($SalesOrderDeliverySub_tb . '.delete_status', '0')
                    ->orWhereNull($SalesOrderDeliverySub_tb . '.delete_status');
            })
            ->groupBy($SalesOrderDeliveryMain_tb . '.id', $SalesOrderDeliveryMain_tb . '.tally_no') // Include tally_no in GROUP BY
            ->get();

            return response()->json($tally_no);
        }

        else if ($action == 'odernum') {

            $dealer_creation_id = $request->input('dealer_creation_id_1');

            $order_number = SalesReturnD2CMain::select('id', 'order_no')
            ->where(function ($query) {
                $query->where('delete_status', '0')->orWhereNull('delete_status');
            })
            ->where('dealer_creation_id', $dealer_creation_id)
            ->get();

            return response()->json($order_number);
        }
        else if ($action == 'getmarket') {

            $dealer_creation_id = $request->input('dealer_creation_id');

            $dealer_address = DealerCreation::select('id', 'address')
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
        else if ($action == 'getshop') {

            $tally_no = $request->input('tally_no');

            $shop_name = ShopCreation::select('id', 'shop_name')
            ->where('beats_id', $tally_no)
            ->get();

            return response()->json($shop_name);
        }
        else if ($action == 'getopeningstock') {

            $dealer_creation_id = $request->input('dealer_creation_id');
            $item_creation_id = $request->input('item_creation_id');
            $order_date_sub = $request->input('order_date_sub');

            $SalesOrderStockMain_tb = (new SalesOrderStockMain)->getTable();
            $SalesOrderStockSub_tb = (new SalesOrderStockSub)->getTable();

            $current_stock = SalesOrderStockMain::orderBy($SalesOrderStockSub_tb . '.id', 'desc')
            ->select($SalesOrderStockSub_tb . '.current_stock')
            ->join($SalesOrderStockSub_tb, $SalesOrderStockSub_tb . '.sales_order_main_id', '=', $SalesOrderStockMain_tb . '.id')
            ->where($SalesOrderStockMain_tb . '.dealer_creation_id', '=', $dealer_creation_id)
            ->where($SalesOrderStockMain_tb . '.stock_entry_date', '=', $order_date_sub)
            ->where($SalesOrderStockSub_tb . '.item_creation_id', '=', $item_creation_id)
            ->orderBy($SalesOrderStockMain_tb . '.stock_entry_date', 'desc')
            ->first();

            if ($current_stock) {
                $current_stock_value = $current_stock->current_stock;
            } else {
                $current_stock_value = "0";
            }

            return response()->json($current_stock_value);
        }
        else if ($action == 'getshortcode') {

            $item_creation_id = $request->input('item_creation_id');

            $short_code = ItemCreation::select('id', 'short_code')
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

            // $item = ItemPropertiesType::find($item_creation_id);
            // $item_properties_type = $item->item_properties_type;
            // $id = $item->id;
            // $all_val=[
            //     'item_properties_type'=>$item_properties_type,
            //     'id'=>$id
            // ];
            //     $arr[]=$all_val;
            $item_properties_type = ItemCreation::select('item_properties_type.id','item_properties_type.item_properties_type')
            ->join('item_properties_type', 'item_properties_type.id', '=', 'item_creation.item_properties_type')
            ->where('item_creation.id', $item_creation_id)
            ->get();

            return response()->json($item_properties_type);
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
    }
}
