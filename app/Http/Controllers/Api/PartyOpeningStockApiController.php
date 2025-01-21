<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use App\Models\MarketManagerCreation;
use App\Models\SalesRepCreation;
use App\Models\DealerCreation;
use App\Models\GroupCreation;
use App\Models\ItemCreation;
use App\Models\ItemLitersType;
use App\Models\ItemPropertiesType;
use App\Models\Entry\SalesOrderStockMain;
use App\Models\Entry\SalesOrderStockSub;
use Illuminate\Http\Request;
use Carbon\Carbon;

use function PHPUnit\Framework\isEmpty;

class PartyOpeningStockApiController extends Controller
{
    public function party_opening_stock_number_list_api(Request $request)
    {
        $dealer_id = $request->input('dealer_id');

        if (empty($dealer_id)) {
            return response()->json(['status' => 'FAILURE', 'message' => 'Dealer Id Not Found'], 404);
        }

        $stock_number_list = SalesOrderStockMain::select('id as party_opening_stock_main_id', 'order_no as stock_number')
            ->where(function ($query) {
                $query->where('delete_status', '0')->orWhereNull('delete_status');
            })
            ->where('dealer_creation_id', $dealer_id)
            ->get();

        if($stock_number_list){
            return response()->json(['status' => 'SUCCESS', 'message' => 'Stock Number List Showed Successfully', 'stock_number_list' => $stock_number_list], 200);
        }else{
            return response()->json(['status' => 'FAILURE', 'message' => 'Stock Number List Not Found'], 404);
        }
    }

    public function party_opening_stock_list_api(Request $request)
    {
        $month = $request->input('month');
        $sales_executive_id = $request->input('sales_executive_id');
        $dealer_id = $request->input('dealer_id');
        $stock_number = $request->input('stock_number');

        if (empty($month)) {
            return response()->json(['status' => 'FAILURE', 'message' => 'Month Not Found'], 404);
        }
        if (empty($sales_executive_id)) {
            return response()->json(['status' => 'FAILURE', 'message' => 'Sales Executive Id Not Found'], 404);
        }

        $cond = " WHERE (delete_status = 0 OR delete_status IS NULL)";

        $params = [];

        if ($month != "") {
            $start_date = $month . '-01';
            $end_date = date('Y-m-t', strtotime($start_date));
            $cond .= " AND stock_entry_date BETWEEN ? AND ?";
            $params[] = $start_date;
            $params[] = $end_date;
        }

        if ($stock_number != "") {
            $cond .= " AND order_no = ?";
            $params[] = $stock_number;
        }

        if ($dealer_id != "") {
            $cond .= " AND dealer_creation_id = ?";
            $params[] = $dealer_id;
        }

        if ($sales_executive_id != "") {
            $cond .= " AND sales_exec = ?";
            $params[] = $sales_executive_id;
        }

        $cond .= ' ORDER BY id DESC';

        $query = 'SELECT id as party_opening_stock_main_id, order_no, stock_entry_date, (SELECT dealer_name FROM dealer_creation WHERE id = sales_order_stock_main.dealer_creation_id) AS dealer_name, SUBSTRING_INDEX((SELECT IF(count(*) > 0, CONCAT(sum(opening_stock), ";", sum(current_stock)), "0;0") FROM sales_order_stock_sublist WHERE sales_order_main_id = sales_order_stock_main.id AND (delete_status = 0 OR delete_status IS NULL)), ";", 1) AS opening_stock_total, SUBSTRING_INDEX((SELECT IF(count(*) > 0, CONCAT(sum(opening_stock), ";", sum(current_stock)), "0;0") FROM sales_order_stock_sublist WHERE sales_order_main_id = sales_order_stock_main.id AND (delete_status = 0 OR delete_status IS NULL)), ";", -1) AS current_stock_total FROM sales_order_stock_main' . $cond;

        $party_opening_stock_list = DB::select($query, $params);

        if($party_opening_stock_list){
            return response()->json(['status' => 'SUCCESS', 'message' => 'Party Opening Stock List Showed Successfully', 'party_opening_stock_list' => $party_opening_stock_list], 200);
        }else{
            return response()->json(['status' => 'FAILURE', 'message' => 'Party Opening Stock List Not Found'], 404);
        }
    }

    public function party_opening_stock_main_stock_number_api(Request $request)
    {
        $main_tb = (new SalesOrderStockMain)->getTable();
        $next_id = DB::select("SHOW TABLE STATUS LIKE '" . $main_tb . "'");
        $stock_number = "STOCK_" . date("ym") . "_" . $next_id[0]->Auto_increment;

        if($stock_number){
            return response()->json(['status' => 'SUCCESS', 'message' => 'Stock Number Showed Successfully', 'stock_number' => $stock_number], 200);
        }else{
            return response()->json(['status' => 'FAILURE', 'message' => 'Stock Number Not Found'], 404);
        }
    }

    public function party_opening_item_list_api (Request $request)
    {
        $group_id = $request->input('group_id');
        $party_opening_entry_date = $request->input('party_opening_entry_date');
        $dealer_id = $request->input('dealer_id');

        if (empty($group_id)) {
            return response()->json(['status' => 'FAILURE', 'message' => 'Group Id Not Found'], 404);
        }

        $item_list = ItemCreation::select('item_creation.id as item_id', 'item_creation.item_name', 'item_creation.piece as pieces_count', 'item_creation.distributor_rate as item_price', 'item_creation.id as short_code_id', 'item_creation.short_code', 'item_liters_type.id as item_uom_id','item_liters_type.item_liters_type as item_uom', 'item_properties_type.id as item_packing_type_id','item_properties_type.item_properties_type as item_packing_type')
            ->join('item_liters_type', 'item_liters_type.id', '=', 'item_creation.item_liters_type')
            ->join('item_properties_type', 'item_properties_type.id', '=', 'item_creation.item_properties_type')
            ->where(function ($query) {
                $query->where('item_creation.delete_status', '0')->orWhereNull('item_creation.delete_status');
            })
            ->orderBy('item_creation.item_name')
            ->where('item_creation.group_id', '=', $group_id)
            ->get();

            $party_opening_item_list = [];

            foreach ($item_list as $item_list_1) {
                $item_id = $item_list_1->item_id ?? 0;
                $item_name = $item_list_1->item_name ?? '';
                $pieces_count = $item_list_1->pieces_count ?? 0;
                $item_price = $item_list_1->item_price ?? 0;
                $short_code_id = $item_list_1->short_code_id ?? 0;
                $short_code = $item_list_1->short_code ?? '';
                $item_uom_id = $item_list_1->item_uom_id ?? 0;
                $item_uom = $item_list_1->item_uom ?? '';
                $item_packing_type_id = $item_list_1->item_packing_type_id ?? 0;
                $item_packing_type = $item_list_1->item_packing_type ?? '';


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
                    ->where($SalesOrderStockMain_tb . '.stock_entry_date', '<=', $party_opening_entry_date)
                    ->where($SalesOrderStockSub_tb . '.item_creation_id', $item_id)
                    ->where($SalesOrderStockSub_tb . '.item_property', $item_packing_type_id)
                    ->where($SalesOrderStockSub_tb . '.item_weights', $item_uom_id)
                    ->orderBy($SalesOrderStockMain_tb . '.stock_entry_date', 'desc')
                    ->first();

                $total_opening_stk = $current_stock ? $current_stock->current_stock : 0;
                $total_opening_stock = number_format($total_opening_stk, 1);

                $party_opening_item_list[] = [
                    'item_id' => $item_id,
                    'item_name' => $item_name,
                    'pieces_count' => $pieces_count,
                    'item_price' => $item_price,
                    'short_code_id' => $short_code_id,
                    'short_code' => $short_code,
                    'item_uom_id' => $item_uom_id,
                    'item_uom' => $item_uom,
                    'item_packing_type_id' => $item_packing_type_id,
                    'item_packing_type' => $item_packing_type,
                    'current_stock' => $total_opening_stock,
                ];
            }

        if (!empty($party_opening_item_list)) {
            return response()->json(['status' => 'SUCCESS', 'message' => 'Party Opening Item List Showed Successfully', 'party_opening_item_list' => $party_opening_item_list], 200);
        } else {
            return response()->json(['status' => 'FAILURE', 'message' => 'Item List Not Found'], 404);
        }
    }

    public function party_opening_stock_sublist_stock_count_api(Request $request)
    {
        $party_opening_entry_date = $request->input('party_opening_entry_date');
        $dealer_id = $request->input('dealer_id');
        $item_id = $request->input('item_id');
        $item_packing_type_id = $request->input('item_packing_type_id');
        $item_uom_id = $request->input('item_uom_id');

        if (empty($party_opening_entry_date)) {
            return response()->json(['status' => 'FAILURE', 'message' => 'Entry Date Not Found'], 404);
        }
        if (empty($dealer_id)) {
            return response()->json(['status' => 'FAILURE', 'message' => 'Dealer Id Not Found'], 404);
        }
        if (empty($item_id)) {
            return response()->json(['status' => 'FAILURE', 'message' => 'Item Id Not Found'], 404);
        }
        if (empty($item_packing_type_id)) {
            return response()->json(['status' => 'FAILURE', 'message' => 'Packing Type Id Not Found'], 404);
        }
        if (empty($item_uom_id)) {
            return response()->json(['status' => 'FAILURE', 'message' => 'UOM Id Not Found'], 404);
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
            ->where($SalesOrderStockMain_tb . '.stock_entry_date', '<=', $party_opening_entry_date)
            ->where($SalesOrderStockSub_tb . '.item_creation_id', $item_id)
            ->where($SalesOrderStockSub_tb . '.item_property', $item_packing_type_id)
            ->where($SalesOrderStockSub_tb . '.item_weights', $item_uom_id)
            ->orderBy($SalesOrderStockMain_tb . '.stock_entry_date', 'desc')
            ->first();

        if ($current_stock) {
            $total_opening_stk = $current_stock->current_stock;
            $total_opening_stock = number_format($total_opening_stk, 1);
        } else {
            $total_opening_stock = 0;
        }
        return response()->json(['status' => 'SUCCESS', 'message' => 'Party Opening Stock Count Showed Successfully', 'stock_count' => $total_opening_stock], 200);
    }

    public function party_opening_stock_sublist_pieces_count_api(Request $request)
    {
        $item_id = $request->input('item_id');
        $stock_count = $request->input('stock_count');

        if (empty($item_id)) {
            return response()->json(['status' => 'FAILURE', 'message' => 'Item Id Not Found'], 404);
        }
        if (empty($stock_count)) {
            $stock_count = 0;
        }

        $item_creation = ItemCreation::select('piece')
            ->where('id', $item_id)
            ->where(function ($query) {
                $query->where('delete_status', '0')->orWhereNull('delete_status');
            })
            ->orderBy('item_name')
            ->first();

        if ($item_creation) {
            $piece = $item_creation->piece;
            $pieces_count = $stock_count * $piece;
        } else {
            $pieces_count = 0;
        }
        return response()->json(['status' => 'SUCCESS', 'message' => 'Party Opening Stock Pieces Count Showed Successfully', 'pieces_count' => $pieces_count], 200);
    }

    public function party_opening_stock_main_insert_api(Request $request)
    {
        $stock_entry_date = $request->input('stock_entry_date');
        // $date = Carbon::createFromFormat('d-m-Y', $entry_date);
        // $stock_entry_date = $date->format('Y-m-d');

        $dealer_id = $request->input('dealer_id');
        $stock_number = $request->input('stock_number');
        $dealer_address = $request->input('dealer_address');
        $sales_executive_id = $request->input('sales_executive_id');
        $description = $request->input('description');

        if (empty($stock_entry_date)) {
            return response()->json(['status' => 'FAILURE', 'message' => 'Stock Entry Date Not Found'], 404);
        }
        if (empty($dealer_id)) {
            return response()->json(['status' => 'FAILURE', 'message' => 'Dealer Id Not Found'], 404);
        }
        if (empty($stock_number)) {
            return response()->json(['status' => 'FAILURE', 'message' => 'Stock Number Not Found'], 404);
        }
        if (empty($sales_executive_id)) {
            return response()->json(['status' => 'FAILURE', 'message' => 'Sales Executive Id Not Found'], 404);
        }

        $tb = new SalesOrderStockMain();
        $tb->entry_date = Carbon::now();
        $tb->stock_entry_date = $stock_entry_date;
        $tb->dealer_creation_id = $dealer_id;
        $tb->dealer_address = $dealer_address;
        $tb->order_no = $stock_number;
        $tb->status = '1';
        $tb->description = $description;
        $tb->sales_exec = $sales_executive_id;
        $tb->save();

        return response()->json(['status' => 'SUCCESS', 'message' => 'Party Opening Stock Data Inserted Successfully'], 200);
    }

    public function party_opening_stock_main_update_api(Request $request)
    {
        $party_opening_stock_main_id = $request->input('party_opening_stock_main_id');

        $stock_entry_date = $request->input('stock_entry_date');
        // $date = Carbon::createFromFormat('d-m-Y', $entry_date);
        // $stock_entry_date = $date->format('Y-m-d');

        $dealer_id = $request->input('dealer_id');
        $stock_number = $request->input('stock_number');
        $dealer_address = $request->input('dealer_address');
        $sales_executive_id = $request->input('sales_executive_id');
        $description = $request->input('description');

        if (empty($party_opening_stock_main_id)) {
            return response()->json(['status' => 'FAILURE', 'message' => 'Party Opening Stock Main Id Not Found'], 404);
        }
        if (empty($stock_entry_date)) {
            return response()->json(['status' => 'FAILURE', 'message' => 'Stock Entry Date Not Found'], 404);
        }
        if (empty($dealer_id)) {
            return response()->json(['status' => 'FAILURE', 'message' => 'Dealer Id Not Found'], 404);
        }
        if (empty($stock_number)) {
            return response()->json(['status' => 'FAILURE', 'message' => 'Stock Number Not Found'], 404);
        }
        if (empty($sales_executive_id)) {
            return response()->json(['status' => 'FAILURE', 'message' => 'Sales Executive Id Not Found'], 404);
        }

        $tb = SalesOrderStockMain::find($party_opening_stock_main_id);
        if ($tb) {
            $tb->entry_date = Carbon::now();
            $tb->stock_entry_date = $stock_entry_date;
            $tb->dealer_creation_id = $dealer_id;
            $tb->dealer_address = $dealer_address;
            $tb->order_no = $stock_number;
            $tb->status = '1';
            $tb->description = $description;
            $tb->sales_exec = $sales_executive_id;
            $tb->save();
        } else {
            return response()->json(['status' => 'FAILURE', 'error' => 'Party Opening Stock Entry Not Found'], 404);
        }

        $affectedRows = SalesOrderStockSub::where('sales_order_main_id', $party_opening_stock_main_id)
        ->update(['order_date' => $stock_entry_date]);

        if ($affectedRows > 0) {
            $records = SalesOrderStockSub::where('sales_order_main_id', $party_opening_stock_main_id)->get();
            foreach ($records as $record) {
                $relatedModel = $record->relatedModel;
                if ($relatedModel) {
                    $relatedModel->save();
                }
            }
        }

        return response()->json(['status' => 'SUCCESS', 'message' => 'Party Opening Stock Data Updated Successfully', 'party_opening_stock_id' => $party_opening_stock_main_id], 200);
    }

    public function party_opening_stock_sublist_insert_api(Request $request)
    {
        $party_opening_stock_main_id = $request->input('party_opening_stock_main_id');
        $stock_entry_date = $request->input('stock_entry_date');
        $dealer_id = $request->input('dealer_id');
        $stock_number = $request->input('stock_number');
        $dealer_address = $request->input('dealer_address');
        $sales_executive_id = $request->input('sales_executive_id');
        $description = $request->input('description');
        $group_id = $request->input('group_id');
        $item_detail = json_decode($request->input('item_detail'), true);

        if (empty($stock_entry_date)) {
            return response()->json(['status' => 'FAILURE', 'message' => 'Stock Entry Date Not Found'], 404);
        }
        if (empty($dealer_id)) {
            return response()->json(['status' => 'FAILURE', 'message' => 'Dealer Id Not Found'], 404);
        }
        if (empty($stock_number)) {
            return response()->json(['status' => 'FAILURE', 'message' => 'Stock Number Not Found'], 404);
        }
        if (empty($sales_executive_id)) {
            return response()->json(['status' => 'FAILURE', 'message' => 'Sales Executive Id Not Found'], 404);
        }
        if (empty($group_id)) {
            return response()->json(['status' => 'FAILURE', 'message' => 'Group Id Not Found'], 404);
        }

        foreach ($item_detail as $item_details) {
            $item_id = $item_details['item_id'] ?? 0;
            $item_short_code_id = $item_details['item_short_code_id'] ?? 0;
            $item_packing_type_id = $item_details['item_packing_type_id'] ?? 0;
            $item_uom_id = $item_details['item_uom_id'] ?? 0;
            $opening_stock = $item_details['opening_stock'] ?? 0;

            if ($party_opening_stock_main_id && $item_id) {
                $sales_order_stock = SalesOrderStockSub::select('item_creation_id')
                    ->where('sales_order_main_id', $party_opening_stock_main_id)
                    ->where('item_creation_id', $item_id)
                    ->where(function ($query) {
                        $query->where('delete_status', '0')->orWhereNull('delete_status');
                    })
                    ->first();

                if ($sales_order_stock) {
                    return response()->json(['status' => 'FAILURE', 'message' => 'This Item Name Already Exist'], 404);
                }

            } else {
                $party_opening_stock_main_id = $party_opening_stock_main_id ?: SalesOrderStockMain::insertGetId([
                    'entry_date' => Carbon::now(),
                    'order_no' => $stock_number,
                    'stock_entry_date' => $stock_entry_date,
                    'dealer_creation_id' => $dealer_id,
                    'dealer_address' => $dealer_address,
                    'status' => '1',
                    'description' => $description,
                    'sales_exec' => $sales_executive_id
                ]);
            }
        }

        foreach ($item_detail as $item_details) {
            $item_id = $item_details['item_id'] ?? 0;
            $item_short_code_id = $item_details['item_short_code_id'] ?? 0;
            $item_packing_type_id = $item_details['item_packing_type_id'] ?? 0;
            $item_uom_id = $item_details['item_uom_id'] ?? 0;
            $opening_stock = $item_details['opening_stock'] ?? 0;

            $item_creation = ItemCreation::select('piece')
                ->where('id', $item_id)
                ->where(function ($query) {
                    $query->where('delete_status', '0')->orWhereNull('delete_status');
                })
                ->orderBy('item_name')
                ->first();

            $tb = new SalesOrderStockSub();
            $tb->entry_date = Carbon::now();
            $tb->sales_order_main_id = $party_opening_stock_main_id;
            $tb->order_date = $stock_entry_date;
            $tb->group_creation_id = $group_id;
            $tb->item_creation_id = $item_id;
            $tb->short_code_id = $item_short_code_id;
            $tb->item_property = $item_packing_type_id;
            $tb->item_weights = $item_uom_id;
            $tb->opening_stock = $opening_stock;
            $piece = $item_creation ? $item_creation->piece : 0;
            $total_current_stock = $opening_stock;
            $total_pieces = $total_current_stock * $piece;
            $tb->current_stock = $total_current_stock;
            $tb->pieces_quantity = $total_pieces;
            $tb->save();
        }

        return response()->json(['status' => 'SUCCESS', 'message' => 'Party Opening Stock Sublist Data Inserted Successfully', 'party_opening_stock_main_id' => $party_opening_stock_main_id], 200);
    }

    public function party_opening_stock_sublist_update_api(Request $request)
    {
        $party_opening_stock_sublist_id = $request->input('party_opening_stock_sublist_id');

        $stock_entry_date = $request->input('stock_entry_date');
        // $date = Carbon::createFromFormat('Y-m-d', $entry_date);
        // $stock_entry_date = $date->format('Y-m-d');
        $group_id = $request->input('group_id');
        $item_id = $request->input('item_id');
        $item_short_code_id = $request->input('item_short_code_id');
        $item_packing_type_id = $request->input('item_packing_type_id');
        $item_uom_id = $request->input('item_uom_id');
        $opening_stock = $request->input('opening_stock');

        if (empty($party_opening_stock_sublist_id)) {
            return response()->json(['status' => 'FAILURE', 'message' => 'Party Opening Stock Sublist Id Not Found'], 404);
        }
        if (empty($stock_entry_date)) {
            return response()->json(['status' => 'FAILURE', 'message' => 'Stock Entry Date Not Found'], 404);
        }
        if (empty($group_id)) {
            return response()->json(['status' => 'FAILURE', 'message' => 'Group Id Not Found'], 404);
        }
        if (empty($item_id)) {
            return response()->json(['status' => 'FAILURE', 'message' => 'Item Id Not Found'], 404);
        }
        if (empty($item_short_code_id)) {
            return response()->json(['status' => 'FAILURE', 'message' => 'Item Short Code Id Not Found'], 404);
        }
        if (empty($item_packing_type_id)) {
            return response()->json(['status' => 'FAILURE', 'message' => 'Item Packing Type Id Not Found'], 404);
        }
        if (empty($item_uom_id)) {
            return response()->json(['status' => 'FAILURE', 'message' => 'Item UOM Id Not Found'], 404);
        }
        if (empty($opening_stock)) {
            return response()->json(['status' => 'FAILURE', 'message' => 'Opening Stocks Not Found'], 404);
        }

        $tb = SalesOrderStockSub::find($party_opening_stock_sublist_id);
        if ($tb) {
            $tb->order_date = $stock_entry_date;
            $tb->group_creation_id = $group_id;
            $tb->item_creation_id = $item_id;
            $tb->short_code_id = $item_short_code_id;
            $tb->item_property = $item_packing_type_id;
            $tb->item_weights = $item_uom_id;
            $tb->opening_stock = $opening_stock;
            $opening_stock_sublist = $opening_stock;
            $total_current_stock_sublist = $opening_stock_sublist;
            $tb->current_stock = $total_current_stock_sublist;
            $item_creation = ItemCreation::select('piece')
            ->where('id', $item_id)
            ->where(function ($query) {
                $query->where('delete_status', '0')->orWhereNull('delete_status');
            })->orderBy('item_name')->first();
            if ($item_creation) {
                $piece = $item_creation->piece;
                $total_pieces = $total_current_stock_sublist * $piece;
            } else {
                $total_pieces = "0";
            }
            $tb->pieces_quantity = $total_pieces;
            $tb->save();
        } else {
            return response()->json(['status' => 'FAILURE', 'error' => 'Party Opening Stock Sublist Id Entry Not Found'], 404);
        }

        $party_opening_stock = SalesOrderStockSub::find($party_opening_stock_sublist_id);
        $party_opening_stock_main_id = $party_opening_stock->sales_order_main_id;

        return response()->json(['status' => 'SUCCESS', 'message' => 'Party Opening Stock Sublist Data Updated Successfully', 'party_opening_stock_main_id' => $party_opening_stock_main_id , 'party_opening_stock_sublist_id' => $party_opening_stock_sublist_id], 200);
    }

    public function party_opening_stock_form_main_api(Request $request)
    {
        $party_opening_stock_main_id = $request->input('party_opening_stock_main_id');

        if (empty($party_opening_stock_main_id)) {
            return response()->json(['status' => 'FAILURE', 'message' => 'Party Opening Stock Sublist Id Not Found'], 404);
        }

        $party_opening_stock_main = SalesOrderStockMain::select('id as party_opening_stock_main_id', 'order_no as order_number', 'stock_entry_date', 'dealer_creation_id as dealer_id', 'dealer_address', 'sales_exec as sales_executive_id', DB::raw('COALESCE(description, "-") as description'))
            ->where('id', $party_opening_stock_main_id)
            ->get();

        if ($party_opening_stock_main->isEmpty()) {
            return response()->json(['status' => 'FAILURE', 'message' => 'No Party Opening Stock Sublist Data Found'], 404);
        } else {
            return response()->json(['status' => 'SUCCESS', 'message' => 'Party Opening Stock Sublist Data Updated Successfully', 'party_opening_stock_main' => $party_opening_stock_main, 'party_opening_stock_main_id' => $party_opening_stock_main_id], 200);
        }
    }

    public function party_opening_stock_form_sublist_api(Request $request)
    {
        $party_opening_stock_main_id = $request->input('party_opening_stock_main_id');

        if (empty($party_opening_stock_main_id)) {
            return response()->json(['status' => 'FAILURE', 'message' => 'Party Opening Stock Main Id Not Found'], 404);
        }

        $SalesOrderStockMain_tb = (new SalesOrderStockMain)->getTable();
        $SalesOrderStockSub_tb = (new SalesOrderStockSub)->getTable();

        $sales_order_stock_main = SalesOrderStockMain::select($SalesOrderStockSub_tb . '.group_creation_id as group_id','group_creation.group_name')
            ->join($SalesOrderStockSub_tb, $SalesOrderStockSub_tb . '.sales_order_main_id', '=', $SalesOrderStockMain_tb . '.id')
            ->join('group_creation', $SalesOrderStockSub_tb . '.group_creation_id', '=', 'group_creation.id')
            ->where(function ($query) use ($SalesOrderStockMain_tb) {
                $query->where($SalesOrderStockMain_tb . '.delete_status', '0')
                    ->orWhereNull($SalesOrderStockMain_tb . '.delete_status');
            })
            ->where($SalesOrderStockMain_tb . '.status', '1')
            ->where($SalesOrderStockMain_tb . '.id', $party_opening_stock_main_id)
            ->groupBy($SalesOrderStockSub_tb . '.group_creation_id', 'group_creation.group_name') // Corrected here
            ->orderBy('group_creation.group_name')
            ->get();

        $party_opening_form_sublist_main = [];

        foreach ($sales_order_stock_main as $sales_order_stock_main_1) {
            $group_id = $sales_order_stock_main_1->group_id ?? 0;
            $group_name = $sales_order_stock_main_1->group_name ?? 0;

            $party_opening_form_sublist = [];

            $party_opening_stock_sublist = DB::select('SELECT id as party_opening_sublist_id, (select item_name from item_creation where id = sales_order_stock_sublist.item_creation_id) as item_name, (select short_code from item_creation where id = sales_order_stock_sublist.short_code_id) as item_short_code, (select group_name from group_creation where id = sales_order_stock_sublist.group_creation_id) as group_name, (select item_properties_type from item_properties_type where id = sales_order_stock_sublist.item_property) as item_packing_type, (select item_liters_type from item_liters_type where id = sales_order_stock_sublist.item_weights) as item_uom, opening_stock, current_stock, pieces_quantity from sales_order_stock_sublist where sales_order_main_id = ' . $party_opening_stock_main_id . ' and group_creation_id = ' . $group_id . ' and (delete_status = 0 or delete_status is null)');

            foreach ($party_opening_stock_sublist as $party_opening) {
                $party_opening_form_sublist[] = [
                    'party_opening_sublist_id' => $party_opening->party_opening_sublist_id,
                    'item_name' => $party_opening->item_name,
                    'item_short_code' => $party_opening->item_short_code,
                    'group_name' => $party_opening->group_name,
                    'item_packing_type' => $party_opening->item_packing_type,
                    'item_uom' => $party_opening->item_uom,
                    'opening_stock' => $party_opening->opening_stock,
                    'current_stock' => $party_opening->current_stock,
                    'pieces_quantity' => $party_opening->pieces_quantity,
                ];
            }

            $party_opening_form_sublist_main[] = [
                'group_id' => $group_id,
                'group_name' => $group_name,
                'party_opening_groupwise_list' => $party_opening_form_sublist
            ];
        }

        if (empty($party_opening_form_sublist)) {
            return response()->json(['status' => 'FAILURE', 'message' => 'No Party Opening Stock Sublist Data Found'], 404);
        } else {
            return response()->json(['status' => 'SUCCESS', 'message' => 'Party Opening Stock Sublist Data Updated Successfully', 'party_opening_stock_sublist' => $party_opening_form_sublist_main, 'party_opening_stock_main_id' => $party_opening_stock_main_id], 200);
        }
    }

    public function party_opening_stock_form_sublist_pdf_api(Request $request)
    {
        $party_opening_stock_main_id = $request->input('party_opening_stock_main_id');

        if (empty($party_opening_stock_main_id)) {
            return response()->json(['status' => 'FAILURE', 'message' => 'Party Opening Stock Main Id Not Found'], 404);
        }

        $SalesOrderStockMain_tb = (new SalesOrderStockMain)->getTable();
        $SalesOrderStockSub_tb = (new SalesOrderStockSub)->getTable();

        $sales_order_stock_main_dealer = SalesOrderStockMain::select($SalesOrderStockMain_tb . '.dealer_creation_id as dealer_id', 'dealer_creation.dealer_name')
            ->join($SalesOrderStockSub_tb, $SalesOrderStockSub_tb . '.sales_order_main_id', '=', $SalesOrderStockMain_tb . '.id')
            ->join('dealer_creation', $SalesOrderStockMain_tb . '.dealer_creation_id', '=', 'dealer_creation.id')
            ->join('group_creation', $SalesOrderStockSub_tb . '.group_creation_id', '=', 'group_creation.id')
            ->where(function ($query) use ($SalesOrderStockMain_tb) {
                $query->where($SalesOrderStockMain_tb . '.delete_status', '0')
                    ->orWhereNull($SalesOrderStockMain_tb . '.delete_status');
            })
            ->where($SalesOrderStockMain_tb . '.status', '1')
            ->where($SalesOrderStockMain_tb . '.id', $party_opening_stock_main_id)
            ->groupBy($SalesOrderStockMain_tb . '.dealer_creation_id', 'dealer_creation.dealer_name')
            ->orderBy('dealer_creation.dealer_name')
            ->get();

        $party_opening_form_sublist_main_dealer = [];

        foreach ($sales_order_stock_main_dealer as $sales_order_stock_main_dealer_1) {
            $dealer_id = $sales_order_stock_main_dealer_1->dealer_id ?? 0;
            $dealer_name = $sales_order_stock_main_dealer_1->dealer_name ?? 0;

            $party_opening_form_sublist_main_group = [];

            $SalesOrderStockMain_tb = (new SalesOrderStockMain)->getTable();
            $SalesOrderStockSub_tb = (new SalesOrderStockSub)->getTable();

            $sales_order_stock_main_group = SalesOrderStockMain::select($SalesOrderStockSub_tb . '.group_creation_id as group_id', 'group_creation.group_name')
                ->join($SalesOrderStockSub_tb, $SalesOrderStockSub_tb . '.sales_order_main_id', '=', $SalesOrderStockMain_tb . '.id')
                ->join('dealer_creation', $SalesOrderStockMain_tb . '.dealer_creation_id', '=', 'dealer_creation.id')
                ->join('group_creation', $SalesOrderStockSub_tb . '.group_creation_id', '=', 'group_creation.id')
                ->where(function ($query) use ($SalesOrderStockMain_tb) {
                    $query->where($SalesOrderStockMain_tb . '.delete_status', '0')
                        ->orWhereNull($SalesOrderStockMain_tb . '.delete_status');
                })
                ->where($SalesOrderStockMain_tb . '.status', '1')
                ->where($SalesOrderStockMain_tb . '.id', $party_opening_stock_main_id)
                ->groupBy($SalesOrderStockSub_tb . '.group_creation_id', 'group_creation.group_name')
                ->orderBy('group_creation.group_name')
                ->get();

            foreach ($sales_order_stock_main_group as $sales_order_stock_main_group_1) {
                $group_id = $sales_order_stock_main_group_1->group_id ?? 0;
                $group_name = $sales_order_stock_main_group_1->group_name ?? 0;

                $party_opening_form_sublist = [];

                $party_opening_stock_sublist = DB::select('SELECT id as party_opening_sublist_id, (select item_name from item_creation where id = sales_order_stock_sublist.item_creation_id) as item_name, (select short_code from item_creation where id = sales_order_stock_sublist.short_code_id) as item_short_code, (select group_name from group_creation where id = sales_order_stock_sublist.group_creation_id) as group_name, (select item_properties_type from item_properties_type where id = sales_order_stock_sublist.item_property) as item_packing_type, (select item_liters_type from item_liters_type where id = sales_order_stock_sublist.item_weights) as item_uom, opening_stock, current_stock, pieces_quantity from sales_order_stock_sublist where sales_order_main_id = ' . $party_opening_stock_main_id . ' and group_creation_id = ' . $group_id . ' and (delete_status = 0 or delete_status is null)');

                foreach ($party_opening_stock_sublist as $party_opening) {
                    $party_opening_form_sublist[] = [
                        'party_opening_sublist_id' => $party_opening->party_opening_sublist_id,
                        'item_name' => $party_opening->item_name,
                        'item_short_code' => $party_opening->item_short_code,
                        'group_name' => $party_opening->group_name,
                        'item_packing_type' => $party_opening->item_packing_type,
                        'item_uom' => $party_opening->item_uom,
                        'opening_stock' => $party_opening->opening_stock,
                        'current_stock' => $party_opening->current_stock,
                        'pieces_quantity' => $party_opening->pieces_quantity,
                    ];
                }
                $party_opening_form_sublist_main_group[] = [
                    'group_id' => $group_id,
                    'group_name' => $group_name,
                    'party_opening_groupwise_list' => $party_opening_form_sublist
                ];
            }
            $party_opening_form_sublist_main_dealer[] = [
                'dealer_id' => $dealer_id,
                'dealer_name' => $dealer_name,
                'party_opening_dealerwise_list' => $party_opening_form_sublist_main_group
            ];
        }

        if (empty($party_opening_form_sublist)) {
            return response()->json(['status' => 'FAILURE', 'message' => 'No Party Opening Stock Sublist Data Found'], 404);
        } else {
            return response()->json(['status' => 'SUCCESS', 'message' => 'Party Opening Stock Sublist Data Updated Successfully', 'party_opening_stock_sublist' => $party_opening_form_sublist_main_dealer, 'party_opening_stock_main_id' => $party_opening_stock_main_id], 200);
        }
    }

    public function party_opening_stock_form_sublist_edit_api(Request $request)
    {
        $party_opening_stock_sublist_id = $request->input('party_opening_stock_sublist_id');

        if (empty($party_opening_stock_sublist_id)) {
            return response()->json(['status' => 'FAILURE', 'message' => 'Party Opening Stock Sublist Id Not Found'], 404);
        }

        $party_opening_stock_sublist = SalesOrderStockSub::select('id as party_opening_stock_sublist_id', 'group_creation_id as group_id', 'item_creation_id as item_id', 'short_code_id as item_short_code_id', 'item_property as item_packing_type_id', 'item_weights as item_uom_id', 'opening_stock', 'current_stock', 'pieces_quantity')
            ->where('id', $party_opening_stock_sublist_id)
            ->where(function ($query) {
                $query->where('delete_status', '0')->orWhereNull('delete_status');
            })
            ->get();

        if ($party_opening_stock_sublist->isEmpty()) {
            return response()->json(['status' => 'FAILURE', 'message' => 'No Party Opening Stock Sublist Data Found'], 404);
        } else {
            return response()->json(['status' => 'SUCCESS', 'message' => 'Party Opening Stock Sublist Data Updated Successfully', 'party_opening_stock_sublist' => $party_opening_stock_sublist, 'party_opening_stock_sublist_id' => $party_opening_stock_sublist_id], 200);
        }
    }

    public function party_opening_sublist_delete_api(Request $request)
    {
        $party_opening_sublist_id = $request->input('party_opening_sublist_id');

        if (empty($party_opening_sublist_id)) {
            return response()->json(['status' => 'FAILURE', 'message' => 'Sublist Id Not Found'], 404);
        }

        $tb = SalesOrderStockSub::find($party_opening_sublist_id);
        $tb->delete_status = "1";

        if ($tb->save()) {
            return response()->json(['status' => 'SUCCESS', 'message' => 'Sublist Deleted Successfully'], 200);
        } else {
            return response()->json(['status' => 'FAILURE', 'message' => 'Sublist Not Found'], 404);
        }
    }

    public function party_opening_main_delete_api(Request $request)
    {
        $party_opening_main_id = $request->input('party_opening_main_id');

        if (empty($party_opening_main_id)) {
            return response()->json(['status' => 'FAILURE', 'message' => 'Mainlist Id Not Found'], 404);
        }

        $main_tb = SalesOrderStockMain::find($party_opening_main_id);
        if (!$main_tb) {
            return response()->json(['status' => 'FAILURE', 'message' => 'Mainlist Not Found'], 404);
        }
        $main_tb->delete_status = "1";

        if ($main_tb->save()) {
            $sub_tb = SalesOrderStockSub::where('sales_order_main_id', $party_opening_main_id)
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
}
