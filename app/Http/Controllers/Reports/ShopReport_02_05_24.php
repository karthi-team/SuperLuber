<?php

namespace App\Http\Controllers\Reports;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ShopCreation;
use App\Models\DealerCreation;
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
        $market_creation=MarketCreation::select('id','area_name')->where(function ($query) {
            $query->where('delete_status', '0')->orWhereNull('delete_status');
        })->get();

        $shop_type = ShopsType::select('id','shops_type')->where(function ($query) {
            $query->where('delete_status', '0')->orWhereNull('delete_status');
        })->get();

        $dealer_list = DealerCreation::select('id','dealer_name')->where(function ($query) {
            $query->where('delete_status', '0')->orWhereNull('delete_status');
        })->get();

        $group_creation=GroupCreation::select('id','group_name')->where(function($query){$query->where('delete_status', '0')->orWhereNull('delete_status');})->orderBy('group_name')->get();

        $item_creation=ItemCreation::select('id','short_code')->where(function($query){$query->where('delete_status', '0')->orWhereNull('delete_status');})->orderBy('short_code')->get();


        return view('Reports.shop_report.admin', [
            'market_creation'=>$market_creation,
            'shop_type'=>$shop_type,
            'dealer_list' => $dealer_list,
            'group_creation' => $group_creation,
            'item_creation'=>$item_creation,
            

        ]);
    }

    public function db_cmd(Request $request)
    {
        $action=$request->input('action');

        if($action=='retrieve')
        {
            $from_date = $request->input('from_date');
            $to_date = $request->input('to_date');
            $dealer_id = $request->input('dealer_id');
            $area_id = $request->input('area_id');
            $shop_type_id = $request->input('shop_type_id');
            $pur_time = $request->input('pur_time');
            $group_id = $request->input('group_id');
            $item_id = $request->input('item_id');
            $pur_volumn = $request->input('pur_volumn');

            $pattern = '/([^\d]+)(\d+)/';

            
            if (preg_match($pattern, $pur_volumn, $matches)) {
                
                $nonDigitPart = $matches[1];
                $numericPart = $matches[2];

            } 

            
            $query = DB::table('sales_order_d2s_main as d2sm')
            ->select(
                'sc.shop_name',
                'sc.address',
                'st.shops_type',
                'dc.place',
                DB::raw('(SELECT count(order_quantity) FROM sales_order_d2s_sublist WHERE shop_creation_id = sc.id) as order_quantity'),
                DB::raw('(SELECT SUM(order_quantity) FROM sales_order_d2s_sublist WHERE shop_creation_id = sc.id) as pieces_quantity'),
                DB::raw('(SELECT MAX(order_date) FROM sales_order_d2s_sublist WHERE shop_creation_id = sc.id) as order_date')
            )
            ->crossjoin('shop_creation as sc')
            ->leftJoin('dealer_creation as dc', 'sc.dealer_id', '=', 'dc.id')
            ->leftJoin('sales_order_d2s_sublist as d2sub', 'd2sm.id', '=', 'd2sub.sales_order_main_id')
            ->leftJoin('shops_type as st', 'st.id', '=', 'sc.shop_type_id')
            ->where(function ($query) {
                $query->whereNull('d2sm.delete_status')
                    ->orWhere('d2sm.delete_status', 0);
            })
            ->whereNotNull('sc.shop_name')
            ->groupBy('sc.id', 'sc.shop_name', 'sc.address','dc.place', 'st.shops_type')
            ->orderBy('order_date', 'desc');
        


            if (empty(($request->input('from_date')))) { 
                $from_date = date('2022-01').'-'.'01';
                $to_date = date('Y-m-t');
                
            }else{
                $from_date =  $request->input('from_date');
                $to_date =  $request->input('to_date');
            }

            if (!empty($from_date)) {
                $query->where('d2sub.order_date', '>=', $from_date);
                // $query->where('sc.entry_date', '>=', $from_date);
            }

            if (!empty($to_date)) {
                $query->where('d2sub.order_date', '<=', $to_date);
                // $query->where('sc.entry_date', '<=', $to_date);
            }

            if (!empty($dealer_id)) {
                $query->where('sc.dealer_id', $dealer_id);
            }

            if (!empty($area_id)) {
                $query->where('sc.beats_id', $area_id);
            }

            if (!empty($shop_type_id)) {
                $query->where('st.id', $shop_type_id);
            }
            
            if (!is_null($pur_time) && $pur_time !== '') {
                $query->having('order_quantity', $pur_time);
            } elseif ($pur_time === 0) {
                $query->having('order_quantity', 0);
            }

            
            if(!empty($group_id)) {
               
                $query = DB::table('sales_order_d2s_main as d2sm')
                ->select('sc.shop_name',
                'sc.address',
                'st.shops_type',
                'dc.place',
                DB::raw("(SELECT count(order_quantity) FROM sales_order_d2s_sublist WHERE shop_creation_id = sc.id and group_creation_id = $group_id) as order_quantity"),
                        DB::raw("(SELECT SUM(order_quantity) FROM sales_order_d2s_sublist WHERE shop_creation_id = sc.id and group_creation_id = $group_id) as pieces_quantity"),
                        DB::raw("(SELECT MAX(order_date) FROM sales_order_d2s_sublist WHERE shop_creation_id = sc.id and group_creation_id = $group_id) as order_date")
                )
                ->leftJoin('sales_order_d2s_sublist as d2sub', 'd2sm.id', '=', 'd2sub.sales_order_main_id')
                ->leftJoin('shop_creation as sc', 'sc.id', '=', 'd2sub.shop_creation_id')
                ->leftJoin('dealer_creation as dc', 'sc.dealer_id', '=', 'dc.id')
                ->leftJoin('shops_type as st', 'st.id', '=', 'sc.shop_type_id')
                ->where(function ($query) {
                    $query->whereNull('d2sm.delete_status')
                          ->orWhere('d2sm.delete_status', 0); 
                })
                ->groupBy('sc.id', 'sc.shop_name', 'sc.address', 'dc.place', 'st.shops_type', 'd2sub.order_date')
                ->orderBy('d2sub.order_date', 'desc');

                
                $query->where('d2sub.group_creation_id', $group_id);

                if (!empty($dealer_id)) {
                    $query->where('sc.dealer_id', $dealer_id);
                }
                if (!empty($area_id)) {
                    $query->where('sc.beats_id', $area_id);
                }
                if (!empty($shop_type_id)) {
                    $query->where('st.id', $shop_type_id);
                }
                if (!is_null($pur_time) && $pur_time !== '') {
                    $query->having('order_quantity', $pur_time);
                } elseif ($pur_time === 0) {
                    $query->having('order_quantity', 0);
                }
            


            if (!empty($item_id)) {
                $query = DB::table('sales_order_d2s_main as d2sm')
                    ->select('sc.shop_name',
                        'sc.address',
                        'st.shops_type',
                        'dc.place',
                        DB::raw("(SELECT count(order_quantity) FROM sales_order_d2s_sublist WHERE shop_creation_id = sc.id and item_creation_id = $item_id) as order_quantity"),
                        DB::raw("(SELECT SUM(order_quantity) FROM sales_order_d2s_sublist WHERE shop_creation_id = sc.id and item_creation_id = $item_id) as pieces_quantity"),
                        DB::raw("(SELECT MAX(order_date) FROM sales_order_d2s_sublist WHERE shop_creation_id = sc.id and item_creation_id = $item_id) as order_date")
                    )
                    ->leftJoin('sales_order_d2s_sublist as d2sub', 'd2sm.id', '=', 'd2sub.sales_order_main_id')
                    ->leftJoin('shop_creation as sc', 'sc.id', '=', 'd2sub.shop_creation_id')
                    ->leftJoin('dealer_creation as dc', 'sc.dealer_id', '=', 'dc.id')
                    ->leftJoin('shops_type as st', 'st.id', '=', 'sc.shop_type_id')
                    ->where(function ($query) {
                        $query->whereNull('d2sm.delete_status')
                            ->orWhere('d2sm.delete_status', 0);
                    })
                    ->groupBy('sc.id', 'sc.shop_name', 'sc.address', 'dc.place', 'st.shops_type', 'd2sub.order_date')
                    ->orderBy('d2sub.order_date', 'desc');

            
                $query->where('d2sub.item_creation_id', $item_id);

                if (!empty($dealer_id)) {
                    $query->where('sc.dealer_id', $dealer_id);
                }
                if (!empty($area_id)) {
                    $query->where('sc.beats_id', $area_id);
                }
                if (!empty($shop_type_id)) {
                    $query->where('st.id', $shop_type_id);
                }
                if (!is_null($pur_time) && $pur_time !== '') {
                    $query->having('order_quantity', $pur_time);
                } elseif ($pur_time === 0) {
                    $query->having('order_quantity', 0);
                }
                
            }
        }
            
            if (!empty($pur_volumn)) {

                $query = DB::table('sales_order_d2s_main as d2sm')
                ->select('sc.shop_name',
                    'sc.address',
                    'st.shops_type',
                    'dc.place',
                    DB::raw("(SELECT count(order_quantity) FROM sales_order_d2s_sublist WHERE shop_creation_id = sc.id and order_quantity $nonDigitPart $numericPart) as order_quantity"),
                    DB::raw("(SELECT SUM(order_quantity) FROM sales_order_d2s_sublist WHERE shop_creation_id = sc.id and order_quantity $nonDigitPart $numericPart) as pieces_quantity"),
                    DB::raw("(SELECT MAX(order_date) FROM sales_order_d2s_sublist WHERE shop_creation_id = sc.id and order_quantity $nonDigitPart $numericPart) as order_date")
                )
                ->leftJoin('sales_order_d2s_sublist as d2sub', 'd2sm.id', '=', 'd2sub.sales_order_main_id')
                ->leftJoin('shop_creation as sc', 'sc.id', '=', 'd2sub.shop_creation_id')
                ->leftJoin('dealer_creation as dc', 'sc.dealer_id', '=', 'dc.id')
                ->leftJoin('shops_type as st', 'st.id', '=', 'sc.shop_type_id')
                ->where(function ($query) {
                    $query->whereNull('d2sm.delete_status')
                        ->orWhere('d2sm.delete_status', 0);
                })
                ->groupBy('sc.id', 'sc.shop_name', 'sc.address', 'dc.place', 'st.shops_type', 'd2sub.order_date')
                ->orderBy('d2sub.order_date', 'desc');


                $query->where('pieces_quantity', $nonDigitPart, $numericPart);

                if (!empty($dealer_id)) {
                    $query->where('sc.dealer_id', $dealer_id);
                }
                if (!empty($area_id)) {
                    $query->where('sc.beats_id', $area_id);
                }
                if (!empty($shop_type_id)) {
                    $query->where('st.id', $shop_type_id);
                }

                if (!empty($group_id)) {
                    $query->where('d2sub.group_creation_id', $group_id);
                }
            
                if (!empty($item_id)) {
                    $query->where('d2sub.item_creation_id', $item_id);
                }

                if (!is_null($pur_time) && $pur_time !== '') {
                    $query->having('order_quantity', $pur_time);
                } elseif ($pur_time === 0) {
                    $query->having('order_quantity', 0);
                }

                
            }

            if($from_date==''){
                $from_date = date('Y-m').'-'.'01';
            }
            if($to_date==''){
                $to_date = date('Y-m-t');
            }

            $result = $query->get();

            return view('Reports.shop_report.list',['list'=> $result,'from_date'=>$from_date,
            'to_date'=>$to_date ,'dealer_id'=> $dealer_id]);

        }

        else if($action == 'getitemName') 
        {
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

        else if($action == 'getareaname') 
        {
            // $dealer_name = $request->input('dealer_name');

            //  $get_area_id = DealerCreation::select('id', 'area_id')->where('id',$dealer_name)
            //  ->where(function ($query) {
            //     $query->where('delete_status', '0')->orWhereNull('delete_status');
            // })->orderBy('id')->first();

            // $get_areaid = $get_area_id->area_id;

            // $get_area_name = MarketCreation::select('id','area_name')->where('id',$get_areaid)->get();
        
            $dealer_name = $request->input('dealer_name');

            $get_area_id = DealerCreation::select('id', 'area_id')
            ->where('id', $dealer_name)
            ->where(function ($query) {
                $query->where('delete_status', '0')->orWhereNull('delete_status');
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

        }

        else if($action == 'getshoptype') 
        {

            $area_name = $request->input('area_name');

            $get_shop_id = ShopCreation::select( 'shop_type_id')
                ->where('beats_id', $area_name)
                ->where(function ($query) {
                    $query->where('delete_status', '0')->orWhereNull('delete_status');
                })
                
                ->groupBy( 'shop_type_id')
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
