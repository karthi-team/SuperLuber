<?php

namespace App\Http\Controllers\Map;

use App\Http\Controllers\Controller;
use App\Models\SalesExecutiveTimelogs;
use App\Models\Entry\SalesOrderD2SMain;
use App\Models\Entry\SalesOrderD2Ssub;
use App\Models\MarketManagerCreation;
use App\Models\SalesRepCreation;
use App\Models\DealerCreation;
use App\Models\MarketCreation;
use App\Models\ShopCreation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class GodsViewController extends Controller
{
    public function gods_view()
    {
        $manager_creation=MarketManagerCreation::select('id','manager_name')->where('status1','0')->where('delete_status', '0')->orWhereNull('delete_status')->orderBy('manager_no')->get();

        return view('Map.gods_view.admin', ['manager_creation' => $manager_creation]);
    }

    public function db_cmd(Request $request)
    {
        $action=$request->input('action');

        if ($action == 'gods_view_dealer') {

            $currentDate = Carbon::today();

            $sales_executive_timelogs = SalesExecutiveTimelogs::select('sales_executive_timelogs.sales_executive_id as id', 'src.sales_ref_name', 'src.mobile_no', 'src.image_name', 'sales_executive_timelogs.latitude', 'sales_executive_timelogs.langititude', 'sales_executive_timelogs.date', 'sales_executive_timelogs.time', 'sales_executive_timelogs.current_status')
                ->join('sales_ref_creation as src', 'src.id', '=', 'sales_executive_timelogs.sales_executive_id')
                ->whereIn('sales_executive_timelogs.id', function($query) {
                    $query->select(DB::raw('MAX(id)'))
                        ->from('sales_executive_timelogs')
                        ->groupBy('sales_executive_id');
                })
                ->where('sales_executive_timelogs.sales_executive_id', '!=', '')
                ->where('sales_executive_timelogs.latitude', '!=', '')
                ->where('sales_executive_timelogs.langititude', '!=', '')
                ->where('sales_executive_timelogs.date', $currentDate)
                ->orderBy('sales_executive_timelogs.id', 'desc')
                ->get();

            return response()->json($sales_executive_timelogs);

        } elseif ($action == 'gods_view_shop_list') {

            $currentDate = Carbon::today();

            $manager_id = $request->input('manager_id');
            $sales_rep_id = $request->input('sales_rep_id');
            $dealer_id = $request->input('dealer_id');
            $market_id = $request->input('market_id');
            $shop_id = $request->input('shop_id');

            $shop_list = [];

            $shop_list_main_1_query = ShopCreation::select(
                'shop_creation.id',
                'shop_creation.shop_name',
                'dealer_creation.dealer_name',
                'shop_creation.mobile_no',
                'shops_type.shops_type',
                'area_creation.area_name',
                DB::raw('COALESCE(shop_creation.address, "-") as address'),
                'shop_creation.latitude',
                'shop_creation.longitude',
                'shop_creation.image_name'
            )
            ->join('shops_type', 'shops_type.id', '=', 'shop_creation.shop_type_id')
            ->join('area_creation', 'area_creation.id', '=', 'shop_creation.beats_id')
            ->join('dealer_creation', 'dealer_creation.id', '=', 'shop_creation.dealer_id')
            ->join('sales_ref_creation', 'sales_ref_creation.id', '=', 'dealer_creation.sales_rep_id')
            ->where('shop_creation.latitude', '!=', '')
            ->where('shop_creation.longitude', '!=', '');
            if ($sales_rep_id != '') {
                $shop_list_main_1_query->where('sales_ref_creation.id', $sales_rep_id);
            }
            if ($dealer_id != '') {
                $shop_list_main_1_query->where('shop_creation.dealer_id', $dealer_id);
            }
            if ($market_id != '') {
                $shop_list_main_1_query->where('shop_creation.beats_id', $market_id);
            }
            if ($shop_id != '') {
                $shop_list_main_1_query->where('shop_creation.id', $shop_id);
            }
            $shop_list_main_1 = $shop_list_main_1_query->get();

            foreach($shop_list_main_1 as $shop_list_1) {

                $shop_id = $shop_list_1->id;

                $secondary_sales = SalesOrderD2SMain::select(
                    'sales_order_d2s_main.sales_exec',
                    'sales_order_d2s_main.dealer_creation_id',
                    'sales_order_d2s_sublist.shop_creation_id',
                    DB::raw('SUM(sales_order_d2s_sublist.order_quantity) as total_order_quantity')
                )
                ->leftJoin('sales_order_d2s_sublist', 'sales_order_d2s_main.id', '=', 'sales_order_d2s_sublist.sales_order_main_id')
                ->where(function ($query) {
                    $query->where('sales_order_d2s_main.delete_status', '0')
                          ->orWhereNull('sales_order_d2s_main.delete_status');
                })
                ->where(function ($query) {
                    $query->where('sales_order_d2s_sublist.delete_status', '0')
                          ->orWhereNull('sales_order_d2s_sublist.delete_status');
                })
                ->where('sales_order_d2s_main.order_date', $currentDate)
                ->where('sales_order_d2s_sublist.shop_creation_id', $shop_id)
                ->where('sales_order_d2s_sublist.shop_creation_id', '!=', '')
                ->groupBy('sales_order_d2s_main.sales_exec', 'sales_order_d2s_main.dealer_creation_id', 'sales_order_d2s_sublist.shop_creation_id')
                ->first();

                $shop_list[] = [
                    'shop_id' => $shop_list_1->id ?? '',
                    'shop_name' => $shop_list_1->shop_name ?? 'Shop Name Not Found',
                    'dealer_name' => $shop_list_1->dealer_name ?? 'Dealer Name Not Found',
                    'mobile_no' => $shop_list_1->mobile_no ?? '',
                    'shops_type' => $shop_list_1->shops_type ?? 'Shop Type Not Found',
                    'area_name' => $shop_list_1->area_name ?? 'Area Name Not Found',
                    'address' => $shop_list_1->address ?? 'Address Not Found',
                    'latitude' => $shop_list_1->latitude ?? '',
                    'longitude' => $shop_list_1->longitude ?? '',
                    'image_name' => $shop_list_1->image_name ?? '',
                    'order_quantity' => $secondary_sales->total_order_quantity ?? 0.00,
                    'shop_color' => $secondary_sales ? 'green' : 'red',
                ];
            }

            return response()->json($shop_list);

        } elseif ($action == 'gods_view_sales_executive') {

            $currentDate = Carbon::today();

            $sales_rep_name = SalesRepCreation::select('sales_ref_creation.id', 'sales_ref_creation.sales_ref_name')
                ->join('sales_executive_timelogs', 'sales_ref_creation.id', '=', 'sales_executive_timelogs.sales_executive_id')
                ->whereIn('sales_executive_timelogs.id', function($query) {
                    $query->select(DB::raw('MAX(id)'))
                        ->from('sales_executive_timelogs')
                        ->groupBy('sales_executive_id');
                })
                ->where('sales_executive_timelogs.date', $currentDate)
                ->orderBy('sales_executive_timelogs.id', 'asc')
                ->get();

            return response()->json($sales_rep_name);

        } else if ($action == 'get_sales_ref') {

            $manager_id= $request->input('manager_id');
            $currentDate = Carbon::today();

            $sales_rep_name = SalesRepCreation::select('sales_ref_creation.id', 'sales_ref_creation.sales_ref_name')
                ->join('sales_executive_timelogs', 'sales_ref_creation.id', '=', 'sales_executive_timelogs.sales_executive_id')
                ->whereIn('sales_executive_timelogs.id', function($query) {
                    $query->select(DB::raw('MAX(id)'))
                        ->from('sales_executive_timelogs')
                        ->groupBy('sales_executive_id');
                })
                ->where(function ($query) {
                    $query->where('sales_ref_creation.delete_status', '0')->orWhereNull('sales_ref_creation.delete_status');
                })
                ->where('sales_ref_creation.manager_id', $manager_id)
                ->where('sales_ref_creation.status', '0')
                ->where('sales_executive_timelogs.date', $currentDate)
                ->orderBy('sales_executive_timelogs.id', 'asc')
                ->get();

            return response()->json($sales_rep_name);

        } else if ($action == 'get_dealer_name') {

            $sales_rep_id = $request->input('sales_rep_id');

            $dealer_name = DealerCreation::select('id', 'dealer_name')
            ->where('sales_rep_id', $sales_rep_id)
            ->where('status', '1')
            ->where(function ($query) {
                $query->where('delete_status', '0')->orWhereNull('delete_status');
            })
            ->get();

            return response()->json($dealer_name);

        } else if ($action == 'get_market_name') {

            $dealer_id = $request->input('dealer_id');

            $dealer_creation = DealerCreation::find($dealer_id);
            $market_id = $dealer_creation->area_id;
            $market_ids = explode(",", $market_id);
            $area_names = [];

            foreach ($market_ids as $marketId) {
                $area_name = MarketCreation::find($marketId);
                if ($area_name) {
                    $area_names[] = $area_name;
                }
            }
            $data = [
                'area_names' => $area_names,
            ];

            return response()->json($data);

        } else if ($action == 'get_shop_name') {

            $market_id = $request->input('market_id');

            $shop_name = ShopCreation::select('id', 'shop_name')
            ->where('beats_id', $market_id)
            ->where(function ($query) {
                $query->where('delete_status', '0')->orWhereNull('delete_status');
            })
            ->groupBy('id', 'shop_name')
            ->get();

            return response()->json($shop_name);
        }
    }
}
