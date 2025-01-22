<?php
namespace App\Http\Controllers\Reports;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Models\AccountLedger;
use App\Models\DealerCreation;
use App\Models\MarketManagerCreation;
use App\Models\SalesRepCreation;
use App\Models\Entry\ReceiptEntry;
use App\Models\Entry\SalesOrderDeliveryMain;
use App\Models\Entry\SalesOrderDeliverySub;



class ReceiptReport extends Controller
{
    public function receipt_Report()
    {
        $manager_creation=MarketManagerCreation::select('id','manager_name')->where('status1', '0')->where('delete_status', '0')->orWhereNull('delete_status')->orderBy('manager_no')->get();

        $sales_name = SalesRepCreation::select('id', 'sales_ref_name')->where('status', '0')->where(function ($query) {
            $query->where('delete_status', '0')->orWhereNull('delete_status');
        })->orderBy('sales_ref_name')->get();

        $dealer_list = DealerCreation::select('id','dealer_name')->where('status', '1')->where(function ($query) {
            $query->where('delete_status', '0')->orWhereNull('delete_status');
        })->get();

        $tally_creation = SalesOrderDeliveryMain::select('id', 'tally_no')->where(function ($query) {
            $query->where('delete_status', '0')->orWhereNull('delete_status');
        })->orderBy('tally_no')->get();

        $ledger_name = AccountLedger::select('id', 'ledger_name')->where(function ($query) {
            $query->where('delete_status', '0')->orWhereNull('delete_status');
        })->orderBy('ledger_name')->get();

// return $tally_creation;

        return view('Reports.receipt_report.admin', [
            'manager_creation'=>$manager_creation,
            'sales_name'=>$sales_name,
            'dealer_list' => $dealer_list,
            'tally_creation' => $tally_creation,
            'ledger_name'=>$ledger_name,

        ]);
    }

    public function db_cmd(Request $request)
    {
        $action = $request->input('action');
        if ($action == 'retrieve') {
            $from_date = $request->input('from_date');
            $to_date = $request->input('to_date');

            $manager_name = $request->input('manager_name');
            $sales_name = $request->input('sales_name');
            $dealer_name = $request->input('dealer_name');
            $tally_no = $request->input('tally_no');
            $ledger_dr = $request->input('ledger_dr');
            $unorder = $request->input('unorder');

            if($ledger_dr!=''){
                $ledger_sas = $ledger_dr;
            }else{
                $ledger_sas = 'All';
            }

            if($tally_no!=''){
                $get_tally = SalesOrderDeliveryMain::find($tally_no);
                $final_tally = $get_tally->tally_no;
            }else{
                $final_tally = 'ALL';
            }

            if($sales_name!=''){
                $get_rep_name = SalesRepCreation::find($sales_name);
                $final_rep = $get_rep_name-> sales_ref_name;
            }else{
                $final_rep = 'ALL';
            }

        if($ledger_dr=='' && $from_date!='' && $unorder==''){
            $query = DB::table('sales_order_delivery_main_c as sodmc')
            ->select(
                'sodmc.dispatch_date',
                're.order_date',
                'rets.total_amount',
                'rets.paid_amount',
                'rets.bal_amount',
                're.manager_id',
                'mmc.manager_name',
                're.sales_exec',
                'src.sales_ref_name',
                're.dealer_creation_id',
                'dc.dealer_name',
                'sodmc.tally_no',
                'dc.whatsapp_no',
                'dc.address'
            )
            ->leftJoin('receipt_entry as re', 'sodmc.id', '=', 're.tally_no')
            ->leftJoin('receipt_entry_tally_sublist as rets', 'rets.receipt_entry_main_id', '=', 're.id')
            ->leftJoin('market_manager_creation as mmc', 'mmc.id', '=', 're.manager_id')
            ->leftJoin('sales_ref_creation as src', 'src.id', '=', 're.sales_exec')
            ->leftJoin('dealer_creation as dc', 'dc.id', '=', 're.dealer_creation_id')
            ->where('mmc.status1', '0')
            ->where('src.status', '0')
            ->where('dc.status', '1')
            ->where(function ($query) use ($from_date, $to_date, $manager_name, $sales_name, $dealer_name, $tally_no) {
                if ($from_date) {
                    $query->where('re.order_date', '>=', $from_date);
                }

                if ($to_date) {
                    $query->where('re.order_date', '<=', $to_date);
                }

                if ($manager_name) {
                    $query->where('re.manager_id', 'like', '%' . $manager_name . '%');
                }

                if ($sales_name) {
                    $query->where('re.sales_exec', 'like', '%' . $sales_name . '%');
                }

                if ($dealer_name) {
                    $query->where('re.dealer_creation_id', 'like', '%' . $dealer_name . '%');
                }

                if ($tally_no) {
                    $query->where('re.tally_no', '=', $tally_no);
                }
                $query->where(function ($query) {
                    $query->where('re.delete_status', 0)->orWhereNull('re.delete_status');
                });
                $query->where(function ($query) {
                    $query->where('rets.delete_status', 0)->orWhereNull('rets.delete_status');
                });
            });

            $result = $query->get();

            return view('Reports.receipt_report.list',
            [
                'result'=>$result,
                'from_date'=>$from_date,
                'to_date'=>$to_date,
                'final_tally'=>$final_tally,
                'final_rep'=>$final_rep,
                'user_rights_edit_1' => $request->input('user_rights_edit_1'),
                'user_rights_delete_1' => $request->input('user_rights_delete_1')
            ]);
        }
        else if($unorder!='' && $ledger_dr=='' && $tally_no==''){
            $from_date = $request->input('from_date');
            $to_date = $request->input('to_date');
            $sales_name = $request->input('sales_name');
            $dealer_name = $request->input('dealer_name');

            $results =  DB::table('sales_order_delivery_main_c as sodmc')
            ->select(
                'sodmc.dispatch_date',
                DB::raw('re.tally_no as tn'),
                DB::raw('SUM(sodsc.total_amount_1) as total_amount'),
                DB::raw('MAX(sodmc.sales_exec) as sales_exec'),
                DB::raw('MAX(src.sales_ref_name) as sales_ref_name'), // Use MAX as an example
                DB::raw('MAX(sodmc.dealer_creation_id) as dealer_creation_id'),
                'dc.dealer_name',
                'sodmc.tally_no',
                'dc.whatsapp_no',
                'dc.address',
                'sodmc.id'
            )
            ->leftJoin('receipt_entry as re', 'sodmc.id', '=', 're.tally_no')
            ->leftJoin('sales_order_delivery_sublist_c as sodsc', 'sodsc.sales_order_main_id', '=', 'sodmc.id')
            ->leftJoin('receipt_entry_tally_sublist as rets', 'rets.receipt_entry_main_id', '=', 're.id')
            ->leftJoin('market_manager_creation as mmc', 'mmc.id', '=', 're.manager_id')
            ->leftJoin('sales_ref_creation as src', 'src.id', '=', 'sodmc.sales_exec')
            ->leftJoin('dealer_creation as dc', 'dc.id', '=', 'sodmc.dealer_creation_id')
            ->where('mmc.status1', '0')
            ->where('src.status', '0')
            ->where('dc.status', '1')
            ->where(function ($query) use ($from_date, $to_date, $sales_name, $dealer_name) {
                if ($from_date) {
                    $query->where('sodmc.dispatch_date', '>=', $from_date);
                }

                if ($to_date) {
                    $query->where('sodmc.dispatch_date', '<=', $to_date);
                }

                if ($sales_name) {
                    $query->where('sodmc.sales_exec', '=', $sales_name);
                }

                if ($dealer_name) {
                    $query->where('sodmc.dealer_creation_id', '=', $dealer_name);
                }

                $query->where(function ($query) {
                    $query->where('sodmc.delete_status', 0)->orWhereNull('sodmc.delete_status');
                });
                $query->where(function ($query) {
                    $query->where('sodsc.delete_status', 0)->orWhereNull('sodsc.delete_status');
                });
            })
            ->groupBy('sodmc.dispatch_date', 're.tally_no', 'sodmc.dealer_creation_id', 'dc.dealer_name', 'sodmc.tally_no', 'dc.whatsapp_no', 'dc.address', 'sodmc.id')
            ->get();

            return view('Reports.receipt_report.dispatch_list', [
                'result' => $results,
                'from_date' => $from_date,
                'to_date' => $to_date,
                'final_tally' => $final_tally,
                'final_rep' => $final_rep,
                'user_rights_edit_1' => $request->input('user_rights_edit_1'),
                'user_rights_delete_1' => $request->input('user_rights_delete_1')
            ]);

        }
        else{
       $query = DB::table('receipt_entry as re')
    ->select(
        're.order_date',
        'res.amount',
        'res.ledger_cr',
        'src.sales_ref_name',
        're.manager_id',
        'mmc.manager_name',
        're.sales_exec',
        'al.ledger_name',
        're.dealer_creation_id',
        'dc.dealer_name',
        'dc.whatsapp_no',
        'dc.address'
    )
    ->leftJoin('receipt_entry_sub as res', 'res.receipt_entry_main_id', '=', 're.id')
    ->leftJoin('market_manager_creation as mmc', 'mmc.id', '=', 're.manager_id')
    ->leftJoin('sales_ref_creation as src', 'src.id', '=', 're.sales_exec')
    ->leftJoin('account_ledgers as al', 'al.id', '=', 'res.ledger_cr')
    ->leftJoin('dealer_creation as dc', 'dc.id', '=', 're.dealer_creation_id')
    ->where('mmc.status1', '0')
    ->where('src.status', '0')
    ->where('dc.status', '1')
    ->where(function ($query) use ($from_date, $to_date, $manager_name, $sales_name, $dealer_name, $ledger_dr) {
        if ($from_date) {
            $query->where('re.order_date', '>=', $from_date);
        }

        if ($to_date) {
            $query->where('re.order_date', '<=', $to_date);
        }

        if ($manager_name) {
            $query->where('re.manager_id', 'like', '%' . $manager_name . '%');
        }

        if ($sales_name) {
            $query->where('re.sales_exec', 'like', '%' . $sales_name . '%');
        }

        if ($dealer_name) {
            $query->where('re.dealer_creation_id', 'like', '%' . $dealer_name . '%');
        }

        if ($ledger_dr) {
            $query->where('re.ledger_dr', '=', $ledger_dr);
        }


        $query->where(function ($query) {
            $query->where('re.delete_status', 0)->orWhereNull('re.delete_status');
        });


        $query->where(function ($query) {
            $query->where('res.delete_status', 0)->orWhereNull('res.delete_status');
        });
    });

$result = $query->get();

            return view('Reports.receipt_report.listsub', [
                'result'=>$result,
                'from_date'=>$from_date,
                'to_date'=>$to_date,
                'ledger_sas'=>$ledger_sas,
                // 'ledger_name_val'=>$ledger_name_val,
                'user_rights_edit_1' => $request->input('user_rights_edit_1'),
                'user_rights_delete_1' => $request->input('user_rights_delete_1'),
            ]);
    }

        } else if ($action == 'getSalesRef') {


            $manager_id= $request->input('manager_id');

            $sales_ref_name = SalesRepCreation::select('id', 'sales_ref_name')->where('manager_id', $manager_id)->where('status', '0')->where(function ($query) {
                $query->where('delete_status', '0')->orWhereNull('delete_status');
            })->orderBy('sales_ref_name')->get();

            return response()->json($sales_ref_name);

        }
        else if ($action == 'getsalesrep_dealername') {

            $sales_exec = $request->input('sales_exec');

            $dealer_name = DealerCreation::select('id', 'dealer_name')
                ->where('sales_rep_id', $sales_exec)
                ->where('status', '1')
                ->where(function ($query) {
                    $query->where('delete_status', '0')->orWhereNull('delete_status');
                })
                ->get();

            return response()->json($dealer_name);

        }
        else if ($action == 'gettallynumber') {

            $SalesOrderDeliveryMain_tb = (new SalesOrderDeliveryMain)->getTable();
            $SalesOrderDeliverySub_tb = (new SalesOrderDeliverySub)->getTable();

            $dealer_creation_id = $request->input('dealer_creation_id');

            $tally_no = SalesOrderDeliveryMain::select($SalesOrderDeliveryMain_tb . '.id', $SalesOrderDeliveryMain_tb . '.tally_no')
            ->join($SalesOrderDeliverySub_tb, $SalesOrderDeliverySub_tb . '.sales_order_main_id', '=', $SalesOrderDeliveryMain_tb . '.id')
            ->where($SalesOrderDeliveryMain_tb . '.dealer_creation_id', $dealer_creation_id)
            ->where($SalesOrderDeliverySub_tb . '.bal_amount', '!=', 0)
            ->where(function ($query) use ($SalesOrderDeliveryMain_tb) {
                $query->where($SalesOrderDeliveryMain_tb . '.delete_status', '0')
                    ->orWhereNull($SalesOrderDeliveryMain_tb . '.delete_status');
            })
            ->where(function ($query) use ($SalesOrderDeliverySub_tb) {
                $query->where($SalesOrderDeliverySub_tb . '.delete_status', '0')
                    ->orWhereNull($SalesOrderDeliverySub_tb . '.delete_status');
            })
            ->groupBy($SalesOrderDeliveryMain_tb . '.id', $SalesOrderDeliveryMain_tb . '.tally_no')
            ->get();

            return response()->json($tally_no);
        }
    }
}
