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
  public function storeChartOfAccounts(Request $request)
  {
    $user = JWTAuth::parseToken()->toUser();
    $current_company_id = CompanyController::getCurrentCompany();
    $account = new ChartOfAccount();
    $account->company_id = $current_company_id;
    $account->ca_company_name=$request->get('ca_company_name');
    $account->ca_company_display_name=$request->get('ca_company_display_name');
    $account->ca_category=$request->get('ca_category');
    $account->ca_code = $request->get('ca_code');
    $account->ca_opening_amount=$request->get('ca_opening_amount');
    $account->ca_opening_type=$request->get('ca_opening_type');
    $account->ca_first_name=$request->get('ca_first_name');
    $account->ca_last_name=$request->get('ca_last_name');
    $account->ca_mobile_number=$request->get('ca_mobile_number');
    $account->ca_fax=$request->get('ca_fax');
    $account->ca_email=$request->get('ca_email');
    $account->ca_website=$request->get('ca_website');
    $account->ca_designation=$request->get('ca_designation');
    $account->ca_branch=$request->get('ca_branch');
    $account->ca_pan=$request->get('ca_pan');
    $account->ca_gstn=$request->get('ca_gstn');
    $account->ca_tan=$request->get('ca_tan');
    $account->ca_commodity=$request->get('ca_commodity');
    $account->ca_ecc_no=$request->get('ca_ecc_no');
    $account->ca_rc_no=$request->get('ca_rc_no');
    $account->ca_division=$request->get('ca_division');
    $account->ca_range=$request->get('ca_range');
    $account->ca_commissionerate=$request->get('ca_commissionerate');
    $account->ca_tin_no=$request->get('ca_tin_no');
    $account->ca_date_opened=$request->get('ca_date_opened');
    $account->ca_cst_no=$request->get('ca_cst_no');
    $address = AddressController::storeAddress($request,'ca_','ChartOfAccounts');    
    try{
        $account->address_id = $address->id;
        $account->save();
        AddressController::updateAddressId($address,$account->id);
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
  public function getChartOfAccounts(Request $request)
  {
        $current_company_id = CompanyController::getCurrentCompany();
        $query = DB::table('chart_of_accounts as ca')
                    ->join('addresses as a','ca.address_id','a.id')
                    ->select('ca.id','ca.ca_company_display_name','ca.ca_category','ca.ca_code')
                    ->addSelect('a.landmark','a.city')
                    ->where('ca.company_id',$current_company_id);
        $coa = $query->get();
    
        return response()
        ->json([
            'status'=>true,
            'data'=>$coa,
        ]);
  }
}
