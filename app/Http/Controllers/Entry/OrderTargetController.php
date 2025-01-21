<?php

namespace App\Http\Controllers\Entry;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Entry\OrderTarget;
use App\Models\MarketManagerCreation;
use App\Models\SalesRepCreation;
use App\Models\DealerCreation;
use App\Models\ItemCreation;
use App\Models\GroupCreation;
use App\Models\CategoryCreation;
use Carbon\Carbon;

class OrderTargetController extends Controller
{
    public function order_target()
    {
        $sales_ref_creation = SalesRepCreation::select('id', 'sales_ref_name')
            ->where('sales_ref_name', '!=', '')
            ->where('status', '0')
            ->where(function ($query) {
                $query->where('delete_status', '0')->orWhereNull('delete_status');
            })
            ->orderBy('sales_ref_name')
            ->get();
        return view('Entry.order_target.admin', ['sales_ref_creation' => $sales_ref_creation]);
    }
    public function retrieve($id)
    {
        if ($id == '') {
            return OrderTarget::select('id', 'entry_date', 'target_number', 'sales_executive_id','description')->where(function ($query) {
                $query->where('delete_status', '0')->orWhereNull('delete_status');
            })->orderBy('entry_date')->get();
        } else {
            return OrderTarget::select('id', 'entry_date', 'target_number', 'sales_executive_id','description', 'order_target')->where('id', '=', $id)->get()->first();
        }
    }
    public function retrieve_admin($from_date_1,$to_date_1,$sales_executive_id_1)
    {
        $cond = "";
        if ($from_date_1 != "") {
            $cond .= " and entry_date>='" . $from_date_1 . "'";
        }
        if ($to_date_1 != "") {
            $cond .= " and entry_date<='" . $to_date_1 . "'";
        }
        if ($sales_executive_id_1 != "") {
            $cond .= " and sales_executive_id='" . $sales_executive_id_1 . "'";
        }

        $tb1 = DB::select('SELECT id, entry_date, target_number, (select sales_ref_name from sales_ref_creation where id = order_target.sales_executive_id) as sales_executive_name FROM order_target WHERE (delete_status=0 OR delete_status IS NULL)' . $cond .'ORDER BY id DESC');

        return json_decode(json_encode($tb1), true);
    }
    public function db_cmd(Request $request)
    {
        $action = $request->input('action');

        if ($action == 'insert') {
            $tb = new OrderTarget();
            $tb->entry_date = $request->input('entry_date');
            $tb->target_number = $request->input('target_number');
            $tb->sales_executive_id = $request->input('sales_executive_id');
            $tb->description = $request->input('description');
            $tb->order_target = $request->input('order_target');
            $tb->save();

        } else if ($action == 'update') {

            $tb = OrderTarget::find($request->input('id'));
            if ($tb) {
                $tb->entry_date = $request->input('entry_date');
                $tb->target_number = $request->input('target_number');
                $tb->sales_executive_id = $request->input('sales_executive_id');
                $tb->description = $request->input('description');
                $tb->order_target = $request->input('order_target');
                $tb->save();
            }

        } else if ($action == 'delete') {

            $tb = OrderTarget::find($request->input('id'));
            $tb->delete_status = "1";
            $tb->save();

        } else if ($action == 'retrieve') {

            $order_target = $this->retrieve_admin($request->input('from_date_1'),$request->input('to_date_1'),$request->input('category_type_1'));
            return view('Entry.order_target.list', ['order_target' => $order_target]);

        } else if ($action == 'create_form') {

            $sales_ref_creation = SalesRepCreation::select('id', 'sales_ref_name')
            ->where('sales_ref_name', '!=', '')
            ->where('status', '0')
            ->where(function ($query) {
                $query->where('delete_status', '0')->orWhereNull('delete_status');
            })
            ->orderBy('sales_ref_name')
            ->get();

            $main_tb = (new OrderTarget)->getTable();
            $next_id = DB::select("SHOW TABLE STATUS LIKE '" . $main_tb . "'");
            $target_no = "TARGET_" . date("ym") . "_" . $next_id[0]->Auto_increment;

            return view('Entry.order_target.create', ['sales_ref_creation' => $sales_ref_creation, 'target_number' => $target_no]);

        } else if ($action == 'sublist_form') {

            $sales_executive_id = $request->input('sales_executive_id');

            $group_creation=GroupCreation::select('id','group_name')->where(function($query){$query->where('delete_status', '0')->orWhereNull('delete_status');})->orderBy('group_name')->get();

            $item_creation=ItemCreation::select('id','short_code','group_id')->where(function($query){$query->where('delete_status', '0')->orWhereNull('delete_status');})->orderBy('short_code')->get();

            $dealer_creation = DealerCreation::select('id', 'dealer_name','sales_rep_id')
            ->where('dealer_name', '!=', '')
            ->where('status', '1')
            ->where('sales_rep_id', $sales_executive_id)
            ->where(function ($query) {
                $query->where('delete_status', '0')->orWhereNull('delete_status');
            })
            ->orderBy('dealer_name')
            ->get();

            return view('Entry.order_target.sublist',
                ['group_creation' => $group_creation, 'dealer_creation' => $dealer_creation, 'item_creation' => $item_creation]);

        } else if ($action == 'update_sublist_form') {

            $id = $request->input('main_id');
            $order_target = '';
            if ($id != '') {
                $order_target = $this->retrieve($id);
            }
            if ($order_target != '') {
                $sales_executive_id = $order_target->sales_executive_id;
            } else {
                $sales_executive_id = $request->input('sales_executive_id');
            }

            $group_creation=GroupCreation::select('id','group_name')->where(function($query){$query->where('delete_status', '0')->orWhereNull('delete_status');})->orderBy('group_name')->get();

            $item_creation=ItemCreation::select('id','short_code','group_id')->where(function($query){$query->where('delete_status', '0')->orWhereNull('delete_status');})->orderBy('short_code')->get();

            $dealer_creation = DealerCreation::select('id', 'dealer_name','sales_rep_id')
            ->where('dealer_name', '!=', '')
            ->where('status', '1')
            ->where('sales_rep_id', $sales_executive_id)
            ->where(function ($query) {
                $query->where('delete_status', '0')->orWhereNull('delete_status');
            })
            ->orderBy('dealer_name')
            ->get();

            return view('Entry.order_target.sublist_update',
                ['order_target_sublist' => $order_target, 'group_creation' => $group_creation, 'dealer_creation' => $dealer_creation, 'item_creation' => $item_creation]);

        } else if ($action == 'update_form') {

            $sales_ref_creation = SalesRepCreation::select('id', 'sales_ref_name')
            ->where('sales_ref_name', '!=', '')
            ->where('status', '0')
            ->where(function ($query) {
                $query->where('delete_status', '0')->orWhereNull('delete_status');
            })
            ->orderBy('sales_ref_name')
            ->get();

            $order_target = $this->retrieve($request->input('id'));
            return view('Entry.order_target.update', ['sales_ref_creation' => $sales_ref_creation, 'order_target' => $order_target]);
        }
    }
    public function db_cmd1(Request $request)
    {
        $action = $request->input('action');

        if ($action == 'insert') {
            $tb = new OrderTarget();
            $tb->entry_date = $request->input('entry_date');
            $tb->target_number = $request->input('target_number');
            $tb->sales_executive_id = $request->input('sales_executive_id');
            $tb->description = $request->input('description');
            $tb->order_target = $request->input('order_target');
            $tb->save();

        } else if ($action == 'update') {

            $tb = OrderTarget::find($request->input('id'));
            if ($tb) {
                $tb->entry_date = $request->input('entry_date');
                $tb->target_number = $request->input('target_number');
                $tb->sales_executive_id = $request->input('sales_executive_id');
                $tb->description = $request->input('description');
                $tb->order_target = $request->input('order_target');
                $tb->save();
            }

        }
    }
}
