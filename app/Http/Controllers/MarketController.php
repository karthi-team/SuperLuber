<?php


namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\StateCreation;
use App\Models\MarketCreation;
use App\Models\DistrictCreation;
use App\Models\PumpCreation;
use Illuminate\Support\Facades\Log;
class MarketController extends Controller
{
    public function retrieve($id)
    {
         if($id=='')
        {return PumpCreation::select('id','operator','description','pumpstatus','datetime','duration')->get();}
        else
        {return PumpCreation::select('id','operator','description','pumpstatus','datetime','duration')->where('id','=',$id)->get();}

    }
    public function db_cmd(Request $request)
    {
        $action=$request->input('action');
        if($action=='insert')
        {
            $tb = new PumpCreation();
            $tb->operator = $request->input('operator');
            $tb->description = $request->input('description');
            $tb->pumpstatus	 = $request->input('pumpstatus');
            $tb->datetime = $request->input('datetime');
            $tb->duration = $request->input('duration');
            $tb->status='1';
            $tb->save();
        }
        else if($action=='update')
        {
            $tb = PumpCreation::find($request->input('id'));
            $tb->operator = $request->input('operator');
            $tb->description = $request->input('description');
            $tb->pumpstatus	 = $request->input('pumpstatus');
            $tb->datetime = $request->input('datetime');
            $tb->duration = $request->input('duration');
            $tb->save();
        }
        else if($action=='delete')
        {
            $tb =PumpCreation::where('id','=',$request->input('id'));
            $tb->delete();
        }
        else if($action=='retrieve')
        {
            $market_creation = $this->retrieve('');
            return view('Masters.market_creation.list',['market_creation'=>$market_creation,'user_rights_edit_1'=>$request->input('user_rights_edit_1'),'user_rights_delete_1'=>$request->input('user_rights_delete_1')]);
        }
        else if($action=='count')
        {
            $cnt=0;
            $id=$request->input('id');$state_name=$request->input('state_name');
            if($id!="0"){$cnt=MarketCreation::where('state_name','=',$state_name)->where('id','!=',$id)->count();}
            else{$cnt=MarketCreation::where('state_name','=',$state_name)->count();}
            return $cnt;
        }
        else if ($action == 'create_form') {
            $state_name = StateCreation::select('id', 'state_name')
                ->where('state_name', '!=', '')
                ->orderBy('state_name')
                ->get();

            $district_creation = DistrictCreation::select('state_creation.state_name', 'district_creation.district_name')
                ->join('state_creation', 'state_creation.id', '!=', 'district_creation.state_id')
                ->groupBy('state_creation.state_name', 'district_creation.district_name')
                ->orderBy('state_creation.state_name')
                ->get();

            return view('Masters.market_creation.create', [
                'state_name' => $state_name,
                'district_creation' => $district_creation
            ]);
        }
        else if($action=='update_form')
        
        {

            $market_creation=$this->retrieve($request->input('id'));
        Log::info("shops_type 123", $market_creation->toArray());
            return view('Masters.market_creation.update',['market_creation'=>$market_creation[0]]);
        }

        else if($action=='getDistricts')
        {
            $stateId = $request->input('state_id');

            $districts = DistrictCreation::select('id', 'district_name')
                ->where('state_id', $stateId)
                ->get();

            return response()->json($districts);
        }
        else if($action=='statusinfo')
        {
            $stat = $request->input('status')==1 ? "0" : "1";

            $tb = MarketCreation::find($request->input('id'));
            $tb->status = $stat;
            $tb->save();

        }



    }


}

