<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\SalesRepCreation;
use App\Models\SalesExecutiveTimelogs;
use Illuminate\Http\Request;
use Carbon\Carbon;

class LoginApiController extends Controller
{
    public function login_application_api(Request $request)
    {
        $username = $request->input('username');
        $password = $request->input('password');
        $latitude = $request->input('latitude');
        $langititude = $request->input('longitude');

        if (empty($username)) {
            return response()->json(['status' => 'FAILURE', 'message' => 'Please enter a username'], 404);
        }
        if (empty($password)) {
            return response()->json(['status' => 'FAILURE', 'message' => 'Please enter a password'], 404);
        }

        $sales_executive_name = SalesRepCreation::select('id as sales_executive_id', 'manager_id', 'sales_ref_name as sales_executive_name', 'mobile_no', 'phone_no', 'address', 'aadhar_no', 'driving_licence', 'state_id', 'district_id', 'image_name', 'username', 'password', 'status')
            ->where('username', $username)
            ->where(function ($query) {
                $query->where('delete_status', '0')->orWhereNull('delete_status');
            })
            ->where('status', 0)
            ->first();

        if ($sales_executive_name) {
            if ($password === $sales_executive_name->password) {
                if ($sales_executive_name->status==0) {

                    $imgUrls = '';
                    $imgUrls = asset('storage/barang/' . $sales_executive_name->image_name);
                    $sales_executive_name->img_urls = $imgUrls;
                    $currentDate = Carbon::today();
                    $currentTime = Carbon::now('Asia/Kolkata')->toTimeString();

                    $tb = new SalesExecutiveTimelogs();
                    $tb->date = $currentDate;
                    $tb->time = $currentTime;
                    $tb->sales_executive_id = $sales_executive_name->sales_executive_id;
                    $tb->latitude = $latitude;
                    $tb->langititude = $langititude;
                    $tb->current_status = 'Login';
                    $tb->save();

                    return response()->json(['status' => 'SUCCESS', 'message' => 'Login successfully', 'user' => $sales_executive_name], 200);
                } else {
                    return response()->json(['status' => 'FAILURE', 'message' => 'Oops! You have not active user'], 404);
                }
            } else {
                return response()->json(['status' => 'FAILURE', 'message' => 'Oops! Your password is wrong'], 404);
            }
        } else {
            return response()->json(['status' => 'FAILURE', 'message' => 'Oops! You have entered invalid credentials'], 404);
        }
    }

    public function logout_application_api(Request $request)
    {
        $sales_executive_id = $request->input('sales_executive_id');
        $latitude = $request->input('latitude');
        $langititude = $request->input('longitude');

        if (empty($sales_executive_id)) {
            return response()->json(['status' => 'FAILURE', 'message' => 'Logout Failed'], 404);
        } else {
            $currentDate = Carbon::today();
            $currentTime = Carbon::now('Asia/Kolkata')->toTimeString();

            $tb = new SalesExecutiveTimelogs();
            $tb->date = $currentDate;
            $tb->time = $currentTime;
            $tb->sales_executive_id = $sales_executive_id;
            $tb->latitude = $latitude;
            $tb->langititude = $langititude;
            $tb->current_status = 'Logout';
            $tb->save();

            return response()->json(['status' => 'SUCCESS', 'message' => 'Logout Successfully'], 200);
        }
    }

    public function forgot_password_application_api(Request $request)
    {
        $mobile_no = $request->input('mobile_no');
        $password = $request->input('new_password');
        $confirm_password = $request->input('confirm_password');

        if (empty($mobile_no)) {
            return response()->json(['status' => 'FAILURE', 'message' => 'Please enter a mobile no'], 404);
        }
        if (empty($password)) {
            return response()->json(['status' => 'FAILURE', 'message' => 'Please enter a new password'], 404);
        }
        if (empty($confirm_password)) {
            return response()->json(['status' => 'FAILURE', 'message' => 'Please enter a confirm password'], 404);
        }

        $sales_executive_name = SalesRepCreation::where('mobile_no', $mobile_no)
            ->where(function ($query) {
                $query->where('delete_status', '0')->orWhereNull('delete_status');
            })
            ->where('status', 0)
            ->first();

        if ($sales_executive_name) {

            SalesRepCreation::where('id', $sales_executive_name->id)->update(['password'=> $password, 'confirm_password'=> $confirm_password]);

            return response()->json(['status' => 'SUCCESS', 'message' => 'Password updated successfully'], 200);
        } else {
            return response()->json(['status' => 'FAILURE', 'message' => 'Oops! You have entered invalid mobile no'], 404);
        }
    }

    public function update_current_location_api(Request $request)
    {
        $sales_executive_id = $request->input('sales_executive_id');
        $latitude = $request->input('latitude');
        $langititude = $request->input('longitude');
        $current_status = $request->input('current_status');

        if (empty($sales_executive_id)) {
            return response()->json(['status' => 'FAILURE', 'message' => 'Sales Executive Not Found'], 404);
        }
        elseif(!empty($current_status)) {
            $currentDate = Carbon::today();
            $currentTime = Carbon::now('Asia/Kolkata')->toTimeString();

            $tb = new SalesExecutiveTimelogs();
            $tb->date = $currentDate;
            $tb->time = $currentTime;
            $tb->sales_executive_id = $sales_executive_id;
            $tb->latitude = $latitude;
            $tb->langititude = $langititude;
            $tb->current_status = $current_status;
            $tb->save();

            return response()->json(['status' => 'SUCCESS', 'message' => 'Location and Market Value Updated Successfully'], 200);
        }
    }
}
