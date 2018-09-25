<?php

namespace App\Api\V1\Controllers\Masters;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Model\Bank;
use Tymon\JWTAuth\Facades\JWTAuth;

class BankMasterController extends Controller
{
    public function storeBank(Request $request)
    {
        $token = JWTAuth::getPayload()->toArray();
        $current_company_id= $token['company_id']['id'];
        $bank = new Bank();
        $bank->company_id= $current_company_id;
        $bank->name = $request->get('bank_name');
        $bank->account_no = $request->get('bank_account_number');
        $bank->ifsc_code = $request->get('bank_ifsc_code');
        $bank->branch = $request->get('bank_branch');
        $bank->save();
        return response()
                ->json([
                    'status'=>true
                ]);

    }
    public function getBanks(Request $request)
    {
        $token = JWTAuth::getPayload()->toArray();
        $current_company_id= $token['company_id']['id'];
        $banks = Bank::where('company_id',$current_company_id)->get();
        return response()
                ->json([
                    'banks'=>$banks,
                    'status'=>true
                ]);
    }
}
