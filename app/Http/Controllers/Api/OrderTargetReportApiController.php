<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\DealerCreation;
use App\Models\Entry\OrderTarget;
use App\Models\Entry\SalesOrderD2SMain;
use App\Models\Entry\SalesOrderD2Ssub;
use App\Models\Entry\SalesOrderC2DMain;
use App\Models\Entry\SalesOrderC2DSub;
use App\Models\GroupCreation;
use Illuminate\Http\Request;

class OrderTargetReportApiController extends Controller
{
   public function order_target_report_api(Request $request)
{
    $month = $request->input('month');
    $dealer_id = $request->input('dealer_id');
    
    $start_date = $month . '-01';
    $end_date = date('Y-m-t', strtotime($start_date));
    $sales_executive_id = $request->input('sales_executive_id');

    if (empty($month)) {
        return response()->json(['status' => 'FAILURE', 'message' => 'Order Date Not Found'], 404);
    }

    if (empty($sales_executive_id)) {
        return response()->json(['status' => 'FAILURE', 'message' => 'Sales Executive Id Not Found'], 404);
    }

    $order_target_report = [];

    $dealer_creation = DealerCreation::select('dealer_creation.id', 'dealer_creation.dealer_name')
        ->where('dealer_creation.status', '1')
        ->where('dealer_creation.sales_rep_id', $sales_executive_id)
        ->where(function ($query) {
            $query->where('dealer_creation.delete_status', '0')->orWhereNull('dealer_creation.delete_status');
        })
        
         ->when(!empty($dealer_id), function ($query) use ($dealer_id) {
        return $query->where('dealer_creation.id', $dealer_id);
    })
        ->groupBy('dealer_creation.id', 'dealer_creation.dealer_name', 'dealer_creation.sales_rep_id')
        ->orderBy('dealer_creation.dealer_name')
        ->get();

    foreach ($dealer_creation as $dealer) {
        $dealer_id = $dealer->id;
        $dealer_name = $dealer->dealer_name;

        $secondary_sales = SalesOrderD2SMain::select(
            DB::raw('COUNT(sales_order_d2s_main.id) as working_count'),
            DB::raw('MAX(sales_order_d2s_main.order_date) as last_date')
        )
            ->where('sales_order_d2s_main.sales_exec', $sales_executive_id)
            ->where('sales_order_d2s_main.dealer_creation_id', $dealer_id)
            ->whereBetween('sales_order_d2s_main.order_date', [$start_date, $end_date])
            ->where(function ($query) {
                $query->where('sales_order_d2s_main.delete_status', '0')
                    ->orWhereNull('sales_order_d2s_main.delete_status');
            })
            ->first();

        $working_count = $secondary_sales ? $secondary_sales->working_count : 0;
        $last_date = $secondary_sales ? $secondary_sales->last_date : null;

        $group_list = [];

        $order_target_entry = OrderTarget::select('order_target')
            ->where('sales_executive_id', '=', $sales_executive_id)
            ->whereBetween('entry_date', [$start_date, $end_date])
            ->where(function ($query) {
                $query->where('delete_status', '0')->orWhereNull('delete_status');
            })
            ->first();

        if ($order_target_entry) {
            $order_target = $order_target_entry->order_target;
            $order_target_json = json_decode($order_target, true);

            if ($order_target_json && is_array($order_target_json)) {
                foreach ($order_target_json as $dealer_json) {
                    if (isset($dealer_json['dealer_id']) && $dealer_json['dealer_id'] == $dealer_id) {
                        $group_list = [];

                        foreach ($dealer_json['groupDetails'] as $groupDetail) {
                            $group_id_target = $groupDetail['group_id'];
                            $item_list = [];

                            if (isset($groupDetail['itemDetails'])) {
                                $itemDetails = $groupDetail['itemDetails'];

                                $target_quantity_target = 0;
                                $primary_order_quantity = 0;

                                foreach ($itemDetails as $itemDetail) {
                                    $item_id_target = $itemDetail['item_id'];
                                    $target_quantity_target += (float) $itemDetail['target_quantity'];

                                    $primary_order = SalesOrderC2DMain::select(DB::raw('SUM(sales_order_c2d_sublist.order_quantity) as order_quantity'))
                                        ->leftJoin('sales_order_c2d_sublist', 'sales_order_c2d_sublist.sales_order_main_id', '=', 'sales_order_c2d_main.id')
                                        ->where(function ($query) {
                                            $query->where('sales_order_c2d_main.delete_status', '0')->orWhereNull('sales_order_c2d_main.delete_status');
                                        })
                                        ->where(function ($query) {
                                            $query->where('sales_order_c2d_sublist.delete_status', '0')->orWhereNull('sales_order_c2d_sublist.delete_status');
                                        })
                                        ->where('sales_order_c2d_main.dealer_creation_id', $dealer_id)
                                        ->where('sales_order_c2d_main.sales_exec', $sales_executive_id)
                                        ->whereBetween('sales_order_c2d_main.order_date', [$start_date, $end_date])
                                        ->where('sales_order_c2d_sublist.group_creation_id', $group_id_target)
                                        ->where('sales_order_c2d_sublist.item_creation_id', $item_id_target)
                                        ->first();

                                    $primary_total = $primary_order ? (float) $primary_order->order_quantity : 0;
                                    $primary_order_quantity += $primary_total;
                                }

                                $closing_quantity = (float) $target_quantity_target - (float) $primary_order_quantity;

                                $item_list[] = [
                                    'target_quantity' => formatFloat($target_quantity_target),
                                    'primary_quantity' => formatFloat($primary_order_quantity),
                                    'closing_quantity' => formatFloat($closing_quantity)
                                ];
                            }

                            $group_name = GroupCreation::select('group_name')
                                ->where(function ($query) {
                                    $query->where('delete_status', '0')->orWhereNull('delete_status');
                                })
                                ->where('id', $group_id_target)
                                ->first();

                            $group_list[] = [
                                'group_id' => $group_id_target,
                                'group_name' => $group_name ? $group_name->group_name : 'Unknown',
                                'item_list' => $item_list
                            ];
                        }
                    }
                }
            }
        }

        $order_target_report[] = [
            'dealer_id' => $dealer_id,
            'dealer_name' => $dealer_name,
            'working_days' => $working_count,
            'last_visit_date' => $last_date,
            'group_list' => $group_list,
        ];
    }

    if (!empty($order_target_report)) {
        return response()->json(['status' => 'SUCCESS', 'message' => 'Target List Showed Successfully', 'order_target_report' => $order_target_report], 200);
    } else {
        return response()->json(['status' => 'FAILURE', 'message' => 'Target List Not Found'], 404);
    }
}

}


function formatFloat($value) {
    
    $formattedValue = number_format($value, 2, '.', '');

    
    $formattedValue = rtrim($formattedValue, '0');

   
    $formattedValue = rtrim($formattedValue, '.');

    return $formattedValue;
}
