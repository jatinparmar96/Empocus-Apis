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
    public function storeHeadBranch(Request $request)
    {
        $token = JWTAuth::getPayload()->toArray();
        $user = JWTAuth::parseToken()->toUser();
        $current_company_id = $token['company_id']['id'];
        $branch = Branch::where('company_id',$current_company_id)->first();
        $address = new Address();
        $address->type = "Branch";
        $address->type_id= $branch->id;
        $address->block_no = $request->get('company_address_building_no');
        $address->road_name = $request->get('company_address_road_no');
        $address->landmark = $request->get('company_address_landmark');
        $address->pincode = $request->get('company_address_pincode');
        $address->country = $request->get('company_address_country');
        $address->state = $request->get('company_address_state');
        $address->city = $request->get('company_address_city');
        $address->save();
        $branch->address_id = $address->id;
        $branch->save();
        return response()
        ->json([
           'status'=>true
           ]);
    }

    public function storeBranch(Request $request)
    {
        $token = JWTAuth::getPayload()->toArray();
        $user = JWTAuth::parseToken()->toUser();
        $current_company_id = $token['company_id']['id'];
        $branch = new Branch();
        $branch->company_id = $current_company_id;
        $branch->name = $request->get('branch_name');
        $branch->code = $request->get('branch_code');
        $branch->gst_number = $request->get('branch_gst_number');
        $branch->bank_id = $request->get('branch_bank');
        $branch->save();
        $address = new Address();
        $address->type = "Branch";
        $address->type_id= $branch->id;
        $address->block_no = $request->get('branch_address_building_name');
        $address->road_name = $request->get('branch_address_road_name');
        $address->landmark = $request->get('branch_address_landmark');
        $address->pincode = $request->get('branch_address_pincode');
        $address->country = $request->get('branch_address_country');
        $address->state = $request->get('branch_address_state');
        $address->city = $request->get('branch_address_city');
        $address->save();
        $branch->address_id = $address->id;
        $branch->save();
        return response()
                 ->json([
                    'status'=>true
                    ]);
    }

    public function getBranches(Request $request)
    {
        $company_id  = CompanyController::getCurrentCompany();
        $branches = Branch::where('company_id',$company_id)->get();
        $address = [];
        foreach($branches as $branch)
        {
            array_push( $address, Address::where('id',$branch->address_id)->first());
        }
        return response()
                ->json([
                    'status'=>true,
                    'branches'=>$branches,
                    'address'=>$address
                ]);
    }
}
