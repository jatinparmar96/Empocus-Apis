<?php

namespace App\Api\V1\Controllers\Masters;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Tymon\JWTAuth\Facades\JWTAuth;
use App\Model\Godown;
use App\Model\Address;

class GodownMasterController extends Controller
{
    public function storeGodown(Request $request)
    {
        $token = JWTAuth::getPayload()->toArray();
        $user = JWTAuth::parseToken()->toUser();
        $current_company_id= $token['company_id']['id'];
        $godown = new Godown();
        $godown->company_id =$current_company_id;
        $godown->name = $request->get('godown_name');
        $godown->code = $request->get('godown_code');
        $godown->save();
        $address = new Address();
        $address->type = "Godown";
        $address->type_id= $godown->id;
        $address->block_no = $request->get('godown_address_building_name');
        $address->road_name = $request->get('godown_address_road_name');
        $address->landmark = $request->get('godown_address_landmark');
        $address->pincode = $request->get('godown_address_pincode');
        $address->country = $request->get('godown_address_country');
        $address->state = $request->get('godown_address_state');
        $address->city = $request->get('godown_address_city');
        $address->save();
        $godown ->address_id = $address->id;
        $godown->save();
        return response()
                ->json([
                    'status'=>true
                ]);
    }
    public function getGodowns(Request $request)
    {
        $company_id  = CompanyController::getCurrentCompany();
        $godowns = Godown::where('company_id',$company_id)->get();
        $address = [];
        foreach($godowns as $godown)
        {
            array_push( $address, Address::where('id',$godown->address_id)->first());
        }
        return response()
                ->json([
                    'status'=>true,
                    'godowns'=>$godowns,
                    'address'=>$address
                ]);
    }
}
