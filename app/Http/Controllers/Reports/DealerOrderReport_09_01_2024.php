<?php

namespace App\Http\Controllers\Reports;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Models\Entry\SalesOrderC2DMain;
use App\Models\Entry\SalesOrderC2DSub;
use App\Models\Entry\SalesOrderD2SMain;
use App\Models\Entry\SalesOrderD2Ssub;
use App\Models\SalesRepCreation;
use App\Models\DealerCreation;
use App\Models\ItemCreation;
use App\Models\GroupCreation;
use App\Models\ItemLitersType;
use App\Models\ShopCreation;
use App\Models\ItemPropertiesType;
use App\Models\MarketManagerCreation;

use Carbon\Carbon;

class DealerOrderReport extends Controller
{
    public function dealer_order_report()
    {

        // $salesRep=SalesRepCreation::select('id','sales_ref_name')->where(function($query){$query->where('delete_status', '0')->orWhereNull('delete_status');})->orderBy('sales_ref_name')->get();

        $manager_creation=MarketManagerCreation::select('id','manager_name')->where('delete_status', '0')->orWhereNull('delete_status')->orderBy('manager_no')->get();

        $dealer_creation=DealerCreation::select('id','dealer_name')->where(function($query){$query->where('delete_status', '0')->orWhereNull('delete_status');})->orderBy('dealer_name')->get();

        $group_creation=GroupCreation::select('id','group_name')->where(function($query){$query->where('delete_status', '0')->orWhereNull('delete_status');})->orderBy('group_name')->get();

        $item_creation=ItemCreation::select('id','short_code')->where(function($query){$query->where('delete_status', '0')->orWhereNull('delete_status');})->orderBy('short_code')->get();

        return view('Reports.dealer_orders_report.admin',[
            'manager_creation'=>$manager_creation,
            'dealer_creation'=>$dealer_creation,
            'group_creation'=>$group_creation,
            'item_creation'=>$item_creation

    ]);
    }



     public function db_cmd(Request $request)
    {
        $action=$request->input('action');

         if($action=='retrieve')
        {
            $from_date = $request->input('from_date');
            $to_date = $request->input('to_date');
            $dea_id = $request->input('dealer_id');
            $manager_id = $request->input('manager_id');
            $sales_ref_id = $request->input('sales_ref_id');
            $getgroup_id = $request->input('group_id');
            $item_id = $request->input('item_id');

            $itemCreationIds = DB::table('sales_order_d2s_sublist as sodss')
            ->leftJoin('sales_order_d2s_main as sodsm', 'sodsm.id', '=', 'sodss.sales_order_main_id')
            ->where(function ($query) {
                $query->where('sodss.delete_status', '0')->orWhereNull('sodss.delete_status');
            })
            ->where(function ($query) {
                $query->where('sodsm.delete_status', '0')->orWhereNull('sodsm.delete_status');
            })
            ->groupBy('sodss.item_creation_id')
            ->select('sodss.item_creation_id');
        
       
        
        $itemCreationIds1 = $itemCreationIds->get();
        
        // Use 'item_creation_id' without 'sodss.' prefix in pluck
        $item_creation_ids = $itemCreationIds1->pluck('item_creation_id')->toArray();





            //$item_creation_ids = SalesOrderD2Ssub::all()->pluck('item_creation_id')->unique();

            $ItemCreation = [];

            foreach ($item_creation_ids as $item_creation_id) {
                $itemCreation = ItemCreation::where('id', $item_creation_id);
                if (!empty($getgroup_id)) {
                    $itemCreation->where('group_id', '=', $getgroup_id);
                }
                if (!empty($item_id)) {
                    $itemCreation->where('id', '=',  $item_id);
                }
    
                $itemCreation = $itemCreation->first();

                if ($itemCreation) {
                    $group_id = $itemCreation->group_id;
                    $groupCreation = GroupCreation::where('id', $group_id)->first();

                    if ($groupCreation) {
                        $ItemCreation[] = [
                            'short_code' => $itemCreation->short_code,
                            'group_name' => $groupCreation->group_name,
                        ];
                    }
                }
            }

            // return $ItemCreation;




            // $ItemCreation=ItemCreation::select('item_creation.id','item_creation.short_code','group_creation.group_name')
            // ->join('group_creation', 'group_creation.id', '=', 'item_creation.group_id')
            // ->where(function($query){
            //     $query->where('item_creation.delete_status', '0')->orWhereNull('item_creation.delete_status');
            // })
            // ->orderBy('item_creation.short_code')
            // ->get();

            $item_namess = ItemCreation::select('short_code')
        ->where(function ($query) {
            $query->where('delete_status', '0')->orWhereNull('delete_status');
        })
        ->whereIn('id', $item_creation_ids)
        ->orderBy('item_name')
        ->get();

            $query = DB::table('sales_order_d2s_main as sodm')
                ->leftJoin('sales_order_d2s_sublist as sods', 'sodm.id', '=', 'sods.sales_order_main_id')
                ->leftJoin('dealer_creation as dc', 'dc.id', '=', 'sodm.dealer_creation_id')
                ->leftJoin('sales_ref_creation as src', 'src.id', '=', 'dc.sales_rep_id')
                ->leftJoin('market_manager_creation as mmc', 'mmc.id', '=', 'src.manager_id')
                ->leftJoin('shop_creation as sc', 'sc.id', '=', 'sods.shop_creation_id')
                ->leftJoin('item_creation as ic', 'ic.id', '=', 'sods.item_creation_id')
                ->leftJoin('group_creation as gc', 'gc.id', '=', 'sods.group_creation_id')
                ->leftJoin('area_creation as ac', 'ac.id', '=', 'sodm.market_creation_id')
                ->where(function ($query) {
                    $query->whereNull('sodm.delete_status')
                          ->orWhere('sodm.delete_status', 0);
                });

                $dynamicColumns = [];

                foreach ($item_namess as $item_name) {
                    $dynamicColumns[] = DB::raw("SUM(CASE WHEN ic.short_code = '$item_name->short_code' THEN sods.order_quantity ELSE 0 END) AS {$item_name->short_code}");
                }


                $query->groupBy(
                    'mmc.manager_name',
                    'src.id',
                    'src.sales_ref_name',
                    'dc.id',
                    
                   
                    'dc.dealer_name',
                    'sc.shop_name',
                    'sc.mobile_no',
                    'sc.whatsapp_no',
                    'sc.address',
                    'sods.order_date',
                   
                   
                    
                    'ac.area_name'
                )
                ->select(array_merge([
                    'mmc.manager_name',
                    'src.sales_ref_name',
                    'dc.dealer_name',
                    'sc.shop_name',
                    'sc.mobile_no',
                    'sc.whatsapp_no',
                    'sc.address',
                    'sods.order_date',
                
                  
                   
                    'ac.area_name',
                ], $dynamicColumns));


                if (empty(($request->input('from_date')))) {
                    $currentDay = date('d');
                    $currentMonth = date('m');
                    $currentYear = date('Y');
                    $from_date = $currentYear . '-' . $currentMonth . '-'.$currentDay;
                    $to_date = $currentYear . '-' . $currentMonth . '-'.$currentDay;

                }else{
                    $from_date =  $request->input('from_date');
                    $to_date =  $request->input('to_date');
                }

            if (!empty($from_date)) {
                $query->whereDate('sods.order_date', '>=', $from_date);
            }

            if (!empty($to_date)) {
                $query->whereDate('sods.order_date', '<=', $to_date);
            }

            if (!empty($dea_id)) {
                $query->where('sodm.dealer_creation_id', $dea_id);
            }
            if (!empty($sales_ref_id)) {
                $query->where('sodm.sales_exec', $sales_ref_id);
            }

            if (!empty($manager_id)) {
                $query->where('mmc.id', $manager_id);
            }
            if(!empty($getgroup_id)) {
                $query->where('gc.id', $getgroup_id);
            }
            if (!empty($item_id)) {
                $query->where('ic.id', $item_id);
            }
            
//$test = $query;
            $records = $query->get();
            if($from_date==''){
                $from_date = date('Y-m').'-'.'01';
            }
            if($to_date==''){
                $to_date = date('Y-m-t');
            }

            //return  $records;
            //return 'test'.$test;
 //return $records;
            return view('Reports.dealer_orders_report.list',[
            'ItemCreation' => $ItemCreation,
            'records' => $records,
            'from_date'=>$from_date,
            'to_date'=>$to_date,
            'user_rights_edit_1'=>$request->input('user_rights_edit_1'),'user_rights_delete_1'=>$request->input('user_rights_delete_1')]);
        } else if ($action == 'getSalesRef') {


            $manager_id= $request->input('manager_id');

            $sales_ref_name = SalesRepCreation::select('id', 'sales_ref_name')->where('manager_id', $manager_id)->where(function ($query) {
                $query->where('delete_status', '0')->orWhereNull('delete_status');
            })->orderBy('sales_ref_name')->get();
            return response()->json($sales_ref_name);
        }
        else if ($action == 'getDealer') {


            $sales_ref_id= $request->input('sales_ref_id');
            $manager_id= $request->input('manager_id');

            $dealer_creation=DealerCreation::select('id','dealer_name')->where('sales_rep_id', $sales_ref_id)->where('manager_name', $manager_id)->where(function($query){$query->where('delete_status', '0')->orWhereNull('delete_status');})->orderBy('dealer_name')->get();
            return response()->json($dealer_creation);
        }

        else if ($action == 'getitemName') {
            $group_id = $request->input('group_id');
             $get_item_name = ItemCreation::select('id', 'short_code')->where(function ($query) {
                $query->where('delete_status', '0')->orWhereNull('delete_status');
            })->orderBy('short_code');

            if (!empty($group_id)) {
                $get_item_name->where('group_id', '=', $group_id);
            }
            $item_name = $get_item_name->get();
            return response()->json($item_name);
        }
    }
}
