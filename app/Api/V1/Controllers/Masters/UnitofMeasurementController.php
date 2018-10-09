<?php

namespace App\Api\V1\Controllers\Masters;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Model\UnitOfMeasurement;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Support\Facades\Input;

class UnitofMeasurementController extends Controller
{
    public function addUOM(Request $request)
    {
        $token = JWTAuth::decode(JWTAuth::getToken());
        $current_company_id = $token['company_id']['id'];
        $uom = new UnitOfMeasurement();
        $uom->company_id = $current_company_id;
        $uom->unit_name = Input::get('unit_name');
        try
        {
            $uom -> save();
        }
        catch(\Exception $e)
        {
            return $e->getMessage();
        }
        return response()->json([
				'status' => true
				]);
    }
    public function getUOM(Request $request)
    {
        $token = JWTAuth::decode(JWTAuth::getToken());
        $current_company_id = $token['company_id']['id'];
        $uoms = UnitOfMeasurement::where('company_id',$current_company_id)->get(['unit_name','id']);
        return response()->json([
                'status' => true,
                'uoms'=>$uoms
                ]);
    }
}
