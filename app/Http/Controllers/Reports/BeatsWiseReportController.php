<?php

namespace App\Http\Controllers\Reports;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Models\MarketCreation;
use App\Models\DealerCreation;
use App\Models\ShopCreation;
use App\Models\ShopsType;
use Carbon\Carbon;

class BeatsWiseReportController extends Controller
{
    public function beats_wise_report()
    {
        $dealer_creation = DealerCreation::select('id', 'dealer_name')
            ->where('dealer_name', '!=', '')
            ->where('status', '1')
            ->where(function ($query) {
                $query->where('delete_status', '0')->orWhereNull('delete_status');
            })
            ->orderBy('dealer_name')
            ->get();

        $market_creation = MarketCreation::select('id', 'area_name')
            ->where('area_name', '!=', '')
            ->where(function ($query) {
                $query->where('delete_status', '0')->orWhereNull('delete_status');
            })
            ->orderBy('area_name')
            ->get();

        $shop_creation = ShopCreation::select('id', 'shop_name')
            ->where('shop_name', '!=', '')
            ->where(function ($query) {
                $query->where('delete_status', '0')->orWhereNull('delete_status');
            })
            ->orderBy('shop_name')
            ->get();

        $shop_type = ShopsType::select('id', 'shops_type')
            ->where('shops_type', '!=', '')
            ->where(function ($query) {
                $query->where('delete_status', '0')->orWhereNull('delete_status');
            })
            ->orderBy('shops_type')
            ->get();

        return view('Reports.beats_wise_report.admin', [
            'dealer_creation' => $dealer_creation,
            'market_creation' => $market_creation,
            'shop_creation' => $shop_creation,
            'shop_type' => $shop_type
        ]);
    }

    public function retrieve($from_date, $to_date, $dealer_id, $beats_id, $shop_id, $shop_type)
    {
        $ShopController_td = (new ShopCreation)->getTable();
        $DealerController_td = (new DealerCreation)->getTable();
        $MarketController_td = (new MarketCreation)->getTable();
        $ShopsType_td = (new ShopsType)->getTable();

        $query = DB::table($ShopController_td)
            ->select(
                $ShopController_td . '.entry_date',
                $DealerController_td . '.dealer_name',
                $ShopsType_td . '.shops_type',
                $MarketController_td . '.area_name',
                $ShopController_td . '.shop_name',
                $ShopController_td . '.whatsapp_no',
                $ShopController_td . '.address'
            )
            ->join($DealerController_td, $DealerController_td . '.id', '=', $ShopController_td . '.dealer_id')
            ->join($MarketController_td, $MarketController_td . '.id', '=', $ShopController_td . '.beats_id')
            ->join($ShopsType_td, $ShopsType_td . '.id', '=', $ShopController_td . '.shop_type_id')
            ->where($DealerController_td . '.status', '=', '1')
            ->where(function ($query) use ($from_date, $to_date, $dealer_id, $beats_id, $shop_id, $shop_type, $ShopController_td, $ShopsType_td) {
                if ($from_date) {
                    $query->where($ShopController_td . '.entry_date', '>=', $from_date);
                }
                if ($to_date) {
                    $query->where($ShopController_td . '.entry_date', '<=', $to_date);
                }
                if ($dealer_id) {
                    $query->where($ShopController_td . '.dealer_id', '=', $dealer_id);
                }
                if ($beats_id) {
                    $query->where($ShopController_td . '.beats_id', '=', $beats_id);
                }
                if ($shop_id) {
                    $query->where($ShopController_td . '.id', '=', $shop_id);
                }
                if ($shop_type !== "" && $shop_type !== null && $shop_type !== 0) {
                    $query->where($ShopsType_td . '.id', '=', $shop_type);
                }
            })
            ->where(function ($query) use ($ShopController_td) {
                $query->where($ShopController_td . '.delete_status', '=', 0)
                    ->orWhereNull($ShopController_td . '.delete_status');
            })
            ->where(function ($query) use ($DealerController_td) {
                $query->where($DealerController_td . '.delete_status', '=', 0)
                    ->orWhereNull($DealerController_td . '.delete_status');
            })
            ->where(function ($query) use ($MarketController_td) {
                $query->where($MarketController_td . '.delete_status', '=', 0)
                    ->orWhereNull($MarketController_td . '.delete_status');
            })
            ->where(function ($query) use ($ShopsType_td) {
                $query->where($ShopsType_td . '.delete_status', '=', 0)
                    ->orWhereNull($ShopsType_td . '.delete_status');
            })

            ->orderBy($ShopController_td . '.id')
            ->get();

        return $query;
    }


    public function db_cmd(Request $request)
    {
        $action = $request->input('action');
        if ($action == 'retrieve') {
            $beats_wise_report = $this->retrieve($request->input('from_date'), $request->input('to_date'), $request->input('dealer_id'), $request->input('beats_id'), $request->input('shop_id'), $request->input('shop_type'));
            return view('Reports.beats_wise_report.list', [
                'beats_wise_report' => $beats_wise_report
            ]);
        } else if ($action == 'find_beats') {

            $dealer_id = $request->input('dealer_id');

            $dealer_creation = DealerCreation::select('area_id')
                ->where('id', $dealer_id)
                ->where('status', '1')
                ->where(function ($query) {
                    $query->where('delete_status', '0')->orWhereNull('delete_status');
                })
                ->get();

            $id = [];
            $area_names = [];

            foreach ($dealer_creation as $dealer_record) {
                $area_id1_list = explode(',', $dealer_record->area_id);

                foreach ($area_id1_list as $area_id) {
                    $market_creation = MarketCreation::select('id', 'area_name')
                        ->where('id', $area_id)
                        ->where(function ($query) {
                            $query->where('delete_status', '0')->orWhereNull('delete_status');
                        })
                        ->orderBy('area_name')
                        ->get();

                    foreach ($market_creation as $market_record) {
                        $id[] = $market_record->id;
                        $area_names[] = $market_record->area_name;
                    }
                }
            }

            $market_creation_result = [
                'id' => $id,
                'area_name' => $area_names,
            ];

            return response()->json($market_creation_result);

        } else if ($action == 'find_shop_type') {

            $beats_id= $request->input('beats_id');

            $shop_type = ShopsType::select('shops_type.id', 'shops_type.shops_type')
            ->join('shop_creation', 'shop_creation.shop_type_id', '=', 'shops_type.id')
            ->where('shop_creation.beats_id', $beats_id)
            ->where(function ($query) {
                $query->where('shops_type.delete_status', '0')->orWhereNull('shops_type.delete_status');
            })
            ->where(function ($query) {
                $query->where('shop_creation.delete_status', '0')->orWhereNull('shop_creation.delete_status');
            })
            ->groupBy('shops_type.id', 'shops_type.shops_type')
            ->get();

            return response()->json($shop_type);

        } else if ($action == 'find_shop_name') {

            $beats_id= $request->input('beats_id');
            $shop_type= $request->input('shop_type');

            $shop_creation = ShopCreation::select('shop_creation.id', 'shop_creation.shop_name')
            ->where('shop_creation.beats_id', $beats_id)
            ->where('shop_creation.shop_type_id', $shop_type)
            ->where(function ($query) {
                $query->where('shop_creation.delete_status', '0')->orWhereNull('shop_creation.delete_status');
            })
            ->get();

            return response()->json($shop_creation);
        }
    }
}
