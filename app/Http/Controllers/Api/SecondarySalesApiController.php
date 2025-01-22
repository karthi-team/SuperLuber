<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use App\Models\MarketManagerCreation;
use App\Models\SalesRepCreation;
use App\Models\DealerCreation;
use App\Models\MarketCreation;
use App\Models\GroupCreation;
use App\Models\ItemCreation;
use App\Models\ItemLitersType;
use App\Models\ItemPropertiesType;
use App\Models\ShopCreation;
use App\Models\ShopsType;
use App\Models\Entry\VisitorCreation;
use App\Models\Entry\SalesOrderStockMain;
use App\Models\Entry\SalesOrderStockSub;
use App\Models\Entry\SalesOrderD2SMain;
use App\Models\Entry\SalesOrderD2Ssub;
use Illuminate\Http\Request;
use Carbon\Carbon;
use DateTime;

class SecondarySalesApiController extends Controller
{
    public function seconadary_sales_order_number_list_api(Request $request)
    {
        $dealer_id = $request->input('dealer_id');

        if (empty($dealer_id)) {
            return response()->json(['status' => 'FAILURE', 'message' => 'Dealer Id Not Found'], 404);
        }

        $order_number_list = SalesOrderD2SMain::select('id as secondary_sales_main_id', 'order_no as order_number')
            ->where(function ($query) {
                $query->where('delete_status', '0')->orWhereNull('delete_status');
            })
            ->where('dealer_creation_id', $dealer_id)
            ->orderby('id', 'desc')
            ->get();

        if (!$order_number_list->isEmpty()) {
            return response()->json(['status' => 'SUCCESS', 'message' => 'Order Number List Showed Successfully', 'order_number_list' => $order_number_list], 200);
        } else {
            return response()->json(['status' => 'FAILURE', 'message' => 'Order Number List Not Found'], 404);
        }
    }

    public function seconadary_sales_list_api(Request $request)
    {
        $month = $request->input('month');
        $sales_executive_id = $request->input('sales_executive_id');
        $dealer_id = $request->input('dealer_id');
        $order_number = $request->input('order_number');

        $main_tb = (new SalesOrderD2SMain)->getTable();
        $sub_tb = (new SalesOrderD2Ssub)->getTable();
        $MarketCreation_tb = (new MarketCreation)->getTable();
        $DealerCreation_tb = (new DealerCreation)->getTable();
        $SalesExec = (new SalesRepCreation)->getTable();

        $query = DB::table($main_tb . ' as s')
            ->select(
                's.id as secondary_sales_main_id',
                's.order_no as order_number',
                's.order_date',
                'a.area_name as market_name',
                'd.dealer_name',
                'sr.sales_ref_name as sales_executive_name',
                DB::raw('ROUND(COALESCE(SUM(sub.order_quantity), 0), 1) as order_quantity_total'),
                DB::raw('ROUND(COALESCE(SUM(sub.total_amount), 0), 1) as total_amount_total')
            )
            ->leftJoin($MarketCreation_tb . ' as a', 's.market_creation_id', '=', 'a.id')
            ->leftJoin($DealerCreation_tb . ' as d', 's.dealer_creation_id', '=', 'd.id')
            ->leftJoin($SalesExec . ' as sr', 's.sales_exec', '=', 'sr.id')
            ->leftJoin($sub_tb . ' as sub', 's.id', '=', 'sub.sales_order_main_id')
            ->where(function ($query) {
                $query->where('s.delete_status', '=', 0)
                    ->orWhereNull('s.delete_status');
            })
            ->where(function ($query) {
                $query->where('sub.delete_status', '=', 0)
                    ->orWhereNull('sub.delete_status');
            });

        if ($month) {
            $start_date = $month . '-01';
            $end_date = date('Y-m-t', strtotime($start_date));
            $query->whereBetween('s.order_date', [$start_date, $end_date]);
        }

        if ($sales_executive_id) {
            $query->where('s.sales_exec', '=', $sales_executive_id);
        }

        if ($dealer_id) {
            $query->where('s.dealer_creation_id', '=', $dealer_id);
        }

        if ($order_number) {
            $query->where('s.order_no', '=', $order_number);
        }

        $query->groupBy(
            's.id',
            's.order_no',
            's.order_date',
            'a.area_name',
            'd.dealer_name',
            'sr.sales_ref_name'
        )
            ->orderBy('s.id', 'DESC');

        $result = $query->get();

        if ($result->isNotEmpty()) {
            return response()->json([
                'status' => 'SUCCESS',
                'message' => 'Secondary Sales List Showed Successfully',
                'secondary_sales_list' => $result
            ], 200);
        } else {
            return response()->json([
                'status' => 'FAILURE',
                'message' => 'Secondary Sales List Not Found'
            ], 404);
        }
    }

    public function seconadary_sales_main_order_number_api(Request $request)
    {
        $main_tb = (new SalesOrderD2SMain)->getTable();
        $next_id = DB::select("SHOW TABLE STATUS LIKE '" . $main_tb . "'");
        $order_number = "SALDS_" . date("ym") . "_" . $next_id[0]->Auto_increment;

        if ($order_number != '') {
            return response()->json(['status' => 'SUCCESS', 'message' => 'Order Number Showed Successfully', 'order_number' => $order_number], 200);
        } else {
            return response()->json(['status' => 'FAILURE', 'message' => 'Order Number Not Found'], 404);
        }
    }

    public function seconadary_sales_main_order_number_vistor_api(Request $request)
    {
        $order_date = $request->input('order_date');
        $sales_executive_id = $request->input('sales_executive_id');
        $dealer_id = $request->input('dealer_id');

        $secondary_sales = SalesOrderD2SMain::select('id as secondary_sales_main_id', 'order_date', 'order_no as order_number')
            ->where('order_date', $order_date)
            ->where('sales_exec', $sales_executive_id)
            ->where(function ($query) {
                $query->where('delete_status', '0')->orWhereNull('delete_status');
            });
        $secondary_sales_main = $secondary_sales->first();

        if ($secondary_sales_main) {
            $secondary_sales_main_id = $secondary_sales_main->secondary_sales_main_id;
            $order_number = $secondary_sales_main->order_number;
        } else {
            $main_tb = (new SalesOrderD2SMain)->getTable();
            $next_id = DB::select("SHOW TABLE STATUS LIKE '" . $main_tb . "'");
            $order_number = "SALDS_" . date("ym") . "_" . $next_id[0]->Auto_increment;
            $secondary_sales_main_id = $next_id[0]->Auto_increment;
        }
        if ($order_number != '') {
            return response()->json(['status' => 'SUCCESS', 'message' => 'Order Number Showed Successfully', 'order_number' => $order_number, 'secondary_sales_main_id' => $secondary_sales_main_id], 200);
        } else {
            return response()->json(['status' => 'FAILURE', 'message' => 'Order Number Not Found'], 404);
        }
    }

    public function seconadary_sales_stock_api(Request $request)
    {
        $dealer_id = $request->input('dealer_id');
        $item_creation_id = $request->input('item_id');
        $order_date = $request->input('order_date');
        $item_property = $request->input('item_packing_type_id');
        $item_weights = $request->input('item_uom_id');

        if (empty($dealer_id)) {
            return response()->json(['status' => 'FAILURE', 'message' => 'Dealer Id Not Found'], 404);
        }

        if (empty($item_creation_id)) {
            return response()->json(['status' => 'FAILURE', 'message' => 'Item Id Not Found'], 404);
        }

        if (empty($order_date)) {
            return response()->json(['status' => 'FAILURE', 'message' => 'Order Date Not Found'], 404);
        }

        if (empty($item_property)) {
            return response()->json(['status' => 'FAILURE', 'message' => 'Item Packing Type Id Not Found'], 404);
        }

        if (empty($item_weights)) {
            return response()->json(['status' => 'FAILURE', 'message' => 'Item UOM Id Not Found'], 404);
        }

        $SalesOrderStockMain_tb = (new SalesOrderStockMain)->getTable();
        $SalesOrderStockSub_tb = (new SalesOrderStockSub)->getTable();

        $current_stock = SalesOrderStockMain::select($SalesOrderStockMain_tb . '.id', $SalesOrderStockMain_tb . '.stock_entry_date', $SalesOrderStockSub_tb . '.current_stock')
            ->join($SalesOrderStockSub_tb, $SalesOrderStockSub_tb . '.sales_order_main_id', '=', $SalesOrderStockMain_tb . '.id')
            ->where($SalesOrderStockMain_tb . '.dealer_creation_id', $dealer_id)
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
            $current_stock_values = $current_stock->current_stock;
            $current_stock_value = number_format($current_stock_values, 1);
        } else {
            $current_stock_value = "0";
        }

        return response()->json(['status' => 'SUCCESS', 'message' => 'Current Stock Quantity Showed Successfully', 'current_stock_value' => $current_stock_value], 200);
    }

    public function seconadary_sales_main_insert_api(Request $request)
    {
        $order_number = $request->input('order_number');
        $order_date = $request->input('order_date');
        $market_id = $request->input('market_id');
        $dealer_id = $request->input('dealer_id');
        $dealer_address = $request->input('dealer_address');
        $status = '1';
        $description = $request->input('description');
        $sales_executive_id = $request->input('sales_executive_id');
        $radio_visit = $request->input('visitors_count');

        if (empty($order_number)) {
            return response()->json(['status' => 'FAILURE', 'message' => 'Order Number Not Found'], 404);
        }

        if (empty($order_date)) {
            return response()->json(['status' => 'FAILURE', 'message' => 'Order Date Not Found'], 404);
        }

        if (empty($sales_executive_id)) {
            return response()->json(['status' => 'FAILURE', 'message' => 'Sales Executive Id Not Found'], 404);
        }

        $get_data_exist = SalesOrderD2SMain::select('id as secondary_sales_main_id', 'order_date', 'order_no as order_number', 'sales_exec as sales_executive_id', 'market_creation_id as market_id', 'dealer_creation_id as dealer_id', 'address as dealer_address', 'description', 'radio_visit as visitors_count')->where('order_date', $order_date)->where('sales_exec', $sales_executive_id)->where(function ($query) {
            $query->where('delete_status', '0')->orWhereNull('delete_status');
        })->get();

        if (!$get_data_exist->isEmpty()) {
            return response()->json(['status' => 'SUCCESS', 'message' => 'Order Date Already Exists Not Found', 'secondary_sales_form_main' => $get_data_exist], 200);
        }

        $main_insrt = new SalesOrderD2SMain();
        $main_insrt->entry_date  = Carbon::now();
        $main_insrt->order_no = $order_number;
        $main_insrt->order_date = $order_date;
        $main_insrt->market_creation_id = $market_id;
        $main_insrt->dealer_creation_id = $dealer_id;
        $main_insrt->address = $dealer_address;
        $main_insrt->status = $status;
        $main_insrt->description = $description;
        $main_insrt->sales_exec = $sales_executive_id;
        $main_insrt->radio_visit = $radio_visit;
        $main_insrt->save();

        $id = $main_insrt->id;

        $secondary_sales_form_main = SalesOrderD2SMain::select('id as secondary_sales_main_id', 'order_no as order_number', 'order_date', 'market_creation_id as market_id', 'address as dealer_address', 'dealer_creation_id as dealer_id', "shop_creation_id as shop_id", 'sales_exec as sales_executive_id', 'radio_visit as visitors_count', 'description')->where('id', $id)->first();

        return response()->json(['status' => 'SUCCESS', 'message' => 'Secondary Sales Main List Inserted Successfully', 'secondary_sales_form_main' => $secondary_sales_form_main], 200);
    }

    public function seconadary_sales_sublist_insert_api(Request $request)
    {
        // main
        $main_id = $request->input('secondary_sales_main_id');
        $order_number = $request->input('order_number');
        $order_date = $request->input('order_date');
        $market_id = $request->input('market_id');
        $dealer_id = $request->input('dealer_id');
        $dealer_address = $request->input('dealer_address');
        $status = '1';
        $description = $request->input('description');
        $sales_executive_id = $request->input('sales_executive_id');
        $radio_visit = $request->input('radio_visit');

        // Sublist
        $status_check = $request->input('order_status');
        $order_status_reason = $request->input('order_status_reason');
        $shop_creation_id = $request->input('shop_id');
        $group_creation_id = $request->input('group_id');
        $scheme = $request->input('scheme');
        $item_detail = json_decode($request->input('item_detail'), true);

        if (empty($order_number)) {
            return response()->json(['status' => 'FAILURE', 'message' => 'Order Number Not Found'], 404);
        }
        if (empty($order_date)) {
            return response()->json(['status' => 'FAILURE', 'message' => 'Order Date Not Found'], 404);
        }
        if (empty($sales_executive_id)) {
            return response()->json(['status' => 'FAILURE', 'message' => 'Sales Executive Id Not Found'], 404);
        }

        //  sublist
        if (empty($status_check)) {
            return response()->json(['status' => 'FAILURE', 'message' => 'Order Status Not Found'], 404);
        }

        if ($main_id == '') {

            $radio_visit = $radio_visit ?? 0;

            // Main Insert
            $main_id = SalesOrderD2SMain::insertGetId([
                'entry_date'  => Carbon::now(),
                'order_no' => $order_number,
                'order_date' => $order_date,
                'market_creation_id' => $market_id,
                'shop_creation_id' => $shop_creation_id,
                'dealer_creation_id' => $dealer_id,
                'address' => $dealer_address,
                'status' => $status,
                'description' => $description,
                'sales_exec' => $sales_executive_id,
                'radio_visit' => $radio_visit,
            ]);
        }

        $order_date_sub = $order_date ?? date("Y-m-d");

        $arriving_time_sub = (new DateTime())->format('Y-m-d H:i:s');
        $closing_time_sub = (new DateTime())->format('Y-m-d H:i:s');

        if (is_array($item_detail) || is_object($item_detail)) {

            // Sublist Insert
            foreach ($item_detail as $item_details) {
                $item_creation_id = $item_details['item_id'] ?? 0;
                $short_code_id = $item_details['item_short_code_id'] ?? 0;
                $item_property = $item_details['item_packing_type_id'] ?? 0;
                $item_weights = $item_details['item_uom_id'] ?? 0;
                $order_quantity = $item_details['order_quantity'] ?? 0;
                $pieces_quantity = $item_details['calculated_pieces'] ?? 0;
                $item_price = $item_details['item_price'] ?? 0;
                $total_amount = $item_details['total_amount'] ?? 0;
                $SalesOrderStockMain_tb = (new SalesOrderStockMain)->getTable();
                $SalesOrderStockSub_tb = (new SalesOrderStockSub)->getTable();
                $current_stock_value = SalesOrderStockMain::select($SalesOrderStockMain_tb . '.id', $SalesOrderStockMain_tb . '.stock_entry_date', $SalesOrderStockSub_tb . '.current_stock')
                    ->join($SalesOrderStockSub_tb, $SalesOrderStockSub_tb . '.sales_order_main_id', '=', $SalesOrderStockMain_tb . '.id')
                    ->where($SalesOrderStockMain_tb . '.dealer_creation_id', $dealer_id)
                    ->where(function ($query) use ($SalesOrderStockMain_tb) {
                        $query->where($SalesOrderStockMain_tb . '.delete_status', '0')
                            ->orWhereNull($SalesOrderStockMain_tb . '.delete_status');
                    })
                    ->where($SalesOrderStockMain_tb . '.status', '1')
                    ->where($SalesOrderStockMain_tb . '.stock_entry_date', '<=', $order_date)
                    ->where($SalesOrderStockSub_tb . '.group_creation_id', $group_creation_id)
                    ->where($SalesOrderStockSub_tb . '.item_creation_id', $item_creation_id)
                    ->where($SalesOrderStockSub_tb . '.item_property', $item_property)
                    ->where($SalesOrderStockSub_tb . '.item_weights', $item_weights)
                    ->orderBy($SalesOrderStockMain_tb . '.stock_entry_date', 'desc')
                    ->first();

                $current_stock_values = $current_stock_value->current_stock ?? 0;
                $current_stock = number_format($current_stock_values, 1);

                $tb = new SalesOrderD2Ssub();
                $tb->entry_date  = Carbon::now();
                $tb->sales_order_main_id = $main_id;
                $tb->order_date = $order_date;
                $tb->status_check = $status_check;
                $tb->order_status_reason = $order_status_reason;
                $tb->scheme = $scheme;
                $tb->order_date_sub = $order_date_sub;
                $tb->arriving_time_sub = $arriving_time_sub;
                $tb->closing_time_sub = $closing_time_sub;
                $tb->market_creation_id = $market_id;
                $tb->shop_creation_id = $shop_creation_id;
                $tb->group_creation_id = $group_creation_id ?? 0;
                $tb->item_creation_id = $item_creation_id;
                $tb->short_code_id = $short_code_id;
                $tb->item_property = $item_property;
                $tb->item_weights = $item_weights;
                $tb->current_stock = $current_stock;
                $tb->order_quantity = $order_quantity;
                $tb->pieces_quantity = $pieces_quantity;
                $tb->item_price = $item_price;
                $tb->total_amount = $total_amount;
                $tb->save();

                // $tb_shop = ShopCreation::find($shop_creation_id);
                // if ($tb_shop) {
                //     $tb_shop->secondary_sales_main_id = $main_id;
                //     if ($request->hasFile('secondary_image')) {
                //         $image = $request->file('secondary_image');
                //         $imgName = $image->getClientOriginalName();
                //         $image->move('storage/shop_img/secondary_image', $imgName);
                //         $tb_shop->secondary_image = $imgName;
                //     }
                //     $tb_shop->save();
                // }

                $dealer_id = $dealer_id ?? SalesOrderD2SMain::find($main_id)->dealer_creation_id;

                // Update Currunt Stock
                $total_current_stock = ($current_stock && $order_quantity) ? ($current_stock - $order_quantity) : 0;

                $query = SalesOrderStockMain::join('sales_order_stock_sublist', 'sales_order_stock_sublist.sales_order_main_id', '=', 'sales_order_stock_main.id')
                    ->where('sales_order_stock_main.dealer_creation_id', $dealer_id)
                    ->where(function ($query) {
                        $query->where('sales_order_stock_main.delete_status', '0')
                            ->orWhereNull('sales_order_stock_main.delete_status');
                    })
                    ->where(function ($query) {
                        $query->where('sales_order_stock_sublist.delete_status', '0')
                            ->orWhereNull('sales_order_stock_sublist.delete_status');
                    })
                    ->where('sales_order_stock_main.status', '1')
                    ->where('sales_order_stock_main.stock_entry_date', '<=', $order_date)
                    ->where('sales_order_stock_sublist.item_creation_id', $item_creation_id)
                    ->where('sales_order_stock_sublist.group_creation_id', $group_creation_id)
                    ->where('sales_order_stock_sublist.item_weights', $item_weights)
                    ->orderBy('sales_order_stock_main.stock_entry_date', 'desc')
                    ->select('sales_order_stock_main.id as stock_main_id', 'sales_order_stock_sublist.id as stock_sub_id')
                    ->first();

                $sales_order_stock_main_max_id = $query->stock_main_id ?? '0';
                $sales_order_stock_sub_max_id = $query->stock_sub_id ?? '0';

                SalesOrderStockSub::join('sales_order_stock_main as sm', 'sales_order_stock_sublist.sales_order_main_id', '=', 'sm.id')
                    ->where('sm.dealer_creation_id', $dealer_id)
                    ->where('sales_order_stock_sublist.item_creation_id', $item_creation_id)
                    ->where('sales_order_stock_sublist.group_creation_id', $group_creation_id)
                    ->where('sales_order_stock_sublist.item_weights', $item_weights)
                    ->where('sm.id', $sales_order_stock_main_max_id)
                    ->where('sales_order_stock_sublist.id', $sales_order_stock_sub_max_id)
                    ->update(['sales_order_stock_sublist.current_stock' => $total_current_stock]);
            }
        } else {
            $tb = new SalesOrderD2Ssub();
            $tb->entry_date  = Carbon::now();
            $tb->sales_order_main_id = $main_id;
            $tb->order_date = $order_date;
            $tb->status_check = $status_check;
            $tb->order_status_reason = $order_status_reason;
            $tb->scheme = $scheme;
            $tb->order_date_sub = $order_date_sub;
            $tb->arriving_time_sub = $arriving_time_sub;
            $tb->closing_time_sub = $closing_time_sub;
            $tb->market_creation_id = $market_id;
            $tb->shop_creation_id = $shop_creation_id;
            $tb->group_creation_id = $group_creation_id ?? 0;
            $tb->item_creation_id = 0;
            $tb->short_code_id = 0;
            $tb->item_property = 0;
            $tb->item_weights = 0;
            $tb->current_stock = 0;
            $tb->order_quantity = 0;
            $tb->pieces_quantity = 0;
            $tb->item_price = 0;
            $tb->total_amount = 0;
            $tb->save();
        }
        return response()->json(['status' => 'SUCCESS', 'message' => 'Secondary Sales Sublist Inserted Successfully', 'secondary_sales_main_id' => $main_id], 200);
    }

    public function seconadary_sales_sublist_insert_api_2(Request $request)
    {
        // main
        $main_id = $request->input('secondary_sales_main_id');
        $order_number = $request->input('order_number');
        $order_date = $request->input('order_date');
        $market_id = $request->input('market_id');
        $dealer_id = $request->input('dealer_id');
        $dealer_address = $request->input('dealer_address');
        $status = '1';
        $description = $request->input('description');
        $sales_executive_id = $request->input('sales_executive_id');
        $radio_visit = $request->input('radio_visit');

        // Sublist
        $status_check = $request->input('order_status');
        $order_status_reason = $request->input('order_status_reason');
        $shop_creation_id = $request->input('shop_id');
        $group_creation_id = $request->input('group_id');
        $scheme = $request->input('scheme');
        $item_detail = json_decode($request->input('item_detail'), true);

        if (empty($order_number)) {
            return response()->json(['status' => 'FAILURE', 'message' => 'Order Number Not Found'], 404);
        }
        if (empty($order_date)) {
            return response()->json(['status' => 'FAILURE', 'message' => 'Order Date Not Found'], 404);
        }
        if (empty($sales_executive_id)) {
            return response()->json(['status' => 'FAILURE', 'message' => 'Sales Executive Id Not Found'], 404);
        }

        //  sublist
        if (empty($status_check)) {
            return response()->json(['status' => 'FAILURE', 'message' => 'Order Status Not Found'], 404);
        }

        if (is_array($item_detail) || is_object($item_detail)) {

            if ($main_id == '') {

                $radio_visit = $radio_visit ?? 0;

                // Main Insert
                $main_id = SalesOrderD2SMain::insertGetId([
                    'entry_date'  => Carbon::now(),
                    'order_no' => $order_number,
                    'order_date' => $order_date,
                    'market_creation_id' => $market_id,
                    'shop_creation_id' => $shop_creation_id,
                    'dealer_creation_id' => $dealer_id,
                    'address' => $dealer_address,
                    'status' => $status,
                    'description' => $description,
                    'sales_exec' => $sales_executive_id,
                    'radio_visit' => $radio_visit,
                ]);
            }

            $order_date_sub = $order_date ?? date("Y-m-d");

            $arriving_time_sub = (new DateTime())->format('Y-m-d H:i:s');
            $closing_time_sub = (new DateTime())->format('Y-m-d H:i:s');

            // Sublist Insert
            foreach ($item_detail as $item_details) {
                $item_creation_id = $item_details['item_id'] ?? 0;
                $short_code_id = $item_details['item_short_code_id'] ?? 0;
                $item_property = $item_details['item_packing_type_id'] ?? 0;
                $item_weights = $item_details['item_uom_id'] ?? 0;
                $order_quantity = $item_details['order_quantity'] ?? 0;
                $pieces_quantity = $item_details['calculated_pieces'] ?? 0;
                $item_price = $item_details['item_price'] ?? 0;
                $total_amount = $item_details['total_amount'] ?? 0;
                $SalesOrderStockMain_tb = (new SalesOrderStockMain)->getTable();
                $SalesOrderStockSub_tb = (new SalesOrderStockSub)->getTable();
                $current_stock_value = SalesOrderStockMain::select($SalesOrderStockMain_tb . '.id', $SalesOrderStockMain_tb . '.stock_entry_date', $SalesOrderStockSub_tb . '.current_stock')
                    ->join($SalesOrderStockSub_tb, $SalesOrderStockSub_tb . '.sales_order_main_id', '=', $SalesOrderStockMain_tb . '.id')
                    ->where($SalesOrderStockMain_tb . '.dealer_creation_id', $dealer_id)
                    ->where(function ($query) use ($SalesOrderStockMain_tb) {
                        $query->where($SalesOrderStockMain_tb . '.delete_status', '0')
                            ->orWhereNull($SalesOrderStockMain_tb . '.delete_status');
                    })
                    ->where($SalesOrderStockMain_tb . '.status', '1')
                    ->where($SalesOrderStockMain_tb . '.stock_entry_date', '<=', $order_date)
                    ->where($SalesOrderStockSub_tb . '.item_creation_id', $item_creation_id)
                    ->where($SalesOrderStockSub_tb . '.group_creation_id', $group_creation_id)
                    ->where($SalesOrderStockSub_tb . '.item_property', $item_property)
                    ->where($SalesOrderStockSub_tb . '.item_weights', $item_weights)
                    ->orderBy($SalesOrderStockMain_tb . '.stock_entry_date', 'desc')
                    ->first();

                $current_stock_values = $current_stock_value->current_stock ?? 0;
                $current_stock = number_format($current_stock_values, 1);

                $tb = new SalesOrderD2Ssub();
                $tb->entry_date  = Carbon::now();
                $tb->sales_order_main_id = $main_id;
                $tb->order_date = $order_date;
                $tb->status_check = $status_check;
                $tb->order_status_reason = $order_status_reason;
                $tb->scheme = $scheme;
                $tb->order_date_sub = $order_date_sub;
                $tb->arriving_time_sub = $arriving_time_sub;
                $tb->closing_time_sub = $closing_time_sub;
                $tb->market_creation_id = $market_id;
                $tb->shop_creation_id = $shop_creation_id;
                $tb->group_creation_id = $group_creation_id ?? 0;
                $tb->item_creation_id = $item_creation_id;
                $tb->short_code_id = $short_code_id;
                $tb->item_property = $item_property;
                $tb->item_weights = $item_weights;
                $tb->current_stock = $current_stock;
                $tb->order_quantity = $order_quantity;
                $tb->pieces_quantity = $pieces_quantity;
                $tb->item_price = $item_price;
                $tb->total_amount = $total_amount;
                $tb->save();

                // $tb_shop = ShopCreation::find($shop_creation_id);
                // if ($tb_shop) {
                //     $tb_shop->secondary_sales_main_id = $main_id;
                //     if ($request->hasFile('secondary_image')) {
                //         $image = $request->file('secondary_image');
                //         $imgName = $image->getClientOriginalName();
                //         $image->move('storage/shop_img/secondary_image', $imgName);
                //         $tb_shop->secondary_image = $imgName;
                //     }
                //     $tb_shop->save();
                // }

                $dealer_id = $dealer_id ?? SalesOrderD2SMain::find($main_id)->dealer_creation_id;

                // Update Currunt Stock
                $total_current_stock = ($current_stock && $order_quantity) ? ($current_stock - $order_quantity) : 0;

                $query = SalesOrderStockMain::join('sales_order_stock_sublist', 'sales_order_stock_sublist.sales_order_main_id', '=', 'sales_order_stock_main.id')
                    ->where('sales_order_stock_main.dealer_creation_id', $dealer_id)
                    ->where(function ($query) {
                        $query->where('sales_order_stock_main.delete_status', '0')
                            ->orWhereNull('sales_order_stock_main.delete_status');
                    })
                    ->where(function ($query) {
                        $query->where('sales_order_stock_sublist.delete_status', '0')
                            ->orWhereNull('sales_order_stock_sublist.delete_status');
                    })
                    ->where('sales_order_stock_main.status', '1')
                    ->where('sales_order_stock_main.stock_entry_date', '<=', $order_date)
                    ->where('sales_order_stock_sublist.item_creation_id', $item_creation_id)
                    ->where('sales_order_stock_sublist.group_creation_id', $group_creation_id)
                    ->where('sales_order_stock_sublist.item_weights', $item_weights)
                    ->orderBy('sales_order_stock_main.stock_entry_date', 'desc')
                    ->select('sales_order_stock_main.id as stock_main_id', 'sales_order_stock_sublist.id as stock_sub_id')
                    ->first();

                $sales_order_stock_main_max_id = $query->stock_main_id ?? '0';
                $sales_order_stock_sub_max_id = $query->stock_sub_id ?? '0';

                SalesOrderStockSub::join('sales_order_stock_main as sm', 'sales_order_stock_sublist.sales_order_main_id', '=', 'sm.id')
                    ->where('sm.dealer_creation_id', $dealer_id)
                    ->where('sales_order_stock_sublist.item_creation_id', $item_creation_id)
                    ->where('sales_order_stock_sublist.group_creation_id', $group_creation_id)
                    ->where('sales_order_stock_sublist.item_weights', $item_weights)
                    ->where('sm.id', $sales_order_stock_main_max_id)
                    ->where('sales_order_stock_sublist.id', $sales_order_stock_sub_max_id)
                    ->update(['sales_order_stock_sublist.current_stock' => $total_current_stock]);
            }
        }
        return response()->json(['status' => 'SUCCESS', 'message' => 'Secondary Sales Sublist Inserted Successfully', 'secondary_sales_main_id' => $main_id, 'main_id' => $sales_order_stock_main_max_id, 'sub_id' => $sales_order_stock_sub_max_id], 200);
    }

    // public function secondary_sales_form_main_api(Request $request)
    // {
    //     $id = $request->input('secondary_sales_edit_main_id');

    //     if (empty($id)) {
    //         return response()->json(['status' => 'FAILURE', 'message' => 'Secondary Sales Main Id Not Found'], 404);
    //     }

    //     $secondary_sales_form_main = SalesOrderD2SMain::select('id as secondary_sales_main_id', 'order_no as order_number', 'order_date', 'market_creation_id as market_id','address as dealer_address', 'dealer_creation_id as dealer_id', "shop_creation_id as shop_id", 'sales_exec as sales_executive_id','radio_visit as visitors_count', 'description')->where('id', $id)->get()->first();

    //     return response()->json(['status' => 'SUCCESS', 'message' => 'Secondary Sales Main Edit Show Successfully','secondary_sales_form_main' => $secondary_sales_form_main ], 200);

    // }

    public function secondary_sales_form_main_api(Request $request)
    {
        $order_date = $request->input('order_date');
        $sales_executive_id = $request->input('sales_executive_id');
        $dealer_id = $request->input('dealer_id');

        if (empty($order_date)) {
            return response()->json(['status' => 'FAILURE', 'message' => 'Order Date Not Found'], 404);
        }

        if (empty($sales_executive_id)) {
            return response()->json(['status' => 'FAILURE', 'message' => 'Sales Executive Id Not Found'], 404);
        }

        if (empty($dealer_id)) {
            return response()->json(['status' => 'FAILURE', 'message' => 'Dealer Id Not Found'], 404);
        }

        $secondary_sales_form_main_1 = SalesOrderD2SMain::select('id as secondary_sales_main_id', 'order_no as order_number', 'order_date', 'market_creation_id as market_id', 'address as dealer_address', 'dealer_creation_id as dealer_id', 'shop_creation_id as shop_id', 'sales_exec as sales_executive_id', 'radio_visit as visitors_count', 'description')->where('order_date', $order_date)->where('sales_exec', $sales_executive_id)->where('dealer_creation_id', $dealer_id)->where(function ($query) {
            $query->where('delete_status', '0')->orWhereNull('delete_status');
        })
            ->first();

        if ($secondary_sales_form_main_1 != '') {

            $secondary_sales_form_main = SalesOrderD2SMain::select('id as secondary_sales_main_id', 'order_no as order_number', 'order_date', 'market_creation_id as market_id', 'address as dealer_address', 'dealer_creation_id as dealer_id', 'shop_creation_id as shop_id', 'sales_exec as sales_executive_id', 'radio_visit as visitors_count', 'description')->where('order_date', $order_date)->where('sales_exec', $sales_executive_id)->where('dealer_creation_id', $dealer_id)->where(function ($query) {
                $query->where('delete_status', '0')->orWhereNull('delete_status');
            })->first();

            return response()->json(['status' => 'SUCCESS', 'message' => 'Secondary Sales Main Show Successfully', 'secondary_sales_form_main' => $secondary_sales_form_main], 200);
        } else {

            $secondary_sales_main_id = '';

            $main_tb = (new SalesOrderD2SMain)->getTable();
            $next_id = DB::select("SHOW TABLE STATUS LIKE '" . $main_tb . "'");
            $order_number = "SALDS_" . date("ym") . "_" . $next_id[0]->Auto_increment;

            $dealer_address = DealerCreation::select('id', 'address')
                ->where('id', $dealer_id)
                ->where('status', '1')
                ->where(function ($query) {
                    $query->where('delete_status', '0')->orWhereNull('delete_status');
                })
                ->first();

            if ($dealer_address) {
                $dealer_id = $dealer_address->id;
                $dealer_address = $dealer_address->address;
            }
            $market_id = '';
            $shop_id = '';
            $order_date = $request->input('order_date');
            $sales_executive_id = $request->input('sales_executive_id');
            $dealer_id = $request->input('dealer_id');
            $visitors_count = 0;
            $description = '';

            $secondary_sales_form_main = [
                'secondary_sales_main_id' => $secondary_sales_main_id,
                'order_number' => $order_number,
                'order_date' => $order_date,
                'market_id' => $market_id,
                'dealer_address' => $dealer_address,
                'dealer_id' => $dealer_id,
                'shop_id' => $shop_id,
                'sales_executive_id' => $sales_executive_id,
                'visitors_count' => $visitors_count,
                'description' => $description
            ];

            return response()->json(['status' => 'SUCCESS', 'message' => 'Secondary Sales Main Show Successfully', 'secondary_sales_form_main' => $secondary_sales_form_main], 200);
        }
    }

    public function secondary_sales_form_sublist_backup_api(Request $request)
    {
        $main_id = $request->input('secondary_sales_main_id');

        if (empty($main_id)) {
            return response()->json(['status' => 'FAILURE', 'message' => 'Secondary Sales Main Id Not Found'], 404);
        }

        $sub_tb = (new SalesOrderD2Ssub)->getTable();
        $ItemCreation_tb = (new ItemCreation)->getTable();
        $ItemPropertiesType_tb = (new ItemPropertiesType)->getTable();
        $ItemLitersType_tb = (new ItemLitersType)->getTable();
        $MarketCreation_tb = (new MarketCreation)->getTable();
        $ShopCreation_tb = (new ShopCreation)->getTable();
        $GroupCreation_tb = (new GroupCreation)->getTable();

        $secondary_sales_form_sublist = DB::select('select id as secondary_sales_sublist_id, status_check as order_status, ' . $sub_tb . '.item_creation_id as item_id, COALESCE((select item_name from ' . $ItemCreation_tb . ' where id=' . $sub_tb . '.item_creation_id),"-") as item_name, ' . $sub_tb . '.short_code_id, COALESCE((select short_code from ' . $ItemCreation_tb . ' where id=' . $sub_tb . '.short_code_id),"-") as item_short_code, ' . $sub_tb . '.group_creation_id, COALESCE((select group_name from ' . $GroupCreation_tb . ' where id=' . $sub_tb . '.group_creation_id),"-") as group_name, ' . $sub_tb . '.market_creation_id as market_id, (select area_name FROM ' . $MarketCreation_tb . ' where id=' . $sub_tb . '.market_creation_id) as market_name, ' . $sub_tb . '.shop_creation_id as shop_id, (select secondary_image from ' . $ShopCreation_tb . ' where id=' . $sub_tb . '.shop_creation_id) as secondary_image, (select shop_name from ' . $ShopCreation_tb . ' where id=' . $sub_tb . '.shop_creation_id) as shop_name, ' . $sub_tb . '.item_property as item_packing_type_id, COALESCE((select item_properties_type from ' . $ItemPropertiesType_tb . ' where id=' . $sub_tb . '.item_property),"-") as item_packing_type, ' . $sub_tb . '.item_weights as item_uom_id, COALESCE((select item_liters_type from ' . $ItemLitersType_tb . ' where id = ' . $sub_tb . '.item_weights),"-") as item_uom, current_stock, order_quantity, pieces_quantity as pieces_count, item_price, total_amount from ' . $sub_tb . ' where sales_order_main_id = ' . $main_id . ' and (delete_status = 0 or delete_status is null)');

        foreach ($secondary_sales_form_sublist as $item) {
            $imgUrl = asset('storage/shop_img/secondary_image/' . $item->secondary_image);
            $item->secondary_image_urls = $imgUrl;
        }

        return response()->json(['status' => 'SUCCESS', 'message' => 'Secondary Sales Sublist Edit Show Successfully', 'secondary_sales_form_sublist' => $secondary_sales_form_sublist], 200);
    }

    public function secondary_sales_form_sublist_api(Request $request)
    {
        $main_id = $request->input('secondary_sales_main_id');
        $shop_id = $request->input('shop_id');

        if (empty($main_id)) {
            return response()->json(['status' => 'FAILURE', 'message' => 'Secondary Sales Main Id Not Found'], 404);
        }

        $sub_tb = (new SalesOrderD2Ssub)->getTable();
        $ItemCreation_tb = (new ItemCreation)->getTable();
        $ItemPropertiesType_tb = (new ItemPropertiesType)->getTable();
        $ItemLitersType_tb = (new ItemLitersType)->getTable();
        $MarketCreation_tb = (new MarketCreation)->getTable();
        $ShopCreation_tb = (new ShopCreation)->getTable();
        $GroupCreation_tb = (new GroupCreation)->getTable();

        $sales_order_d2s_main = SalesOrderD2SMain::select('sales_order_d2s_sublist.group_creation_id as group_id', 'group_creation.group_name')
            ->join('sales_order_d2s_sublist', 'sales_order_d2s_sublist.sales_order_main_id', '=', 'sales_order_d2s_main.id')
            ->join('group_creation', 'sales_order_d2s_sublist.group_creation_id', '=', 'group_creation.id')
            ->where(function ($query) {
                $query->where('sales_order_d2s_main.delete_status', '0')
                    ->orWhereNull('sales_order_d2s_main.delete_status');
            })
            ->where(function ($query) {
                $query->where('sales_order_d2s_sublist.delete_status', '0')
                    ->orWhereNull('sales_order_d2s_sublist.delete_status');
            })
            ->where('sales_order_d2s_main.id', $main_id)
            ->where('sales_order_d2s_sublist.shop_creation_id', $shop_id)
            ->groupBy('sales_order_d2s_sublist.group_creation_id', 'group_creation.group_name')
            ->orderBy('group_creation.group_name')
            ->get();

        $secondary_sales_form_sublist_group = [];

        foreach ($sales_order_d2s_main as $sales_order_d2s_main_1) {
            $group_id = $sales_order_d2s_main_1->group_id ?? 0;
            $group_name = $sales_order_d2s_main_1->group_name ?? 0;

            $secondary_sales_form_sublist = DB::select('select id as secondary_sales_sublist_id, status_check as order_status, ' . $sub_tb . '.item_creation_id as item_id, COALESCE((select item_name from ' . $ItemCreation_tb . ' where id=' . $sub_tb . '.item_creation_id),"-") as item_name, ' . $sub_tb . '.short_code_id, COALESCE((select short_code from ' . $ItemCreation_tb . ' where id=' . $sub_tb . '.short_code_id),"-") as item_short_code, ' . $sub_tb . '.group_creation_id, COALESCE((select group_name from ' . $GroupCreation_tb . ' where id=' . $sub_tb . '.group_creation_id),"-") as group_name, ' . $sub_tb . '.market_creation_id as market_id, (select area_name FROM ' . $MarketCreation_tb . ' where id=' . $sub_tb . '.market_creation_id) as market_name, ' . $sub_tb . '.shop_creation_id as shop_id, (select secondary_image from ' . $ShopCreation_tb . ' where id=' . $sub_tb . '.shop_creation_id) as secondary_image, (select shop_name from ' . $ShopCreation_tb . ' where id=' . $sub_tb . '.shop_creation_id) as shop_name, ' . $sub_tb . '.item_property as item_packing_type_id, COALESCE((select item_properties_type from ' . $ItemPropertiesType_tb . ' where id=' . $sub_tb . '.item_property),"-") as item_packing_type, ' . $sub_tb . '.item_weights as item_uom_id, COALESCE((select item_liters_type from ' . $ItemLitersType_tb . ' where id = ' . $sub_tb . '.item_weights),"-") as item_uom, current_stock, order_quantity, pieces_quantity as pieces_count, item_price, total_amount, scheme from ' . $sub_tb . ' where sales_order_main_id = ' . $main_id . ' and group_creation_id = ' . $group_id . ' and shop_creation_id = ' . $shop_id . ' and (delete_status = 0 or delete_status is null)');

            foreach ($secondary_sales_form_sublist as $item) {
                $imgUrl = asset('storage/shop_img/secondary_image/' . $item->secondary_image);
                $item->secondary_image_urls = $imgUrl;
            }

            $secondary_sales_form_sublist_main = [];

            foreach ($secondary_sales_form_sublist as $secondary_sales) {
                $secondary_sales_form_sublist_main[] = [
                    'secondary_sales_sublist_id' => $secondary_sales->secondary_sales_sublist_id,
                    'order_status' => $secondary_sales->order_status,
                    'item_id' => $secondary_sales->item_id,
                    'item_name' => $secondary_sales->item_name,
                    'short_code_id' => $secondary_sales->short_code_id,
                    'item_short_code' => $secondary_sales->item_short_code,
                    'group_creation_id' => $secondary_sales->group_creation_id,
                    'group_name' => $secondary_sales->group_name,
                    'market_id' => $secondary_sales->market_id,
                    'market_name' => $secondary_sales->market_name,
                    'shop_id' => $secondary_sales->shop_id,
                    'secondary_image' => $secondary_sales->secondary_image,
                    'shop_name' => $secondary_sales->shop_name,
                    'item_packing_type_id' => $secondary_sales->item_packing_type_id,
                    'item_packing_type' => $secondary_sales->item_packing_type,
                    'item_uom_id' => $secondary_sales->item_uom_id,
                    'item_uom' => $secondary_sales->item_uom,
                    'current_stock' => $secondary_sales->current_stock,
                    'order_quantity' => $secondary_sales->order_quantity,
                    'pieces_count' => $secondary_sales->pieces_count,
                    'item_price' => $secondary_sales->item_price,
                    'total_amount' => $secondary_sales->total_amount,
                    'scheme' => $secondary_sales->scheme ?? "-",
                    'secondary_image_urls' => $imgUrl,
                ];
            }
            $secondary_sales_form_sublist_group[] = [
                'group_id' => $group_id,
                'group_name' => $group_name,
                'secondary_sales_groupwise_list' => $secondary_sales_form_sublist_main
            ];
        }

        return response()->json(['status' => 'SUCCESS', 'message' => 'Secondary Sales Sublist Showed Successfully', 'group_list' => $sales_order_d2s_main, 'secondary_sales_form_sublist' => $secondary_sales_form_sublist_group], 200);
    }

    public function secondary_sales_form_sublist_pdf_api(Request $request)
    {
        $main_id = $request->input('secondary_sales_main_id');
        $shop_id = $request->input('shop_id');

        if (empty($main_id)) {
            return response()->json(['status' => 'FAILURE', 'message' => 'Secondary Sales Main Id Not Found'], 404);
        }

        $sub_tb = (new SalesOrderD2Ssub)->getTable();
        $ItemCreation_tb = (new ItemCreation)->getTable();
        $ItemPropertiesType_tb = (new ItemPropertiesType)->getTable();
        $ItemLitersType_tb = (new ItemLitersType)->getTable();
        $MarketCreation_tb = (new MarketCreation)->getTable();
        $ShopCreation_tb = (new ShopCreation)->getTable();
        $GroupCreation_tb = (new GroupCreation)->getTable();

        $sales_order_d2s_main_shop_query = SalesOrderD2SMain::select('dealer_creation.id as dealer_id', 'dealer_creation.dealer_name', 'area_creation.id as market_id', 'area_creation.area_name as market_name', 'shop_creation.id as shop_id', 'shop_creation.shop_name')
            ->join('sales_order_d2s_sublist', 'sales_order_d2s_sublist.sales_order_main_id', '=', 'sales_order_d2s_main.id')
            ->join('dealer_creation', 'dealer_creation.id', '=', 'sales_order_d2s_main.dealer_creation_id')
            ->join('area_creation', 'area_creation.id', '=', 'sales_order_d2s_sublist.market_creation_id')
            ->join('shop_creation', 'shop_creation.id', '=', 'sales_order_d2s_sublist.shop_creation_id')
            ->where(function ($query) {
                $query->where('sales_order_d2s_main.delete_status', '0')
                    ->orWhereNull('sales_order_d2s_main.delete_status');
            })
            ->where(function ($query) {
                $query->where('sales_order_d2s_sublist.delete_status', '0')
                    ->orWhereNull('sales_order_d2s_sublist.delete_status');
            })
            ->where('sales_order_d2s_main.id', $main_id)
            ->groupBy('dealer_creation.id', 'dealer_creation.dealer_name', 'area_creation.id', 'area_creation.area_name', 'shop_creation.id', 'shop_creation.shop_name')
            ->orderBy('area_creation.id');
            if ($shop_id) {
                $sales_order_d2s_main_shop_query->where('sales_order_d2s_sublist.shop_creation_id', $shop_id);
            }
            $sales_order_d2s_main_shop = $sales_order_d2s_main_shop_query->get();

        $secondary_sales_form_sublist_shop = [];

        foreach ($sales_order_d2s_main_shop as $sales_order_d2s_main_shop_1) {
            $dealer_id = $sales_order_d2s_main_shop_1->dealer_id ?? 0;
            $dealer_name = $sales_order_d2s_main_shop_1->dealer_name ?? 0;
            $market_id = $sales_order_d2s_main_shop_1->market_id ?? 0;
            $market_name = $sales_order_d2s_main_shop_1->market_name ?? 0;
            $shop_id = $sales_order_d2s_main_shop_1->shop_id ?? 0;
            $shop_name = $sales_order_d2s_main_shop_1->shop_name ?? 0;

            $sales_order_d2s_main = SalesOrderD2SMain::select('sales_order_d2s_sublist.group_creation_id as group_id', 'group_creation.group_name', 'sales_order_d2s_sublist.group_creation_id as group_id')
                ->join('sales_order_d2s_sublist', 'sales_order_d2s_sublist.sales_order_main_id', '=', 'sales_order_d2s_main.id')
                ->join('group_creation', 'sales_order_d2s_sublist.group_creation_id', '=', 'group_creation.id')
                ->where(function ($query) {
                    $query->where('sales_order_d2s_main.delete_status', '0')
                        ->orWhereNull('sales_order_d2s_main.delete_status');
                })
                ->where(function ($query) {
                    $query->where('sales_order_d2s_sublist.delete_status', '0')
                        ->orWhereNull('sales_order_d2s_sublist.delete_status');
                })
                ->where('sales_order_d2s_main.id', $main_id)
                ->where('sales_order_d2s_sublist.shop_creation_id', $shop_id)
                ->groupBy('sales_order_d2s_sublist.group_creation_id', 'group_creation.group_name')
                ->orderBy('group_creation.group_name')
                ->get();

            $secondary_sales_form_sublist_group = [];

            foreach ($sales_order_d2s_main as $sales_order_d2s_main_1) {
                $group_id = $sales_order_d2s_main_1->group_id ?? 0;
                $group_name = $sales_order_d2s_main_1->group_name ?? 0;

                $secondary_sales_form_sublist = DB::select('select id as secondary_sales_sublist_id, status_check as order_status, ' . $sub_tb . '.item_creation_id as item_id, COALESCE((select item_name from ' . $ItemCreation_tb . ' where id=' . $sub_tb . '.item_creation_id),"-") as item_name, ' . $sub_tb . '.short_code_id, COALESCE((select short_code from ' . $ItemCreation_tb . ' where id=' . $sub_tb . '.short_code_id),"-") as item_short_code, ' . $sub_tb . '.group_creation_id, COALESCE((select group_name from ' . $GroupCreation_tb . ' where id=' . $sub_tb . '.group_creation_id),"-") as group_name, ' . $sub_tb . '.market_creation_id as market_id, (select area_name FROM ' . $MarketCreation_tb . ' where id=' . $sub_tb . '.market_creation_id) as market_name, ' . $sub_tb . '.shop_creation_id as shop_id, (select secondary_image from ' . $ShopCreation_tb . ' where id=' . $sub_tb . '.shop_creation_id) as secondary_image, (select shop_name from ' . $ShopCreation_tb . ' where id=' . $sub_tb . '.shop_creation_id) as shop_name, ' . $sub_tb . '.item_property as item_packing_type_id, COALESCE((select item_properties_type from ' . $ItemPropertiesType_tb . ' where id=' . $sub_tb . '.item_property),"-") as item_packing_type, ' . $sub_tb . '.item_weights as item_uom_id, COALESCE((select item_liters_type from ' . $ItemLitersType_tb . ' where id = ' . $sub_tb . '.item_weights),"-") as item_uom, current_stock, order_quantity, pieces_quantity as pieces_count, item_price, total_amount, scheme from ' . $sub_tb . ' where sales_order_main_id = ' . $main_id . ' and group_creation_id = ' . $group_id . ' and market_creation_id = ' . $market_id . ' and shop_creation_id = ' . $shop_id . ' and (delete_status = 0 or delete_status is null)');

                foreach ($secondary_sales_form_sublist as $item) {
                    $imgUrl = asset('storage/shop_img/secondary_image/' . $item->secondary_image);
                    $item->secondary_image_urls = $imgUrl;
                }

                $secondary_sales_form_sublist_main = [];
                $snoCounter = 1;

                foreach ($secondary_sales_form_sublist as $secondary_sales) {
                    $isFirst = ($snoCounter === 1);

                    $secondary_sales_form_sublist_main[] = [
                        's_no' => $snoCounter++,
                        'secondary_sales_sublist_id' => $secondary_sales->secondary_sales_sublist_id,
                        'order_status' => $secondary_sales->order_status,
                        'item_id' => $secondary_sales->item_id,
                        'item_name' => $secondary_sales->item_name,
                        'short_code_id' => $secondary_sales->short_code_id,
                        'item_short_code' => $secondary_sales->item_short_code,
                        'group_creation_id' => $secondary_sales->group_creation_id,
                        'group_name' => $secondary_sales->group_name,
                        'market_id' => $secondary_sales->market_id,
                        'market_name' => $secondary_sales->market_name,
                        'shop_id' => $secondary_sales->shop_id,
                        'secondary_image' => $secondary_sales->secondary_image,
                        'shop_name' => $secondary_sales->shop_name,
                        'item_packing_type_id' => $secondary_sales->item_packing_type_id,
                        'item_packing_type' => $secondary_sales->item_packing_type,
                        'item_uom_id' => $secondary_sales->item_uom_id,
                        'item_uom' => $secondary_sales->item_uom,
                        'current_stock' => $secondary_sales->current_stock,
                        'order_quantity' => $secondary_sales->order_quantity,
                        'pieces_count' => $secondary_sales->pieces_count,
                        'item_price' => $secondary_sales->item_price,
                        'total_amount' => $secondary_sales->total_amount,
                        'secondary_image_urls' => $imgUrl,
                        'scheme' => $isFirst ? $secondary_sales->scheme : '',
                    ];
                }
                $secondary_sales_form_sublist_group[] = [
                    'group_id' => $group_id,
                    'group_name' => $group_name,
                    'secondary_sales_groupwise_list' => $secondary_sales_form_sublist_main
                ];
            }
            $secondary_sales_form_sublist_shop[] = [
                'dealer_id' => $dealer_id,
                'dealer_name' => $dealer_name,
                'market_id' => $market_id,
                'market_name' => $market_name,
                'shop_id' => $shop_id,
                'shop_name' => $shop_name,
                'secondary_sales_shopwise_list' => $secondary_sales_form_sublist_group
            ];
        }

        return response()->json(['status' => 'SUCCESS', 'message' => 'Secondary Sales Sublist Pdf Showed Successfully', 'sales_order_d2s_main_shop' => $sales_order_d2s_main_shop, 'secondary_sales_form_sublist' => $secondary_sales_form_sublist_shop], 200);
    }

    public function secondary_sales_sublist_edit_api(Request $request)
    {
        $sub_id = $request->input('secondary_sales_sublist_id');

        if (empty($sub_id)) {
            return response()->json(['status' => 'FAILURE', 'message' => 'Secondary Sales Main Id Not Found'], 404);
        }

        $Secondary_Sales_get_sublist_Edit = SalesOrderD2Ssub::where('sales_order_d2s_sublist.id', $sub_id)
            ->join('sales_order_d2s_main', 'sales_order_d2s_main.id', '=', 'sales_order_d2s_sublist.sales_order_main_id')
            ->where(function ($query) {
                $query->where('sales_order_d2s_sublist.delete_status', '0')
                    ->orWhereNull('sales_order_d2s_sublist.delete_status');
            })
            ->selectRaw('
            sales_order_d2s_main.dealer_creation_id as dealer_id,
            sales_order_d2s_main.id as secondary_sales_main_id,
            sales_order_d2s_sublist.id as secondary_sales_sublist_id,
            sales_order_d2s_sublist.status_check as order_status,
            CAST(sales_order_d2s_sublist.group_creation_id AS UNSIGNED) as group_id,
            sales_order_d2s_sublist.market_creation_id as market_id,
            CAST(sales_order_d2s_sublist.shop_creation_id AS UNSIGNED) as shop_id,
            sales_order_d2s_sublist.item_creation_id as item_id,
            sales_order_d2s_sublist.short_code_id as item_short_code_id,
            sales_order_d2s_sublist.current_stock,
            sales_order_d2s_sublist.order_quantity,
            sales_order_d2s_sublist.pieces_quantity as pieces_count,
            sales_order_d2s_sublist.item_property as item_packing_type_id,
            sales_order_d2s_sublist.item_weights as item_uom_id,
            sales_order_d2s_sublist.item_price,
            sales_order_d2s_sublist.total_amount
        ')
            ->first();

        return response()->json(['status' => 'SUCCESS', 'message' => 'Secondary Sales Get Sublist Edit Show Successfully', 'Secondary_Sales_get_sublist_Edit' => $Secondary_Sales_get_sublist_Edit], 200);
    }

    public function secondary_sales_sublist_update_api(Request $request)
    {
        $id = $request->input('secondary_sales_sublist_id');

        if (empty($id)) {
            return response()->json(['status' => 'FAILURE', 'message' => 'Secondary Sales Sublist Id Not Found'], 404);
        }

        $secondary_sales = SalesOrderD2Ssub::find($id);

        if ($secondary_sales) {
            if ($id) {
                $sales_order_d2s_sub = SalesOrderD2Ssub::find($id);
                $main_id = $sales_order_d2s_sub->sales_order_main_id;
                $order_quantity_sub = $sales_order_d2s_sub->order_quantity;
                $sales_order_d2s_main = SalesOrderD2SMain::find($main_id);
                $order_date = $sales_order_d2s_main->order_date;
                $dealer_id = $sales_order_d2s_main->dealer_creation_id;
            }

            $status_check = $request->input('order_status');
            $order_date = $request->input('order_date');
            $market_id = $request->input('market_id');
            $shop_creation_id = $request->input('shop_id');
            $short_code_id = $request->input('item_short_code_id');
            $item_creation_id = $request->input('item_id');
            $group_creation_id = $request->input('group_id');
            $current_stock = $request->input('current_stock');
            $order_quantity = $request->input('order_quantity');
            $pieces_quantity = $request->input('pieces_count');
            $item_property = $request->input('item_packing_type_id');
            $item_weights = $request->input('item_uom_id');
            $item_price = $request->input('item_price');
            $total_amount = $request->input('total_amount');

            if ($order_date) {
                $order_date_sub = $order_date;
            } else {
                $order_date_sub = date("Y-m-d");
            }

            $arriving_time_sub = (new DateTime())->format('Y-m-d H:i:s');
            $closing_time_sub = (new DateTime())->format('Y-m-d H:i:s');

            $SalesOrderStockMain_tb = (new SalesOrderStockMain)->getTable();
            $SalesOrderStockSub_tb = (new SalesOrderStockSub)->getTable();
            $SalesOrderStockMain_tb = (new SalesOrderStockMain)->getTable();
            $SalesOrderStockSub_tb = (new SalesOrderStockSub)->getTable();

            $current_stock = SalesOrderStockMain::select($SalesOrderStockMain_tb . '.id', $SalesOrderStockMain_tb . '.stock_entry_date', DB::raw('COALESCE(' . $SalesOrderStockSub_tb . '.current_stock, 0) as current_stock'))
                ->join($SalesOrderStockSub_tb, $SalesOrderStockSub_tb . '.sales_order_main_id', '=', $SalesOrderStockMain_tb . '.id')
                ->where($SalesOrderStockMain_tb . '.dealer_creation_id', $dealer_id)
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
            } else {
                $total_current_stock = 0;
            }

            $sales_order_stock_id = SalesOrderStockMain::join('sales_order_stock_sublist', 'sales_order_stock_sublist.sales_order_main_id', '=', 'sales_order_stock_main.id')
                ->where('sales_order_stock_main.dealer_creation_id', $dealer_id)
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

            if ($sales_order_stock_id) {
                $sales_order_stock_main_max_id = $sales_order_stock_id->stock_main_id;
                $sales_order_stock_sub_max_id = $sales_order_stock_id->stock_sub_id;
            } else {
                $sales_order_stock_main_max_id = '0';
                $sales_order_stock_sub_max_id = '0';
            }

            SalesOrderStockSub::join('sales_order_stock_main as sm', 'sales_order_stock_sublist.sales_order_main_id', '=', 'sm.id')
                ->where('sm.dealer_creation_id', $dealer_id)
                ->where('sales_order_stock_sublist.item_creation_id', $item_creation_id)
                ->where('sales_order_stock_sublist.item_property', $item_property)
                ->where('sales_order_stock_sublist.item_weights', $item_weights)
                ->where('sm.id', $sales_order_stock_main_max_id)
                ->where('sales_order_stock_sublist.id', $sales_order_stock_sub_max_id)
                ->update(['sales_order_stock_sublist.current_stock' => $total_current_stock]);

            $tb = SalesOrderD2Ssub::find($id);
            $tb->status_check = $status_check;
            $tb->order_date = $order_date;
            $tb->order_date_sub = $order_date_sub;
            $tb->arriving_time_sub = $arriving_time_sub;
            $tb->closing_time_sub = $closing_time_sub;
            $tb->market_creation_id = $market_id;
            $tb->shop_creation_id = $shop_creation_id;
            $tb->short_code_id = $short_code_id;
            if ($group_creation_id) {
                $tb->group_creation_id = $group_creation_id;
            } else {
                $tb->group_creation_id = 0;
            }
            if ($item_creation_id) {
                $tb->item_creation_id = $item_creation_id;
            } else {
                $tb->item_creation_id = 0;
            }
            if ($current_stock) {
                $tb->current_stock = $current_stock;
            } else {
                $tb->current_stock = 0;
            }
            if ($order_quantity) {
                $tb->order_quantity = $order_quantity;
            } else {
                $tb->order_quantity = 0;
            }
            if ($pieces_quantity) {
                $tb->pieces_quantity = $pieces_quantity;
            } else {
                $tb->pieces_quantity = 0;
            }
            if ($item_property) {
                $tb->item_property = $item_property;
            } else {
                $tb->item_property = 0;
            }
            if ($item_weights) {
                $tb->item_weights = $item_weights;
            } else {
                $tb->item_weights = 0;
            }
            if ($item_price) {
                $tb->item_price = $item_price;
            } else {
                $tb->item_price = 0;
            }
            if ($total_amount) {
                $tb->total_amount = $total_amount;
            } else {
                $tb->total_amount = 0;
            }

            if ($current_stock && $order_quantity) {
                $total_current_stock = $current_stock - $order_quantity;
            } else {
                $total_current_stock = 0;
            }

            $sales_order_stock_id = SalesOrderStockMain::join('sales_order_stock_sublist', 'sales_order_stock_sublist.sales_order_main_id', '=', 'sales_order_stock_main.id')
                ->where('sales_order_stock_main.dealer_creation_id', $dealer_id)
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

            if ($sales_order_stock_id) {
                $sales_order_stock_main_max_id = $sales_order_stock_id->stock_main_id;
                $sales_order_stock_sub_max_id = $sales_order_stock_id->stock_sub_id;
            } else {
                $sales_order_stock_main_max_id = '0';
                $sales_order_stock_sub_max_id = '0';
            }

            SalesOrderStockSub::join('sales_order_stock_main as sm', 'sales_order_stock_sublist.sales_order_main_id', '=', 'sm.id')
                ->where('sm.dealer_creation_id', $dealer_id)
                ->where('sales_order_stock_sublist.item_creation_id', $item_creation_id)
                ->where('sales_order_stock_sublist.item_property', $item_property)
                ->where('sales_order_stock_sublist.item_weights', $item_weights)
                ->where('sm.id', $sales_order_stock_main_max_id)
                ->where('sales_order_stock_sublist.id', $sales_order_stock_sub_max_id)
                ->update(['sales_order_stock_sublist.current_stock' => $total_current_stock]);

            $tb->save();

            $tb_shop = ShopCreation::find($shop_creation_id);
            if ($tb_shop) {
                $tb_shop->secondary_sales_main_id = $id;
                if ($request->hasFile('secondary_image')) {
                    $image = $request->file('secondary_image');
                    $imgName = $image->getClientOriginalName();
                    $image->move('storage/shop_img/secondary_image', $imgName);
                    $tb_shop->secondary_image = $imgName;
                }
                $tb_shop->save();
            }

            return response()->json(['status' => 'SUCCESS', 'message' => 'Secondary Sales Sublist Updated Successfully', 'secondary_sales_sublist_id' => $id, 'secondary_sales_main_id' => $main_id], 200);
        } else {
            return response()->json(['status' => 'FAILURE', 'message' => 'Secondary Sales Sublist Data Not Found'], 404);
        }
    }

    public function secondary_sales_sublist_scheme_update_api(Request $request)
    {
        $secondary_sales_main_id = $request->input('secondary_sales_main_id');
        $sales_executive_id = $request->input('sales_executive_id');
        $dealer_id = $request->input('dealer_id');
        $scheme = $request->input('scheme');

        if (empty($secondary_sales_main_id)) {
            return response()->json(['status' => 'FAILURE', 'message' => 'Secondary Sales Id Not Found'], 404);
        }
        if (empty($sales_executive_id)) {
            return response()->json(['status' => 'FAILURE', 'message' => 'Sales Executive Id Not Found'], 404);
        }
        if (empty($dealer_id)) {
            return response()->json(['status' => 'FAILURE', 'message' => 'Dealer Id Not Found'], 404);
        }

        $sales_order_d2s_main = SalesOrderD2SMain::select('sales_order_d2s_sublist.id')
            ->join('sales_order_d2s_sublist', 'sales_order_d2s_sublist.sales_order_main_id', '=', 'sales_order_d2s_main.id')
            ->where(function ($query) {
                $query->where('sales_order_d2s_main.delete_status', '0')
                    ->orWhereNull('sales_order_d2s_main.delete_status');
            })
            ->where(function ($query) {
                $query->where('sales_order_d2s_sublist.delete_status', '0')
                    ->orWhereNull('sales_order_d2s_sublist.delete_status');
            })
            ->where('sales_order_d2s_main.id', $secondary_sales_main_id)
            ->where('sales_order_d2s_main.sales_exec', $sales_executive_id)
            ->where('sales_order_d2s_main.dealer_creation_id', $dealer_id)
            ->orderBy('sales_order_d2s_sublist.id', 'desc')
            ->first();

        if ($sales_order_d2s_main) {
            $id = $sales_order_d2s_main->id;
            $tb = SalesOrderD2Ssub::find($id);
            if ($tb) {
                $tb->scheme = $scheme;
                $tb->save();

                return response()->json(['status' => 'SUCCESS', 'message' => 'Secondary Sales Sublist Scheme Updated Successfully', 'secondary_sales_sublist_id' => $id], 200);
            } else {
                return response()->json(['status' => 'FAILURE', 'message' => 'Secondary Sales Sublist Data Not Found'], 404);
            }
        }
    }

    public function seconadary_sales_main_update_api(Request $request)
    {
        $id = $request->input('secondary_sales_main_id');
        $order_number = $request->input('order_number');
        $order_date = $request->input('order_date');
        $market_id = $request->input('market_id');
        $dealer_id = $request->input('dealer_id');
        $dealer_address = $request->input('dealer_address');
        $status = '1';
        $description = $request->input('description');
        $sales_executive_id = $request->input('sales_executive_id');
        $radio_visit = $request->input('visitors_count');

        if (empty($order_number)) {
            return response()->json(['status' => 'FAILURE', 'message' => 'Order Number Not Found'], 404);
        }

        if (empty($order_date)) {
            return response()->json(['status' => 'FAILURE', 'message' => 'Order Date Not Found'], 404);
        }

        if (empty($dealer_id)) {
            return response()->json(['status' => 'FAILURE', 'message' => 'Dealer Id Not Found'], 404);
        }

        if (empty($sales_executive_id)) {
            return response()->json(['status' => 'FAILURE', 'message' => 'Sales Executive Id Not Found'], 404);
        }

        $secondary_sales_main = SalesOrderD2SMain::find($id);

        $secondary_sales_main->entry_date  = Carbon::now();
        $secondary_sales_main->order_date = $order_date;
        $secondary_sales_main->market_creation_id = $market_id;
        $secondary_sales_main->dealer_creation_id = $dealer_id;
        $secondary_sales_main->address = $dealer_address;
        $secondary_sales_main->order_no = $order_number;
        $secondary_sales_main->status = $status;
        $secondary_sales_main->description = $description;
        $secondary_sales_main->sales_exec = $sales_executive_id;
        $secondary_sales_main->radio_visit = $radio_visit;
        $secondary_sales_main->save();

        $affectedRows = SalesOrderD2Ssub::where('sales_order_main_id', $id)
            ->update(['order_date' => $order_date]);

        if ($affectedRows > 0) {
            // Fetch the records and save the related model
            $records = SalesOrderD2Ssub::where('sales_order_main_id', $id)->get();

            foreach ($records as $record) {
                // Assuming there is a relationship between SalesOrderD2Ssub and another model
                // Replace 'relatedModel' with the actual related model name
                $relatedModel = $record->relatedModel;

                if ($relatedModel) {
                    $relatedModel->save();
                }
            }
        }

        return response()->json(['status' => 'SUCCESS', 'message' => 'Secondary Sales Main List Updated Successfully', 'Secondary_sales_main_id' => $id], 200);
    }


    public function seconadary_sales_visitor_insert_api(Request $request)
    {
        // Capture input fields
        $d2s_id = $request->input('Secondary_sales_main_id');
        $order_date = $request->input('order_date');
        $order_number = $request->input('order_number');
        $dealer_id = $request->input('dealer_id');
        $purpose_of_visits = $request->input('purpose_of_visits');
        $visitor_name = $request->input('visitor_name');
        $sales_executive_id = $request->input('sales_executive_id');
        $mobile_no = $request->input('mobile_no');
        $description = $request->input('description');
        $address = $request->input('address');

        // Validate required fields
        if (empty($order_number)) {
            return response()->json(['status' => 'FAILURE', 'message' => 'Order Number Not Found'], 404);
        }

        if (empty($order_date)) {
            return response()->json(['status' => 'FAILURE', 'message' => 'Order Date Not Found'], 404);
        }

        if (empty($sales_executive_id)) {
            return response()->json(['status' => 'FAILURE', 'message' => 'Sales Executive Id Not Found'], 404);
        }

        // Check if visitor already exists
        $get_exist_data = VisitorCreation::select('order_date', 'sales_exec as sales_executive_id', 'visitor_name', 'address as dealer_address')
            ->where('order_date', $order_date)
            ->where('sales_exec', $sales_executive_id)
            ->where('visitor_name', $visitor_name)
            ->where('address', $address)
            ->get();

        if (!$get_exist_data->isEmpty()) {
            return response()->json(['status' => 'SUCCESS', 'message' => 'New Dealer Visits Already Exists', 'secondary_sales_visitor' => $get_exist_data], 200);
        }

        // Create new visitor entry
        $tb = new VisitorCreation();
        $tb->d2s_id = $d2s_id;
        $tb->order_date = $order_date;
        $tb->order_no = $order_number;

        if ($dealer_id !== 'new') {
            $tb->dealer_id = $dealer_id;
        }

        $tb->visitor_dis = $purpose_of_visits;
        $tb->visitor_name = $visitor_name;
        $tb->sales_exec = $sales_executive_id;
        $tb->mobile_no = $mobile_no;
        $tb->description = $description;
        $tb->address = $address;

        if ($request->hasFile('image_name')) {
            $image = $request->file('image_name');
            $imgName = "visitor_img_" . date('YmdHis') . "_" . uniqid() . "." . $image->getClientOriginalExtension();
            $image->move('storage/visitors_img', $imgName);
            $tb->image_name = $imgName;
        }

        // Save the visitor entry
        $tb->save();

        // Update related SalesOrderD2SMain entry if dealer_id is not 'new'
        if ($dealer_id && $dealer_id != 'new') {
            $tb1 = SalesOrderD2SMain::find($d2s_id);
            $tb1->dealer_creation_id = $dealer_id;
            $tb1->save();
        }

        // Return a success response
        return response()->json(['status' => 'SUCCESS', 'message' => 'Secondary Sales New Visitor Inserted Successfully', 'Secondary_sales_main_id' => $d2s_id], 200);
    }

    public function seconadary_sales_visitor_sublist_api(Request $request)
    {
        $order_date = $request->input('order_date');
        $order_number = $request->input('order_number');
        $sales_executive_id = $request->input('sales_executive_id');

        if (empty($order_number)) {
            return response()->json(['status' => 'FAILURE', 'message' => 'Order Number Not Found'], 404);
        }

        if (empty($order_date)) {
            return response()->json(['status' => 'FAILURE', 'message' => 'Order Date Not Found'], 404);
        }

        if (empty($sales_executive_id)) {
            return response()->json(['status' => 'FAILURE', 'message' => 'Sales Executive Id Not Found'], 404);
        }

        $VisitorCreation = DB::table('visitors_shops as vs')
            ->select('vs.id as visitor_id', 'vs.d2s_id as secondary_sales_main_id', 'vs.order_date', 'vs.order_no as order_number', 'vs.visitor_dis as purpose_of_visits', 'vs.visitor_name', 'vs.sales_exec as sales_executive_id', 'vs.mobile_no', 'vs.description', 'vs.address as visitor_address', 'dc.dealer_name', 'vs.image_name')
            ->leftJoin('dealer_creation as dc', 'vs.dealer_id', '=', 'dc.id')
            ->orderBy('vs.id')
            ->where('order_date', '=', $order_date)
            ->where('order_no', '=', $order_number)
            ->where('sales_exec', '=', $sales_executive_id)
            ->get();

        foreach ($VisitorCreation as $item) {
            if ($item->image_name != '') {
                $imgUrl = asset('public/storage/visitors_img/' . $item->image_name);
                $item->visitor_image_urls = $imgUrl;
            }
        }

        return response()->json(['status' => 'SUCCESS', 'message' => 'Secondary Sales New Visitor Inserted Successfully', 'seconadary_sales_visitor_sublist' => $VisitorCreation], 200);
    }

    public function seconadary_sales_visitor_edit_api(Request $request)
    {

        $id = $request->input('secondary_sales_visitor_id');

        if (empty($id)) {
            return response()->json(['status' => 'FAILURE', 'message' => 'Secondary Sales Visitor Id Not Found'], 404);
        }

        $img_url = '';
        $seconadary_sales_visitor_edit = VisitorCreation::select('id as visitor_id', 'd2s_id as secondary_sales_main_id', 'order_date', 'order_no as order_number', 'dealer_id', 'visitor_dis as purpose_of_visits', 'visitor_name', 'sales_exec as sales_executive_id', 'mobile_no', 'description', 'address as visitor_address', 'image_name')->where('id', '=', $id)->get();

        foreach ($seconadary_sales_visitor_edit as $visitor_edit) {

            $images = json_decode($visitor_edit->image_name, true);

            $img_urls = [];
            if (is_array($images)) {

                foreach ($images as $image) {
                    $img_urls[] = asset('/storage/visitors_img/' . trim($image));
                }

                $visitor_edit->img_url = implode(',', $img_urls);
            } else {

                $visitor_edit->img_url = asset('/storage/visitors_img/' . $visitor_edit->image_name);
            }
        }


        return response()->json(['status' => 'SUCCESS', 'message' => 'Secondary Sales Visitor Edit Showed Successfully', 'seconadary_sales_visitor_edit' => $seconadary_sales_visitor_edit], 200);
    }


    public function seconadary_sales_visitor_update_api(Request $request)
    {
        $id = $request->input('visitor_id');
        $d2s_id = $request->input('Secondary_sales_main_id');
        $order_date = $request->input('order_date');
        $order_number = $request->input('order_number');
        $dealer_id = $request->input('dealer_id');
        $purpose_of_visits = $request->input('purpose_of_visits');
        $visitor_name = $request->input('visitor_name');
        $sales_executive_id = $request->input('sales_executive_id');
        $mobile_no = $request->input('mobile_no');
        $description = $request->input('description');
        $address = $request->input('dealer_address');

        if (empty($order_number)) {
            return response()->json(['status' => 'FAILURE', 'message' => 'Order Number Not Found'], 404);
        }

        if (empty($order_date)) {
            return response()->json(['status' => 'FAILURE', 'message' => 'Order Date Not Found'], 404);
        }

        if (empty($sales_executive_id)) {
            return response()->json(['status' => 'FAILURE', 'message' => 'Sales Executive Id Not Found'], 404);
        }

        $tb = VisitorCreation::find($id);
        $tb->d2s_id = $d2s_id;
        $tb->order_date = $order_date;
        $tb->order_no = $order_number;

        // Update dealer_id: set it to NULL if it is 'new'
        if ($dealer_id === 'new') {
            $tb->dealer_id = "0";
        } else {
            $tb->dealer_id = $dealer_id;
        }

        $tb->visitor_dis = $purpose_of_visits;
        $tb->visitor_name = $visitor_name;
        $tb->sales_exec = $sales_executive_id;
        $tb->mobile_no = $mobile_no;
        $tb->description = $description;
        $tb->address = $address;

        if ($request->hasFile('image_name')) {
            $image = $request->file('image_name');
            $imgName = "visitor_img_" . date('YmdHis') . "_" . uniqid() . "." . $image->getClientOriginalExtension();
            $image->move('storage/visitors_img', $imgName);
            $tb->image_name = $imgName;
        }

        $tb->save();

        if ($dealer_id && $dealer_id !== 'new') {
            $tb1 = SalesOrderD2SMain::find($d2s_id);
            $tb1->dealer_creation_id = $dealer_id;
            $tb1->save();
        }

        return response()->json(['status' => 'SUCCESS', 'message' => 'Secondary Sales Update Visitor Successfully', 'Secondary_sales_visitor_update' => $id, 'Secondary_sales_main_id' => $d2s_id], 200);
    }

    public function attendence_report_api(Request $request)
    {
        $sales_executive_id = $request->input('sales_executive_id');

        if (empty($sales_executive_id)) {
            return response()->json(['status' => 'FAILURE', 'message' => 'Sales Executive Id Not Found'], 404);
        }

        $current_date = date('y-m-d');

        $secondary_sales_asc = SalesOrderD2SMain::select('sales_order_d2s_sublist.closing_time_sub')
            ->leftJoin('sales_order_d2s_sublist', 'sales_order_d2s_main.id', '=', 'sales_order_d2s_sublist.sales_order_main_id')
            ->where('sales_order_d2s_sublist.closing_time_sub', '!=', '')
            ->where('sales_order_d2s_main.sales_exec', $sales_executive_id)
            ->where('sales_order_d2s_main.order_date', $current_date)
            ->orderBy('sales_order_d2s_sublist.id', 'asc')
            ->first();

        $secondary_sales_desc = SalesOrderD2SMain::select('sales_order_d2s_sublist.closing_time_sub')
            ->leftJoin('sales_order_d2s_sublist', 'sales_order_d2s_main.id', '=', 'sales_order_d2s_sublist.sales_order_main_id')
            ->where('sales_order_d2s_sublist.closing_time_sub', '!=', '')
            ->where('sales_order_d2s_main.sales_exec', $sales_executive_id)
            ->where('sales_order_d2s_main.order_date', $current_date)
            ->orderBy('sales_order_d2s_sublist.id', 'desc')
            ->first();

        $total_working_hours = '';
        $shop_starting_time = '';
        $shop_ending_time = '';

        if ($secondary_sales_asc && $secondary_sales_desc) {
            $shop_starting_time = $secondary_sales_asc->closing_time_sub;;
            $shop_ending_time = $secondary_sales_desc->closing_time_sub;;

            if (!empty($shop_starting_time)) {
                $shop_starting_time_asc = Carbon::createFromFormat('H:i:s', $shop_starting_time);
            } else {
                $shop_starting_time_asc = Carbon::createFromFormat('H:i:s', '00:00:00');
            }

            if (!empty($shop_ending_time)) {
                $shop_ending_time_desc = Carbon::createFromFormat('H:i:s', $shop_ending_time);
            } else {
                $shop_ending_time_desc = Carbon::createFromFormat('H:i:s', '00:00:00');
            }

            $diffs = $shop_starting_time_asc->diff($shop_ending_time_desc);
            $total_working_hours = $diffs->format('%h hours %i minutes');

            $current_month = Carbon::now()->startOfMonth()->toDateString();
            $last_current_month = Carbon::now()->endOfMonth()->toDateString();

            $month_secondary_sales_count = SalesOrderD2SMain::select('sales_order_d2s_sublist.id')
                ->leftJoin('sales_order_d2s_sublist', 'sales_order_d2s_main.id', '=', 'sales_order_d2s_sublist.sales_order_main_id')
                ->where('sales_order_d2s_main.sales_exec', $sales_executive_id)
                ->where('status_check', '=', 'yes')
                ->where('sales_order_d2s_main.order_date', '>=', $current_month)
                ->where('sales_order_d2s_main.order_date', '<=', $last_current_month)
                ->count();

            $curr_day_yes = SalesOrderD2SMain::selectRaw('count(distinct sales_order_d2s_sublist.shop_creation_id) as distinct_count')
                ->leftJoin('sales_order_d2s_sublist', 'sales_order_d2s_main.id', '=', 'sales_order_d2s_sublist.sales_order_main_id')
                ->where('sales_order_d2s_main.sales_exec', $sales_executive_id)
                ->where('sales_order_d2s_main.order_date', $current_date)
                ->value('distinct_count');

            $curr_day_all_yes = SalesOrderD2SMain::selectRaw('count(distinct sales_order_d2s_sublist.shop_creation_id) as distinct_count')
                ->leftJoin('sales_order_d2s_sublist', 'sales_order_d2s_main.id', '=', 'sales_order_d2s_sublist.sales_order_main_id')
                ->where('sales_order_d2s_main.sales_exec', $sales_executive_id)
                ->where('sales_order_d2s_main.order_date', $current_date)
                ->where('status_check', '=', 'yes')
                ->value('distinct_count');

            $curr_day_all_no = SalesOrderD2SMain::selectRaw('count(distinct sales_order_d2s_sublist.shop_creation_id) as distinct_count')
                ->leftJoin('sales_order_d2s_sublist', 'sales_order_d2s_main.id', '=', 'sales_order_d2s_sublist.sales_order_main_id')
                ->where('sales_order_d2s_main.sales_exec', $sales_executive_id)
                ->where('sales_order_d2s_main.order_date', $current_date)
                ->where('status_check', '=', 'no')
                ->value('distinct_count');

            $curr_day_all = (int) ($curr_day_all_yes - $curr_day_all_no);

            // $curr_day_all = SalesOrderD2SMain::select('sales_order_d2s_sublist.id')
            //     ->leftJoin('sales_order_d2s_sublist', 'sales_order_d2s_main.id', '=', 'sales_order_d2s_sublist.sales_order_main_id')
            //     ->where('sales_order_d2s_main.sales_exec', $sales_executive_id)
            //     ->where('sales_order_d2s_main.order_date', $current_date)
            //     ->where('status_check', '=', 'yes')
            //     ->count();

            return response()->json(['status' => 'SUCCESS', 'message' => 'Attendance Report Showed Successfully', 'shop_starting_time' => $shop_starting_time, 'shop_ending_time' => $shop_ending_time, 'total_working_hours' => $total_working_hours, 'month_secondary_sales_count' => $month_secondary_sales_count, 'current_date_yes' => $curr_day_yes, 'current_date_all' => $curr_day_all], 200);
        } else {
            return response()->json(['status' => 'FAILURE', 'message' => 'Attendance Report Not Found'], 404);
        }
    }
}
