<?php

namespace App\Http\Controllers\Map;

use App\Http\Controllers\Controller;
use App\Models\SalesExecutiveTimelogs;
use App\Models\Entry\SalesOrderD2SMain;
use App\Models\Entry\SalesOrderD2Ssub;
use App\Models\SalesRepCreation;
use App\Models\ShopCreation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class TimelineController extends Controller
{
    public function timeline()
    {
        return view('Map.timeline.admin');
    }

    public function db_cmd(Request $request)
    {
        $action=$request->input('action');

        if ($action == 'filter_timeline') {

            $filter_date = $request->input('filter_date');
            $filter_from_time = $request->input('filter_from_time');
            $filter_to_time = $request->input('filter_to_time');
            $filter_sales_executive_id = $request->input('filter_sales_executive_id');
            $filter_sales_executive_status = $request->input('filter_sales_executive_status');

            $query = SalesExecutiveTimelogs::select(
                'src.id',
                'src.sales_ref_name',
                'src.image_name',
                'sales_executive_timelogs.latitude',
                'sales_executive_timelogs.langititude',
                'sales_executive_timelogs.date',
                'sales_executive_timelogs.time',
                'sales_executive_timelogs.current_status'
            )
            ->join('sales_ref_creation as src', 'src.id', '=', 'sales_executive_timelogs.sales_executive_id');

            if ($filter_date != '') {
                $query->where('sales_executive_timelogs.date', $filter_date);
            }
            if ($filter_from_time != '' && $filter_to_time != '') {
                if($filter_from_time != $filter_to_time){
                    $query->whereBetween('sales_executive_timelogs.time', [$filter_from_time, $filter_to_time]);
                }
            }
            if ($filter_sales_executive_id != '') {
                $query->where('sales_executive_timelogs.sales_executive_id', $filter_sales_executive_id);
            }
            if ($filter_sales_executive_status != '') {
                $query->where('sales_executive_timelogs.current_status', $filter_sales_executive_status);
            }

            $query->orderBy('sales_executive_timelogs.id', 'asc');
            $sales_executive_timelogs = $query->get();

            return response()->json($sales_executive_timelogs);
        }
        else if ($action == 'get_sales_executive') {

            $filter_date = $request->input('filter_date');

            $sales_rep_name = SalesRepCreation::select('sales_ref_creation.id', 'sales_ref_creation.sales_ref_name')
            ->join('sales_executive_timelogs', 'sales_ref_creation.id', '=', 'sales_executive_timelogs.sales_executive_id')
            ->whereIn('sales_executive_timelogs.id', function($query) {
                $query->select(DB::raw('MAX(id)'))
                    ->from('sales_executive_timelogs')
                    ->groupBy('sales_executive_id');
            })
            ->where('sales_executive_timelogs.date', $filter_date)
            ->orderBy('sales_executive_timelogs.id', 'asc')
            ->get();

            return response()->json($sales_rep_name);

        } elseif ($action == 'timeline_shop_list') {

            $currentDate = Carbon::today();
            $filter_date = $request->input('filter_date');
            $filter_from_time = $request->input('filter_from_time');
            $filter_to_time = $request->input('filter_to_time');
            $filter_sales_executive_id = $request->input('filter_sales_executive_id');
            $filter_sales_executive_status = $request->input('filter_sales_executive_status');

            $shop_list = [];

            $shop_list_main_1 = ShopCreation::select(
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
            ->where('shop_creation.latitude', '!=', '')
            ->where('shop_creation.longitude', '!=', '')
            ->get();

            foreach($shop_list_main_1 as $shop_list_1) {

                $shop_id = $shop_list_1->id;

                $secondary_sales_query = SalesOrderD2SMain::select(
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
                ->where('sales_order_d2s_sublist.shop_creation_id', $shop_id)
                ->where('sales_order_d2s_sublist.shop_creation_id', '!=', '')
                ->groupBy('sales_order_d2s_main.sales_exec', 'sales_order_d2s_main.dealer_creation_id', 'sales_order_d2s_sublist.shop_creation_id');
                if ($filter_date != '') {
                    $secondary_sales_query->where('sales_order_d2s_main.order_date', $filter_date);
                }
                if ($filter_from_time != '' && $filter_to_time != '') {
                    if ($filter_from_time != $filter_to_time) {
                        $secondary_sales_query->whereBetween('sales_order_d2s_sublist.arriving_time_sub', [$filter_from_time, $filter_to_time]);
                    }
                }
                if ($filter_sales_executive_id != '') {
                    $secondary_sales_query->where('sales_order_d2s_main.sales_exec', $filter_sales_executive_id);
                }
                $secondary_sales = $secondary_sales_query->first();

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

        }
    }
}
