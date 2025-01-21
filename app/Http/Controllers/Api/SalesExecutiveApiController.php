<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\StateCreation;
use App\Models\DistrictCreation;
use App\Models\MarketCreation;
use App\Models\SalesRepCreation;
use App\Models\DealerCreation;
use App\Models\MarketManagerCreation;

class SalesExecutiveApiController extends Controller
{

    public function sales_exe_profile_api(Request $request)
    {

        $id = $request->input('sales_executive_id');

        if (empty($id)) {
            return response()->json(['status' => 'FAILURE', 'message' => 'Sales Executive Id Not Found'], 404);
        }

        $img_url = '';
        $sales_rep_name=SalesRepCreation::select('id','sales_ref_name','mobile_no','phone_no','address','image_name','username','password','confirm_password')->where('id', $id)->first();

        
            
        if($sales_rep_name)
        {
            $sales_rep_name->img_url = asset('/storage/barang/' . $sales_rep_name->image_name);

            return response()->json(['status' => 'SUCCESS', 'message' => 'Sales Executive Profile showed Successfully','sales_Executive_profile_view' => $sales_rep_name ], 200);
        }
        else{
            return response()->json(['status' => 'FAILURE', 'message' => 'Sales Executive Id Not Found'], 404);  
        }

    }

    public function sales_exe_profile_update_api(Request $request)
    {

        $id = $request->input('sales_executive_id');
        $sales_ref_name = $request->input('sales_executive_name');
        $mobile_no = $request->input('mobile_no');
        $phone_no = $request->input('whatsapp_no');
        $address = $request->input('address');
        $username = $request->input('username');
        $password = $request->input('password');
        $confirm_password = $request->input('confirm_password');

        if (empty($id)) {
            return response()->json(['status' => 'FAILURE', 'message' => 'Sales Executive Id Not Found'], 404);
        }

        if (empty($sales_ref_name)) {
            return response()->json(['status' => 'FAILURE', 'message' => 'Sales Executive Name Not Found'], 404);
        }

        $tb = SalesRepCreation::find($id);
        $tb->sales_ref_name = $sales_ref_name;
        $tb->mobile_no = $mobile_no;
        $tb->phone_no = $phone_no;
        $tb->address = $address;
        $tb->username = $username;
        $tb->password = $password;
        $tb->confirm_password = $confirm_password;

        if ($request->hasFile('image_name')) {
            $image = $request->file('image_name');
            $FilePath = 'storage/barang/';
            $Imagesaveas = $sales_ref_name . "." . $image->getClientOriginalExtension();
            $image->move($FilePath, $Imagesaveas);
            $input['image'] = "$Imagesaveas";

            $tb->image_name = $input['image'];

        }
        $tb->save();

        return response()->json(['status' => 'SUCCESS', 'message' => 'Sales Executive Profile Update Successfully','sales_executive_profile_update' => $id ], 200);
    }

}