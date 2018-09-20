<?php

namespace App\Api\V1\Controllers\Masters;

use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Input;
use App\Http\Controllers\Controller;
use Tymon\JWTAuth\JWTAuth as JWTAuth1;
use JWTAuth ;
use App\Model\Company;
use App\Model\Branch;
use Dingo\Api\Routing\Helpers;

class CompanyController extends Controller
{

    //use Helpers;
	public function index()
	{
		$limit = 10;
		return Company::paginate($limit);
	}
	
	public function storeOtherDetails(Request $request)
	{
		
		$token = JWTAuth::decode(JWTAuth::getToken());
		$current_company_id = $token['company_id']['id'];
		$company= Company::find($current_company_id);
		$company->pan_number = Input::get('company_pan_number');
		$company->logo = Input::get('company_logo');
		$company->tan_number = Input::get('company_tan_number');
		$company->ecc_number = Input::get('company_ecc_number');
		$company->division_code = Input::get('company_division_code');
		$company->cin_number = Input::get('company_cin_number');
	
		try{
			$company->save();
			$branch = new Branch();
			$branch->company_id = $company->id;
			$branch->gst_no = Input::get('company_gst_number');
			$branch->save();
		}
		catch(\Exception $e)
		{
			return $e->getMessage();
		}
	
		return $company->id;
	}
	public function setCompany(Request $request)
	{
		$user= JWTAuth::parseToken()->toUser();
		$company = $user->getCompanies()->where('id',$request['company_id'])->first(['id','display_name']);
		$user_payload = [
            'id' => (int)$user['id'],
            'name' => $user['display_name'],
            'company_id'=>$company
		];
		try {
			$token = JWTAuth::fromUser($user,$user_payload);
			if(!$token) {
				return response()->json([
					'status' => false,
					'status_code' => 200,
					'message' => "Company Set Failed"
				]);
			}

		} 
		catch (JWTException $e) {
			return response()->json([
					'status' => 'error',
					'status_code' => 500,
					'message' => "Something is Wrong !!"
			]);
		}
		return response()->json([
			'status' => true,
			'status_code' => 200,
			'message' => "Company Set Successfully",
			'data' => $user_payload,
			'token' => $token
		]);
	}

	public function store(Request $request)
	{
	
    	$user = JWTAuth::parseToken()->toUser();
		$company = new Company();
		$company->user_id= $user->id;
		$company->name = Input::get('company_name');
		$company->display_name = Input::get('company_display_name');
		$company->fax = Input::get('company_fax_number');
		$company->website = Input::get('company_website');
		try{
		$company->save();
		}
		catch(\Exception $e) 
		{
				return $e->getMessage();
		}
		return ($company->id);
		
		
		// return Company::create([
		// 	"userlevel_alias"=>$request->get('userlevel_alias'),
		// 	"name"=>$request->get('name'),
		// 	"email"=>$request->get('email'),
		// 	"company_type"=>$request->get('company_type'),
		// 	"website"=>$request->get('website'),
		// 	"logo"=>$request->get('logo'),
		// 	"employee_count"=>$request->get('employee_count'),
		// 	"address"=>$request->get('address'),
		// 	"city"=>$request->get('city'),
		// 	"state_id"=>$request->get('state_id'),
		// 	"country_id"=>$request->get('country_id'),
		// 	"pincode"=>$request->get('pincode'),
		// 	"phone1"=>$request->get('phone1'),
		// 	"phone2"=>$request->get('phone2'),
		// 	"description"=>$request->get('description'),
		// 	"founded_year"=>$request->get('founded_year'),
		// 	"expiry_date"=>$request->get('expiry_date'),
		// 	"status"=>$request->get('status'),

		// 	"inserted_by_id"=>$user->id,
		// 	"updated_by_id"=>$user->id
		// 	]);
	}

 	public function show($id)
 	{
 		try {
            $company = Company::findOrFail($id);
        } 
        catch (\Exception $e) {
            throw new NotFoundHttpException('Opps! Company not found');
        }
 		return $company;
 	}

    public function update(Request $request, $id)
    {
    	$user = JWTAuth::parseToken()->toUser();
    	$company = Company::findOrFail($id);
    	$data = $request->all();
    	$data['updated_by_id'] = $user->id;
    	$company->update($data);
    	return $company;
    }

	public function destroy($id)
	{
		if(Company::destroy($id)){
			return response()
			->json([
				'status' => 'ok'
				]);
		}
		else{
			throw new NotFoundHttpException('Oops! Company not found.');
		}
	}

}
