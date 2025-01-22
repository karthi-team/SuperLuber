<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use App\Models\MarketManagerCreation;
use App\Models\SalesRepCreation;
use App\Models\DealerCreation;
use App\Models\ItemLitersType;
use App\Models\ItemPropertiesType;
use App\Models\ShopCreation;
use App\Models\MarketCreation;
use App\Models\GroupCreation;
use App\Models\ItemCreation;
use App\Models\Entry\SalesOrderD2SMain;
use App\Models\Entry\SalesOrderD2Ssub;
use Illuminate\Http\Request;
use Carbon\Carbon;

class DailySalesReportApiController extends Controller
{
    public function daily_sales_report_date_wise_dealer_api(Request $request)
    {
        $order_date = $request->input('order_date');

        $secondary_sales_main_date_wise_dealer = SalesOrderD2SMain::select('dealer_creation_id')
            ->where(function ($query) {
                $query->where('delete_status', '0')->orWhereNull('delete_status');
            })
            ->where('order_date', $order_date)
            ->distinct()
            ->get();

        $dealers = collect();
        foreach ($secondary_sales_main_date_wise_dealer as $sublist) {
            $dealer = DB::table('dealer_creation')
                ->select('id as dealer_id', 'dealer_name')
                ->where('id', $sublist->dealer_creation_id)
                ->first();

            if ($dealer && !$dealers->contains('dealer_id', $dealer->dealer_id)) {
                $dealers->push($dealer);
            }
        }

        if ($dealers->isNotEmpty()) {
            return response()->json(['status' => 'SUCCESS', 'message' => 'Secondary Dealer Name List Showed Successfully', 'secondary_sales_main_date_wise_dealer' => $dealers], 200);
        } else {
            return response()->json(['status' => 'FAILURE', 'message' => 'Secondary Dealer Name List Not Found'], 404);
        }
    }

    public function daily_sales_report_dealer_wise_market_api(Request $request)
    {
        $order_date = $request->input('order_date');
        $dealer_id = $request->input('dealer_id');

        $sales_order_d2s_main = SalesOrderD2SMain::select('sales_order_d2s_sublist.market_creation_id as market_id')
            ->join('sales_order_d2s_sublist', 'sales_order_d2s_sublist.sales_order_main_id', '=', 'sales_order_d2s_main.id')
            ->where(function ($query) {
                $query->where('sales_order_d2s_main.delete_status', '0')
                    ->orWhereNull('sales_order_d2s_main.delete_status');
            })
            ->where(function ($query) {
                $query->where('sales_order_d2s_sublist.delete_status', '0')
                    ->orWhereNull('sales_order_d2s_sublist.delete_status');
            })
            ->where('sales_order_d2s_main.order_date', $order_date)
            ->where('sales_order_d2s_main.dealer_creation_id', $dealer_id)
            ->distinct()
            ->get();

        $markets = collect();
        foreach ($sales_order_d2s_main as $sublist) {
            $market = DB::table('area_creation')
                ->select('id as market_id', 'area_name as market_name')
                ->where('id', $sublist->market_id)
                ->first();

            if ($market && !$markets->contains('market_id', $market->market_id)) {
                $markets->push($market);
            }
        }

        if ($markets->isNotEmpty()) {
            return response()->json(['status' => 'SUCCESS', 'message' => 'Secondary Market Name List Showed Successfully', 'daily_sales_report_dealer_wise_market' => $markets], 200);
        } else {
            return response()->json(['status' => 'FAILURE', 'message' => 'Secondary Dealer Name List Not Found'], 404);
        }
    }

    public function daily_sales_report_api(Request $request)
    {
        $order_date = $request->input('order_date');
        $sales_executive_id = $request->input('sales_executive_id');
        $dealer_id = $request->input('dealer_id');
        $market_id = $request->input('market_id');
        $shop_id = $request->input('shop_id');

        if (empty($order_date)) {
            return response()->json(['status' => 'FAILURE', 'message' => 'Order Date Not Found'], 404);
        }
        if (empty($sales_executive_id)) {
            return response()->json(['status' => 'FAILURE', 'message' => 'Sales Executive Id Not Found'], 404);
        }
        if (empty($dealer_id)) {
            return response()->json(['status' => 'FAILURE', 'message' => 'Dealer Id Not Found'], 404);
        }

        $secondary_sales_main_list = [];
        $secondary_sales = [];

        $secondary_sales_main = SalesOrderD2SMain::select('order_date', 'order_no', 'sales_exec', 'dealer_creation_id')
            ->where(function ($query) {
                $query->where('delete_status', '0')->orWhereNull('delete_status');
            })
            ->where('order_date', $order_date)
            ->where('sales_exec', $sales_executive_id)
            ->where('dealer_creation_id', $dealer_id)
            ->first();
        if ($secondary_sales_main) {
            $order_date = $secondary_sales_main->order_date;
            $order_number = $secondary_sales_main->order_no;
            $sales_executive_id = $secondary_sales_main->sales_exec;
            $dealer_id = $secondary_sales_main->dealer_creation_id;

            if ($secondary_sales_main) {
                $secondary_sales_main_list[] = [
                    'order_date' => $secondary_sales_main->order_date,
                    'order_number' => $secondary_sales_main->order_no,
                ];
            }
        }

        $market_manager_list = MarketManagerCreation::select('market_manager_creation.id as market_manager_id', 'market_manager_creation.manager_name as market_manager_name')
            ->join('sales_ref_creation as src', 'src.manager_id', '=', 'market_manager_creation.id')
            ->where('market_manager_creation.status1', '0')
            ->where('src.id', $sales_executive_id)
            ->where(function ($query) {
                $query->where('market_manager_creation.delete_status', '0')->orWhereNull('market_manager_creation.delete_status');
            })
            ->orderBy('market_manager_creation.manager_name')
            ->get();

        $sales_executive_list = SalesRepCreation::select('id as sales_executive_id', 'sales_ref_name as sales_executive_name')
            ->where('status', '0')
            ->where('id', $sales_executive_id)
            ->where(function ($query) {
                $query->where('delete_status', '0')->orWhereNull('delete_status');
            })
            ->orderBy('sales_ref_name')
            ->get();

        $dealer_list = DealerCreation::select('id as dealer_id', 'dealer_name')
            ->where('status', '1')
            ->where('id', $dealer_id)
            ->where(function ($query) {
                $query->where('delete_status', '0')->orWhereNull('delete_status');
            })
            ->orderBy('dealer_name')
            ->get();

        $secondary_sales_shops_query = SalesOrderD2SMain::select('sales_order_d2s_sublist.market_creation_id as market_id', 'area_creation.area_name as market_name', 'sales_order_d2s_sublist.shop_creation_id as shop_id', 'shop_creation.shop_name')
            ->leftJoin('sales_order_d2s_sublist', 'sales_order_d2s_main.id', '=', 'sales_order_d2s_sublist.sales_order_main_id')
            ->leftJoin('shop_creation', 'shop_creation.id', '=', 'sales_order_d2s_sublist.shop_creation_id')
            ->leftJoin('area_creation', 'area_creation.id', '=', 'sales_order_d2s_sublist.market_creation_id')
            ->where(function ($query) {
                $query->where('sales_order_d2s_main.delete_status', '0')->orWhereNull('sales_order_d2s_main.delete_status');
            })
            ->where(function ($query) {
                $query->where('sales_order_d2s_sublist.delete_status', '0')->orWhereNull('sales_order_d2s_sublist.delete_status');
            })
            ->where('sales_order_d2s_main.order_date', $order_date)
            ->where('sales_order_d2s_main.sales_exec', $sales_executive_id)
            ->where('sales_order_d2s_main.dealer_creation_id', $dealer_id)
            ->groupBy('sales_order_d2s_sublist.market_creation_id', 'area_creation.area_name', 'sales_order_d2s_sublist.shop_creation_id', 'shop_creation.shop_name');
        if ($market_id) {
            $secondary_sales_shops_query->where('sales_order_d2s_sublist.market_creation_id', $market_id);
        }
        if ($shop_id) {
            $secondary_sales_shops_query->where('sales_order_d2s_sublist.shop_creation_id', $shop_id);
        }
        $secondary_sales_shops = $secondary_sales_shops_query->get();

        $group_name_list = GroupCreation::select('group_creation.id as group_id', 'group_creation.group_name', 'item_creation.id as short_code_id', 'item_creation.item_name', 'item_creation.short_code')
            ->leftJoin('item_creation', 'item_creation.group_id', '=', 'group_creation.id')
            ->where(function ($query) {
                $query->where('group_creation.delete_status', '0')->orWhereNull('group_creation.delete_status');
            })
            ->where(function ($query) {
                $query->where('item_creation.delete_status', '0')->orWhereNull('item_creation.delete_status');
            })
            ->orderBy('group_name')
            ->get();

        foreach ($secondary_sales_shops as $secondary_sales_shop) {
            $shop_id = $secondary_sales_shop->shop_id;
            $shop_name = $secondary_sales_shop->shop_name;

            foreach ($group_name_list as $group_name_obj) {
                $group_id = $group_name_obj->group_id;
                $group_name = $group_name_obj->group_name;
                $item_name = $group_name_obj->item_name;

                $short_code_id = $group_name_obj->short_code_id;
                $short_code = $group_name_obj->short_code;

                $secondary_sales_query = SalesOrderD2SMain::select(DB::raw('SUM(sales_order_d2s_sublist.order_quantity) as total_order_quantity'), DB::raw('SUM(sales_order_d2s_sublist.pieces_quantity) as total_pieces_quantity'), 'sales_order_d2s_sublist.item_price', DB::raw('SUM(sales_order_d2s_sublist.total_amount) as total_amount'), 'sales_order_d2s_sublist.scheme')
                    ->leftJoin('sales_order_d2s_sublist', 'sales_order_d2s_main.id', '=', 'sales_order_d2s_sublist.sales_order_main_id')
                    ->where(function ($query) {
                        $query->where('sales_order_d2s_main.delete_status', '0')->orWhereNull('sales_order_d2s_main.delete_status');
                    })
                    ->where(function ($query) {
                        $query->where('sales_order_d2s_sublist.delete_status', '0')->orWhereNull('sales_order_d2s_sublist.delete_status');
                    })
                    ->where('sales_order_d2s_main.order_date', $order_date)
                    ->where('sales_order_d2s_main.sales_exec', $sales_executive_id)
                    ->where('sales_order_d2s_main.dealer_creation_id', $dealer_id)
                    ->where('sales_order_d2s_sublist.shop_creation_id', $shop_id)
                    ->where('sales_order_d2s_sublist.group_creation_id', $group_id)
                    ->where('sales_order_d2s_sublist.item_creation_id', $short_code_id)
                    ->groupBy('sales_order_d2s_sublist.item_price', 'sales_order_d2s_sublist.scheme');
                if ($market_id) {
                    $secondary_sales_query->where('sales_order_d2s_sublist.market_creation_id', $market_id);
                }
                if ($shop_id) {
                    $secondary_sales_query->where('sales_order_d2s_sublist.shop_creation_id', $shop_id);
                }
                $secondary_sale = $secondary_sales_query->first();

                if ($secondary_sale) {
                    $secondary_sales[] = [
                        'shop_id' => $shop_id,
                        'shop_name' => $shop_name,
                        'group_id' => $group_id,
                        'group_name' => $group_name,
                        'item_name' => $item_name,
                        'short_code_id' => $short_code_id,
                        'short_code' => $short_code,
                        'order_quantity' => $secondary_sale->total_order_quantity,
                        'pieces_quantity' => $secondary_sale->total_pieces_quantity,
                        'item_price' => $secondary_sale->item_price,
                        'total_amount' => $secondary_sale->total_amount,
                        'scheme' => $secondary_sale->scheme,
                    ];
                }
            }
        }

        if (!empty($secondary_sales_main)) {
            return response()->json(['status' => 'SUCCESS', 'message' => 'Secondary List Showed Successfully', 'secondary_sales_main' => $secondary_sales_main_list, 'market_manager_list' => $market_manager_list, 'sales_executive' => $sales_executive_list, 'dealer_list' => $dealer_list, 'secondary_sales_shops' => $secondary_sales_shops, 'secondary_sales' => $secondary_sales], 200);
        } else {
            return response()->json(['status' => 'FAILURE', 'message' => 'Secondary List Not Found'], 404);
        }
    }

    public function daily_sales_report_pdf_api(Request $request)
    {
        $order_date = $request->input('order_date');
        $sales_executive_id = $request->input('sales_executive_id');
        $dealer_id = $request->input('dealer_id');
        $market_id = $request->input('market_id');
        $shop_id = $request->input('shop_id');

        if (empty($order_date)) {
            return response()->json(['status' => 'FAILURE', 'message' => 'Secondary Sales Order Date Not Found'], 404);
        }

        $sub_tb = (new SalesOrderD2Ssub)->getTable();
        $ItemCreation_tb = (new ItemCreation)->getTable();
        $ItemPropertiesType_tb = (new ItemPropertiesType)->getTable();
        $ItemLitersType_tb = (new ItemLitersType)->getTable();
        $MarketCreation_tb = (new MarketCreation)->getTable();
        $ShopCreation_tb = (new ShopCreation)->getTable();
        $GroupCreation_tb = (new GroupCreation)->getTable();

        $sales_order_d2s_main_shop_1_query = SalesOrderD2SMain::select('id')
            ->where(function ($query) {
                $query->where('delete_status', '0')
                    ->orWhereNull('delete_status');
            })
            ->where('order_date', $order_date)
            ->where('sales_exec', $sales_executive_id);
        if ($dealer_id) {
            $sales_order_d2s_main_shop_1_query->where('dealer_creation_id', $dealer_id);
        }
        $sales_order_d2s_main_shop_1 = $sales_order_d2s_main_shop_1_query->first();

        $main_id = $sales_order_d2s_main_shop_1->id ?? 0;

        $sub_tb = (new SalesOrderD2Ssub)->getTable();
        $ItemCreation_tb = (new ItemCreation)->getTable();
        $ItemPropertiesType_tb = (new ItemPropertiesType)->getTable();
        $ItemLitersType_tb = (new ItemLitersType)->getTable();
        $MarketCreation_tb = (new MarketCreation)->getTable();
        $ShopCreation_tb = (new ShopCreation)->getTable();
        $GroupCreation_tb = (new GroupCreation)->getTable();

        $sales_order_d2s_main_shop = SalesOrderD2SMain::select('dealer_creation.id as dealer_id', 'dealer_creation.dealer_name', 'area_creation.id as market_id', 'area_creation.area_name as market_name', 'shop_creation.id as shop_id', 'shop_creation.shop_name', 'shop_creation.whatsapp_no as shop_mobile_no', 'shop_creation.address as shop_address')
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
            ->groupBy('dealer_creation.id', 'dealer_creation.dealer_name', 'area_creation.id', 'area_creation.area_name', 'shop_creation.id', 'shop_creation.shop_name', 'shop_creation.whatsapp_no', 'shop_creation.address')
            ->orderBy('area_creation.id')
            ->get();

        $secondary_sales_form_sublist_shop = [];

        foreach ($sales_order_d2s_main_shop as $sales_order_d2s_main_shop_1) {
            $dealer_id = $sales_order_d2s_main_shop_1->dealer_id ?? 0;
            $dealer_name = $sales_order_d2s_main_shop_1->dealer_name ?? 0;
            $market_id = $sales_order_d2s_main_shop_1->market_id ?? 0;
            $market_name = $sales_order_d2s_main_shop_1->market_name ?? 0;
            $shop_id = $sales_order_d2s_main_shop_1->shop_id ?? 0;
            $shop_name = $sales_order_d2s_main_shop_1->shop_name ?? 0;
            $shop_mobile_no = $sales_order_d2s_main_shop_1->shop_mobile_no ?? 0;
            $shop_address = $sales_order_d2s_main_shop_1->shop_address ?? 0;

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

                $secondary_sales_form_sublist = DB::select('select id as secondary_sales_sublist_id, sales_order_main_id as secondary_sales_main_id, status_check as order_status, ' . $sub_tb . '.item_creation_id as item_id, COALESCE((select item_name from ' . $ItemCreation_tb . ' where id=' . $sub_tb . '.item_creation_id),"-") as item_name, ' . $sub_tb . '.short_code_id, COALESCE((select short_code from ' . $ItemCreation_tb . ' where id=' . $sub_tb . '.short_code_id),"-") as item_short_code, ' . $sub_tb . '.group_creation_id, COALESCE((select group_name from ' . $GroupCreation_tb . ' where id=' . $sub_tb . '.group_creation_id),"-") as group_name, ' . $sub_tb . '.market_creation_id as market_id, (select area_name FROM ' . $MarketCreation_tb . ' where id=' . $sub_tb . '.market_creation_id) as market_name, ' . $sub_tb . '.shop_creation_id as shop_id, (select secondary_image from ' . $ShopCreation_tb . ' where id=' . $sub_tb . '.shop_creation_id) as secondary_image, (select shop_name from ' . $ShopCreation_tb . ' where id=' . $sub_tb . '.shop_creation_id) as shop_name, ' . $sub_tb . '.item_property as item_packing_type_id, COALESCE((select item_properties_type from ' . $ItemPropertiesType_tb . ' where id=' . $sub_tb . '.item_property),"-") as item_packing_type, ' . $sub_tb . '.item_weights as item_uom_id, COALESCE((select item_liters_type from ' . $ItemLitersType_tb . ' where id = ' . $sub_tb . '.item_weights),"-") as item_uom, current_stock, order_quantity, pieces_quantity as pieces_count, item_price, total_amount, scheme from ' . $sub_tb . ' where sales_order_main_id = ' . $main_id . ' and shop_creation_id = ' . $shop_id . ' and group_creation_id = ' . $group_id . ' and (delete_status = 0 or delete_status is null)');

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
                        'secondary_sales_main_id' => $secondary_sales->secondary_sales_main_id,
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
                        'opening_stock' => $secondary_sales->current_stock,
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
                'shop_address' => $shop_address,
                'shop_mobile_no' => strval($shop_mobile_no ?? "-"),
                'secondary_sales_shopwise_list' => $secondary_sales_form_sublist_group
            ];
        }

        return response()->json(['status' => 'SUCCESS', 'message' => 'Secondary Sales Sublist Showed Successfully', 'sales_order_d2s_main_shop' => $sales_order_d2s_main_shop, 'secondary_sales_form_sublist' => $secondary_sales_form_sublist_shop], 200);
    }

    public function daily_sales_report_shop_pdf_api(Request $request)
    {
        $order_date = $request->input('order_date');
        $sales_executive_id = $request->input('sales_executive_id');
        $dealer_id = $request->input('dealer_id');
        $market_id = $request->input('market_id');
        $shop_id = $request->input('shop_id');

        if (empty($order_date)) {
            return response()->json(['status' => 'FAILURE', 'message' => 'Secondary Sales Order Date Not Found'], 404);
        }

        $sub_tb = (new SalesOrderD2Ssub)->getTable();
        $ItemCreation_tb = (new ItemCreation)->getTable();
        $ItemPropertiesType_tb = (new ItemPropertiesType)->getTable();
        $ItemLitersType_tb = (new ItemLitersType)->getTable();
        $MarketCreation_tb = (new MarketCreation)->getTable();
        $ShopCreation_tb = (new ShopCreation)->getTable();
        $GroupCreation_tb = (new GroupCreation)->getTable();

        $sales_order_d2s_main_shop_1_query = SalesOrderD2SMain::select('id')
            ->where(function ($query) {
                $query->where('delete_status', '0')
                    ->orWhereNull('delete_status');
            })
            ->where('order_date', $order_date)
            ->where('sales_exec', $sales_executive_id);
        if ($dealer_id) {
            $sales_order_d2s_main_shop_1_query->where('dealer_creation_id', $dealer_id);
        }
        $sales_order_d2s_main_shop_1 = $sales_order_d2s_main_shop_1_query->first();

        $main_id = $sales_order_d2s_main_shop_1->id ?? 0;

        $sub_tb = (new SalesOrderD2Ssub)->getTable();
        $ItemCreation_tb = (new ItemCreation)->getTable();
        $ItemPropertiesType_tb = (new ItemPropertiesType)->getTable();
        $ItemLitersType_tb = (new ItemLitersType)->getTable();
        $MarketCreation_tb = (new MarketCreation)->getTable();
        $ShopCreation_tb = (new ShopCreation)->getTable();
        $GroupCreation_tb = (new GroupCreation)->getTable();

        $sales_order_d2s_main_shop = SalesOrderD2SMain::select('dealer_creation.id as dealer_id', 'dealer_creation.dealer_name', 'area_creation.id as market_id', 'area_creation.area_name as market_name', 'shop_creation.id as shop_id', 'shop_creation.shop_name', 'shop_creation.whatsapp_no as shop_mobile_no', 'shop_creation.address as shop_address')
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
            ->where('sales_order_d2s_sublist.shop_creation_id', $shop_id)
            ->groupBy('dealer_creation.id', 'dealer_creation.dealer_name', 'area_creation.id', 'area_creation.area_name', 'shop_creation.id', 'shop_creation.shop_name', 'shop_creation.whatsapp_no', 'shop_creation.address')
            ->orderBy('area_creation.id')
            ->get();

        $secondary_sales_form_sublist_shop = [];

        foreach ($sales_order_d2s_main_shop as $sales_order_d2s_main_shop_1) {
            $dealer_id = $sales_order_d2s_main_shop_1->dealer_id ?? 0;
            $dealer_name = $sales_order_d2s_main_shop_1->dealer_name ?? 0;
            $market_id = $sales_order_d2s_main_shop_1->market_id ?? 0;
            $market_name = $sales_order_d2s_main_shop_1->market_name ?? 0;
            $shop_id = $sales_order_d2s_main_shop_1->shop_id ?? 0;
            $shop_name = $sales_order_d2s_main_shop_1->shop_name ?? 0;
            $shop_mobile_no = $sales_order_d2s_main_shop_1->shop_mobile_no ?? 0;
            $shop_address = $sales_order_d2s_main_shop_1->shop_address ?? 0;

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

                $secondary_sales_form_sublist = DB::select('select id as secondary_sales_sublist_id, status_check as order_status, ' . $sub_tb . '.item_creation_id as item_id, COALESCE((select item_name from ' . $ItemCreation_tb . ' where id=' . $sub_tb . '.item_creation_id),"-") as item_name, ' . $sub_tb . '.short_code_id, COALESCE((select short_code from ' . $ItemCreation_tb . ' where id=' . $sub_tb . '.short_code_id),"-") as item_short_code, ' . $sub_tb . '.group_creation_id, COALESCE((select group_name from ' . $GroupCreation_tb . ' where id=' . $sub_tb . '.group_creation_id),"-") as group_name, ' . $sub_tb . '.market_creation_id as market_id, (select area_name FROM ' . $MarketCreation_tb . ' where id=' . $sub_tb . '.market_creation_id) as market_name, ' . $sub_tb . '.shop_creation_id as shop_id, (select secondary_image from ' . $ShopCreation_tb . ' where id=' . $sub_tb . '.shop_creation_id) as secondary_image, (select shop_name from ' . $ShopCreation_tb . ' where id=' . $sub_tb . '.shop_creation_id) as shop_name, ' . $sub_tb . '.item_property as item_packing_type_id, COALESCE((select item_properties_type from ' . $ItemPropertiesType_tb . ' where id=' . $sub_tb . '.item_property),"-") as item_packing_type, ' . $sub_tb . '.item_weights as item_uom_id, COALESCE((select item_liters_type from ' . $ItemLitersType_tb . ' where id = ' . $sub_tb . '.item_weights),"-") as item_uom, current_stock, order_quantity, pieces_quantity as pieces_count, item_price, total_amount, scheme from ' . $sub_tb . ' where sales_order_main_id = ' . $main_id . ' and group_creation_id = ' . $group_id . ' and shop_creation_id = ' . $shop_id . ' and (delete_status = 0 or delete_status is null)');

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
                        'opening_stock' => $secondary_sales->current_stock,
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
                'shop_address' => $shop_address ?? "-",
                'shop_mobile_no' => strval($shop_mobile_no ?? "-"),
                'secondary_sales_shopwise_list' => $secondary_sales_form_sublist_group
            ];
        }

        return response()->json(['status' => 'SUCCESS', 'message' => 'Secondary Sales Sublist Showed Successfully', 'sales_order_d2s_main_shop' => $sales_order_d2s_main_shop, 'secondary_sales_form_sublist' => $secondary_sales_form_sublist_shop], 200);
    }
}
