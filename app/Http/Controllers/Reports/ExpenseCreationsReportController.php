<?php

namespace App\Http\Controllers\Reports;

use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Entry\ExpenseCreationsMain;
use App\Models\Entry\ExpenseCreationsSub;
use App\Models\MarketCreation;
use App\Models\DealerCreation;
use App\Models\ItemCreation;
use App\Models\ItemLitersType;
use App\Models\SalesRepCreation;
use App\Models\MarketManagerCreation;
use App\Models\ExpenseTypeCreation;
use App\Models\SubExpenseTypeCreation;
use App\Models\Entry\SalesOrderD2SMain;
use App\Models\Entry\VisitorCreation;
use App\Models\ItemPropertiesType;
use Carbon\Carbon;


class ExpenseCreationsReportController extends Controller
{
    public function Expense_Creations_Report()
    {
        $expense_no_list = ExpenseCreationsMain::select('expense_no')->where(function ($query) {
            $query->where('delete_status', '0')->orWhereNull('delete_status');
        })->get();
        $market_creation = MarketCreation::select('id', 'area_name')->where(function ($query) {
            $query->where('delete_status', '0')->orWhereNull('delete_status');
        })->orderBy('area_name')->get();
        $MarketManagerCreation = MarketManagerCreation::select('id', 'manager_name')->where('status1', '0')->where(function ($query) {
            $query->where('delete_status', '0')->orWhereNull('delete_status');
        })->orderBy('manager_name')->get();

        $sales_ref_creation = SalesRepCreation::select('id', 'sales_ref_name')->where('status', '0')->where(function ($query) {
            $query->where('delete_status', '0')->orWhereNull('delete_status');
        })->orderBy('sales_ref_name')->get();
        return view('Reports.expense_creations_report.admin', ['expense_no_list' => $expense_no_list, 'market_creation' => $market_creation, 'MarketManagerCreation' => $MarketManagerCreation, 'sales_ref_creation' => $sales_ref_creation]);
    }
    public function retrieve($from_date_1, $to_date_1, $expense_no_1, $sales_rep_creation_id_1, $market_manager_id_1, $status_1, $description_1, $mode_of_payment_1)
    {
        $cond = "";
        if ($from_date_1 != "") {
            $cond .= " and expense_date>='" . $from_date_1 . "'";
        }
        if ($to_date_1 != "") {
            $cond .= " and expense_date<='" . $to_date_1 . "'";
        }
        if ($expense_no_1 != "") {
            $cond .= " and expense_no='" . $expense_no_1 . "'";
        }
        if ($sales_rep_creation_id_1 != "") {
            $cond .= " and sales_rep_creation_id=" . $sales_rep_creation_id_1;
        }
        if ($market_manager_id_1 != "") {
            $cond .= " and market_manager_id=" . $market_manager_id_1;
        }
        if ($status_1 != "") {
            $cond .= " and status=" . $status_1;
        }
        if ($description_1 != "") {
            $cond .= " and description=" . $description_1;
        }
        if ($mode_of_payment_1 != "") {
            $cond .= " and mode_of_payment=" . "'" . $mode_of_payment_1 . "'";
        }
        $main_tb = (new ExpenseCreationsMain)->getTable();
        $sub_tb = (new ExpenseCreationsSub)->getTable();
        $MarketCreation_tb = (new MarketCreation)->getTable();
        $DealerCreation_tb = (new DealerCreation)->getTable();
        $SalesRepCreation_tb = (new SalesRepCreation)->getTable();
        $SalesOrderD2SMain_tb = (new SalesOrderD2SMain)->getTable();
        $MarketManagerCreation_tb = (new MarketManagerCreation)->getTable();


        $tb1 = DB::select("SELECT id, expense_no, expense_date, mode_of_payment, (SELECT sales_ref_name FROM $SalesRepCreation_tb WHERE id = $main_tb.sales_rep_creation_id) AS sales_ref_name,(SELECT id FROM $SalesRepCreation_tb WHERE id = $main_tb.sales_rep_creation_id) AS sales_ref_id, (SELECT SUM(total_amount) FROM $sub_tb WHERE sales_expense_main_id = $main_tb.id AND (delete_status = 0 OR delete_status IS NULL)) AS total,(SELECT SUM(travel) FROM $sub_tb WHERE sales_expense_main_id = $main_tb.id AND (delete_status = 0 OR delete_status IS NULL)) AS travel,(SELECT SUM(fuel) FROM $sub_tb WHERE sales_expense_main_id = $main_tb.id AND (delete_status = 0 OR delete_status IS NULL)) AS fuel,(SELECT SUM(da_1) FROM $sub_tb WHERE sales_expense_main_id = $main_tb.id AND (delete_status = 0 OR delete_status IS NULL)) AS da_1,(SELECT SUM(courier) FROM $sub_tb WHERE sales_expense_main_id = $main_tb.id AND (delete_status = 0 OR delete_status IS NULL)) AS courier,(SELECT SUM(lodging) FROM $sub_tb WHERE sales_expense_main_id = $main_tb.id AND (delete_status = 0 OR delete_status IS NULL)) AS lodging,(SELECT SUM(phone) FROM $sub_tb WHERE sales_expense_main_id = $main_tb.id AND (delete_status = 0 OR delete_status IS NULL)) AS phone,(SELECT SUM(others) FROM $sub_tb WHERE sales_expense_main_id = $main_tb.id AND (delete_status = 0 OR delete_status IS NULL)) AS others, (SELECT manager_name FROM $MarketManagerCreation_tb WHERE id = $main_tb.market_manager_id) AS market_manager_name, (SELECT dealer_name FROM $DealerCreation_tb WHERE id = $main_tb.dealer_creation_id) AS dealer_name, status, (SELECT IF(COUNT(*) > 0, CONCAT(SUM(to_loct), ';', SUM(from_loct), ';', SUM(total_amount)), '0;0;0') FROM $sub_tb WHERE sales_expense_main_id = $main_tb.id AND (delete_status = 0 OR delete_status IS NULL)) AS total_sublist FROM $main_tb WHERE (delete_status = 0 OR delete_status IS NULL) $cond");


        return json_decode(json_encode($tb1), true);
    }


       public function calculateDistance($startLat, $startLng, $endLat, $endLng)
        {
            $earthRadius = 6371;

            $latDelta = deg2rad($endLat - $startLat);
            $lngDelta = deg2rad($endLng - $startLng);

            $a = sin($latDelta / 2) * sin($latDelta / 2) +
                cos(deg2rad($startLat)) * cos(deg2rad($endLat)) *
                sin($lngDelta / 2) * sin($lngDelta / 2);

            $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

            return $earthRadius * $c;
        }



    public function retrieve_latitude_start($from_date_1, $sales_rep_creation_id_1)
    {
        $query = DB::table('sales_executive_timelogs')
            ->select('latitude', 'langititude')  // Fetch both latitude and langititude
            ->where('current_status', 'Market Start');

        if ($from_date_1 != "") {
            $query->where('date', '=', $from_date_1);
        }

        if ($sales_rep_creation_id_1 != "") {
            $query->where('sales_executive_id', $sales_rep_creation_id_1);
        }

        // Get the first matching record
        $tb1 = $query->orderBy('id', 'asc')->first();


        if ($tb1) {
            return [
                'latitude' => $tb1->latitude,
                'langititude' => $tb1->langititude
            ];
        }


        return null;
    }


    public function retrieve_latitude_over($from_date_1, $sales_rep_creation_id_1)
    {
        $query = DB::table('sales_executive_timelogs')
            ->select('latitude', 'langititude')
            ->where('current_status', 'Market Over');

        if ($from_date_1 != "") {
            $query->where('date', '=', $from_date_1);
        }

        if ($sales_rep_creation_id_1 != "") {
            $query->where('sales_executive_id', $sales_rep_creation_id_1);
        }


        $tb1 = $query->orderBy('id', 'desc')->first();

        if ($tb1) {
            return [
                'latitude' => $tb1->latitude,
                'langititude' => $tb1->langititude
            ];
        }


        return null;
    }








    public function retrieve_main($id)
    {
        return ExpenseCreationsMain::select('id', 'expense_no', 'expense_date', 'sales_rep_creation_id', 'dealer_creation_id', 'status', 'description', 'mode_of_payment', 'market_manager_id')->where('id', $id)->get()->first();
    }
    public function retrieve_sub($main_id, $sub_id)
    {
        if ($sub_id == '') {
            $sub_tb = (new ExpenseCreationsSub)->getTable();
            $ExpenseTypeCreation_tb = (new ExpenseTypeCreation)->getTable();
            $DealerCreation_tb = (new DealerCreation)->getTable();
            $MarketCreation_tb = (new MarketCreation)->getTable();
            $VisitorCreation_tb = (new VisitorCreation)->getTable();
            $ExpenseTypeCreation_tb = (new ExpenseTypeCreation)->getTable();
            $ItemPropertiesType_tb = (new ItemPropertiesType)->getTable();
            $SubExpenseTypeCreation_tb = (new SubExpenseTypeCreation)->getTable();
            $ItemLitersType_tb = (new ItemLitersType)->getTable();
            $tb1 = DB::select('select id,(select area_name from ' . $MarketCreation_tb . ' where id=' . $sub_tb . '.market_sub_id) as market_sub_id,(select visitor_name from ' . $VisitorCreation_tb . ' where id=' . $sub_tb . '.visitor_sub_id) as visitor_sub_id,(select dealer_name from ' . $DealerCreation_tb . ' where id=' . $sub_tb . '.dealer_sub_id) as dealer_sub_id,(select item_liters_type from ' . $ItemLitersType_tb . ' where id=' . $sub_tb . '.from_loct) as from_loct,from_loct,travel,fuel,da_1,courier,lodging,phone,others,image_name,to_loct,expense_amount,ta_amount,total_amount from ' . $sub_tb . ' where sales_expense_main_id=' . $main_id . ' and (delete_status=0 or delete_status is null)');
            return json_decode(json_encode($tb1), true);
        } else {
            return ExpenseCreationsSub::where('id', $sub_id)->where(function ($query) {
                $query->where('delete_status', '0')->orWhereNull('delete_status');
            })->get(['id', 'visitor_sub_id', 'dealer_sub_id', 'market_sub_id', 'to_loct', 'from_loct', 'total_amount', 'expense_amount', 'ta_amount', 'travel', 'fuel', 'da_1', 'courier', 'lodging', 'phone', 'others'])->first();
        }
    }
    public function db_cmd(Request $request)
    {
        $action = $request->input('action');
        if ($action == 'insert') {
            $tb = new ExpenseCreationsMain();
            $tb->entry_date  = Carbon::now();
            $tb->expense_no = $request->input('expense_no');
            $tb->expense_date = $request->input('expense_date');
            $tb->sales_rep_creation_id = $request->input('sales_rep_creation_id');
            $tb->market_manager_id = $request->input('market_manager_id');
            $tb->status = $request->input('status');
            $tb->description = $request->input('description');
            $tb->mode_of_payment = $request->input('mode_of_payment');
            $tb->save();
        } else if ($action == 'update') {
            $tb = ExpenseCreationsMain::find($request->input('id'));
            $tb->expense_no = $request->input('expense_no');
            $tb->expense_date = $request->input('expense_date');
            $tb->sales_rep_creation_id = $request->input('sales_rep_creation_id');
            $tb->market_manager_id = $request->input('market_manager_id');
            $tb->status = $request->input('status');
            $tb->description = $request->input('description');
            $tb->mode_of_payment = $request->input('mode_of_payment');
            $tb->save();
        } else if ($action == 'delete') {
            $tb = ExpenseCreationsMain::find($request->input('id'));
            $tb->delete_status = "1";
            $tb->save();
        }
        if ($action == 'insert_sub') {
            $main_id = $request->input('main_id');

            if (!$main_id) {
                $main_id = ExpenseCreationsMain::insertGetId([
                    'entry_date' => Carbon::now(),
                    'expense_no' => $request->input('expense_no'),
                    'expense_date' => $request->input('expense_date'),
                    'sales_rep_creation_id' => $request->input('sales_rep_creation_id'),
                    'market_manager_id' => $request->input('market_manager_id'),
                    'status' => $request->input('status'),
                    'description' => $request->input('description'),
                    'mode_of_payment' => $request->input('mode_of_payment'),
                ]);
            }

            if ($request->hasFile('image_name')) {
                $image = $request->file('image_name');
                $imgName = $image->getClientOriginalName();
                $image->storeAs('public/expense_img', $imgName);

                $imageData = ['image_name' => $imgName];
            } else {
                $imageData = [];
            }

            ExpenseCreationsSub::create(array_merge([
                'entry_date' => Carbon::now(),
                'sales_expense_main_id' => $main_id,
                'total_amount' => $request->input('total_amount'),
                'expense_amount' => $request->input('expense_amount'),
                'ta_amount' => $request->input('ta_amount'),
                'dealer_sub_id' => $request->input('dealer_sub_id'),
                'visitor_sub_id' => $request->input('visitor_sub_id'),
                'market_sub_id' => $request->input('market_sub_id'),
                'travel' => $request->input('travel'),
                'fuel' => $request->input('fuel'),
                'da_1' => $request->input('da_1'),
                'courier' => $request->input('courier'),
                'lodging' => $request->input('lodging'),
                'phone' => $request->input('phone'),
                'others' => $request->input('others'),
            ], $imageData));

            return response()->json(['success' => true, 'main_id' => $main_id]);
        } elseif ($action == 'update_sub') {
            $tb = ExpenseCreationsSub::find($request->input('id'));

            if ($tb) {
                // Prepare updated data
                $updateData = $request->only([
                    'expense_amount',
                    'ta_amount',
                    'total_amount',
                    'dealer_sub_id',
                    'visitor_sub_id',
                    'market_sub_id',
                    'travel',
                    'fuel',
                    'da_1',
                    'courier',
                    'lodging',
                    'phone',
                    'others'
                ]);


                if ($request->hasFile('image_name')) {
                    $image = $request->file('image_name');
                    $imgName = $image->getClientOriginalName();
                    $image->storeAs('public/expense_img', $imgName);

                    $updateData['image_name'] = $imgName;
                }


                $tb->update($updateData);

                return response()->json(['success' => true]);
            } else {
                return response()->json(['success' => false, 'message' => 'Record not found'], 404);
            }
        } else if ($action == 'getstaff') {
            $entry_date = $request->input('entry_date');

            $SalesRepCreation_tb = (new SalesRepCreation)->getTable();
            $SalesOrderD2SMain_tb = (new SalesOrderD2SMain)->getTable();
            // $VisitorCreation_tb = (new VisitorCreation)->getTable();

            $sales_execs = SalesOrderD2SMain::select(
                $SalesRepCreation_tb . '.id',
                $SalesRepCreation_tb . '.sales_ref_name'
            )
                ->join($SalesRepCreation_tb, $SalesRepCreation_tb . '.id', '=', $SalesOrderD2SMain_tb . '.sales_exec')
                ->where($SalesOrderD2SMain_tb . '.entry_date', '=', $entry_date)
                ->where($SalesOrderD2SMain_tb . '.status', '=', '1')
                ->groupBy(
                    $SalesRepCreation_tb . '.id',
                    $SalesRepCreation_tb . '.sales_ref_name'
                )
                ->get();

            return response()->json($sales_execs);
        } else if ($action == 'delete_sub') {
            $tb = ExpenseCreationsSub::find($request->input('id'));
            $tb->delete_status = "1";
            $tb->save();
        } else if ($action == 'retrieve') {
            $expense_creations_main = $this->retrieve($request->input('from_date_1'), $request->input('to_date_1'), $request->input('expense_no_1'), $request->input('sales_rep_creation_id_1'), $request->input('market_manager_id_1'), $request->input('status_1'), $request->input('description'), $request->input('mode_of_payment_1'));
            if (!empty($expense_creations_main)) {
                foreach ($expense_creations_main as &$expense_creations_main1) {
                    if (!empty($expense_creations_main1['expense_date']) && !empty($expense_creations_main1['sales_ref_id'])) {

                        $starts = $this->retrieve_latitude_start($expense_creations_main1['expense_date'], $expense_creations_main1['sales_ref_id']);
                        if (!empty($starts)) {
                            $expense_creations_main1['start_latitude'] = $starts['latitude'];
                            $expense_creations_main1['start_langititude'] = $starts['langititude'];
                        } else {
                            $expense_creations_main1['start_latitude'] = null;
                            $expense_creations_main1['start_langititude'] = null;
                        }


                        $ends = $this->retrieve_latitude_over($expense_creations_main1['expense_date'], $expense_creations_main1['sales_ref_id']);
                        if (!empty($ends)) {
                            $expense_creations_main1['end_latitude'] = $ends['latitude'];
                            $expense_creations_main1['end_langititude'] = $ends['langititude'];
                        } else {
                            $expense_creations_main1['end_latitude'] = null;
                            $expense_creations_main1['end_langititude'] = null;
                        }

                        if (
                            isset($expense_creations_main1['start_latitude'], $expense_creations_main1['start_langititude'],
                                  $expense_creations_main1['end_latitude'], $expense_creations_main1['end_langititude']) &&
                            $expense_creations_main1['start_latitude'] !== null &&
                            $expense_creations_main1['start_langititude'] !== null &&
                            $expense_creations_main1['end_latitude'] !== null &&
                            $expense_creations_main1['end_langititude'] !== null
                        ) {
                            $expense_creations_main1['distance'] = $this->calculateDistance(
                                $expense_creations_main1['start_latitude'],
                                $expense_creations_main1['start_langititude'],
                                $expense_creations_main1['end_latitude'],
                                $expense_creations_main1['end_langititude']
                            );
                        } else {
                            $expense_creations_main1['distance'] = 'No data';
                        }
                    }
                }
            }

            return view('Reports.expense_creations_report.list', ['expense_creations_main' => $expense_creations_main, 'user_rights_edit_1' => $request->input('user_rights_edit_1'), 'user_rights_delete_1' => $request->input('user_rights_delete_1')]);
        } else if ($action == 'create_form') {
            $main_tb = (new ExpenseCreationsMain)->getTable();
            $next_id = DB::select("SHOW TABLE STATUS LIKE '" . $main_tb . "'");
            $expense_no = "SALEXP_" . date("ym") . "_" . $next_id[0]->Auto_increment;
            $market_creation = MarketCreation::select('id', 'area_name')->where(function ($query) {
                $query->where('delete_status', '0')->orWhereNull('delete_status');
            })->orderBy('area_name')->get();

            $market_manager_creation = MarketManagerCreation::select('id', 'manager_name')->where('status1', '0')->where(function ($query) {
                $query->where('delete_status', '0')->orWhereNull('delete_status');
            })->orderBy('manager_name')->get();

            return view('Reports.expense_creations_report.create', ['expense_no' => $expense_no, 'market_creation' => $market_creation, 'market_manager_creation' => $market_manager_creation]);
        } else if ($action == 'update_form') {
            $expense_creations_main = $this->retrieve_main($request->input('id'));

            $sales_rep_id = ExpenseCreationsMain::select('sales_rep_creation_id')->where('id', $expense_creations_main)->get()->first();

            $market_creation = MarketCreation::select('id', 'area_name')->where(function ($query) {
                $query->where('delete_status', '0')->orWhereNull('delete_status');
            })->orderBy('area_name')->get();

            $sales_ref_creation = SalesRepCreation::select('id', 'sales_ref_name')->where('status', '0')->where(function ($query) {
                $query->where('delete_status', '0')->orWhereNull('delete_status');
            })->orderBy('sales_ref_name')
                ->get();

            $dealer_creation = DealerCreation::select('id', 'dealer_name')->where('status', '1')->where(function ($query) {
                $query->where('delete_status', '0')->orWhereNull('delete_status');
            })->orderBy('dealer_name')->get();

            $market_manager_creation = MarketManagerCreation::select('id', 'manager_name')->where('status1', '0')->where(function ($query) {
                $query->where('delete_status', '0')->orWhereNull('delete_status');
            })->orderBy('manager_name')->get();



            return view('Reports.expense_creations_report.update', ['expense_creations_main' => $expense_creations_main, 'market_creation' => $market_creation, 'sales_ref_creation' => $sales_ref_creation, 'market_manager_creation' => $market_manager_creation]);
        } else if ($action == 'get_dealer') {
            $entry_date =  $request->input('expense_date');
            $sales_rep_id = $request->input('sales_rep_creation_id');
            $DealerCreation_tb = (new DealerCreation)->getTable();
            $SalesOrderD2SMain_tb = (new SalesOrderD2SMain)->getTable();
            $dealer_creation = SalesOrderD2SMain::select(
                $DealerCreation_tb . '.id',
                $DealerCreation_tb . '.dealer_name'
            )
                ->join($DealerCreation_tb, $DealerCreation_tb . '.id', '=', $SalesOrderD2SMain_tb . '.dealer_creation_id')
                ->where($SalesOrderD2SMain_tb . '.entry_date', '=', $entry_date)
                ->where($SalesOrderD2SMain_tb . '.sales_exec', '=', $sales_rep_id)
                ->where($DealerCreation_tb . '.status', '=', '1')
                ->groupBy(
                    $DealerCreation_tb . '.id',
                    $DealerCreation_tb . '.dealer_name'
                )
                ->get();

            return response()->json($dealer_creation);
        } else if ($action == 'get_visitor') {
            $entry_date =  $request->input('expense_date');
            $sales_rep_id = $request->input('sales_rep_creation_id');

            $SalesOrderD2SMain_tb = (new SalesOrderD2SMain)->getTable();
            $VisitorCreation_tb = (new VisitorCreation)->getTable();
            $visitor_creation = SalesOrderD2SMain::select(
                $VisitorCreation_tb . '.id',
                $VisitorCreation_tb . '.visitor_name'
            )

                ->join($VisitorCreation_tb, $VisitorCreation_tb . '.d2s_id', '=', $SalesOrderD2SMain_tb . '.id')
                ->where($VisitorCreation_tb . '.order_date', '=', $entry_date)
                ->where($VisitorCreation_tb . '.sales_exec', '=', $sales_rep_id)

                ->groupBy(
                    $VisitorCreation_tb . '.id',
                    $VisitorCreation_tb . '.visitor_name'
                )
                ->get();

            return response()->json($visitor_creation);
        } else if ($action == 'get_market') {
            $entry_date =  $request->input('entry_date');
            $dealer_sub_id = $request->input('dealer_sub_id');
            $sales_rep_creation_id = $request->input('sales_rep_creation_id');
            $MarketCreation_tb = (new MarketCreation)->getTable();
            $SalesOrderD2SMain_tb = (new SalesOrderD2SMain)->getTable();
            $sales_market = SalesOrderD2SMain::select(
                $MarketCreation_tb . '.id',
                $MarketCreation_tb . '.area_name'
            )
                ->join($MarketCreation_tb, $MarketCreation_tb . '.id', '=', $SalesOrderD2SMain_tb . '.market_creation_id')
                ->where($SalesOrderD2SMain_tb . '.entry_date', '=', $entry_date)
                ->where($SalesOrderD2SMain_tb . '.dealer_creation_id', '=', $dealer_sub_id)
                ->where($SalesOrderD2SMain_tb . '.sales_exec', '=', $sales_rep_creation_id)

                ->groupBy(
                    $MarketCreation_tb . '.id',
                    $MarketCreation_tb . '.area_name'
                )
                ->get();

            return response()->json($sales_market);
        } else if ($action == 'get_sub_expense') {











            $sub_expense_creation = SubExpenseTypeCreation::select('id', 'sub_expense_type')->where(function ($query) {
                $query->where('delete_status', '0')->orWhereNull('delete_status')->where('status', '0');
            })

                ->orderBy('sub_expense_type')
                ->get();
            return response()->json($sub_expense_creation);
        } else if ($action == 'form_sublist') {

            $main_id = $request->input('main_id');
            $sub_id = $request->input('sub_id');

            $visitor_creation = VisitorCreation::select('id', 'visitor_name')

                ->orderBy('visitor_name')
                ->get();


            // $main_tb = (new ExpenseCreationsMain)->getTable();
            // $sub_tb = (new ExpenseCreationsSub)->getTable();

            // $result = DB::table('expense_creations_sublist as es')
            // ->join('expense_creations_main as em', 'es.sales_expense_main_id', '=', 'em.id')
            // ->select('em.entry_date', 'es.dealer_sub_id', 'em.sales_rep_creation_id')
            // ->where('es.sales_expense_main_id', '=', $main_id)
            // ->get();





            $expense_creations_sub = null;
            if ($sub_id != "") {
                $expense_creations_sub = $this->retrieve_sub($main_id, $sub_id);
            }
            $expense_creations_sub_list = [];
            if ($main_id != "") {
                $expense_creations_sub_list = $this->retrieve_sub($main_id, '');
            }

            // $expense_creation = ExpenseTypeCreation::where(function ($query) {
            //     $query->where('expense_type', '!=', '')
            //         ->where('status', '!=', '1');
            // })
            // ->orderBy('expense_type')
            // ->select('id', 'expense_type')
            // ->get();

            $expense_creation = ExpenseTypeCreation::select('id', 'expense_type')->where(function ($query) {
                $query->where('delete_status', '0')->orWhereNull('delete_status')->where('status', '0');
            })->orderBy('expense_type')->get();



            $sub_expense_creation = SubExpenseTypeCreation::select('id', 'sub_expense_type')->where(function ($query) {
                $query->where('delete_status', '0')->orWhereNull('delete_status')->where('status', '0');
            })->orderBy('sub_expense_type')->get();

            $dealer_creation = DealerCreation::select('id', 'dealer_name')->where('status', '1')->where(function ($query) {
                $query->where('delete_status', '0')->orWhereNull('delete_status');
            })->orderBy('dealer_name')->get();

            $market_creation = MarketCreation::select('id', 'area_name')->where(function ($query) {
                $query->where('delete_status', '0')->orWhereNull('delete_status');
            })->orderBy('area_name')->get();

            $lastExpenseCreationId = ExpenseCreationsSub::orderBy('id', 'desc')
                ->select('dealer_sub_id')
                ->first();

            $last_id_sub_expense = $lastExpenseCreationId ? $lastExpenseCreationId->dealer_sub_id : null;

            $lastMarketCreationId = ExpenseCreationsSub::orderBy('id', 'desc')
                ->select('market_sub_id')
                ->first();

            $last_id_market = $lastMarketCreationId ? $lastMarketCreationId->market_sub_id : null;
            if ($main_id != '') {

                $result = DB::table('expense_creations_sublist as es')
                    ->join('expense_creations_main as em', 'es.sales_expense_main_id', '=', 'em.id')
                    ->select('em.expense_date', 'es.dealer_sub_id', 'em.sales_rep_creation_id')
                    ->where('es.sales_expense_main_id', '=', $main_id)
                    ->first();

                $expense_date = $result->expense_date;
                $dealer_sub_id = $result->dealer_sub_id;
                $sales_rep_creation_id = $result->sales_rep_creation_id;


                $sub_expense_creation = SubExpenseTypeCreation::select('id', 'sub_expense_type')->where(function ($query) {
                    $query->where('delete_status', '0')->orWhereNull('delete_status')->where('status', '0');
                })

                    ->orderBy('sub_expense_type')
                    ->get();

                $visitor_creation = VisitorCreation::select('id', 'visitor_name')
                    ->where('order_date', '=', $expense_date)
                    ->where('sales_exec', '=', $sales_rep_creation_id)
                    ->orderBy('visitor_name')
                    ->get();
                $MarketCreation_tb = (new MarketCreation)->getTable();
                $SalesOrderD2SMain_tb = (new SalesOrderD2SMain)->getTable();
                $market_creation = SalesOrderD2SMain::select(
                    $MarketCreation_tb . '.id',
                    $MarketCreation_tb . '.area_name'
                )
                    ->join($MarketCreation_tb, $MarketCreation_tb . '.id', '=', $SalesOrderD2SMain_tb . '.market_creation_id')
                    ->where($SalesOrderD2SMain_tb . '.entry_date', '=', $expense_date)
                    ->where($SalesOrderD2SMain_tb . '.dealer_creation_id', '=', $dealer_sub_id)
                    ->where($SalesOrderD2SMain_tb . '.sales_exec', '=', $sales_rep_creation_id)


                    ->groupBy(
                        $MarketCreation_tb . '.id',
                        $MarketCreation_tb . '.area_name'
                    )
                    ->get();


                $DealerCreation_tb = (new DealerCreation)->getTable();

                $dealer_creation = SalesOrderD2SMain::select(
                    $DealerCreation_tb . '.id',
                    $DealerCreation_tb . '.dealer_name'
                )
                    ->join($DealerCreation_tb, $DealerCreation_tb . '.id', '=', $SalesOrderD2SMain_tb . '.dealer_creation_id')
                    ->where($SalesOrderD2SMain_tb . '.entry_date', '=', $expense_date)
                    ->where($SalesOrderD2SMain_tb . '.sales_exec', '=', $sales_rep_creation_id)
                    ->where($DealerCreation_tb . '.status', '=', '1')
                    ->groupBy(
                        $DealerCreation_tb . '.id',
                        $DealerCreation_tb . '.dealer_name'
                    )
                    ->get();
            }


            $item_liters_type = ItemLitersType::select('id', 'item_liters_type')->where('status1', '1')->where(function ($query) {
                $query->where('delete_status', '0')->orWhereNull('delete_status');
            })->orderBy('item_liters_type')->get();

            // return $expense_creations_sub_list;
            return view('Reports.expense_creations_report.sublist', [
                'expense_creations_sub' => $expense_creations_sub,
                'dealer_creation' => $dealer_creation,
                'market_creation' => $market_creation,
                'expense_creations_sub_list' => $expense_creations_sub_list,
                'main_id' => $main_id,
                'sub_id' => $sub_id,
                'expense_creation' => $expense_creation,
                'last_id_sub_expense' => $last_id_sub_expense,
                'last_id_market' => $last_id_market,
                'sub_expense_creation' => $sub_expense_creation,
                'visitor_creation' => $visitor_creation,
                'item_liters_type' => $item_liters_type,
                'user_rights_edit_1' => $request->input('user_rights_edit_1'),
                'user_rights_delete_1' => $request->input('user_rights_delete_1')
            ]);
        }
    }
}
