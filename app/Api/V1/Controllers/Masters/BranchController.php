<?php

namespace App\Api\V1\Controllers\Masters;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Input;
use JWTAuth;
use App\Model\Branch;
use App\Model\Address;
class BranchController extends Controller
{
    public function storeBranch(Request $request)
    {
        $token = JWTAuth::decode(JWTAuth::getToken());
        $user = JWTAuth::parseToken()->toUser();
        $current_company_id = $token['company_id']['id'];
        $branch = Branch::where('company_id',$current_company_id)->first();
        $address = new Address();
        $address->type = "Branch";
        $address->type_id= $branch->id;
        $address->block_no = Input::get('company_address_building_no');
        $address->road_name = Input::get('company_address_road_no');
        $address->landmark = Input::get('company_address_landmark');
        $address->pincode = Input::get('company_address_pincode');
        $address->country = Input::get('company_address_country');
        $address->state = Input::get('company_address_state');
        $address->city = Input::get('company_address_city');
        $address->save();
        $branch->address_id = $address->id;
        $branch->save();

       
    }
}
