<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\DesignationCreation;
use App\Models\AlertCreation;
use Illuminate\Support\Facades\Log;
class DesignationCreationController extends Controller
{
    public function retrieve($id)
    {
        if($id=='')
        {return AlertCreation::select('id','alert_title','status','description','alert_type')->orderBy('alert_title')->get();}
        else
        {return AlertCreation::select('id','alert_title','status','description','alert_type')->orderBy('alert_title')->where('id','=',$id)->get();}
    }
    public function db_cmd(Request $request)
    {
        $action=$request->input('action');
        if ($action == 'insert') {
            // Insert new record
            $tb = new AlertCreation();
            $tb->alert_title = $request->input('alert_title');
            $tb->alert_type = $request->input('alert_type');
            $tb->status = $request->input('status', '1'); // Default to '1' if not provided
            $tb->description = $request->input('description');
            $tb->save();

            // Optionally, you could return a success response or the created object
            // return response()->json(['message' => 'Alert created successfully', 'data' => $tb], 201);
        } else if ($action == 'update') {
            // Find the record by ID
            $tb = AlertCreation::find($request->input('id'));

            if ($tb) {
                // Update the record if found
                $tb->alert_title = $request->input('alert_title');
                $tb->alert_type = $request->input('alert_type');
                $tb->description = $request->input('description');
                $tb->status = $request->input('status', '1'); // Default to '1' if not provided
                $tb->save();

                // Optionally, you could return a success response or the updated object
                // return response()->json(['message' => 'Alert updated successfully', 'data' => $tb]);
            } else {
                // If the record doesn't exist, log an error or return a response
                Log::error('AlertCreation record not found for update', ['id' => $request->input('id')]);
                return response()->json(['error' => 'Record not found'], 404);
            }
        }

        else if ($action == 'delete') {
            // Delete the record
            $tb = AlertCreation::find($request->input('id'));

            if ($tb) {
                $tb->delete();
                // Log deletion
                Log::info('Deleted alert record:', ['id' => $request->input('id')]);
            } else {
                // Log if the record doesn't exist
                Log::error('AlertCreation record not found for deletion', ['id' => $request->input('id')]);
            }
        }
        else if($action=='retrieve')
        {
            $designation_creation = $this->retrieve('');
            return view('Masters.designation_creation.list',['designation_creation'=>$designation_creation,'user_rights_edit_1'=>$request->input('user_rights_edit_1'),'user_rights_delete_1'=>$request->input('user_rights_delete_1')]);
        }
        // else if($action=='count')
        // {
        //     $cnt=0;
        //     $id=$request->input('id');$designation_creation=$request->input('designation_name');
        //     if($id!="0"){$cnt=DesignationCreation::where('designation_name','=',$designation_name)->where('id','!=',$id)->count();}
        //     else{$cnt=DesignationCreation::where('designation_name','=',$designation_name)->count();}
        //     return $cnt;
        // }
        else if($action=='create_form')
        {
            return view('Masters.designation_creation.create');
        }
        else if($action=='update_form')
        {
            $designation_creation=$this->retrieve($request->input('id'));
            return view('Masters.designation_creation.update',['designation_creation'=>$designation_creation[0]]);
        }
        // else if($action=='statusinfo')
        // {
        //     $stat = $request->input('status')==1 ? "0" : "1";

        //     $tb = DesignationCreation::find($request->input('id'));
        //     $tb->status = $stat;
        //     $tb->save();

        // }
        else if ($action == 'statusinfo') {
            $stat = $request->input('status') == 1 ? "0" : "1";
            $tb = AlertCreation::find($request->input('id'));
            if ($tb) {
                $tb->status = $stat;
                $tb->save();
            } else {
                Log::error('Shop record not found for status update', ['id' => $request->input('id')]);
            }
        }
    }
}
