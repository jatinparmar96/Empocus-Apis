<?php

namespace App\Api\V1\Controllers\CRM;

use App\Api\V1\Controllers\Authentication\TokenController;
use App\Api\V1\Controllers\Masters\AddressController;
use App\CRM_Models\Account;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AccountController extends Controller
{
    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function form(Request $request)
    {
        $status = true;
        $id = $request->get('id');
        $user = TokenController::getUser();
        $current_company_id = TokenController::getCompanyId();
        if ($id === 'new') {
            $account = new Account();
            $account->company_id = $current_company_id;
            $message = 'Record Added Successfully';
            $account->created_by_id = $user->id;
            $status = true;
        } else {
            $message = 'Record Updated Successfully';
            $account = Account::findOrFail($id);
        }
        if ($status) {
            $account->account_name = $request->get('account_name');
            $account->account_employee_number = $request->get('account_employee_number');
            $account->account_annual_revenue = $request->get('account_annual_revenue');
            $account->account_website = $request->get('account_website');
            $account->account_phone = $request->get('account_phone');
            $account->account_industry_type = $request->get('account_industry_type');
            $account->account_business_type = $request->get('account_business_type');
            $account->account_facebook_link = $request->get('account_facebook_link');
            $account->account_twitter_link = $request->get('account_twitter_link');
            $account->account_linkedin_link = $request->get('account_linkedin_link');
            $account->updated_by_id = $user->id;
            try {
                $account->save();
                AddressController::storeAddress($request, 'account_', 'Account', $account->id);
            } catch (\Exception $e) {
                $status = false;
                $message = 'Something is wrong' . $e;
            }
            return response()->json([
                'status' => $status,
                'data' => $account,
                'message' => $message
            ]);
        } else {
            return response()->json([
                'status' => $status,
                'message' => $message,
            ]);
        }
    }

    public function index()
    {
        $limit = 10;
        $query = $this->query();
        $query = $this->search($query);
        $query = $this->sort($query);
        $result = $query->paginate($limit);
        return response()->json([
            'status' => true,
            'status_code' => 200,
            'message' => 'Account List',
            'data' => $result
        ]);
    }

    public function query()
    {
        $current_company = TokenController::getCompanyId();
        $query = DB::table('accounts as a')
            ->select('a.account_name', 'a.account_employee_number', 'a.account_annual_revenue', 'a.account_website',
                'a.account_phone', 'a.account_industry_type', 'a.account_business_type', 'a.account_facebook_link',
                'a.account_twitter_link', 'a.account_linkedin_link', 'a.created_by_id', 'a.updated_by_id')
            ->where('a.company_id', $current_company);
        return $query;
    }

    public function search($query)
    {
        $search = \Request::get('search');
        if (!empty($search)) {
            $TableColumn = $this->TableColumn();
            foreach ($search as $key => $searchvalue) {
                if ($searchvalue !== '')
                    $query = $query->Where($TableColumn[$key], 'LIKE', '%' . $searchvalue . '%');
            }
        }
        return $query;
    }

    public function TableColumn()
    {
        $TableColumn = array(
            "id" => "a.id",
            "account_name" => "a.account_name",
            "account_employee_number" => "a.account_employee_number",
            "account_annual_revenue" => "a.account_annual_revenue",
            "account_website" => "a.account_website",
            "account_phone" => "a.account_phone",
            "account_industry_type" => "a.account_industry_type",
            "account_business_type" => "a.account_business_type",
            "account_facebook_link" => "a.account_facebook_link",
            "account_twitter_link" => "a.account_twitter_link",
            "account_linkedin_link" => "a.account_linkedin_link",
            "created_by_id" => "a.created_by_id",
            "updated_by_id" => "a.updated_by_id",
        );
        return $TableColumn;
    }

    //use Helpers;

    public function sort($query)
    {
        $sort = \Request::get('sort');
        if (!empty($sort)) {
            $TableColumn = $this->TableColumn();
            $query = $query->orderBy($TableColumn[key($sort)], $sort[key($sort)]);
        } else
            $query = $query->orderBy('a.account_name', 'ASC');
        return $query;
    }

    public function full_list()
    {
        $query = $this->query();
        $query = $this->search($query);
        $query = $this->sort($query);
        $result = $query->get();
        return response()->json([
            'status' => true,
            'status_code' => 200,
            'message' => 'Account Full List',
            'data' => $result
        ]);
    }

    public function show($id)
    {
        $query = $this->query();
        $query = $this->search($query);
        $query = $this->sort($query);
        $result = $query->where('a.id', $id)->get();
        return response()->json([
            'status' => true,
            'status_code' => 200,
            'message' => 'Account Show List',
            'data' => $result
        ]);
    }
}
