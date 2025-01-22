<?php

namespace App\Http\Controllers\Reports;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Models\Entry\SalesOrderDeliveryMain;
use App\Models\Entry\SalesOrderDeliverySub;
use App\Models\Entry\SalesOrderC2DSub;
use App\Models\SalesRepCreation;
use App\Models\DealerCreation;
use App\Models\Entry\SalesOrderC2DMain;
use App\Models\GroupCreation;
use App\Models\ItemCreation;
use App\Models\MarketManagerCreation;
use Carbon\Carbon;

class ItemSalesPendingMonthWiseReportController extends Controller
{
    public function item_sales_pending_month_wise_report()
    {
        $sales_rep_creation = SalesRepCreation::select('id', 'sales_ref_name')
            ->where('sales_ref_name', '!=', '')
            ->orderBy('sales_ref_name')
            ->get();

        $item_creation = ItemCreation::select('id', 'item_name')->where(function ($query) {
            $query->where('delete_status', '0')->orWhereNull('delete_status');
        })->orderBy('id')->get();

        $group_creation = GroupCreation::select('id', 'group_name')->where(function ($query) {
            $query->where('delete_status', '0')->orWhereNull('delete_status');
        })->orderBy('group_name')->get();

        $manager_creation = MarketManagerCreation::select('id', 'manager_name')->where('delete_status', '0')->where('status1', '0')->orWhereNull('delete_status')->orderBy('manager_no')->get();

        return view('Reports.item_sales_pending_month_wise_report.admin', [
            'sales_rep_creation' => $sales_rep_creation,
            'manager_creation' => $manager_creation,
            'item_creation' => $item_creation,
            'group_creation' => $group_creation

        ]);
    }

    public function db_cmd(Request $request)
    {
        $action = $request->input('action');
        if ($action == 'retrieve') {
            $from_date = $request->input('from_date');
            $to_date = ($request->input('to_date')!='') ? $request->input('to_date') : date('Y-m-d');
            $manager_id = $request->input('manager_id');
            $group_by = $request->input('group_by');
            $sales_ref_id = $request->input('sales_ref_id');
            $ref_by = $request->input('ref_by');
            $dealer_id = $request->input('dealer_id');
            $group_id = $request->input('group_id');
            $item_id = $request->input('item_id');
            $sales_det = $request->input('sales_det');
            $ch_dispatch = (($sales_det == '') || ($sales_det == 'dispatch')) ? 1 : 0;
            $ch_pendings = (($sales_det == '') || ($sales_det == 'pending')) ? 1 : 0;

            $salesref_tb = (new SalesRepCreation)->getTable();
            $dealer_tb = (new DealerCreation)->getTable();
            $group_tb = (new GroupCreation)->getTable();
            $item_tb = (new ItemCreation)->getTable();
            $so_c2d_main_tb = (new SalesOrderC2DMain)->getTable();
            $so_c2d_sub_tb = (new SalesOrderC2DSub)->getTable();
            $disp_main_tb = (new SalesOrderDeliveryMain)->getTable();
            $disp_sub_tb = (new SalesOrderDeliverySub)->getTable();
            $sales_ref_list = [];
            $group_list = [];
            if (($manager_id != '') && ($from_date != '') && ($to_date != '')) {
                $sel_sales_refs = [];
                $sel_dealers = [];
                $sel_items = [];
                $from_date1 = date('Y-m-d', strtotime($from_date));
                $to_date1 = date('Y-m-d', strtotime($to_date));
                $dealer_order_list = [];
                $so_main_list1 = SalesOrderC2DMain::selectRaw("$so_c2d_main_tb.id,$so_c2d_main_tb.order_date,$so_c2d_main_tb.dealer_creation_id,$so_c2d_main_tb.description,$dealer_tb.sales_rep_id")->leftJoin($dealer_tb, "$dealer_tb.id", "$so_c2d_main_tb.dealer_creation_id")->whereRaw("($so_c2d_main_tb.delete_status='0' or $so_c2d_main_tb.delete_status is null) and ($so_c2d_main_tb.status='1') and ($so_c2d_main_tb.order_date>='$from_date1' and $so_c2d_main_tb.order_date<='$to_date1')")->orderBy("$so_c2d_main_tb.id", 'asc')->get();
                $sub_ids_list = [];
                if ($so_main_list1 != null) {
                    $avail_main = SalesOrderDeliveryMain::whereRaw("(delete_status='0' or delete_status is null) and status='1'")->distinct()->pluck('id')->toArray();
                    foreach ($so_main_list1 as $so_main_list2) {
                        $main_id = $so_main_list2['id'];
                        $sales_rep_id2 = $so_main_list2['sales_rep_id'];
                        $dealer_creation_id2 = $so_main_list2['dealer_creation_id'];
                        $order_date2 = $so_main_list2['order_date'];
                        $description2 = $so_main_list2['description'];
                        $so_sub_list1 = SalesOrderC2DSub::selectRaw("item_creation_id,order_quantity,id")->whereRaw("(delete_status='0' or delete_status is null) and sales_order_main_id=$main_id")->get();
                        if($so_sub_list1 != null){
                            foreach($so_sub_list1 as $so_sub_list2){
                                $item_creation_id2 = $so_sub_list2['item_creation_id'];
                                $order_quantity2 = $so_sub_list2['order_quantity'];
                                $sub_id2 = $so_sub_list2['id'];
                                if (!in_array($sales_rep_id2, $sel_sales_refs)) {
                                    $sel_sales_refs[] = $sales_rep_id2;
                                }
                                if (!in_array($dealer_creation_id2, $sel_dealers)) {
                                    $sel_dealers[] = $dealer_creation_id2;
                                }
                                if (!in_array($item_creation_id2, $sel_items)) {
                                    $sel_items[] = $item_creation_id2;
                                }
                                $sub_ids_list[$sub_id2] = ['sales_rep_id' => $sales_rep_id2, 'dealer_creation_id' => $dealer_creation_id2, 'order_date' => $order_date2, 'item_creation_id' => $item_creation_id2];
                                if(!isset($dealer_order_list[$dealer_creation_id2][$order_date2])){
                                    $dealer_order_list[$dealer_creation_id2][$order_date2] = ['description'=>"",'all_cnt'=>[],'dispatch'=>[]];
                                }
                                if($description2 != ''){
                                    $dealer_order_list[$dealer_creation_id2][$order_date2]['description'] = $description2;
                                }
                                if(!isset($dealer_order_list[$dealer_creation_id2][$order_date2]['all_cnt'][$item_creation_id2])){
                                    $dealer_order_list[$dealer_creation_id2][$order_date2]['all_cnt'][$item_creation_id2] = 0;
                                }
                                $dealer_order_list[$dealer_creation_id2][$order_date2]['all_cnt'][$item_creation_id2] += $order_quantity2;
                            }
                        }
                    }
                    if(count($avail_main) > 0){
                        $sub_ids_list0 = array_keys($sub_ids_list);
                        $disp_sub_list1 = SalesOrderDeliverySub::selectRaw("distinct order_recipt_sub_id,return_quantity")->whereRaw("(sales_order_main_id in (".implode(",",$avail_main).")) and (delete_status='0' or delete_status is null) and order_recipt_sub_id in (".implode(",",$sub_ids_list0).")")->get();
                        if ($disp_sub_list1 != null) {
                            foreach ($disp_sub_list1 as $disp_sub_list2) {
                                $sub_ids_list2 = $sub_ids_list[$disp_sub_list2['order_recipt_sub_id']];
                                $sales_rep_id2 = $sub_ids_list2['sales_rep_id'];
                                $dealer_creation_id2 = $sub_ids_list2['dealer_creation_id'];
                                $item_creation_id2 = $sub_ids_list2['item_creation_id'];
                                $order_date2 = $sub_ids_list2['order_date'];
                                $order_quantity2 = $disp_sub_list2['return_quantity'];
                                if(!isset($dealer_order_list[$dealer_creation_id2][$order_date2]['dispatch'][$item_creation_id2])){
                                    $dealer_order_list[$dealer_creation_id2][$order_date2]['dispatch'][$item_creation_id2] = 0;
                                }
                                $dealer_order_list[$dealer_creation_id2][$order_date2]['dispatch'][$item_creation_id2] += $order_quantity2;
                            }
                        }
                    }
                }
                if (count($sel_items) > 0) {
                    $cond1 = [];
                    $cond1[] = "$salesref_tb.manager_id='$manager_id'";
                    if ($sales_ref_id != '') {
                        $cond1[] = "$salesref_tb.id='$sales_ref_id'";
                    }
                    $cond2 = [];
                    if ($dealer_id != '') {
                        $cond2[] = " and $dealer_tb.id='$dealer_id'";
                    }
                    $sales_ref_list1 = SalesRepCreation::selectRaw("$salesref_tb.id,$salesref_tb.sales_ref_name")->whereRaw("($salesref_tb.delete_status='0' or $salesref_tb.delete_status is null) and (status='0')")->whereRaw("(" . implode(' and ', $cond1) . ")")->orderBy("$salesref_tb.sales_ref_name", 'asc')->get();
                    if ($sales_ref_list1 != null) {
                        foreach ($sales_ref_list1 as $sales_ref_list2) {
                            $dealer_list = [];
                            $dealer_list1 = DealerCreation::selectRaw("$dealer_tb.id,$dealer_tb.dealer_name,$dealer_tb.place")->whereRaw("($dealer_tb.delete_status='0' or $dealer_tb.delete_status is null) and ($dealer_tb.status='1') and $dealer_tb.sales_rep_id='$sales_ref_list2[id]'" . ((count($cond2) > 0) ? implode('', $cond2) : ""))->orderBy("$dealer_tb.dealer_name", 'asc')->get();
                            if ($dealer_list1 != null) {
                                foreach ($dealer_list1 as $dealer_list2) {
                                    if(isset($dealer_order_list[$dealer_list2['id']])){
                                        $dealer_list2['dealer_order_list'] = $dealer_order_list[$dealer_list2['id']];
                                    }else{
                                        $dealer_list2['dealer_order_list'] = null;
                                    }
                                    $dealer_list[] = json_decode(json_encode($dealer_list2), true);
                                }
                            }
                            $sales_ref_list2['dealer_list'] = $dealer_list;
                            $sales_ref_list[] = json_decode(json_encode($sales_ref_list2), true);
                        }
                    }

                    $cond3 = ($group_id != '') ? " and id='$group_id'" : "";
                    $cond4 = [];
                    if (count($sel_items) > 0) {
                        $cond4[] = " and id in (" . implode(',', $sel_items) . ")";
                    }
                    if ($item_id != '') {
                        $cond4[] = " and id='$item_id'";
                    }
                    $group_list1 = GroupCreation::selectRaw("id,group_name")->whereRaw("(delete_status='0' or delete_status is null) and (status='1')$cond3")->orderBy("group_name", 'asc')->get();
                    if ($group_list1 != null) {
                        foreach ($group_list1 as $group_list2) {
                            $item_list = [];
                            $item_list1 = ItemCreation::selectRaw("id,short_code")->whereRaw("(delete_status='0' or delete_status is null) and (status='1') and group_id='$group_list2[id]'" . ((count($cond4) > 0) ? implode('', $cond4) : ""))->get();
                            if ($item_list1 != null) {
                                $item_list = json_decode(json_encode($item_list1), true);
                            }
                            $group_list2['item_list'] = $item_list;
                            $group_list[] = json_decode(json_encode($group_list2), true);
                        }
                    }
                }
            }
            $result = [
                'sales_ref_list' => $sales_ref_list,
                'manager_id' => $manager_id,
                'from_date' => $from_date,
                'group_list' => $group_list,
                'ch_dispatch' => $ch_dispatch,
                'ch_pendings' => $ch_pendings,
            ];
            if ($group_by == 'con') {
                return view('Reports.item_sales_pending_month_wise_report.con_list', $result);
            } else if ($ref_by == 'ref') {
                $sales_ref_list1 = SalesRepCreation::where("id", $sales_ref_id)->first(['sales_ref_name']);
                $result['sales_rep_detail'] = ($sales_ref_list1 != null) ? $sales_ref_list1->sales_ref_name : "-";
                return view('Reports.item_sales_pending_month_wise_report.ref_list', $result);
            } else {
                return view('Reports.item_sales_pending_month_wise_report.list', $result);
            }
        } else if ($action == 'getSalesRef') {


            $manager_id = $request->input('manager_id');

            $sales_ref_name = SalesRepCreation::select('id', 'sales_ref_name')->where('manager_id', $manager_id)->whereRaw("(delete_status='0' or delete_status is null) and status='0'")->orderBy('sales_ref_name')->get();
            return response()->json($sales_ref_name);
        } else if ($action == 'getDealerName') {

            $sales_ref_id = $request->input('sales_ref_id');

            $dealer_name = DealerCreation::select('id', 'dealer_name')->whereRaw("(delete_status='0' or delete_status is null) and status='1'")->orderBy('dealer_name');

            if (!empty($sales_ref_id)) {
                $dealer_name->where('sales_rep_id', '=', $sales_ref_id);
            }

            $dealer_name1 = $dealer_name->get();
            return response()->json($dealer_name1);
        } else if ($action == 'getitemName') {
            $group_id = $request->input('group_id');
            $get_item_name = ItemCreation::select('id', 'short_code')->whereRaw("(delete_status='0' or delete_status is null) and (status='1')")->orderBy('short_code');

            if (!empty($group_id)) {
                $get_item_name->where('group_id', '=', $group_id);
            }
            $item_name = $get_item_name->get();
            return response()->json($item_name);
        }
    }
}
