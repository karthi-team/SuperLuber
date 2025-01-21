<?php

namespace App\Http\Controllers\Reports;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Models\SalesRepCreation;
use App\Models\DealerCreation;
use App\Models\MarketCreation;
use App\Models\ItemCreation;
use App\Models\DistrictCreation;
use App\Models\MarketManagerCreation;
use App\Models\GroupCreation;
use App\Models\Entry\SalesOrderStockMain;
use App\Models\Entry\SalesOrderStockSub;
use Carbon\Carbon;

class SalesBoxReportController extends Controller
{
    public function sales_box_report()
    {
        $sales_rep_creation = SalesRepCreation::select('id', 'sales_ref_name')
            ->where('sales_ref_name', '!=', '')
            ->orderBy('sales_ref_name')
            ->get();

        $district_name = DistrictCreation::select('id', 'district_name')->where('district_name', '!=', '')->orderBy('district_name')->get();

        $item_creation = ItemCreation::select('id', 'item_name')
            ->where('item_name', '!=', '')
            ->orderBy('item_name')
            ->get();


        $group_creation = GroupCreation::select('id', 'group_name')
        ->where(function ($query) {
            $query->where('delete_status', '0')->orWhereNull('delete_status');
        }) ->orderBy('group_name')
            ->get();

        $dealer_name = DealerCreation::select('id', 'dealer_name')->where(function ($query) {
            $query->where('delete_status', '0')->orWhereNull('delete_status');
        })->orderBy('dealer_name')->get();

        $area_name = MarketCreation::select('id', 'area_name')->where(function ($query) {
            $query->where('delete_status', '0')->orWhereNull('delete_status');
        })->orderBy('area_name')->get();

        $manager_creation=MarketManagerCreation::select('id','manager_name')->where('delete_status', '0')->orWhereNull('delete_status')->orderBy('manager_no')->get();

        //return print_r($area_name);


        return view('Reports.sales_box_report.admin', [
            'sales_rep_creation' => $sales_rep_creation,
            'item_creation' => $item_creation,
            'dealer_creation' => $dealer_name,
            'market_creation' => $area_name,
            'district_name' => $district_name,
            'manager_creation' => $manager_creation,
            'group_creation'=>$group_creation,


        ]);
    }


    public function retrieve_sales_ref($sales_ref_id)
    {
             $sales_name = SalesRepCreation::select('id', 'sales_ref_name')->where(function ($query) {
                $query->where('delete_status', '0')->orWhereNull('delete_status');
            })->orderBy('sales_ref_name');
            if (!empty($sales_ref_id)) {
                $sales_name->where('id', '=', $sales_ref_id);

            }
            $sales_name1 = $sales_name->get();

        return $sales_name1;
    }

    public function retrieve_item($group_id,$groupCreationIdsArray)
    {
        $item_name = GroupCreation::select('group_creation.group_name', 'group_creation.id as group_id')
    ->whereIn('group_creation.id', $groupCreationIdsArray)
    ->where(function ($query) {
        $query->where('group_creation.delete_status', '0')
            ->orWhereNull('group_creation.delete_status');
    })
    ->groupBy('group_creation.group_name', 'group_creation.id'); // Include the missing column in GROUP BY

if (!empty($group_id)) {
    $item_name->where('group_creation.id', '=', $group_id);
}

$item_name1 = $item_name->get();

return $item_name1;




        //return $item_name1;
    }

    public function retrieve_dealer($dealer_id)
    {
         $dealer_name1 = DealerCreation::select('id', 'dealer_name', 'area_id','place')->where(function ($query) {
                $query->where('delete_status', '0')->orWhereNull('delete_status');
            })->orderBy('dealer_name');


            if (!empty($dealer_id)) {
                if (is_array($dealer_id)) {
                    // If $dealer_id is an array, use whereIn to match multiple IDs
                    $dealer_name1->whereIn('id', $dealer_id);
                } else {
                    // If $dealer_id is a single value, apply the normal where condition
                    $dealer_name1->where('id', $dealer_id);
                }
            }


            $dealer_name = $dealer_name1->get();



        return $dealer_name;
    }

    public function retrieve_sales_exec($sales_exeIdArrays)
    {


        $result = DB::table('sales_ref_creation')

    ->whereIn('id', $sales_exeIdArrays)
    ->select('id as sales_rep_id','sales_ref_name')

    ->get();





    return $result;
    }



    public function retrieve_count($from_date,  $sales_ref_id, $dealer_id, $manager_id,$group_id)
    {
        $sales_box_count1 = DB::table('sales_order_d2s_main as sodsm')
        ->leftJoin('sales_order_d2s_sublist as sodss', 'sodss.sales_order_main_id', '=', 'sodsm.id')
        ->leftJoin('sales_ref_creation as src', 'src.id', '=', 'sodsm.sales_exec')
        ->whereYear('sodss.order_date', '=', date('Y', strtotime($from_date)))
        ->whereMonth('sodss.order_date', '=', date('m', strtotime($from_date)))

        ->where(function ($query) {
            $query->where('sodss.delete_status', 0)
                ->orWhereNull('sodss.delete_status');
        })
        ->where(function ($query) {
            $query->where('sodsm.delete_status', 0)
                ->orWhereNull('sodsm.delete_status');
        })
        ->where('sodss.status_check', 'Yes')
        ->groupBy('sodsm.dealer_creation_id', DB::raw('DATE(sodsm.created_at)'))
        ->select(
            'sodsm.dealer_creation_id as dealer_id',
            DB::raw('DATE(sodsm.created_at) as date'),
            DB::raw('COUNT(sodss.status_check) as total_status_count'),
            DB::raw('SUM(sodss.order_quantity) as total_order_quantity')
        );

    // If you want to order the results by date in descending order
    $sales_box_count1->orderBy('date', 'desc');
            if (!empty($from_date)) {
                $year = date('Y', strtotime($from_date));
                $month = date('m', strtotime($from_date));

                $sales_box_count1->whereYear('sodss.order_date', $year)
                    ->whereMonth('sodss.order_date', $month);
            }

            if (!empty($sales_ref_id)) {
                $sales_box_count1->where('sodsm.sales_exec', '=', $sales_ref_id);
            }

            if (!empty($dealer_id)) {
                $sales_box_count1->where('sodsm.dealer_creation_id', '=', $dealer_id);
            }

            if (!empty($manager_id)) {
                $sales_box_count1->where('src.manager_id', '=', $manager_id);
            }
            if (!empty($group_id)) {
                $sales_box_count1->where('sodss.group_creation_id', '=', $group_id);
            }
            $sales_box_count1->where(function ($query) {
                $query->where('sodss.delete_status', '0')->orWhereNull('sodss.delete_status');
            });
            $sales_box_count1->where(function ($query) {
                $query->where('sodsm.delete_status', '0')->orWhereNull('sodsm.delete_status');
            });

            $sales_box_count1->where('sodss.status_check', '=', "Yes");
            $sales_box_count = $sales_box_count1->get();



        return $sales_box_count;
        //return $sales_box_report;
    }

    public function retrieve_last_visit_date($from_date, $sales_ref_id, $dealer_id, $item_id)
    {


            $last_visit1 = DB::table('sales_order_d2s_main as sodsm')

                ->leftJoin('dealer_creation as dc', 'dc.id', '=', 'sodsm.dealer_creation_id')

                ->leftJoin('sales_order_d2s_sublist as sodss', 'sodss.sales_order_main_id', '=', 'sodsm.id')


                ->groupBy( 'sodss.order_date', 'dc.id')
                ->select(
                    'sodss.order_date',
                    'dc.id' )
                ->orderByDesc('sodss.order_date');
            if (!empty($from_date)) {
                $year = date('Y', strtotime($from_date));
                $month = date('m', strtotime($from_date));

                $last_visit1->whereYear('sodss.order_date', $year)
                    ->whereMonth('sodss.order_date', $month);
            }

            if (!empty($sales_ref_id)) {
                $last_visit1->where('sodsm.sales_exec', '=', $sales_ref_id);
            }
            if (!empty($dealer_id)) {
                $last_visit1->where('sodsm.dealer_creation_id', '=', $dealer_id);
            }

            $last_visit1->where(function ($query) {
                $query->where('sodss.delete_status', '0')->orWhereNull('sodss.delete_status');
            });
            $last_visit1->where(function ($query) {
                $query->where('sodsm.delete_status', '0')->orWhereNull('sodsm.delete_status');
            });

            $last_visit1->where('sodss.status_check', '=', "Yes");
            $last_visit = $last_visit1->limit(1)
                ->get();



        return $last_visit;
        //return $sales_box_report;
    }

    public function retrieve_market($dealerCreationIdArray)
    {


        $result = DB::table('dealer_creation')

    ->whereIn('id', $dealerCreationIdArray)
    ->select('id as dealer_id','sales_rep_id')

    ->get();





    return $result;
    }


    public function db_cmd(Request $request)
    {
        $action = $request->input('action');
        if ($action == 'retrieve') {
            // $sales_box_report = $this->retrieve_sales_box($request->input('from_date'), $request->input('sales_ref_id'), $request->input('dealer_id'), $request->input('manager_id'), $request->input('group_id'));



            $sales_rep_creation = $this->retrieve_sales_ref($request->input('sales_ref_id'));



           // $dealer_creation = $this->retrieve_dealer($request->input('dealer_id'));





            if (empty($request->input('from_date'))) {
                $currentDay = date('d');
                $currentMonth = date('m');
                $currentYear = date('Y');
                $from_date = $currentYear . '-' . $currentMonth . '-' . $currentDay;

                // Assuming $currentDay1 is defined somewhere

            }


            if (empty($request->input('from_date'))) {
                $currentDay = date('d');
                $currentMonth = date('m');
                $currentYear = date('Y');
                $from_date = $currentYear . '-' . $currentMonth . '-' . $currentDay;
                $to_date = $currentYear . '-' . $currentMonth . '-'.
                '31';



                // Assuming $currentDay1 is defined somewhere

            } else {
                $from_date = date('Y-m-d', strtotime($request->input('from_date')));
                $to_date = date('Y-m-d', strtotime($request->input('to_date')));

            }
            $groupCreationIds = DB::table('sales_order_d2s_sublist as sodss')
            ->leftJoin('sales_order_d2s_main as sodsm', 'sodsm.id', '=', 'sodss.sales_order_main_id')
            ->where(function ($query) {
                $query->where('sodss.delete_status', '0')->orWhereNull('sodss.delete_status');
            })
            ->where(function ($query) {
                $query->where('sodsm.delete_status', '0')->orWhereNull('sodsm.delete_status');
            })
            ->groupBy('sodss.group_creation_id')
            ->select('sodss.group_creation_id');

        if (!empty($from_date)) {
            $year = date('Y', strtotime($from_date));
            $month = date('m', strtotime($from_date));

            $groupCreationIds->whereYear('sodss.order_date', $year)
                ->whereMonth('sodss.order_date', $month);
        }

        $groupCreationIds1 = $groupCreationIds->get();

        // Use 'item_creation_id' without 'sodss.' prefix in pluck
        $groupCreationIdsArray = $groupCreationIds1->pluck('group_creation_id')->toArray();


        $item_creation = $this->retrieve_item($request->input('group_id'), $groupCreationIdsArray);



    $salesExecs = DB::table('sales_order_stock_main')
    ->select('dealer_creation_id')
    ->where(function ($query) {
        $query->where('sales_order_stock_main.delete_status', '0')->orWhereNull('sales_order_stock_main.delete_status');
    })
    ->groupBy('dealer_creation_id');

    if (!empty($from_date)) {
        $year = date('Y', strtotime($from_date));
        $month = date('m', strtotime($from_date));

        $salesExecs->whereYear('stock_entry_date', $year)
            ->whereMonth('stock_entry_date', $month);
    }
    $sales_ref_id=$request->input('sales_ref_id');

    if (!empty($sales_ref_id)) {
        $salesExecs->where('sales_exec', '=', $sales_ref_id);
    }
    $dealer_id=$request->input('dealer_id');

    if (!empty($dealer_id)) {
        $salesExecs->where('dealer_creation_id', '=', $dealer_id);
    }

    $salesExecs1 = $salesExecs->get();

   $dealerCreationIdArray = $salesExecs1->pluck('dealer_creation_id')->toArray();




           /*  $market_creation = $this->retrieve_market($dealerCreationIdArray);
            $dealer_creation = $this->retrieve_dealer($dealerCreationIdArray); */

            $sales_box_report_count = $this->retrieve_count($request->input('from_date'), $request->input('sales_ref_id'), $request->input('dealer_id'),  $request->input('manager_id'),  $request->input('group_id'));

          /*   $retrieve_last_visit_date = $this->retrieve_last_visit_date($request->input('from_date'), $request->input('sales_ref_id'), $request->input('dealer_id'), $request->input('item_id')); */

          if (empty($from_date)) {
            $currentDay = date('d');
            $currentMonth = date('m');
            $currentYear = date('Y');
            $from_date = $currentYear . '-' . $currentMonth . '-' . $currentDay;
        } else {
            $from_date = date('Y-m-d', strtotime($request->input('from_date')));
            $currentDay = date('d', strtotime($from_date));
            $currentMonth = date('m', strtotime($from_date));
            $currentYear = date('Y', strtotime($from_date));

        }
        $to_date = date('Y-m-d', strtotime($currentYear . '-' . $currentMonth . '-' . '31'));

            $ref_id = $request->input('sales_ref_id');
            $dealer_id = $request->input('dealer_id');

            $group_id = $request->input('group_id');


            $query_1 = DB::table('sales_order_d2s_main as sodsm')
            ->leftJoin('sales_order_d2s_sublist as sodss', 'sodsm.id', '=', 'sodss.sales_order_main_id')

            ->select(
                DB::raw('SUM(sodss.order_quantity) as total_order_quantity'),
                DB::raw('COUNT(sodss.status_check) as total_status_check'),
                DB::raw('SUM(sodss.current_stock) as total_current_stock'),
                'sodss.group_creation_id',
                'sodsm.dealer_creation_id',


            )
            ->where(function($query){
                $query->where('sodsm.delete_status', '0')->orWhereNull('sodsm.delete_status');
            })
            ->where(function($query){
                $query->where('sodss.delete_status', '0')->orWhereNull('sodss.delete_status');
            })
            ->groupBy('sodss.group_creation_id', 'sodsm.dealer_creation_id');

            if (!empty($from_date) && !empty($to_date)) {
            $query_1->whereBetween('sodss.order_date', [$from_date, $to_date]);
            }

            if (!empty($ref_id)) {
            $query_1->where('sodsm.sales_exec', $ref_id);
            }

            if (!empty($dealer_id)) {
            $query_1->where('sodsm.dealer_creation_id', $dealer_id);
            }

            if (!empty($group_id)) {
            $query_1->where('sodss.group_creation_id', $group_id);
            }

            $sales_box_report_2 = $query_1->get();


            $query_2 = DB::table('sales_order_stock_main as sosm')
            ->leftJoin('sales_order_stock_sublist as soss', 'sosm.id', '=', 'soss.sales_order_main_id')
            ->select(
                DB::raw('SUM(soss.current_stock) as t_order_qty'),
                'soss.group_creation_id',
                'sosm.dealer_creation_id'
            )
            ->where(function($query){
                $query->where('sosm.delete_status', '0')->orWhereNull('sosm.delete_status');
            })
            ->where(function($query){
                $query->where('soss.delete_status', '0')->orWhereNull('soss.delete_status');
            })

            ->groupBy('soss.group_creation_id', 'sosm.dealer_creation_id');

            if (!empty($from_date) && !empty($to_date)) {
            $query_2->whereBetween('soss.order_date', [$from_date, $to_date]);
            }

            if (!empty($ref_id)) {
            $query_2->where('sosm.sales_exec', $ref_id);
            }

            if (!empty($dealer_id)) {
            $query_2->where('sosm.dealer_creation_id', $dealer_id);
            }

            if (!empty($group_id)) {
            $query_2->where('soss.group_creation_id', $group_id);
            }

            $opening_stock_rep = $query_2->get();
//return $opening_stock_rep ;

$dealerCreationIdArray = $opening_stock_rep->pluck('dealer_creation_id')->unique()->values()->toArray();

//return $dealerCreationIdArray;
$groupCreationIdsArray = $opening_stock_rep->pluck('group_creation_id')->unique()->values()->toArray();
//$groupCreationIdsArray = $opening_stock_rep->pluck('group_creation_id')->toArray();
$market_creation = $this->retrieve_market($dealerCreationIdArray);
$dealer_creation = $this->retrieve_dealer($dealerCreationIdArray);
$item_creation = $this->retrieve_item($group_id, $groupCreationIdsArray);




$arr = [];

foreach ($market_creation as $market_creations) {
    $dealer_id = $market_creations->dealer_id;
    $sales_rep_id = $market_creations->sales_rep_id;


    $last_visit1 = DB::table('sales_order_d2s_main as sodsm')
        ->leftJoin('sales_order_d2s_sublist as sodss', 'sodss.sales_order_main_id', '=', 'sodsm.id')
        ->where('sodsm.dealer_creation_id', $dealer_id) // Added this condition to filter by dealer_id
        ->whereYear('sodsm.order_date', '=', date('Y', strtotime($from_date)))
    ->whereMonth('sodsm.order_date', '=', date('m', strtotime($from_date)))
        ->where(function ($query) {
            $query->where('sodsm.delete_status', '0')
                ->orWhereNull('sodsm.delete_status');
        })
        ->where(function ($query) {
            $query->where('sodss.delete_status', '0')
                ->orWhereNull('sodss.delete_status');
        })
        ->groupBy('sodss.order_date', 'sodsm.dealer_creation_id')
        ->select('sodss.order_date')
        ->orderByDesc('sodss.order_date');
        if (!empty($group_id)) {
            $last_visit1->where('sodss.group_creation_id', $group_id);
            }

    // Use first() instead of find() to get a single result
    $get_last_date = $last_visit1->first();

//$get_last_count = 0;
    $last_count = DB::table('sales_order_d2s_main as sodsm')
    ->leftJoin('sales_order_d2s_sublist as sodss', 'sodss.sales_order_main_id', '=', 'sodsm.id')
    ->where('sodsm.dealer_creation_id', $dealer_id) // Filter by dealer_id
    ->whereYear('sodsm.order_date', '=', date('Y', strtotime($from_date)))
    ->whereMonth('sodsm.order_date', '=', date('m', strtotime($from_date)))
    ->where(function ($query) {
        $query->where('sodsm.delete_status', '0')
            ->orWhereNull('sodsm.delete_status');
    })
    ->where(function ($query) {
        $query->where('sodss.delete_status', '0')
            ->orWhereNull('sodss.delete_status');
    })
    ->groupBy('sodsm.dealer_creation_id')
    ->select(DB::raw('COUNT(DISTINCT sodsm.order_date) as total_status_count'));

    if (!empty($group_id)) {
        $last_visit1->where('sodss.group_creation_id', $group_id);
        }
// Use first() instead of find() to get a single result
$get_last_count = $last_count->first();



$last_count_2 = DB::table('sales_order_d2s_main as sodsm')
->rightJoin('visitors_shops as vs', 'vs.d2s_id', '=', 'sodsm.id')
->where('sodsm.sales_exec', $sales_rep_id)
->where('sodsm.radio_visit','=', '1') // Filter by dealer_id
->whereYear('sodsm.order_date', '=', date('Y', strtotime($from_date)))
->whereMonth('sodsm.order_date', '=', date('m', strtotime($from_date)))
->where(function ($query) {
    $query->where('sodsm.delete_status', '0')
        ->orWhereNull('sodsm.delete_status');
})

->groupBy('sodsm.sales_exec')
->select(DB::raw('COUNT(sodsm.radio_visit) as total_status_count_2'));



// Use first() instead of find() to get a single result
$get_last_count_2 = $last_count_2->first();


$total_status_counts_2 = $get_last_count_2 ? $get_last_count_2->total_status_count_2 : 0;

$sales_rep_id_2 = $sales_rep_id ;





    // Check if a result was found before accessing properties
    $entry_dates = $get_last_date ? $get_last_date->order_date : 0;
    $total_status_count = $get_last_count ? $get_last_count->total_status_count : 0;

    $get_dealer = DealerCreation::find($dealer_id);

    // Check if a dealer was found before accessing properties
    $dealer_names = $get_dealer ? $get_dealer->dealer_name : 0;
    $place = $get_dealer ? $get_dealer->place : 0;

    $finally = [
        'dealer_names' => $dealer_names,
        'place' => $place,
        'entry_date' => $entry_dates,
        'total_status_count' => $total_status_count,
        'total_status_counts_2' => $total_status_counts_2,
        'sales_rep_id_2' => $sales_rep_id_2

    ];

    $arr[] = $finally;
}




$last_count_2 = DB::table('sales_order_d2s_main as sodsm')
->leftJoin('visitors_shops as vs', 'vs.d2s_id', '=', 'sodsm.id')

->where('sodsm.radio_visit','=', '1') // Filter by dealer_id
->whereYear('sodsm.order_date', '=', date('Y', strtotime($from_date)))
->whereMonth('sodsm.order_date', '=', date('m', strtotime($from_date)))
->where(function ($query) {
    $query->where('sodsm.delete_status', '0')
        ->orWhereNull('sodsm.delete_status');
})

->groupBy('sodsm.sales_exec', 'vs.visitor_name')
->select(DB::raw('COUNT(sodsm.radio_visit) as total_status_count_2'),'sodsm.sales_exec as s_name','vs.visitor_name');




$get_last_count_2 = $last_count_2->get();





        $query_2 = DB::table('sales_order_d2s_main as sodsm')
    ->select('sodsm.sales_exec as sales_ref')
    ->whereYear('sodsm.order_date', '=', date('Y', strtotime($from_date)))
    ->whereMonth('sodsm.order_date', '=', date('m', strtotime($from_date)))
    ->where(function ($query) {
        $query->where('sodsm.delete_status', '0')->orWhereNull('sodsm.delete_status');
    })
    ->groupBy('sodsm.sales_exec');

$get_last_count_21 = $query_2->get();

$sales_exeIdArrays = $get_last_count_21->pluck('sales_ref')->unique()->values()->toArray();

$sales_exeIdArrays1 = $this->retrieve_sales_exec($sales_exeIdArrays);

$arr2 = [];

foreach ($sales_exeIdArrays1 as $sales_exeIdArray) {
    $sales_rep_id = $sales_exeIdArray->sales_rep_id;
    $month = date('m', strtotime($from_date));
    $year = date('Y', strtotime($from_date));
    $number_of_days = cal_days_in_month(CAL_GREGORIAN, $month, $year);

    for ($d = 1; $d <= $number_of_days; $d++) {
        $currentDate = date('Y-m-d', strtotime("$year-$month-$d"));

        $last_count_4i = DB::table('sales_order_d2s_main as sodsm')
            ->where('sodsm.sales_exec', $sales_rep_id)
            ->where('sodsm.radio_visit', '=', '0')
            ->whereDate('sodsm.order_date', '=', $currentDate)
            ->where(function ($query) {
                $query->where('sodsm.delete_status', '0')
                    ->orWhereNull('sodsm.delete_status');
            })
            ->groupBy('sodsm.sales_exec')
            ->select('sodsm.sales_exec as sales_exec');

        $get_last_count_2_4i = $last_count_4i->first();
        $sales_exec_id = $get_last_count_2_4i ? $get_last_count_2_4i->sales_exec : 0;

        $last_count_4 = DB::table('sales_order_d2s_main as sodsm')
            ->leftJoin('visitors_shops as vs', 'vs.d2s_id', '=', 'sodsm.id')
            ->where('sodsm.radio_visit', '=', '1')
            ->where('sodsm.sales_exec', $sales_rep_id)
            ->whereDate('sodsm.order_date', '=', $currentDate)
            ->where(function ($query) {
                $query->where('sodsm.delete_status', '0')
                    ->orWhereNull('sodsm.delete_status');
            })
            ->groupBy('sodsm.sales_exec')
            ->select(DB::raw('COUNT(distinct sodsm.radio_visit) as total_status_count_2'), 'sodsm.sales_exec as sales_exec1');

        $get_last_count_2_4 = $last_count_4->first();

        if ($get_last_count_2_4) {
            $sales_exec1_t = $get_last_count_2_4->sales_exec1;
            $total_status_count_2_t = $sales_exec_id == 0
                ? $get_last_count_2_4->total_status_count_2
                : 0;

            $get_dealer = DealerCreation::find($dealer_id);

            if (!empty($sales_exec1_t)) {
                $finally_2 = [
                    'total_status_count_2_t' => $total_status_count_2_t,
                    'entry_date' => $currentDate,
                    'sales_exec1_t' => $sales_exec1_t,
                ];

                $arr2[] = $finally_2;
            }
        }
    }
}

//return $arr2;

//return  $finally_2;
//return $sales_exec_id;

//return $sales_exeIdArrays1;

//return $retrieve_last_visit_date;
             //return   print_r($sales_box_report);

            return view('Reports.sales_box_report.list', ['arr' => $arr,
                'item_creation' => $item_creation, 'sales_box_report_2' => $sales_box_report_2, 'sales_box_report_count' => $sales_box_report_count,'opening_stock_rep' => $opening_stock_rep,'from_date' => $from_date,  'dealer_creation' => $dealer_creation, 'market_creation' => $market_creation,    'sales_rep_creation' => $sales_rep_creation,'arr2' => $arr2,'sales_exeIdArrays1'=>$sales_exeIdArrays1,
            ]);
        }  
        
        
        else if ($action == 'other_retrieve') {

                $sales_rep_creation = $this->retrieve_sales_ref($request->input('sales_ref_id'));

                if (empty($request->input('from_date'))) {
                    $currentDay = date('d');
                    $currentMonth = date('m');
                    $currentYear = date('Y');
                    $from_date = $currentYear . '-' . $currentMonth . '-' . $currentDay;

                }


                if (empty($request->input('from_date'))) {
                    $currentDay = date('d');
                    $currentMonth = date('m');
                    $currentYear = date('Y');
                    $from_date = $currentYear . '-' . $currentMonth . '-' . $currentDay;
                    $to_date = $currentYear . '-' . $currentMonth . '-'.
                    '31';

                } else {
                    $from_date = date('Y-m-d', strtotime($request->input('from_date')));
                    $to_date = date('Y-m-d', strtotime($request->input('to_date')));

                }
                $groupCreationIds = DB::table('sales_order_d2s_sublist as sodss')
                ->leftJoin('sales_order_d2s_main as sodsm', 'sodsm.id', '=', 'sodss.sales_order_main_id')
                ->where(function ($query) {
                    $query->where('sodss.delete_status', '0')->orWhereNull('sodss.delete_status');
                })
                ->where(function ($query) {
                    $query->where('sodsm.delete_status', '0')->orWhereNull('sodsm.delete_status');
                })
                ->groupBy('sodss.group_creation_id')
                ->select('sodss.group_creation_id');

            if (!empty($from_date)) {
                $year = date('Y', strtotime($from_date));
                $month = date('m', strtotime($from_date));

                $groupCreationIds->whereYear('sodss.order_date', $year)
                    ->whereMonth('sodss.order_date', $month);
            }

            $groupCreationIds1 = $groupCreationIds->get();


            $groupCreationIdsArray = $groupCreationIds1->pluck('group_creation_id')->toArray();


            $item_creation = $this->retrieve_item($request->input('group_id'), $groupCreationIdsArray);



        $salesExecs = DB::table('sales_order_stock_main')
        ->select('dealer_creation_id')
        ->where(function ($query) {
            $query->where('sales_order_stock_main.delete_status', '0')->orWhereNull('sales_order_stock_main.delete_status');
        })
        ->groupBy('dealer_creation_id');

        if (!empty($from_date)) {
            $year = date('Y', strtotime($from_date));
            $month = date('m', strtotime($from_date));

            $salesExecs->whereYear('stock_entry_date', $year)
                ->whereMonth('stock_entry_date', $month);
        }
        $sales_ref_id=$request->input('sales_ref_id');

        if (!empty($sales_ref_id)) {
            $salesExecs->where('sales_exec', '=', $sales_ref_id);
        }
        $dealer_id=$request->input('dealer_id');

        if (!empty($dealer_id)) {
            $salesExecs->where('dealer_creation_id', '=', $dealer_id);
        }

        $salesExecs1 = $salesExecs->get();

       $dealerCreationIdArray = $salesExecs1->pluck('dealer_creation_id')->toArray();


                $sales_box_report_count = $this->retrieve_count($request->input('from_date'), $request->input('sales_ref_id'), $request->input('dealer_id'),  $request->input('manager_id'),  $request->input('group_id'));

              if (empty($from_date)) {
                $currentDay = date('d');
                $currentMonth = date('m');
                $currentYear = date('Y');
                $from_date = $currentYear . '-' . $currentMonth . '-' . $currentDay;
            } else {
                $from_date = date('Y-m-d', strtotime($request->input('from_date')));
                $currentDay = date('d', strtotime($from_date));
                $currentMonth = date('m', strtotime($from_date));
                $currentYear = date('Y', strtotime($from_date));

            }
            $to_date = date('Y-m-d', strtotime($currentYear . '-' . $currentMonth . '-' . '31'));

                $ref_id = $request->input('sales_ref_id');
                $dealer_id = $request->input('dealer_id');

                $group_id = $request->input('group_id');


                $query_1 = DB::table('sales_order_d2s_main as sodsm')
                ->leftJoin('sales_order_d2s_sublist as sodss', 'sodsm.id', '=', 'sodss.sales_order_main_id')

                ->select(
                    DB::raw('SUM(sodss.order_quantity) as total_order_quantity'),
                    DB::raw('COUNT(sodss.status_check) as total_status_check'),
                    DB::raw('SUM(sodss.current_stock) as total_current_stock'),
                    'sodss.group_creation_id',
                    'sodsm.dealer_creation_id',


                )
                ->where(function($query){
                    $query->where('sodsm.delete_status', '0')->orWhereNull('sodsm.delete_status');
                })
                ->where(function($query){
                    $query->where('sodss.delete_status', '0')->orWhereNull('sodss.delete_status');
                })
                ->groupBy('sodss.group_creation_id', 'sodsm.dealer_creation_id');

                if (!empty($from_date) && !empty($to_date)) {
                $query_1->whereBetween('sodss.order_date', [$from_date, $to_date]);
                }

                if (!empty($ref_id)) {
                $query_1->where('sodsm.sales_exec', $ref_id);
                }

                if (!empty($dealer_id)) {
                $query_1->where('sodsm.dealer_creation_id', $dealer_id);
                }

                if (!empty($group_id)) {
                $query_1->where('sodss.group_creation_id', $group_id);
                }

                $sales_box_report_2 = $query_1->get();


                $query_2 = DB::table('sales_order_stock_main as sosm')
                ->leftJoin('sales_order_stock_sublist as soss', 'sosm.id', '=', 'soss.sales_order_main_id')
                ->select(
                    DB::raw('SUM(soss.current_stock) as t_order_qty'),
                    'soss.group_creation_id',
                    'sosm.dealer_creation_id'
                )
                ->where(function($query){
                    $query->where('sosm.delete_status', '0')->orWhereNull('sosm.delete_status');
                })
                ->where(function($query){
                    $query->where('soss.delete_status', '0')->orWhereNull('soss.delete_status');
                })

                ->groupBy('soss.group_creation_id', 'sosm.dealer_creation_id');

                if (!empty($from_date) && !empty($to_date)) {
                $query_2->whereBetween('soss.order_date', [$from_date, $to_date]);
                }

                if (!empty($ref_id)) {
                $query_2->where('sosm.sales_exec', $ref_id);
                }

                if (!empty($dealer_id)) {
                $query_2->where('sosm.dealer_creation_id', $dealer_id);
                }

                if (!empty($group_id)) {
                $query_2->where('soss.group_creation_id', $group_id);
                }

                $opening_stock_rep = $query_2->get();


            $query_2 = DB::table('sales_order_d2s_main as sodsm')
        ->select('sodsm.sales_exec as sales_ref')
        ->whereYear('sodsm.order_date', '=', date('Y', strtotime($from_date)))
        ->whereMonth('sodsm.order_date', '=', date('m', strtotime($from_date)))
        ->where(function ($query) {
            $query->where('sodsm.delete_status', '0')->orWhereNull('sodsm.delete_status');
        })
        ->groupBy('sodsm.sales_exec');

    $get_last_count_21 = $query_2->get();

    $sales_exeIdArrays = $get_last_count_21->pluck('sales_ref')->unique()->values()->toArray();

    $sales_exeIdArrays1 = $this->retrieve_sales_exec($sales_exeIdArrays);

    $arr2 = [];

    foreach ($sales_exeIdArrays1 as $sales_exeIdArray) {
        $sales_rep_id = $sales_exeIdArray->sales_rep_id;
        $month = date('m', strtotime($from_date));
        $year = date('Y', strtotime($from_date));
        $number_of_days = cal_days_in_month(CAL_GREGORIAN, $month, $year);

        for ($d = 1; $d <= $number_of_days; $d++) {
            $currentDate = date('Y-m-d', strtotime("$year-$month-$d"));

            $last_count_4i = DB::table('sales_order_d2s_main as sodsm')
                ->where('sodsm.sales_exec', $sales_rep_id)
                ->where('sodsm.radio_visit', '=', '0')
                ->whereDate('sodsm.order_date', '=', $currentDate)
                ->where(function ($query) use ($ref_id, $dealer_id, $group_id) {
                    $query->where('sodsm.delete_status', '0')
                        ->orWhereNull('sodsm.delete_status');

                    if (!empty($ref_id)) {
                        $query->where('sodsm.sales_exec', $ref_id);
                    }

                    if (!empty($dealer_id)) {
                        $query->where('sodsm.dealer_creation_id', $dealer_id);
                    }


                })
                ->groupBy('sodsm.sales_exec')
                ->select('sodsm.sales_exec as sales_exec');

            $get_last_count_2_4i = $last_count_4i->first();
            $sales_exec_id = $get_last_count_2_4i ? $get_last_count_2_4i->sales_exec : 0;

            $last_count_4 = DB::table('sales_order_d2s_main as sodsm')
                ->leftJoin('visitors_shops as vs', 'vs.d2s_id', '=', 'sodsm.id')
                ->where('sodsm.radio_visit', '=', '1')
                ->where('sodsm.sales_exec', $sales_rep_id)
                ->whereDate('sodsm.order_date', '=', $currentDate)
                ->where(function ($query) use ($ref_id, $dealer_id, $group_id) {
                    $query->where('sodsm.delete_status', '0')
                        ->orWhereNull('sodsm.delete_status');

                    if (!empty($ref_id)) {
                        $query->where('sodsm.sales_exec', $ref_id);
                    }

                    if (!empty($dealer_id)) {
                        $query->where('sodsm.dealer_creation_id', $dealer_id);
                    }


                })
                ->groupBy('sodsm.sales_exec')
                ->select(DB::raw('COUNT(distinct sodsm.radio_visit) as total_status_count_2'), 'sodsm.sales_exec as sales_exec1');

            $get_last_count_2_4 = $last_count_4->first();

            if ($get_last_count_2_4) {
                $sales_exec1_t = $get_last_count_2_4->sales_exec1;
                $total_status_count_2_t = $sales_exec_id == 0
                    ? $get_last_count_2_4->total_status_count_2
                    : 0;

                $get_dealer = DealerCreation::find($dealer_id);

                if (!empty($sales_exec1_t)) {
                    $finally_2 = [
                        'total_status_count_2_t' => $total_status_count_2_t,
                        'entry_date' => $currentDate,
                        'sales_exec1_t' => $sales_exec1_t,
                    ];

                    $arr2[] = $finally_2;
                }
            }
        }
    }

                return view('Reports.sales_box_report.otherlist', [
                    'item_creation' => $item_creation, 'sales_box_report_2' => $sales_box_report_2, 'sales_box_report_count' => $sales_box_report_count,'opening_stock_rep' => $opening_stock_rep,'from_date' => $from_date,  'sales_rep_creation' => $sales_rep_creation,'arr2' => $arr2,'sales_exeIdArrays1'=>$sales_exeIdArrays1,
                ]);
            }

        
        
        
        
        
        else if ($action == 'getSalesRef') {


            $manager_id= $request->input('manager_id');

            $sales_ref_name = SalesRepCreation::select('id', 'sales_ref_name')->where('manager_id', $manager_id)->where(function ($query) {
                $query->where('delete_status', '0')->orWhereNull('delete_status');
            })->orderBy('sales_ref_name')->get();
            return response()->json($sales_ref_name);
        }else if ($action == 'getMarketName') {

            $market_name = MarketCreation::select('id', 'area_name')->where(function ($query) {
                $query->where('delete_status', '0')->orWhereNull('delete_status');
            })->orderBy('area_name')->get();
            return response()->json($market_name);
        } else if ($action == 'getDealerName') {



            $sales_ref_id = $request->input('sales_ref_id');


            $market_name1 = DealerCreation::select('id', 'dealer_name')->where(function ($query) {
                $query->where('delete_status', '0')->orWhereNull('delete_status');
            })->orderBy('dealer_name');

            if (!empty($sales_ref_id)) {
                $market_name1->where('sales_rep_id', '=', $sales_ref_id);
            }


            $market_name = $market_name1->get();
            return response()->json($market_name);
        }

        else if ($action == 'getitemName') {
            $group_id = $request->input('group_id');
             $item_name1 = ItemCreation::select('id', 'item_name')->where(function ($query) {
                $query->where('delete_status', '0')->orWhereNull('delete_status');
            })->orderBy('item_name');

            if (!empty($group_id)) {
                $item_name1->where('group_id', '=', $group_id);
            }
            $item_name = $item_name1->get();
            return response()->json($item_name);
        }
    }
}
