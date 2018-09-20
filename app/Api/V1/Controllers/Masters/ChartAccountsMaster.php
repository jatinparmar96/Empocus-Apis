<?php

namespace App\Api\V1\Controllers\Masters;

use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Input;
use JWTAuth;
use App\Model\ChartOfAccount;
use App\Model\Address;


class ChartAccountsMaster extends Controller
{
  public function storeChartOfAccounts()
  {
    $token = JWTAuth::decode(JWTAuth::getToken());
    $user = JWTAuth::parseToken()->toUser();
    $current_company_id = $token['company_id']['id'];
    $account = new ChartOfAccount();
    $account->company_id = $current_company_id;
    $account->ca_company_name=Input::get('ca_company_name');
    $account->ca_company_display_name=Input::get('ca_company_display_name');
    $account->ca_category=Input::get('ca_category');
    $account->ca_code = Input::get('ca_code');
    $account->ca_opening_amount=Input::get('ca_opening_amount');
    $account->ca_opening_type=Input::get('ca_opening_type');
    $account->ca_first_name=Input::get('ca_first_name');
    $account->ca_last_name=Input::get('ca_last_name');
    $account->ca_mobile_number=Input::get('ca_mobile_number');
    $account->ca_fax=Input::get('ca_fax');
    $account->ca_email=Input::get('ca_email');
    $account->ca_website=Input::get('ca_website');
    $account->ca_designation=Input::get('ca_designation');
    $account->ca_branch=Input::get('ca_branch');
    $account->ca_pan=Input::get('ca_pan');
    $account->ca_gstn=Input::get('ca_gstn');
    $account->ca_tan=Input::get('ca_tan');
    $account->ca_commodity=Input::get('ca_commodity');
    $account->ca_ecc_no=Input::get('ca_ecc_no');
    $account->ca_rc_no=Input::get('ca_rc_no');
    $account->ca_division=Input::get('ca_division');
    $account->ca_range=Input::get('ca_range');
    $account->ca_commissionerate=Input::get('ca_commissionerate');
    $account->ca_tin_no=Input::get('ca_tin_no');
    $account->ca_date_opened=Input::get('ca_date_opened');
    $account->ca_cst_no=Input::get('ca_cst_no');
    $address = new Address();
    $address->type = 'ChartOfAccounts';
    $address->block_no = Input::get('ca_address_building');
    $address->road_name = Input::get('ca_address_road_name');
    $address->landmark = Input::get('ca_address_landmark');
    $address->pincode = Input::get('ca_address_pincode');
    $address->country = Input::get('ca_address_country');
    $address->state = Input::get('ca_address_state');
    $address->city = Input::get('ca_address_city');    
    try{
        $address->save();
        $account->address_id = $address->id;
        $account->save();
    }
    catch(\Exception $e)
    {
        return $e->getMessage();
    }
    return response()
			->json([
				'status' => true
				]);
  }
}
