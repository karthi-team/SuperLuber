<?php

namespace App\Http\Controllers\Reports;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\MarketManagerCreation;
use App\Models\SalesRepCreation;
use App\Models\ShopCreation;
use App\Models\DealerCreation;
use App\Models\Entry\SalesOrderD2SMain;
use App\Models\MarketCreation;
use App\Models\ShopsType;
use App\Models\ItemCreation;
use App\Models\GroupCreation;
use App\Models\Entry\SalesOrderD2Ssub;
use Illuminate\Support\Facades\DB;

use Carbon\Carbon;


class ShopReport extends Controller
{
    public function Shop_Report()
    {

        $manager_creation = MarketManagerCreation::select('id', 'manager_name')->where('delete_status', '0')->where('status1', '0')->orWhereNull('delete_status')->orderBy('manager_no')->get();

        $market_creation = MarketCreation::select('id', 'area_name')->where(function ($query) {
            $query->where('delete_status', '0')->orWhereNull('delete_status');
        })->get();

        $shop_type = ShopsType::select('id', 'shops_type')->where(function ($query) {
            $query->where('delete_status', '0')->orWhereNull('delete_status');
        })->get();

        $dealer_list = DealerCreation::select('id', 'dealer_name')->where(function ($query) {
            $query->where('delete_status', '0')->where('status', '=', '1')->orWhereNull('delete_status');
        })->get();

        $group_creation = GroupCreation::select('id', 'group_name')->where(function ($query) {
            $query->where('delete_status', '0')->orWhereNull('delete_status');
        })->orderBy('group_name')->get();

        $item_creation = ItemCreation::select('id', 'short_code')->where(function ($query) {
            $query->where('delete_status', '0')->orWhereNull('delete_status');
        })->orderBy('short_code')->get();


        return view('Reports.shop_report.admin', [
            'manager_creation' => $manager_creation,
            'market_creation' => $market_creation,
            'shop_type' => $shop_type,
            'dealer_list' => $dealer_list,
            'group_creation' => $group_creation,
            'item_creation' => $item_creation,


        ]);
    }

    public function db_cmd(Request $request)
    {
        $action = $request->input('action');
        if ($action == 'retrieve') {
            $from_date = $request->input('from_date');
            $to_date = $request->input('to_date');
            $manager_id = $request->input('manager_id');
            $sales_ref_id = $request->input('sales_ref_id');
            $dealer_id = $request->input('dealer_id');
            $area_id = $request->input('area_id');
            $shop_type_id = $request->input('shop_type_id');
            $pur_time = $request->input('pur_time');
            $group_id = $request->input('group_id');
            $item_id = $request->input('item_id');
            $pur_volumn = $request->input('pur_volumn');

            $shop_tb = (new ShopCreation)->getTable();
            $dealer_tb = (new DealerCreation)->getTable();
            $shoptype_tb = (new ShopsType)->getTable();
            $market_tb = (new MarketCreation)->getTable();
            $d2smain_tb = (new SalesOrderD2SMain)->getTable();
            $d2ssub_tb = (new SalesOrderD2Ssub)->getTable();
            $cond1 = [];
            if (!empty($shop_type_id)) {
                $cond1[] = "$shop_tb.shop_type_id='$shop_type_id'";
            }
            if (!empty($dealer_id)) {
                $cond1[] = "$shop_tb.dealer_id='$dealer_id'";
            }
            if (!empty($area_id)) {
                $cond1[] = "$shop_tb.beats_id='$area_id'";
            }
            $shop_list1 = ShopCreation::selectRaw("$shop_tb.id as shop_id,$shop_tb.shop_name,$shop_tb.address,$dealer_tb.place,$shoptype_tb.shops_type,$market_tb.area_name")->leftJoin($dealer_tb,"$dealer_tb.id","$shop_tb.dealer_id")->leftJoin($shoptype_tb,"$shoptype_tb.id","$shop_tb.shop_type_id")->leftJoin($market_tb,"$market_tb.id","$shop_tb.beats_id")->whereRaw("($shop_tb.delete_status='0' or $shop_tb.delete_status is null) and $shop_tb.shop_name is not null and $shop_tb.shop_type_id is not null" . ((count($cond1) > 0) ? " and (" . implode(" and ", $cond1) . ")" : ""))->orderByRaw("$shop_tb.shop_name,$dealer_tb.place,$shoptype_tb.shops_type asc")->get();
            $result = [];
            if ($shop_list1 != null) {
                $d2s_main_ids = [];
                if (!empty($sales_ref_id)) {
                    $d2s_main_ids = SalesOrderD2SMain::where('sales_exec', $sales_ref_id)->distinct()->pluck('id')->toArray();
                } else {
                    $d2s_main_ids = SalesOrderD2SMain::distinct()->pluck('id')->toArray();
                }
                $d2s_sub_ids = [];
                if ((!empty($from_date)) || (!empty($to_date)) || (!empty($group_id)) || (!empty($item_id))) {
                    $cond = [];
                    if (!empty($from_date)) {
                        $cond[] = "order_date>='" . date('Y-m-d', strtotime($from_date)) . "'";
                    }
                    if (!empty($to_date)) {
                        $cond[] = "order_date<='" . date('Y-m-d', strtotime($to_date)) . "'";
                    }
                    if (!empty($group_id)) {
                        $cond[] = "group_creation_id='$group_id'";
                    }
                    if (!empty($item_id)) {
                        $cond[] = "item_creation_id='$item_id'";
                    }
                    $d2s_sub_ids = SalesOrderD2Ssub::whereRaw(implode(' and ', $cond))->distinct()->pluck('id')->toArray();
                } else {
                    $d2s_sub_ids = SalesOrderD2Ssub::distinct()->pluck('id')->toArray();
                }
                $ch_cond = "";
                if (count($d2s_main_ids) > 0) {
                    $ch_cond .= " and $d2ssub_tb.sales_order_main_id in (" . implode(',', $d2s_main_ids) . ")";
                }
                if (count($d2s_sub_ids) > 0) {
                    $ch_cond .= " and $d2ssub_tb.id in (" . implode(',', $d2s_sub_ids) . ")";
                }
                foreach ($shop_list1 as $shop_list2) {
                    $shop_id = $shop_list2['shop_id'];
                    $main_datas = [];
                    $Purchase_Ltrs = 0;
                    if ((count($d2s_main_ids) > 0) && (count($d2s_sub_ids) > 0)) {
                        $sales_sub1 = SalesOrderD2Ssub::selectRaw("sales_order_main_id,order_date,status_check,order_quantity")->whereRaw("shop_creation_id='$shop_id'$ch_cond")->orderBy("sales_order_main_id", 'asc')->get();
                        if ($sales_sub1 != null) {
                            foreach ($sales_sub1 as $sales_sub2) {
                                $sales_order_main_id2 = $sales_sub2['sales_order_main_id'];
                                $order_date2 = $sales_sub2['order_date'];
                                if (!array_key_exists($sales_order_main_id2, $main_datas)) {
                                    $main_datas[$sales_order_main_id2] = ['date' => [], 'yes' => []];
                                }
                                if (!in_array($order_date2, $main_datas[$sales_order_main_id2]['date'])) {
                                    $main_datas[$sales_order_main_id2]['date'][] = $order_date2;
                                }
                                if ($sales_sub2['status_check'] == 'Yes') {
                                    if (!in_array($order_date2, $main_datas[$sales_order_main_id2]['yes'])) {
                                        $main_datas[$sales_order_main_id2]['yes'][] = $order_date2;
                                    }
                                }
                                if ($sales_sub2['order_quantity'] != '') {
                                    $Purchase_Ltrs += floatval($sales_sub2['order_quantity']);
                                }
                            }
                        }
                    }
                    $No_of_Visits = 0;
                    $Productive_Purchase = 0;
                    if (count($main_datas) > 0) {
                        foreach ($main_datas as $main_datas1) {
                            $No_of_Visits += count($main_datas1['date']);
                            $Productive_Purchase += count($main_datas1['yes']);
                        }
                    }
                    $ch_row = true;
                    if (!empty($pur_time)) {
                        $ch_row = ($Productive_Purchase == $pur_time);
                    }
                    if (!empty($pur_volumn)) {
                        $symbol = "";
                        if (preg_match('/([^\d]+)(\d+)/', $pur_volumn, $matches)) {
                            $symbol = $matches[1];
                        }
                        $valu1 = str_replace($symbol, "", $pur_volumn);
                        $valu2 = floatval($valu1);
                        if ($symbol == '>') {
                            $ch_row = ($Purchase_Ltrs > $valu2);
                        } elseif ($symbol == '<') {
                            $ch_row = ($Purchase_Ltrs < $valu2);
                        } elseif ($symbol == '>=') {
                            $ch_row = ($Purchase_Ltrs >= $valu2);
                        } elseif ($symbol == '<=') {
                            $ch_row = ($Purchase_Ltrs <= $valu2);
                        } else {
                            $ch_row = ($Purchase_Ltrs == $valu2);
                        }
                    }
                    if ($ch_row) {
                        $last_dt1 = SalesOrderD2Ssub::selectRaw('max(order_date) as last_dt')->whereRaw("shop_creation_id='$shop_id'")->first();
                        $last_dt = "";
                        if ($last_dt1 != null) {
                            $last_dt = ($last_dt1->last_dt != '') ? date('d-m-Y', strtotime($last_dt1->last_dt)) : "";
                        }
                        $Productive_Not_Purchase = ($No_of_Visits > $Productive_Purchase)?($No_of_Visits - $Productive_Purchase):0;

                        $key1 = "$shop_list2[shop_name];;$shop_list2[place];;$shop_list2[area_name];;$shop_list2[shops_type]";
                        if(!isset($result[$key1])){
                            $result[$key1] = [
                                'Shop_Name' => $shop_list2->shop_name,
                                'Place' => $shop_list2->place,
                                'Market' => $shop_list2->area_name,
                                'Address' => $shop_list2->address,
                                'Shop_Type' => $shop_list2->shops_type,
                                'No_of_Visits' => $No_of_Visits,
                                'Productive_Purchase' => $Productive_Purchase,
                                'Productive_Not_Purchase' => $Productive_Not_Purchase,
                                'Purchase_Ltrs' => $Purchase_Ltrs,
                                'Last_Purchase_Date' => $last_dt,
                            ];
                        }else{
                            $result[$key1]['No_of_Visits'] += $No_of_Visits;
                            $result[$key1]['Productive_Purchase'] += $Productive_Purchase;
                            $result[$key1]['Productive_Not_Purchase'] += $Productive_Not_Purchase;
                            $result[$key1]['Purchase_Ltrs'] += $Purchase_Ltrs;
                            if($result[$key1]['Last_Purchase_Date'] == ""){
                                $result[$key1]['Last_Purchase_Date'] = $last_dt;
                            }elseif($last_dt != ""){
                                $dt1 = date('d-m-Y', strtotime($result[$key1]['Last_Purchase_Date']));
                                $dt2 = date('d-m-Y', strtotime($last_dt));
                                if($dt1 < $dt2){
                                    $result[$key1]['Last_Purchase_Date'] = $last_dt;
                                }
                            }
                        }
                    }
                }
            }
            return view('Reports.shop_report.list', [
                'list' => $result,
                'from_date' => $from_date,
                'to_date' => $to_date,
                'dealer_id' => $dealer_id
            ]);

        } else if ($action == 'getSalesRef') {


            $manager_id = $request->input('manager_id');

            $sales_ref_name = SalesRepCreation::select('id', 'sales_ref_name')->where('manager_id', $manager_id)->where(function ($query) {
                $query->where('delete_status', '0')->where('status', '=', '0')->orWhereNull('delete_status');
            })->orderBy('sales_ref_name')->get();
            return response()->json($sales_ref_name);
        } else if ($action == 'getDealer') {


            $sales_ref_id = $request->input('sales_ref_id');
            $manager_id = $request->input('manager_id');

            $dealer_creation = DealerCreation::select('id', 'dealer_name')->where('sales_rep_id', $sales_ref_id)->where('manager_name', $manager_id)->where(function ($query) {
                $query->where('delete_status', '0')->where('status', '=', '1')->orWhereNull('delete_status');
            })->orderBy('dealer_name')->get();
            return response()->json($dealer_creation);
        } else if ($action == 'getitemName') {
            $group_id = $request->input('group_id');
            $get_item_name = ItemCreation::select('id', 'short_code')->where(function ($query) {
                $query->where('delete_status', '0')->orWhereNull('delete_status');
            })->orderBy('short_code');

            if (!empty($group_id)) {
                $get_item_name->where('group_id', '=', $group_id);
            }
            $item_name = $get_item_name->get();
            return response()->json($item_name);
        } else if ($action == 'getareaname') {

            $dealer_name = $request->input('dealer_name');

            $get_area_id = DealerCreation::select('id', 'area_id')
                ->where('id', $dealer_name)
                ->where(function ($query) {
                    $query->where('delete_status', '0')->where('status', '=', '1')->orWhereNull('delete_status');
                })
                ->orderBy('id')
                ->first();

            if ($get_area_id) {
                $area_ids = explode(',', $get_area_id->area_id);

                $get_area_name = MarketCreation::select('id', 'area_name')
                    ->whereIn('id', $area_ids)
                    ->get();

                return response()->json($get_area_name);
            }

        } else if ($action == 'getshoptype') {

            $area_name = $request->input('area_name');

            $get_shop_id = ShopCreation::select('shop_type_id')
                ->where('beats_id', $area_name)
                ->where(function ($query) {
                    $query->where('delete_status', '0')->orWhereNull('delete_status');
                })
                // ->orderBy('id')
                ->groupBy('shop_type_id')
                ->get();

            $resultArray = [];

            foreach ($get_shop_id as $shop) {
                $shop_type_id = $shop->shop_type_id;

                $get_shop_type = ShopsType::select('id', 'shops_type')
                    ->where('id', $shop_type_id)
                    ->get();

                foreach ($get_shop_type as $shopType) {
                    $resultArray[] = [
                        'id' => $shopType->id,
                        'shops_type' => $shopType->shops_type,
                    ];
                }
            }

            return response()->json($resultArray);

        }
    }
}
