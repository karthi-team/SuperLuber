<?php

namespace App\Http\Controllers\Map;

use App\Http\Controllers\Controller;
use App\Models\Entry\SalesOrderD2SMain;
use App\Models\Entry\SalesOrderD2Ssub;
use App\Models\SalesExecutiveTimelogs;
use App\Models\MarketManagerCreation;
use App\Models\SalesRepCreation;
use App\Models\DealerCreation;
use App\Models\MarketCreation;
use App\Models\ShopsType;
use App\Models\ShopCreation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class MarketViewController extends Controller
{
    public function market_view()
    {
        $manager_creation=MarketManagerCreation::select('id','manager_name')->where('status1','0')->where('delete_status', '0')->orWhereNull('delete_status')->orderBy('manager_no')->get();
        return view('Map.market_view.admin', ['manager_creation' => $manager_creation]);
    }

    public function db_cmd(Request $request)
    {
        $action=$request->input('action');

        if ($action == 'filter_market_view') {

            $manager_id = $request->input('manager_id');
            $sales_rep_id = $request->input('sales_rep_id');
            $dealer_id = $request->input('dealer_id');
            $market_id = $request->input('market_id');
            $shop_type_id = $request->input('shop_type_id');
            $shop_id = $request->input('shop_id');

            $data = [];

            $sales_rep_query = SalesRepCreation::select(
                'sales_ref_creation.id as sales_executive_id',
                'sales_ref_creation.sales_ref_name',
                'dealer_creation.id as dealer_id',
                'dealer_creation.dealer_name',
                'dealer_creation.area_id'
            )
            ->join('dealer_creation', 'dealer_creation.sales_rep_id', '=', 'sales_ref_creation.id')
            ->where(function ($query) {
                $query->where('sales_ref_creation.delete_status', '0')
                    ->orWhereNull('sales_ref_creation.delete_status');
            })
            ->where('sales_ref_creation.status', '0');

            if ($manager_id) {
                $sales_rep_query->where('sales_ref_creation.manager_id', $manager_id);
            }
            if ($sales_rep_id) {
                $sales_rep_query->where('sales_ref_creation.id', $sales_rep_id);
            }
            if ($dealer_id) {
                $sales_rep_query->where('dealer_creation.id', $dealer_id);
            }

            $sales_reps = $sales_rep_query->get();

            foreach ($sales_reps as $rep) {
                $sales_executive_id = $rep->sales_executive_id;
                $sales_executive_name = $rep->sales_ref_name;
                $dealer_id = $rep->dealer_id;
                $dealer_name = $rep->dealer_name;
                $market_ids = explode(",", $rep->area_id);

                foreach ($market_ids as $marketId) {

                    if ($marketId == $market_id){
                        $area = MarketCreation::find($marketId);
                        if ($area) {
                            $area_id = $area->id;
                            $area_name = $area->area_name;

                            $shop_query = ShopCreation::select(
                                    'shop_creation.id as shop_id',
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
                                ->where('shop_creation.longitude', '!=', '');

                            if ($dealer_id) {
                                $shop_query->where('shop_creation.dealer_id', $dealer_id);
                            }
                            if ($area_id) {
                                $shop_query->where('shop_creation.beats_id', $area_id);
                            }
                            if ($shop_type_id) {
                                $shop_query->where('shop_creation.shop_type_id', $shop_type_id);
                            }
                            if ($shop_id) {
                                $shop_query->where('shop_creation.id', $shop_id);
                            }

                            $shop_list = $shop_query->get();

                            $data[] = [
                                'sales_executive_id' => $sales_executive_id,
                                'sales_executive_name' => $sales_executive_name,
                                'dealer_id' => $dealer_id,
                                'dealer_name' => $dealer_name,
                                'market_id' => $area_id,
                                'market_name' => $area_name,
                                'shop_list' => $shop_list,
                            ];
                        }
                    } elseif ($market_id == '') {
                        $area = MarketCreation::find($marketId);
                        if ($area) {
                            $area_id = $area->id;
                            $area_name = $area->area_name;

                            $shop_query = ShopCreation::select(
                                    'shop_creation.id as shop_id',
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
                                ->where('shop_creation.longitude', '!=', '');

                            if ($dealer_id) {
                                $shop_query->where('shop_creation.dealer_id', $dealer_id);
                            }
                            if ($area_id) {
                                $shop_query->where('shop_creation.beats_id', $area_id);
                            }
                            if ($shop_type_id) {
                                $shop_query->where('shop_creation.shop_type_id', $shop_type_id);
                            }
                            if ($shop_id) {
                                $shop_query->where('shop_creation.id', $shop_id);
                            }

                            $shop_list = $shop_query->get();

                            $data[] = [
                                'sales_executive_id' => $sales_executive_id,
                                'sales_executive_name' => $sales_executive_name,
                                'dealer_id' => $dealer_id,
                                'dealer_name' => $dealer_name,
                                'market_id' => $area_id,
                                'market_name' => $area_name,
                                'shop_list' => $shop_list,
                            ];
                        }
                    }
                }
            }

            return response()->json($data);


        } elseif ($action == 'market_view_shop_list') {

            // $current_date = date('y-m-d');

            // $secondary_sales_asc = SalesOrderD2SMain::select('sales_order_d2s_sublist.closing_time_sub')
            // ->leftJoin('sales_order_d2s_sublist', 'sales_order_d2s_main.id', '=', 'sales_order_d2s_sublist.sales_order_main_id')
            // ->where('sales_order_d2s_main.sales_exec', $sales_executive_id)
            // ->where('sales_order_d2s_main.order_date', $current_date)
            // ->orderBy('sales_order_d2s_sublist.id','asc')
            // ->first();

            $shop_list = ShopCreation::select('shop_creation.id as shop_id', 'shop_creation.shop_name', 'dealer_creation.dealer_name', 'shop_creation.mobile_no', 'shops_type.shops_type', 'area_creation.area_name',  DB::raw('COALESCE(shop_creation.address, "-") as address'), 'shop_creation.latitude', 'shop_creation.longitude', 'shop_creation.image_name')
            ->join('shops_type', 'shops_type.id', '=', 'shop_creation.shop_type_id')
            ->join('area_creation', 'area_creation.id', '=', 'shop_creation.beats_id')
            ->join('dealer_creation', 'dealer_creation.id', '=', 'shop_creation.dealer_id')
            ->where('shop_creation.latitude', '!=', '')
            ->where('shop_creation.longitude', '!=', '')
            ->orderBy('shop_creation.id')
            ->get();

            return response()->json($shop_list);

        } else if ($action == 'get_sales_ref') {

            $manager_id= $request->input('manager_id');

            $sales_rep_name = SalesRepCreation::select('id', 'sales_ref_name')
            ->where(function ($query) {
                $query->where('delete_status', '0')->orWhereNull('delete_status');
            })
            ->where('manager_id', $manager_id)
            ->where('status', '0')
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

        } else if ($action == 'get_shop_type') {

            $shop_type = ShopsType::select('id', 'shops_type')
            ->where(function ($query) {
                $query->where('delete_status', '0')->orWhereNull('delete_status');
            })
            ->get();

            return response()->json($shop_type);

        } else if ($action == 'get_shop_name') {

            $market_id = $request->input('market_id');
            $shop_type_id = $request->input('shop_type_id');

            $shop_name_query = ShopCreation::select('id', 'shop_name')
            ->where(function ($query) {
                $query->where('delete_status', '0')->orWhereNull('delete_status');
            })
            ->groupBy('id', 'shop_name');
            if($market_id != '') {
                $shop_name_query->where('beats_id', $market_id);
            }
            if($shop_type_id != '') {
                $shop_name_query->where('shop_type_id', $shop_type_id);
            }
            $shop_name = $shop_name_query->get();

            return response()->json($shop_name);
        }
    }
}
