<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ShopsType;
use App\Models\ShopCreation;
use App\Models\SupplierCreation;
use Illuminate\Support\Facades\Log;
class ShopsTypeController extends Controller
{
    public function retrieve($id)
    {
        if($id=='')
        {return SupplierCreation::select('id','supplier_name','supplier_id','contact_person','contact_number','email_id','address','gst_number','creation_time','next_review_date','status','description')->orderBy('id')->get();}
        else
        {return SupplierCreation::select('id','supplier_name','supplier_id','contact_person','contact_number','email_id','address','gst_number','creation_time','next_review_date','status','description')->orderBy('id')->where('id','=',$id)->get();}
    }


    public function db_cmd(Request $request)
{
    $action = $request->input('action');
    Log::info($action);

    if ($action == 'insert') {
        // Insert logic
        $tb = new SupplierCreation();
        $tb->supplier_name = $request->input('supplier_name');
        $tb->supplier_id = $request->input('supplier_id');
        $tb->contact_person = $request->input('contact_person');
        $tb->contact_number = $request->input('contact_number');
        $tb->email_id = $request->input('email_id');
        $tb->address = $request->input('address');
        $tb->gst_number = $request->input('gst_number');
        $tb->creation_time = $request->input('creation_time');
        $tb->next_review_date = $request->input('next_review_date');
        $tb->status = $request->input('status');
        $tb->description = $request->input('description');
        $tb->save();

        // Log the data before saving
        Log::info('Inserting new shop record:', [
            'supplier_name' => $tb->supplier_name,
            'supplier_id' => $tb->supplier_id,
            'contact_person' => $tb->contact_person,
            'contact_number' => $tb->contact_number,
            'email_id' => $tb->email_id,
            'address' => $tb->address,
            'gst_number' => $tb->gst_number,
            'creation_time' => $tb->creation_time,
            'next_review_date' => $tb->next_review_date,
            'status' => $tb->status,
        ]);
    }
    else if ($action == 'update') {
        // Update logic
        $tb = SupplierCreation::find($request->input('id'));

        if ($tb) {
            // Update fields with values from the request
            $tb->supplier_name = $request->input('supplier_name');
            $tb->supplier_id = $request->input('supplier_id');
            $tb->contact_person = $request->input('contact_person');
            $tb->contact_number = $request->input('contact_number');
            $tb->email_id = $request->input('email_id');
            $tb->address = $request->input('address');
            $tb->gst_number = $request->input('gst_number');
            $tb->creation_time = $request->input('creation_time');
            $tb->next_review_date = $request->input('next_review_date');
            $tb->status = $request->input('status');
            $tb->description = $request->input('description');

            // Save the updated data
            $tb->save();



            return response()->json(['success' => true, 'message' => 'Shop record updated successfully']);
        } else {
            // Log if the record doesn't exist
            Log::error('Shop record not found for update', ['id' => $request->input('id')]);

            return response()->json(['success' => false, 'message' => 'Shop record not found']);
        }
    }


    else if ($action == 'delete') {
        // Delete logic
        $tb = SupplierCreation::find($request->input('id'));

        if ($tb) {
            $tb->delete();
            // Log deletion
            Log::info('Deleted shop record:', ['id' => $request->input('id')]);
        } else {
            // Log if the record doesn't exist
            Log::error('Shop record not found for deletion', ['id' => $request->input('id')]);
        }
    }
    else if ($action == 'retrieve') {
        // Retrieve logic
        $shops_type = $this->retrieve('');
        // Convert the collection to an array before logging
        Log::info("Retrieved shops type:", $shops_type->toArray());
        return view('Masters.shops_type.list', [
            'shops_type' => $shops_type,
            'user_rights_edit_1' => $request->input('user_rights_edit_1'),
            'user_rights_delete_1' => $request->input('user_rights_delete_1')
        ]);
    }

    else if ($action == 'count') {
        // Count logic
        $cnt = 0;
        $id = $request->input('id');
        $shops_type = $request->input('shops_type');
        if ($id != "0") {
            $cnt = SupplierCreation::where('shops_type', '=', $shops_type)
                                    ->where('id', '!=', $id)
                                    ->count();
        } else {
            $cnt = SupplierCreation::where('shops_type', '=', $shops_type)->count();
        }
        return $cnt;
    }
    else if ($action == 'create_form') {
        return view('Masters.shops_type.create');
    }
    else if ($action == 'update_form') {
        $shops_type = $this->retrieve($request->input('id'));
        // Log the data properly
        Log::info("shops_type 123", $shops_type->toArray());
        return view('Masters.shops_type.update', ['shops_type' => $shops_type[0]]);
    }
    else if ($action == 'statusinfo') {
        $stat = $request->input('status') == 1 ? "0" : "1";
        $tb = SupplierCreation::find($request->input('id'));
        if ($tb) {
            $tb->status = $stat;
            $tb->save();
        } else {
            Log::error('Shop record not found for status update', ['id' => $request->input('id')]);
        }
    }
}

}
