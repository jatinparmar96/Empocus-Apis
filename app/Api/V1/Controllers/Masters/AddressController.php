<?php

namespace App\Api\V1\Controllers\Masters;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Model\Bank;
use App\Model\Address;
use Tymon\JWTAuth\Facades\JWTAuth;

class AddressController extends Controller
{
  public static function storeAddress($request,$query='',$type)
  {
    $address = new Address();
    $address->type = $type;
    $address->block_no = $request->get($query.'address_building');
    $address->road_name = $request->get($query.'address_road_name');
    $address->landmark = $request->get($query.'address_landmark');
    $address->pincode = $request->get($query.'address_pincode');
    $address->country = $request->get($query.'address_country');
    $address->state = $request->get($query.'address_state');
    $address->city = $request->get($query.'address_city');    
    try{
        $address->save();
    }
    catch(\Exception $e)
    {
        return $e->getMessage();
    }
    return $address;
  }
  public static function updateAddressId(Address $address,$id)
  {
    $address->type_id = $id;
    $address->save();
  }
}

