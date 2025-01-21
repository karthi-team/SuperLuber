<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Models\Entry\SalesOrderC2DMain;
use App\Models\Entry\SalesOrderC2DSub;
use App\Models\Entry\SalesOrderStockMain;
use App\Models\Entry\SalesOrderDeliveryMain;
use App\Models\Entry\SalesOrderStockSub;
use App\Models\MarketCreation;
use App\Models\DealerCreation;
use App\Models\ItemCreation;
use App\Models\GroupCreation;
use App\Models\ItemLitersType;
use App\Models\ItemPropertiesType;
use App\Models\SalesRepCreation;
use Carbon\Carbon;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use Illuminate\Support\Facades\Log;
use DateTime;
use Exception;

class PrimaryOrderSalesApiController extends Controller
{
    public function primary_sales_main_order_number_api(Request $request)
    {
        $main_tb = (new SalesOrderC2DMain)->getTable();
        $next_id = DB::select("SHOW TABLE STATUS LIKE '" . $main_tb . "'");
        $order_no = "SALCD_" . date("ym") . "_" . $next_id[0]->Auto_increment;

        if ($order_no != '') {
            return response()->json(['status' => 'SUCCESS', 'message' => 'Order Number Showed Successfully', 'order_number' => $order_no], 200);
        } else {
            return response()->json(['status' => 'FAILURE', 'message' => 'Order Number Not Found'], 404);
        }
    }

    public function primary_sales_main_insert_api(Request $request)
    {
        $order_no = $request->input('order_number');
        $order_date = $request->input('order_date');
        $dealer_creation_id = $request->input('dealer_id');
        $dealer_address = $request->input('dealer_address');
        $status = '1';
        $description = $request->input('description');
        $sales_exec = $request->input('sales_executive_id');

        if (empty($order_no)) {
            return response()->json(['status' => 'FAILURE', 'message' => 'Order Number Not Found'], 404);
        }
        if (empty($order_date)) {
            return response()->json(['status' => 'FAILURE', 'message' => 'Order Date Not Found'], 404);
        }
        if (empty($sales_exec)) {
            return response()->json(['status' => 'FAILURE', 'message' => 'Sales Executive Id Not Found'], 404);
        }

        $main_insrt = new SalesOrderC2DMain();
        $main_insrt->entry_date  = Carbon::now();
        $main_insrt->order_no = $order_no;
        $main_insrt->order_date = $order_date;
        $main_insrt->dealer_creation_id = $dealer_creation_id;
        $main_insrt->address = $dealer_address;
        $main_insrt->status = $status;
        $main_insrt->description = $description;
        $main_insrt->sales_exec = $sales_exec;
        $main_insrt->save();

        $get_id =  SalesOrderC2DMain::select('id as primary_sales_main_id')
            ->orderBy('id', 'desc')
            ->first();

        return response()->json(['status' => 'SUCCESS', 'message' => 'Primary Sales Main List Inserted Successfully', 'primary_sales' => $get_id], 200);
    }

    public function primary_sales_sublist_insert_api(Request $request)
    {
        // main
        $main_id = $request->input('primary_sales_main_id');
        $order_no = $request->input('order_number');
        $order_date = $request->input('order_date');
        $dealer_creation_id = $request->input('dealer_id');
        $dealer_address = $request->input('dealer_address');
        $status = '1';
        $description = $request->input('description');
        $sales_exec = $request->input('sales_executive_id');

        // sub
        $item_detail = json_decode($request->input('item_detail'), true);
        $group_creation_id = $request->input('group_id');
        $scheme = $request->input('scheme');

        if (empty($order_no)) {
            return response()->json(['status' => 'FAILURE', 'message' => 'Order Number Not Found'], 404);
        }
        if (empty($order_date)) {
            return response()->json(['status' => 'FAILURE', 'message' => 'Order Date Not Found'], 404);
        }
        if (empty($sales_exec)) {
            return response()->json(['status' => 'FAILURE', 'message' => 'Sales Executive Id Not Found'], 404);
        }

        if ($main_id == '') {
            $main_id = SalesOrderC2DMain::insertGetId([
                'entry_date' => Carbon::now(),
                'order_no' => $order_no,
                'order_date' => $order_date,
                'dealer_creation_id' => $dealer_creation_id,
                'address' => $dealer_address,
                'status' => $status,
                'description' => $description,
                'sales_exec' => $sales_exec
            ]);
        }

        if ($order_date) {
            $order_date_sub = $order_date;
        } else {
            $order_date_sub = date("Y-m-d");
        }
        $time_sub = (new DateTime())->format('Y-m-d H:i:s');

        foreach ($item_detail as $item_details) {
            $item_creation_id = $item_details['item_id'] ?? 0;
            $short_code_id = $item_details['item_short_code_id'] ?? 0;
            $item_property = $item_details['item_packing_type_id'] ?? 0;
            $item_weights = $item_details['item_uom_id'] ?? 0;
            $order_quantity = $item_details['order_quantity'] ?? 0;
            $balance_quantity = $item_details['order_quantity'] ?? 0;
            $item_price = $item_details['item_price'] ?? 0;
            $total_amount = $item_details['total_amount'] ?? 0;

            $tb = new SalesOrderC2DSub();
            $tb->entry_date = Carbon::now();
            $tb->sales_order_main_id = $main_id;
            $tb->order_date_sub = $order_date_sub;
            $tb->time_sub = $time_sub;
            $tb->scheme = $scheme;
            $tb->group_creation_id = $group_creation_id;
            $tb->item_creation_id = $item_creation_id;
            $tb->short_code_id = $short_code_id;
            $tb->order_quantity = $order_quantity;
            $tb->balance_quantity = $balance_quantity;
            $tb->item_property = $item_property;
            $tb->item_weights = $item_weights;
            $tb->item_price = $item_price;
            $tb->total_amount = $total_amount;
            $tb->save();
        }

        return response()->json(['status' => 'SUCCESS', 'message' => 'Primary Sales Sublist Inserted Successfully', 'primary_sales_main_id' => $main_id], 200);
    }

    public function primary_sales_form_main_api(Request $request)
    {
        $primary_sales_main_id_edit = $request->input('primary_sales_main_id');
        $order_date = $request->input('order_date');
        $sales_executive_id = $request->input('sales_executive_id');
        $dealer_id = $request->input('dealer_id');

        $primary_sales_form_main_1_query = SalesOrderC2DMain::select('id as primary_sales_main_id', 'order_no as order_number', 'order_date', 'dealer_creation_id as dealer_id', DB::raw('COALESCE(description, "-") as description'), 'sales_exec as sales_executive_id', 'address as dealer_address');
        if ($order_date && $sales_executive_id && $dealer_id != '') {
            $primary_sales_form_main_1_query->where('order_date', $order_date);
            $primary_sales_form_main_1_query->where('sales_exec', $sales_executive_id);
            $primary_sales_form_main_1_query->where('dealer_creation_id', $dealer_id);
        }
        if ($primary_sales_main_id_edit != '') {
            $primary_sales_form_main_1_query->where('id', $primary_sales_main_id_edit);
        }
        $primary_sales_form_main_1 = $primary_sales_form_main_1_query->first();
        if ($primary_sales_form_main_1 != '') {
            $primary_sales_form_main_query = SalesOrderC2DMain::select('id as primary_sales_main_id', 'order_no as order_number', 'order_date', 'dealer_creation_id as dealer_id', DB::raw('COALESCE(description, "-") as description'), 'sales_exec as sales_executive_id', 'address as dealer_address');
            if ($order_date && $sales_executive_id && $dealer_id != '') {
                $primary_sales_form_main_1_query->where('order_date', $order_date);
                $primary_sales_form_main_1_query->where('sales_exec', $sales_executive_id);
                $primary_sales_form_main_1_query->where('dealer_creation_id', $dealer_id);
            }
            if ($primary_sales_main_id_edit != '') {
                $primary_sales_form_main_query->where('id', $primary_sales_main_id_edit);
            }
            $primary_sales_form_main = $primary_sales_form_main_query->first();

            return response()->json(['status' => 'SUCCESS', 'message' => 'Primary Sales Main Show Successfully', 'primary_sales_form_main' => $primary_sales_form_main], 200);
        } else {
            $primary_sales_main_id = '';

            $main_tb = (new SalesOrderC2DMain)->getTable();
            $next_id = DB::select("SHOW TABLE STATUS LIKE '" . $main_tb . "'");
            $order_no = "SALCD_" . date("ym") . "_" . $next_id[0]->Auto_increment;

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
            $order_date = $request->input('order_date');
            $sales_executive_id = $request->input('sales_executive_id');
            $dealer_id = $request->input('dealer_id');
            $description = '';

            $primary_sales_form_main = [
                'primary_sales_main_id' => $primary_sales_main_id,
                'order_number' => $order_no,
                'order_date' => $order_date,
                'dealer_address' => $dealer_address,
                'dealer_id' => $dealer_id,
                'sales_executive_id' => $sales_executive_id,
                'description' => $description
            ];

            return response()->json(['status' => 'SUCCESS', 'message' => 'Primary Sales Main Show Successfully', 'primary_sales_form_main' => $primary_sales_form_main], 200);
        }
    }

    public function primary_sales_form_sublist_api(Request $request)
    {
        $main_id = $request->input('primary_sales_main_id');

        if (empty($main_id)) {
            return response()->json(['status' => 'FAILURE', 'message' => 'Primary Sales Main Id Not Found'], 404);
        }

        $main_tb = (new SalesOrderC2DMain)->getTable();
        $sub_tb = (new SalesOrderC2DSub)->getTable();
        $MarketCreation_tb = (new MarketCreation)->getTable();
        $GroupCreation_tb = (new GroupCreation)->getTable();
        $ItemCreation_tb = (new ItemCreation)->getTable();
        $ItemPropertiesType_tb = (new ItemPropertiesType)->getTable();
        $ItemLitersType_tb = (new ItemLitersType)->getTable();
        $DealerCreation_tb = (new DealerCreation)->getTable();

        $sales_order_c2d_main = SalesOrderC2DMain::select('sales_order_c2d_sublist.group_creation_id as group_id', 'group_creation.group_name')
            ->join('sales_order_c2d_sublist', 'sales_order_c2d_sublist.sales_order_main_id', '=', 'sales_order_c2d_main.id')
            ->join('group_creation', 'sales_order_c2d_sublist.group_creation_id', '=', 'group_creation.id')
            ->where(function ($query) {
                $query->where('sales_order_c2d_main.delete_status', '0')
                    ->orWhereNull('sales_order_c2d_main.delete_status');
            })
            ->where(function ($query) {
                $query->where('sales_order_c2d_sublist.delete_status', '0')
                    ->orWhereNull('sales_order_c2d_sublist.delete_status');
            })
            ->where('sales_order_c2d_main.id', $main_id)
            ->groupBy('sales_order_c2d_sublist.group_creation_id', 'group_creation.group_name')
            ->orderBy('group_creation.group_name')
            ->get();

        $sales_order_c2d_main_description = SalesOrderC2DMain::find($main_id);
        $description = $sales_order_c2d_main_description->description ?? "-";

        $primary_sales_form_sublist_main_1 = [];
        foreach ($sales_order_c2d_main as $sales_order_c2d_main_1) {
            $group_id = $sales_order_c2d_main_1->group_id ?? 0;
            $group_name = $sales_order_c2d_main_1->group_name ?? 0;
            $primary_sales_form_sublist = [];

            $primary_sales_form_sublist_main = DB::select('SELECT id AS primary_sales_sublist_id, COALESCE((SELECT item_name FROM ' . $ItemCreation_tb . ' WHERE id = ' . $sub_tb . '.item_creation_id), "-") AS item_name, COALESCE((SELECT short_code FROM ' . $ItemCreation_tb . ' WHERE id = ' . $sub_tb . '.short_code_id), "-") AS item_short_code, COALESCE((SELECT group_name FROM ' . $GroupCreation_tb . ' WHERE id = ' . $sub_tb . '.group_creation_id), "-") AS group_name, COALESCE((SELECT item_properties_type FROM ' . $ItemPropertiesType_tb . ' WHERE id = ' . $sub_tb . '.item_property), "-") AS item_packing_type, COALESCE((SELECT item_liters_type FROM ' . $ItemLitersType_tb . ' WHERE id = ' . $sub_tb . '.item_weights), "-") AS item_uom, COALESCE((SELECT dealer_creation_id FROM ' . $main_tb . ' WHERE id = ' . $sub_tb . '.sales_order_main_id), "-") AS dealer_creation_id, order_quantity, item_price, total_amount, scheme FROM ' . $sub_tb . ' WHERE sales_order_main_id = ' . $main_id . ' AND group_creation_id = ' . $group_id . ' AND (delete_status = 0 OR delete_status IS NULL)');

            foreach ($primary_sales_form_sublist_main as $key => $sublist) {
                $dealer_name = DB::table($DealerCreation_tb)
                    ->where('id', $sublist->dealer_creation_id)
                    ->value('dealer_name');

                $primary_sales_form_sublist_main[$key]->dealer_name = $dealer_name;
            }

            foreach ($primary_sales_form_sublist_main as $primary_sales) {
                $primary_sales_form_sublist[] = [
                    'primary_sales_sublist_id' => $primary_sales->primary_sales_sublist_id,
                    'item_name' => $primary_sales->item_name,
                    'item_short_code' => $primary_sales->item_short_code,
                    'group_name' => $primary_sales->group_name,
                    'dealer_creation_id' => $primary_sales->dealer_creation_id,
                    'dealer_name' => $primary_sales->dealer_name,
                    'item_packing_type' => $primary_sales->item_packing_type,
                    'item_uom' => $primary_sales->item_uom,
                    'order_quantity' => $primary_sales->order_quantity,
                    'item_price' => $primary_sales->item_price,
                    'total_amount' => $primary_sales->total_amount,
                    'scheme' => $primary_sales->scheme,
                    'description' => $description,
                ];
            }

            $primary_sales_form_sublist_main_1[] = [
                'group_id' => $group_id,
                'group_name' => $group_name,
                'description' => $description,
                'primary_sales_groupwise_list' => $primary_sales_form_sublist,
            ];
        }

        return response()->json(['status' => 'SUCCESS', 'message' => 'Primary Sales Sublist Show Successfully', 'primary_sales_form_sublist' => $primary_sales_form_sublist_main_1], 200);
    }

    public function primary_sales_form_sublist_pdf_api(Request $request)
    {
        $main_id = $request->input('primary_sales_main_id');

        if (empty($main_id)) {
            return response()->json(['status' => 'FAILURE', 'message' => 'Primary Sales Main Id Not Found'], 404);
        }

        $main_tb = (new SalesOrderC2DMain)->getTable();
        $sub_tb = (new SalesOrderC2DSub)->getTable();
        $MarketCreation_tb = (new MarketCreation)->getTable();
        $GroupCreation_tb = (new GroupCreation)->getTable();
        $ItemCreation_tb = (new ItemCreation)->getTable();
        $ItemPropertiesType_tb = (new ItemPropertiesType)->getTable();
        $ItemLitersType_tb = (new ItemLitersType)->getTable();
        $DealerCreation_tb = (new DealerCreation)->getTable();

        $primary_sales_form_sublist_main = [];

        $sales_order_c2d_main_dealer = SalesOrderC2DMain::select('sales_order_c2d_main.dealer_creation_id as dealer_id', 'dealer_creation.dealer_name')
            ->join('sales_order_c2d_sublist', 'sales_order_c2d_sublist.sales_order_main_id', '=', 'sales_order_c2d_main.id')
            ->join('dealer_creation', 'sales_order_c2d_main.dealer_creation_id', '=', 'dealer_creation.id')
            ->where(function ($query) {
                $query->where('sales_order_c2d_main.delete_status', '0')
                    ->orWhereNull('sales_order_c2d_main.delete_status');
            })
            ->where(function ($query) {
                $query->where('sales_order_c2d_sublist.delete_status', '0')
                    ->orWhereNull('sales_order_c2d_sublist.delete_status');
            })
            ->where('sales_order_c2d_main.id', $main_id)
            ->groupBy('sales_order_c2d_main.dealer_creation_id', 'dealer_creation.dealer_name')
            ->orderBy('dealer_creation.dealer_name')
            ->get();

        $sales_order_c2d_main_description = SalesOrderC2DMain::find($main_id);
        $description = $sales_order_c2d_main_description->description ?? "-";

        foreach ($sales_order_c2d_main_dealer as $sales_order_c2d_main_dealer_1) {
            $dealer_id = $sales_order_c2d_main_dealer_1->dealer_id;
            $dealer_name = $sales_order_c2d_main_dealer_1->dealer_name;

            $primary_sales_dealerwise_1 = [];

            $sales_order_c2d_main = SalesOrderC2DMain::select('sales_order_c2d_sublist.group_creation_id as group_id', 'group_creation.group_name')
                ->join('sales_order_c2d_sublist', 'sales_order_c2d_sublist.sales_order_main_id', '=', 'sales_order_c2d_main.id')
                ->join('group_creation', 'sales_order_c2d_sublist.group_creation_id', '=', 'group_creation.id')
                ->where(function ($query) {
                    $query->where('sales_order_c2d_main.delete_status', '0')
                        ->orWhereNull('sales_order_c2d_main.delete_status');
                })
                ->where(function ($query) {
                    $query->where('sales_order_c2d_sublist.delete_status', '0')
                        ->orWhereNull('sales_order_c2d_sublist.delete_status');
                })
                ->where('sales_order_c2d_main.id', $main_id)
                ->where('sales_order_c2d_main.dealer_creation_id', $dealer_id)
                ->groupBy('sales_order_c2d_sublist.group_creation_id', 'group_creation.group_name')
                ->orderBy('group_creation.group_name')
                ->get();

            foreach ($sales_order_c2d_main as $sales_order_c2d_main_1) {
                $group_id = $sales_order_c2d_main_1->group_id ?? 0;
                $group_name = $sales_order_c2d_main_1->group_name ?? 0;

                $primary_sales_form_sublist = [];

                $primary_sales_form_sublist_main = DB::select('SELECT id AS primary_sales_sublist_id, COALESCE((SELECT item_name FROM ' . $ItemCreation_tb . ' WHERE id = ' . $sub_tb . '.item_creation_id), "-") AS item_name, COALESCE((SELECT short_code FROM ' . $ItemCreation_tb . ' WHERE id = ' . $sub_tb . '.short_code_id), "-") AS item_short_code, COALESCE((SELECT group_name FROM ' . $GroupCreation_tb . ' WHERE id = ' . $sub_tb . '.group_creation_id), "-") AS group_name, COALESCE((SELECT item_properties_type FROM ' . $ItemPropertiesType_tb . ' WHERE id = ' . $sub_tb . '.item_property), "-") AS item_packing_type, COALESCE((SELECT item_liters_type FROM ' . $ItemLitersType_tb . ' WHERE id = ' . $sub_tb . '.item_weights), "-") AS item_uom, COALESCE((SELECT dealer_creation_id FROM ' . $main_tb . ' WHERE id = ' . $sub_tb . '.sales_order_main_id), "-") AS dealer_creation_id, order_quantity, item_price, total_amount, scheme FROM ' . $sub_tb . ' WHERE sales_order_main_id = ' . $main_id . ' AND group_creation_id = ' . $group_id . ' AND (delete_status = 0 OR delete_status IS NULL)');

                foreach ($primary_sales_form_sublist_main as $key => $sublist) {
                    $dealer_name = DB::table($DealerCreation_tb)
                        ->where('id', $sublist->dealer_creation_id)
                        ->value('dealer_name');

                    $primary_sales_form_sublist_main[$key]->dealer_name = $dealer_name;
                }

                foreach ($primary_sales_form_sublist_main as $primary_sales) {
                    $primary_sales_form_sublist[] = [
                        'primary_sales_sublist_id' => $primary_sales->primary_sales_sublist_id,
                        'item_name' => $primary_sales->item_name,
                        'item_short_code' => $primary_sales->item_short_code,
                        'group_name' => $primary_sales->group_name,
                        'dealer_creation_id' => $primary_sales->dealer_creation_id,
                        'dealer_name' => $primary_sales->dealer_name,
                        'item_packing_type' => $primary_sales->item_packing_type,
                        'item_uom' => $primary_sales->item_uom,
                        'order_quantity' => $primary_sales->order_quantity,
                        'item_price' => $primary_sales->item_price,
                        'total_amount' => $primary_sales->total_amount,
                        'scheme' => $primary_sales->scheme,
                        'description' => $description,
                    ];
                }

                $primary_sales_dealerwise_1[] = [
                    'group_id' => $group_id,
                    'group_name' => $group_name,
                    'primary_sales_groupwise_list' => $primary_sales_form_sublist
                ];
            }

            $primary_sales_form_sublist_main_1[] = [
                'dealer_id' => $dealer_id,
                'dealer_name' => $dealer_name,
                'description' => $description,
                'primary_sales_dealerwise_list' => $primary_sales_dealerwise_1
            ];
        }

        return response()->json(['status' => 'SUCCESS', 'message' => 'Primary Sales Sublist Pdf Show Successfully', 'primary_sales_form_sublist_pdf' => $primary_sales_form_sublist_main_1], 200);
    }

    public function primary_sales_sublist_edit_api(Request $request)
    {
        $sub_id = $request->input('primary_sales_sublist_id');

        if (empty($sub_id)) {
            return response()->json(['status' => 'FAILURE', 'message' => 'Primary Sales Main Id Not Found'], 404);
        }

        $primary_sales_sublist_edit = SalesOrderC2DSub::where('id', $sub_id)->where(function ($query) {
            $query->where('delete_status', '0')->orWhereNull('delete_status');
        })->get(['id as primary_sales_sublist_id', 'sales_order_main_id as primary_sales_main_id', 'market_creation_id as market_id', 'group_creation_id as group_id', 'item_creation_id as item_id', 'short_code_id as item_short_code_id', 'order_quantity', 'item_property as item_packing_type_id', 'item_weights as item_uom_id', 'item_price', 'total_amount'])->first();

        return response()->json(['status' => 'SUCCESS', 'message' => 'Primary Sales Main Edit Show Successfully', 'primary_sales_sublist_edit' => $primary_sales_sublist_edit], 200);
    }

    public function primary_sales_sublist_update_api(Request $request)
    {
        $id = $request->input('primary_sales_sublist_id');
        $group_creation_id = $request->input('group_id');
        $item_creation_id = $request->input('item_id');
        $short_code_id = $request->input('item_short_code_id');
        $order_quantity = $request->input('order_quantity');
        $balance_quantity = $request->input('order_quantity');
        $item_property = $request->input('item_packing_type_id');
        $item_weights = $request->input('item_uom_id');
        $item_price = $request->input('item_price');
        $total_amount = $request->input('total_amount');

        $sales_order_d2s_sub = SalesOrderC2DSub::find($id);
        if ($sales_order_d2s_sub) {
            $main_id = $sales_order_d2s_sub->sales_order_main_id;
            $sales_order_d2s_main = SalesOrderC2DMain::find($main_id);
            if ($sales_order_d2s_main) {
                $order_date = $sales_order_d2s_main->order_date;
            }
        }

        if (empty($id)) {
            return response()->json(['status' => 'FAILURE', 'message' => 'Primary Sales Id Not Found'], 404);
        }

        if ($order_date) {
            $order_date_sub = $order_date;
        } else {
            $order_date_sub = date("Y-m-d");
        }
        $time_sub = (new DateTime())->format('Y-m-d H:i:s');

        $tb = SalesOrderC2DSub::find($id);
        $tb->order_date_sub = $order_date_sub;
        $tb->time_sub = $time_sub;
        $tb->group_creation_id = $group_creation_id;
        $tb->item_creation_id = $item_creation_id;
        $tb->short_code_id = $short_code_id;
        $tb->order_quantity = $order_quantity;
        $tb->balance_quantity = $order_quantity;
        $tb->item_property = $item_property;
        $tb->item_weights = $item_weights;
        $tb->item_price = $item_price;
        $tb->total_amount = $total_amount;
        $tb->save();

        return response()->json(['status' => 'SUCCESS', 'message' => 'Primary Sales Sublist Updated Successfully', 'primary_sales_main_id' => $main_id], 200);
    }

    public function primary_sales_main_update_api(Request $request)
    {
        $id = $request->input('primary_sales_main_id');
        $order_no = $request->input('order_number');
        $order_date = $request->input('order_date');
        $dealer_creation_id = $request->input('dealer_id');
        $dealer_address = $request->input('dealer_address');
        $status = '1';
        $description = $request->input('description');
        $sales_exec = $request->input('sales_executive_id');

        if (empty($order_no)) {
            return response()->json(['status' => 'FAILURE', 'message' => 'Order Number Not Found'], 404);
        }
        if (empty($order_date)) {
            return response()->json(['status' => 'FAILURE', 'message' => 'Order Date Not Found'], 404);
        }
        if (empty($sales_exec)) {
            return response()->json(['status' => 'FAILURE', 'message' => 'Sales Executive Id Not Found'], 404);
        }

        $tb = SalesOrderC2DMain::find($id);
        $tb->order_no = $order_no;
        $tb->order_date = $order_date;
        $tb->dealer_creation_id = $dealer_creation_id;
        $tb->address = $dealer_address;
        $tb->status = $status;
        $tb->description = $description;
        $tb->sales_exec = $sales_exec;

        try {
            $tb->save();

            return response()->json([
                'status' => 'SUCCESS',
                'message' => 'Primary Sales Main List Updated Successfully',
                'primary_sales_main_id' => $id
            ], 200);
        } catch (Exception $e) {
            // Log the exception for debugging purposes
            Log::error('Error updating primary sales main list: ' . $e->getMessage());

            // Return a generic failure response
            return response()->json([
                'status' => 'FAILURE',
                'message' => 'Failed to update primary sales main list. Please try again later.'
            ], 500); // Use appropriate HTTP status code
        }
    }


    public function primary_sales_order_number_list_api(Request $request)
    {
        $dealer_id = $request->input('dealer_id');

        if (empty($dealer_id)) {
            return response()->json(['status' => 'FAILURE', 'message' => 'Dealer Id Not Found'], 404);
        }

        $order_number_list = SalesOrderC2DMain::select('id as primary_sales_main_id', 'order_no as order_number')
            ->where(function ($query) {
                $query->where('delete_status', '0')->orWhereNull('delete_status');
            })
            ->where('dealer_creation_id', $dealer_id)
            ->get();

        if (!$order_number_list->isEmpty()) {
            return response()->json(['status' => 'SUCCESS', 'message' => 'Order Number List Showed Successfully', 'order_number_list' => $order_number_list], 200);
        } else {
            return response()->json(['status' => 'FAILURE', 'message' => 'Order Number List Not Found'], 404);
        }
    }

    public function primary_sales_sublist_delete_api(Request $request)
    {
        $primary_sales_sublist_id = $request->input('primary_sales_sublist_id');

        if (empty($primary_sales_sublist_id)) {
            return response()->json(['status' => 'FAILURE', 'message' => 'Sublist Id Not Found'], 404);
        }

        $tb = SalesOrderC2DSub::find($primary_sales_sublist_id);
        $tb->delete_status = "1";

        if ($tb->save()) {
            return response()->json(['status' => 'SUCCESS', 'message' => 'Sublist Deleted Successfully'], 200);
        } else {
            return response()->json(['status' => 'FAILURE', 'message' => 'Sublist Not Found'], 404);
        }
    }

    public function primary_sales_main_delete_api(Request $request)
    {
        $primary_sales_main_id = $request->input('primary_sales_main_id');

        if (empty($primary_sales_main_id)) {
            return response()->json(['status' => 'FAILURE', 'message' => 'Mainlist Id Not Found'], 404);
        }

        $main_tb = SalesOrderC2DMain::find($primary_sales_main_id);
        if (!$main_tb) {
            return response()->json(['status' => 'FAILURE', 'message' => 'Mainlist Not Found'], 404);
        }
        $main_tb->delete_status = "1";

        if ($main_tb->save()) {
            $sub_tb = SalesOrderC2DSub::where('sales_order_main_id', $primary_sales_main_id)
                ->get();
            foreach ($sub_tb as $subRecord) {
                $subRecord->delete_status = "1";
                $subRecord->save();
            }
            return response()->json(['status' => 'SUCCESS', 'message' => 'Mainlist Deleted Successfully'], 200);
        } else {
            return response()->json(['status' => 'FAILURE', 'message' => 'Mainlist Not Found'], 404);
        }
    }

    public function primary_sales_list_api(Request $request)
    {
        // Retrieve input parameters
        $month = $request->input('month');
        $order_number = $request->input('order_number');
        $dealer_id = $request->input('dealer_id');
        $sales_executive_id = $request->input('sales_executive_id');

        // Table names
        $main_tb = (new SalesOrderC2DMain)->getTable();
        $sub_tb = (new SalesOrderC2DSub)->getTable();
        $DealerCreation_tb = (new DealerCreation)->getTable();
        $salesCreation_tb = (new SalesRepCreation)->getTable();

        // Initialize query builder
        $query = DB::table($main_tb . ' as main')
            ->select(
                'main.id as primary_sales_main_id',
                'main.order_no',
                'main.order_date',
                DB::raw('(SELECT dealer_name FROM ' . $DealerCreation_tb . ' WHERE id = main.dealer_creation_id) AS dealer_name'),
                DB::raw('(SELECT sales_ref_name FROM ' . $salesCreation_tb . ' WHERE id = main.sales_exec) AS sales_name'),
                'main.status',
                DB::raw('IFNULL(sub.total_quantity, 0) AS total_quantity'),
                DB::raw('IFNULL(sub.total_weights, 0) AS total_weights'),
                DB::raw('IFNULL(sub.total_amount, 0) AS total_amount')
            )
            ->leftJoin(DB::raw('(SELECT sales_order_main_id, SUM(order_quantity) AS total_quantity, SUM(item_weights) AS total_weights, SUM(total_amount) AS total_amount FROM ' . $sub_tb . ' WHERE delete_status = 0 OR delete_status IS NULL GROUP BY sales_order_main_id) AS sub'), 'main.id', '=', 'sub.sales_order_main_id')
            ->where(function ($query) {
                $query->where('main.delete_status', '=', 0)
                    ->orWhereNull('main.delete_status');
            });

        // Apply month filter if provided
        if ($month) {
            $start_date = $month . '-01';
            $end_date = date('Y-m-t', strtotime($start_date));
            $query->whereBetween('main.order_date', [$start_date, $end_date]);
        }

        // Apply other filters if provided
        if ($order_number) {
            $query->where('main.order_no', '=', $order_number);
        }

        if ($dealer_id) {
            $query->where('main.dealer_creation_id', '=', $dealer_id);
        }

        if ($sales_executive_id) {
            $query->where('main.sales_exec', '=', $sales_executive_id);
        }

        // Order by ID in descending order
        $query->orderBy('main.id', 'DESC');

        // Execute the query and get results
        $result = $query->get();

        // Fetch all relevant receipt numbers in one go
        $existingReceipts = SalesOrderDeliveryMain::whereIn('order_recipt_no', $result->pluck('primary_sales_main_id'))->pluck('order_recipt_no')->toArray();

        // Add 'pass_upd' field to each record in the result
        foreach ($result as $record) {
            $order_recipt_no = $record->primary_sales_main_id;
            $record->pass_upd = in_array($order_recipt_no, $existingReceipts) ? '0' : '1';
        }

        // Return the response
        if ($result->isNotEmpty()) {
            return response()->json([
                'status' => 'SUCCESS',
                'message' => 'Primary Sales List showed Successfully',
                'primary_sales_list' => $result,
            ], 200);
        } else {
            return response()->json([
                'status' => 'FAILURE',
                'message' => 'Primary Sales List Not Found'
            ], 404);
        }
    }
}
