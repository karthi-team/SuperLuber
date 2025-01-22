<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\SalesRepCreation;
use App\Models\DealerCreation;
use App\Models\ShopCreation;
use App\Models\MarketCreation;
use Illuminate\Http\Request;
use Carbon\Carbon;

class DealerCreationApiController extends Controller
{
    public function sales_executive_assigned_dealers_api(Request $request)
    {
        $sales_executive_id = $request->input('sales_executive_id');

        if (empty($sales_executive_id)) {
            return response()->json(['status' => 'FAILURE', 'message' => 'Sales Executive Id Not Found'], 404);
        }

        $dealer_query = DealerCreation::select('id as dealer_id','dealer_name','mobile_no','address','place')
            ->where('sales_rep_id', $sales_executive_id)
            ->where('status', '1')
            ->where(function ($query) {
                $query->where('delete_status', '0')->orWhereNull('delete_status');
            })
            ->get();

        if(!$dealer_query->isEmpty()){
            return response()->json(['status' => 'SUCCESS', 'message' => 'Assigned Dealers List Showed Successfully', 'dealer_query' => $dealer_query], 200);
        }else{
            return response()->json(['status' => 'FAILURE', 'message' => 'Assigned Dealers List Not Found'], 404);
        }
    }

    public function dealer_assigned_market_api(Request $request)
    {
        $dealer_id = $request->input('dealer_id');

        if (empty($dealer_id)) {
            return response()->json(['status' => 'FAILURE', 'message' => 'Dealer Id Not Found'], 404);
        }

        $dealer_creation = DealerCreation::find($dealer_id);
        $market_id = $dealer_creation->area_id;
        $market_ids = explode(",", $market_id);
        $area_names = [];
        $cnt_values = [];
        foreach ($market_ids as $marketId) {
            $area_name = MarketCreation::select('id as market_id', 'area_name as market_name', 'state_id', 'district_id', 'week_day', 'description')->where('id', $marketId)->get();
            if ($area_name) {
                $area_names[] = $area_name;

                $cnt = ShopCreation::where('beats_id', '=', $marketId)
                    ->where('dealer_id', '=', $dealer_id)
                    ->where(function ($query) {
                        $query->where('delete_status', '0')->orWhereNull('delete_status');
                    })
                    ->count();
                $cnt_values[] = $cnt;
            }
        }

        if (!$area_name->isEmpty()) {
            return response()->json(['status' => 'SUCCESS', 'message' => 'Dealer Assigned Markets List Showed Successfully', 'dealer_id' => $dealer_id, 'area_names' => $area_names, 'count' => $cnt_values], 200);
        } else {
            return response()->json(['status' => 'FAILURE', 'message' => 'Dealer Assigned Markets List Not Found'], 404);
        }
    }

    public function shops_list_api(Request $request)
    {
        $dealer_id = $request->input('dealer_id');
        $market_id = $request->input('market_id');

        if (empty($dealer_id)) {
            return response()->json(['status' => 'FAILURE', 'message' => 'Dealer Id Not Found'], 404);
        }
        if (empty($market_id)) {
            return response()->json(['status' => 'FAILURE', 'message' => 'Market Id Not Found'], 404);
        }

        $shop_list_query = ShopCreation::select('id as shop_id', 'shop_name', 'whatsapp_no', 'gst_no', 'language')
            ->where('beats_id','=',$market_id)
            ->where('dealer_id','=',$dealer_id)
            ->orderBy('id')
            ->get();

        if(!$shop_list_query->isEmpty()){
            return response()->json(['status' => 'SUCCESS', 'message' => 'Shop List Showed Successfully', 'shop_list' => $shop_list_query], 200);
        }else{
            return response()->json(['status' => 'FAILURE', 'message' => 'Shop List Not Found'], 404);
        }
    }

    public function shops_insert_api(Request $request)
    {
        $shop_type_id = $request->input('shop_type_id');
        $shop_name = $request->input('shop_name');
        $beats_id = $request->input('market_id');
        $dealer_id = $request->input('dealer_id');
        $mobile_no = $request->input('mobile_number');
        $whatsapp_no = $request->input('whatsapp_number');
        $address = $request->input('address');
        $gst_no = $request->input('gst_no');
        $language = $request->input('language');
        $latitude = $request->input('latitude');
        $longitude = $request->input('longitude');

        if (empty($shop_type_id)) {
            return response()->json(['status' => 'FAILURE', 'message' => 'Shop Type Not Found'], 404);
        }
        if (empty($shop_name)) {
            return response()->json(['status' => 'FAILURE', 'message' => 'Shop Name Not Found'], 404);
        }
        if (empty($dealer_id)) {
            return response()->json(['status' => 'FAILURE', 'message' => 'Dealer Id Not Found'], 404);
        }
        if (empty($beats_id)) {
            return response()->json(['status' => 'FAILURE', 'message' => 'Market Id Not Found'], 404);
        }

        $crnt_date= date('Y-m-d');
        $shop_insert = new ShopCreation();
        $shop_insert->entry_date = $crnt_date;
        $shop_insert->shop_type_id = $shop_type_id;
        $shop_insert->shop_name = $shop_name;
        $shop_insert->beats_id = $beats_id;
        $shop_insert->dealer_id = $dealer_id;
        $shop_insert->mobile_no = $mobile_no;
        $shop_insert->whatsapp_no = $whatsapp_no;
        $shop_insert->address = $address;
        $shop_insert->gst_no = $gst_no;
        $shop_insert->language = $language;
        $shop_insert->latitude = $latitude;
        $shop_insert->longitude = $longitude;

        $input = $request->all();
        
        if ($request->hasFile('image_name')) {
            $image = $request->file('image_name');
            $imgName = "shopimg" . date('YmdHis') . "_" . uniqid() . "." . $image->getClientOriginalExtension();
            $image->move('storage/shop_img', $imgName);
            $shop_insert->image_name = $imgName;
        }
        
        $shop_insert->save();

        return response()->json(['status' => 'SUCCESS', 'message' => 'Shop Inserted Successfully', 'shop_list' => $shop_insert], 200);
       
    }

    public function shops_edit_api(Request $request)
    {
        $id = $request->input('shop_id');

        if (empty($id)) {
            return response()->json(['status' => 'FAILURE', 'message' => 'Shop Id Not Found'], 404);
        }

        $shop_creation_edit = ShopCreation::select('id as shop_id','shop_name','shop_type_id','beats_id as market_id','dealer_id','mobile_no as mobile_number','whatsapp_no as whatsapp_number','address','gst_no','image_name','language')->orderBy('id')->where('id','=',$id)->get();

        foreach ($shop_creation_edit as $shop_creation_edits) {

            $image = $shop_creation_edits->image_name;
            
            if ($image != '') {
                $shop_creation_edits->img_url = asset('/storage/shop_img/' . trim($image));
                
            } else {
                $shop_creation_edits->img_url = asset('/storage/shop_img/' . $shop_creation_edits->image_name);
            }
        }
        

        return response()->json(['status' => 'SUCCESS', 'message' => 'Shop Edit Showed Successfully', 'shop_list' => $shop_creation_edit], 200);
       
    }

    public function shops_update_api(Request $request)
    {
        $id = $request->input('shop_id');
        $shop_type_id = $request->input('shop_type_id');
        $shop_name = $request->input('shop_name');
        $beats_id = $request->input('market_id');
        $dealer_id = $request->input('dealer_id');
        $mobile_no = $request->input('mobile_no');
        $whatsapp_no = $request->input('whatsapp_no');
        $address = $request->input('address');
        $gst_no = $request->input('gst_no');
        $language = $request->input('language');
        $latitude = $request->input('latitude');
        $longitude = $request->input('longitude');

        if (empty($shop_type_id)) {
            return response()->json(['status' => 'FAILURE', 'message' => 'Shop Type Not Found'], 404);
        }
        if (empty($shop_name)) {
             return response()->json(['status' => 'FAILURE', 'message' => 'Shop Name Not Found'], 404);
        }
        if (empty($dealer_id)) {
            return response()->json(['status' => 'FAILURE', 'message' => 'Dealer Id Not Found'], 404);
        }
        if (empty($beats_id)) {
            return response()->json(['status' => 'FAILURE', 'message' => 'Market Id Not Found'], 404);
        }

        $tb = ShopCreation::find($id);
        if ($tb) {
            $tb->shop_type_id = $shop_type_id;
            $tb->shop_name = $shop_name;
            $tb->mobile_no = $mobile_no;
            $tb->whatsapp_no = $whatsapp_no;
            $tb->address = $address;
            $tb->gst_no = $gst_no;
            $tb->language = $language;
            $tb->latitude = $latitude;
            $tb->longitude = $longitude;
 
            $input = $request->all();

            if ($request->hasFile('image_name')) {
                $image = $request->file('image_name');
                $imgName = "shopimg" . date('YmdHis') . "_" . uniqid() . "." . $image->getClientOriginalExtension();
                $image->move('storage/shop_img', $imgName);
                $tb->image_name = $imgName;
            }
            $tb->save();
        }

        return response()->json(['status' => 'SUCCESS', 'message' => 'Shop Updated Successfully', 'shop_id' => $tb], 200);
    }
    
    public function shops_location_find_api(Request $request)
    {
        $shop_id = $request->input('shop_id');
        $latitude2 = $request->input('latitude');
        $longitude2 = $request->input('longitude');

        if (empty($shop_id)) {
            return response()->json(['status' => 'FAILURE', 'message' => 'Shop Id Not Found'], 404);
        }
        if (empty($latitude2)) {
             return response()->json(['status' => 'FAILURE', 'message' => 'Latitude Not Found'], 404);
        }
        if (empty($longitude2)) {
            return response()->json(['status' => 'FAILURE', 'message' => 'Longitude Not Found'], 404);
        }

        $shop_creation = ShopCreation::select('id as shop_id','shop_name','latitude','longitude')
            ->where('id', $shop_id)
            ->where('latitude', '!=', '')
            ->where('longitude', '!=', '')
            ->first();

        if ($shop_creation) {
            $latitude1 = $shop_creation->latitude;
            $longitude1 = $shop_creation->longitude;
        } else {
            return response()->json(['status' => 'FAILURE', 'message' => 'Longitude and Longitude Not Found'], 404);
        }

        $theta = $longitude1 - $longitude2;
        $dist = sin(deg2rad($latitude1)) * sin(deg2rad($latitude2)) +  cos(deg2rad($latitude1)) * cos(deg2rad($latitude2)) * cos(deg2rad($theta));
        $dist = acos($dist);
        $dist = rad2deg($dist);
        $distance = $dist * 60 * 1.1515 * 1609.34;
        $maxDistance = 7;

        if ($distance <= $maxDistance) {
            return response()->json(['status' => 'SUCCESS', 'message' => "Point ($latitude2, $longitude2) is within $maxDistance meters from point ($latitude1, $longitude1)"], 200);
        } else {
            return response()->json(['status' => 'FAILURE', 'message' => "Point ($latitude2, $longitude2) is more than $maxDistance meters away from point ($latitude1, $longitude1)"], 404);
        }
    }
}
