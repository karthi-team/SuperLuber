<?php
namespace App\Http\Controllers\Reports;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Models\Entry\SalesOrderD2SMain;
use App\Models\Entry\SalesOrderD2Ssub;
use App\Models\Entry\SalesOrderStockMain;
use App\Models\Entry\SalesOrderStockSub;
use App\Models\MarketCreation;
use App\Models\DealerCreation;
use App\Models\ItemCreation;
use App\Models\ItemLitersType;
use App\Models\ItemPropertiesType;
use App\Models\ShopsType;
use App\Models\ShopCreation;
use App\Models\SalesRepCreation;
use App\Models\Entry\VisitorCreation;
use Carbon\Carbon;
use App\Models\MarketManagerCreation;
use App\Models\GroupCreation;
use PhpParser\Node\Stmt\Return_;

class EffectiveCalls extends Controller
{
    public function effective_calls_Report()
    {
        $order_no_list = SalesOrderD2SMain::select('sales_exec')->where(function ($query) {
            $query->where('delete_status', '0')->orWhereNull('delete_status');
        })->get();
        $market_creation = MarketCreation::select('id', 'area_name')->where(function ($query) {
            $query->where('delete_status', '0')->orWhereNull('delete_status');
        })->orderBy('area_name')->get();
        $dealer_creation = DealerCreation::select('id', 'dealer_name')->where(function ($query) {
            $query->where('delete_status', '0')->orWhereNull('delete_status');
        })->orderBy('dealer_name')->get();
        $shop_creation = ShopsType::select('id', 'shops_type')->where(function ($query) {
            $query->where('delete_status', '0')->orWhereNull('delete_status');
        })->orderBy('shops_type')->get();

        $sales_name = SalesRepCreation::select('id', 'sales_ref_name')->where(function ($query) {
            $query->where('delete_status', '0')->orWhereNull('delete_status');
        })->orderBy('sales_ref_name')->get();

        $item_namess = ItemCreation::select('id', 'item_name')->where(function ($query) {
            $query->where('delete_status', '0')->orWhereNull('delete_status');
        })->orderBy('item_name')->get();

        $manager_names = MarketManagerCreation::select('id', 'manager_name')->where(function ($query) {
            $query->where('delete_status', '0')->orWhereNull('delete_status');
        })->orderBy('manager_name')->get();

        $group_creation = GroupCreation::select('id', 'group_name')
        ->where(function ($query) {
            $query->where('delete_status', '0')->orWhereNull('delete_status');
        })->orderBy('group_name')
        ->get();

        return view('Reports.effective_calls_Report.admin', ['order_no_list' => $order_no_list, 'market_creation' => $market_creation, 'dealer_creation' => $dealer_creation, 'shop_creation' => $shop_creation, "sales_name" => $sales_name,'item_namess' =>$item_namess,'manager_names'=>$manager_names, 'group_creation'=>$group_creation]);
    }
    public function retrieve($from_date_1, $sales_exec, $dealer_creation_id_1, $manager_na, $order_shop, $item_name_1, $group_id, $item_creation_ids)
    {
        $item_namess = ItemCreation::select('short_code')
            ->where(function ($query) {
                $query->where('delete_status', '0')->orWhereNull('delete_status');
            })
            ->whereIn('id', $item_creation_ids)
            ->orderBy('item_name')
            ->get();
    
        $query = DB::table('sales_order_d2s_main AS main_table')
            ->leftJoin('area_creation', 'area_creation.id', '=', 'main_table.market_creation_id')
            ->leftJoin('sales_ref_creation', 'sales_ref_creation.id', '=', 'main_table.sales_exec')
            ->leftJoin('dealer_creation', 'dealer_creation.id', '=', 'main_table.dealer_creation_id')
            ->leftJoin('sales_order_d2s_sublist AS sublist', 'sublist.sales_order_main_id', '=', 'main_table.id')
            ->leftJoin('shop_creation', 'shop_creation.id', '=', 'sublist.shop_creation_id')
            ->leftJoin('item_creation AS short_code', 'short_code.id', '=', 'sublist.short_code_id')
            ->leftJoin('item_creation', 'item_creation.id', '=', 'sublist.item_creation_id')
            ->leftJoin('market_manager_creation', function ($join) {
                $join->on('market_manager_creation.id', '=', 'dealer_creation.manager_name')
                    ->on('dealer_creation.sales_rep_id', '=', 'main_table.sales_exec');
            })
            ->whereYear('sublist.order_date', '=', date('Y', strtotime($from_date_1)))
            ->whereMonth('sublist.order_date', '=', date('m', strtotime($from_date_1)));
    
        $dynamicColumns = [];
    
        foreach ($item_namess as $item_name) {
            $dynamicColumns[] = DB::raw("SUM(CASE WHEN short_code.short_code = '$item_name->short_code' THEN sublist.order_quantity ELSE 0 END) AS {$item_name->short_code}");
        }
    
        $query->select(array_merge([
            'main_table.order_date',
            DB::raw('GROUP_CONCAT(DISTINCT area_creation.area_name) AS area_name'),
            DB::raw('GROUP_CONCAT(DISTINCT sales_ref_creation.sales_ref_name) AS sales_ref_name'),
            DB::raw('GROUP_CONCAT(DISTINCT dealer_creation.dealer_name) AS dealer_name'),
            DB::raw('MAX(market_manager_creation.manager_name) AS manager_name'),
            DB::raw('COUNT(DISTINCT CASE WHEN sublist.status_check = "Yes" 
            AND DATE(sublist.order_date) = main_table.order_date 
            AND sublist.shop_creation_id IN (SELECT DISTINCT shop_creation_id FROM sales_order_d2s_sublist) THEN CONCAT(sublist.shop_creation_id, sublist.order_date) ELSE NULL END) AS status_check_count_1'),
    
            DB::raw('COUNT(DISTINCT sublist.shop_creation_id) AS status_check_count'),
            DB::raw('SUM(sublist.order_quantity) AS order_1'),
        ], $dynamicColumns))
    
            ->groupBy([
                'main_table.order_date',
                'market_manager_creation.id',
                'sales_ref_creation.id',
                'dealer_creation.id',
                'area_creation.id', DB::raw('DATE_FORMAT(sublist.order_date, "%Y-%m-%d")')
            ]);
    
        if (!empty($from_date_1)) {
            $query->where('main_table.order_date', '>=', $from_date_1);
        }
        // Remove the $to_date_1 condition
    
        if (!empty($sales_exec)) {
            $query->where('main_table.sales_exec', '=', $sales_exec);
        }
    
        if (!empty($dealer_creation_id_1)) {
            $query->where('main_table.dealer_creation_id', '=', $dealer_creation_id_1);
        }
    
        if (!empty($item_name_1)) {
            $query->where('sublist.item_creation_id', '=', $item_name_1);
        }
    
        if (!empty($group_id)) {
            $query->where('short_code.group_id', '=', $group_id);
        }
    
        if ($order_shop === "1") {
            $query->where('sublist.status_check', '=', 'Yes');
        } elseif ($order_shop === "0") {
            $query->where('sublist.status_check', '=', 'No');
        }
    
        // Adding deletion status checks
        $query->where(function ($query) {
            $query->where('main_table.delete_status', '0')
                ->orWhereNull('main_table.delete_status');
        });
    
        $query->orderBy('main_table.order_date');
        $results = $query->get();
    
        $resultsArray = $results->toArray();
    
        return $resultsArray;
    }
    
    
    public function db_cmd(Request $request)
    {
        $action = $request->input('action');
      if ($action == 'retrieve') {

       $group_id1= $request->input('group_id');
       $item_names_1= $request->input('item_name_1');


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
        $item_creation_ids = $itemCreationIds1->pluck('item_creation_id')->toArray();


        $item_creation_short = [];

        foreach ($item_creation_ids as $item_creation_id) {
            $itemCreation = ItemCreation::where('id', $item_creation_id);

            if (!empty($group_id1)) {
                $itemCreation->where('group_id', '=', $group_id1);
            }
            if (!empty($item_names_1)) {
                $itemCreation->where('id', '=',  $item_names_1);
            }

            $itemCreation = $itemCreation->first(); // Corrected this line

            if ($itemCreation) {
                $group_id = $itemCreation->group_id;
                $groupCreation = GroupCreation::where('id', $group_id)->first();

                if ($groupCreation) {
                    $item_creation_short[] = [
                        'short_code' => $itemCreation->short_code,
                        'group_name' => $groupCreation->group_name,
                    ];
                }
            }
        }

        $item_create = SalesOrderD2Ssub::select('id', 'item_creation_id')->where(function ($query) {
            $query->where('delete_status', '0')->orWhereNull('delete_status');
        })->orderBy('item_creation_id')->get();
        // $itemCreationShortCount = $item_creation_short->count();

            $sales_order_d2s_main = $this->retrieve(
                $request->input('from_date_1'),
             
                $request->input('sales_exec'),
                $request->input('dealer_creation_id_1'),
                $request->input('sales_exec_123'),
                $request->input('order_shop'),
                $request->input('item_name_1'),
                $request->input('group_id'),
                $item_creation_ids

            );

            $sales_exec1 = $request->input('sales_exec');
            $sales_rep_name = SalesRepCreation::find($sales_exec1);
            if($sales_exec1!=''){
            $rep_name = $sales_rep_name->sales_ref_name;
            }else{
                $rep_name = $sales_exec1;
            }

            $dealer1 =  $request->input('dealer_creation_id_1');
            $dealer_id = DealerCreation::find($dealer1);
            if($dealer1!=''){
                $dea_name = $dealer_id->dealer_name;
                }else{
                    $dea_name = $dealer1;
                }

                $manager2 =  $request->input('sales_exec_123');
                $manager_id = MarketManagerCreation::find($manager2);
                if($manager2!=''){
                    $mana_name = $manager_id->manager_name;
                    }else{
                        $mana_name = $manager2;
                    }


                    $item_1 =  $request->input('item_name_1');
                    $item__id_1 = ItemCreation::find($item_1);
                    if($item_1!=''){
                        $item_nam = $item__id_1->item_name;
                        }else{
                            $item_nam = $item_1;
                        }


                    $grp =  $request->input('group_id');
                    $group_id_1 = GroupCreation::find($grp);
                    if($grp!=''){
                        $grp_nam = $group_id_1->group_name;
                        }else{
                            $grp_nam = $grp;
                        }

                           //return $sales_order_d2s_main;

            return view('Reports.effective_calls_Report.list', [
                'item_creation_short' =>$item_creation_short,
                'sales_order_d2s_main' => $sales_order_d2s_main,
                // 'itemCreationShortCount' => $itemCreationShortCount,
                'item_create'=> $item_create,
                'rep_name' =>$rep_name,
                'dea_name' =>$dea_name,
                'mana_name' =>$mana_name,
                'item_nam' =>$item_nam,
                'grp_nam' =>$grp_nam,
                'user_rights_edit_1' => $request->input('user_rights_edit_1'),
                'user_rights_delete_1' => $request->input('user_rights_delete_1')
            ]);
        }
        else if ($action == 'getsalesexec') {

            $manag = $request->input('manager_na');

            $dealer_name = SalesRepCreation::select('id', 'sales_ref_name')
            ->where('manager_id', $manag)
            ->get();

            return response()->json($dealer_name);
        }
         else if ($action == 'getdearlername') {

            $sales_exec = $request->input('sales_exec');

            $dealer_name = DealerCreation::select('id', 'dealer_name')
            ->where('sales_rep_id', $sales_exec)
            ->where(function ($query) {
                $query->where('delete_status', '0')->orWhereNull('delete_status');
            })
            ->get();

            return response()->json($dealer_name);
        }
        else if ($action == 'getitemname') {

            $group_id = $request->input('group_id');

             $item_name = ItemCreation::select('id', 'item_name')
             ->where(function ($query) {
                $query->where('delete_status', '0')->orWhereNull('delete_status');
            })->orderBy('item_name')
            ->where('group_id', '=', $group_id)
            ->get();

            return response()->json($item_name);
        }
    }
}
