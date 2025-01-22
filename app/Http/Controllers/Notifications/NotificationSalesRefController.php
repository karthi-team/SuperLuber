<?php

namespace App\Http\Controllers\Notifications;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\CountryCreation;
use App\Models\Notifications\NotificationSalesRef;
use App\Models\SalesRepCreation;
use App\Models\GroupCreation;
use App\Models\ItemCreation;
class NotificationSalesRefController extends Controller
{
    public function retrieve($id)
    {
        $GroupCreation_tb = (new GroupCreation)->getTable();
        $ItemCreation_tb = (new ItemCreation)->getTable();
        $NotificationSalesRef_tb = (new NotificationSalesRef)->getTable();

        $query = NotificationSalesRef::join($GroupCreation_tb, "$GroupCreation_tb.id", '=', "$NotificationSalesRef_tb.group_id")
            ->join($ItemCreation_tb, "$ItemCreation_tb.id", '=', "$NotificationSalesRef_tb.item_id");

        if ($id == '') {
            return $query->where("$NotificationSalesRef_tb.delete_status", '!=', 1)->orWhereNull("$NotificationSalesRef_tb.delete_status")->get([
                "$NotificationSalesRef_tb.id",
                "$NotificationSalesRef_tb.group_id",
                "$NotificationSalesRef_tb.datetime",
                "$GroupCreation_tb.group_name",
                "$ItemCreation_tb.item_name",
                "$NotificationSalesRef_tb.item_id",
                "$NotificationSalesRef_tb.status",
                "$NotificationSalesRef_tb.upd_images",
                "$NotificationSalesRef_tb.before_login_or_after_login",
                "$NotificationSalesRef_tb.description"
            ]);
        } else {
            return $query->where("$NotificationSalesRef_tb.id", '=', $id)
                ->where(function($query) use ($NotificationSalesRef_tb) {
                    $query->where("$NotificationSalesRef_tb.delete_status", '!=', 1)
                          ->orWhereNull("$NotificationSalesRef_tb.delete_status");
                })->get([
                "$NotificationSalesRef_tb.id",
                "$NotificationSalesRef_tb.group_id",
                "$NotificationSalesRef_tb.datetime",
                "$NotificationSalesRef_tb.item_id",
                "$GroupCreation_tb.group_name",
                "$ItemCreation_tb.item_name",
                "$NotificationSalesRef_tb.item_id",
                "$NotificationSalesRef_tb.upd_images",
                "$NotificationSalesRef_tb.before_login_or_after_login",
                "$NotificationSalesRef_tb.checkbox",
                "$NotificationSalesRef_tb.sales_ref_name",
                "$NotificationSalesRef_tb.notification_status",
                "$NotificationSalesRef_tb.description"
            ]);
        }
    }

    public function db_cmd(Request $request)
    {
        $action = $request->input('action');
        if ($action == 'insert') {
            $request->validate([
                'image_name.*' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            ]);
            $item_id = $request->input('item_id');
            $description = $request->input('description');
            $group_id = $request->input('group_id');
            $datetime = $request->input('datetime');
            $checkbox = $request->input('checkbox');
            $sales_ref_name = $request->input('sales_ref_name');
            $notification_status = $request->input('notification_status');
            $before_login_or_after_login = $request->input('before_login_or_after_login');
            $status = 1;
            $count = NotificationSalesRef::where('item_id', $item_id)
                ->where('group_id', $group_id)
                ->count();
            $tb = new NotificationSalesRef();
            $tb->group_id = $group_id;
            $tb->item_id = $item_id;
            $tb->datetime = $datetime;
            $tb->checkbox = $checkbox;
            $tb->sales_ref_name = $sales_ref_name;
            $tb->notification_status = $notification_status;
            $tb->before_login_or_after_login = $before_login_or_after_login;
            $tb->status = $status;
            $tb->description = $description;
            if ($request->hasFile('image_name')) {
                $FilePath = 'storage/notification_sales_ref_img/';
                $imageNames = [];
                foreach ($request->file('image_name') as $image) {
                    $Imagesaveas = "shopimg" . date('YmdHis') . "_" . uniqid() . "." . $image->getClientOriginalExtension();
                    $image->move($FilePath, $Imagesaveas);
                    $imageNames[] = $Imagesaveas;
                }
                $tb->upd_images = json_encode($imageNames);
            }
            $tb->save();
        }

        else if ($action == 'update') {

            $request->validate([
                'image_name.*' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            ]);
            $notification_id = $request->input('id');
            $group_id = $request->input('group_id');
            $item_id = $request->input('item_id');
            $description = $request->input('description');
            $datetime = $request->input('datetime');
            $checkbox = $request->input('checkbox');
            $sales_ref_name = $request->input('sales_ref_name');
            $notification_status = $request->input('notification_status');
            $before_login_or_after_login = $request->input('before_login_or_after_login');
            // Check if a record with the same state name and country exists
            $count = NotificationSalesRef::where('item_id', $item_id)
                ->where('group_id', $group_id)
                ->where('id', '!=', $notification_id) // Exclude the current record being updated
                ->where('delete_status', '0')->orWhereNull('delete_status')
                ->count();

            $tb = NotificationSalesRef::find($notification_id);
            if (!$tb) {
                return response()->json(['error' => 'Record not found.']);
            }
            $tb->group_id = $group_id;
            $tb->item_id = $item_id;
            $tb->description = $description;
            $tb->datetime = $datetime;
            $tb->before_login_or_after_login = $before_login_or_after_login;
            $tb->checkbox = $checkbox;
            $tb->sales_ref_name = $sales_ref_name;
            $tb->notification_status = $notification_status;

            if ($request->hasFile('image_name')) {
                $FilePath = 'storage/notification_sales_ref_img/';
                $imageNames = [];
                foreach ($request->file('image_name') as $image) {
                    $Imagesaveas = "shopimg" . date('YmdHis') . "_" . uniqid() . "." . $image->getClientOriginalExtension();
                    $image->move($FilePath, $Imagesaveas);
                    $imageNames[] = $Imagesaveas;
                }
                $tb->upd_images = json_encode($imageNames);
            }

            $tb->save();
            return response()->json(['message' => 'Record updated successfully']);
        }

        else if($action=='delete')
        {
            $tb = NotificationSalesRef::where('id', '=', $request->input('id'))->first();
            $tb->delete_status = 1;
            $tb->save();
        }
        else if($action=='retrieve')
        {
            $notification_for_sales_ref = $this->retrieve('');
            return view('Notifications.notification_for_sales_ref.list',['notification_for_sales_ref'=>$notification_for_sales_ref,'user_rights_edit_1'=>$request->input('user_rights_edit_1'),'user_rights_delete_1'=>$request->input('user_rights_delete_1')]);
        }
        else if($action=='count')
        {
            $cnt=0;
            $id=$request->input('id');$country_name=$request->input('country_name');
            if($id!="0"){$cnt=NotificationSalesRef::where('group_id','=',$country_name)->where('id','!=',$id)->count();}
            else{$cnt=NotificationSalesRef::where('group_id','=',$country_name)->count();}
            return $cnt;
        }
        else if($action=='create_form')
        {
            $country_name=CountryCreation::select('id','country_name')
                ->orderBy('country_name')
                ->get();
            $group_creation = GroupCreation::select('id', 'group_name')
                ->where('group_name', '!=', '')
                ->where(function($query){$query->where('delete_status', '0')->orWhereNull('delete_status');})
                ->orderBy('group_name')
                ->get();
            $sales_rep_creation = SalesRepCreation::select('id', 'sales_ref_name')
                ->where('sales_ref_name', '!=', '')
                ->where('status', '0')
                ->where(function ($query) {
                    $query->where('delete_status', '0')->orWhereNull('delete_status');
                })
                ->orderBy('sales_ref_name')
                ->get();
            return view('Notifications.notification_for_sales_ref.create', ['country_name'=>$country_name, 'group_names'=>$group_creation, 'sales_rep_creation' => $sales_rep_creation]);
        }
        else if($action=='update_form')
        {
            $country_name=CountryCreation::select('id','country_name')
                ->orderBy('country_name')
                ->get();

            $group_creation = GroupCreation::select('id', 'group_name')
                ->where('group_name', '!=', '')
                ->where(function($query){$query->where('delete_status', '0')->orWhereNull('delete_status');})
                ->orderBy('group_name')
                ->get();

            $item_name = ItemCreation::select('id', 'item_name')
                ->where(function ($query) {
                    $query->where('delete_status', '0')->orWhereNull('delete_status');
                })->orderBy('item_name')
                ->get();

            $sales_rep_creation = SalesRepCreation::select('id', 'sales_ref_name')
                ->where('sales_ref_name', '!=', '')
                ->where('status', '0')
                ->where(function ($query) {
                    $query->where('delete_status', '0')->orWhereNull('delete_status');
                })
                ->orderBy('sales_ref_name')
                ->get();

            $notification_for_sales_ref=$this->retrieve($request->input('id'));

            return view('Notifications.notification_for_sales_ref.update',['group_names'=>$group_creation,
            'item_names'=>$item_name,
            'sales_rep_creation'=>$sales_rep_creation, 'notification_for_sales_ref'=>$notification_for_sales_ref[0]]);

        }
        else if($action=='statusinfo')
        {
            $status = $request->input('status'); // Directly use the status from the request
            $tb = NotificationSalesRef::find($request->input('id'));
            $tb->status = $status;
            $tb->save();

        }

        else if ($action == 'getitemname') {

            $group_creation_id = $request->input('group_id');

             $item_name = ItemCreation::select('id', 'item_name')
             ->where(function ($query) {
                $query->where('delete_status', '0')->orWhereNull('delete_status');
            })->orderBy('item_name')
            ->where('group_id', '=', $group_creation_id)
            ->get();

            return response()->json($item_name);
        }


    }
}

