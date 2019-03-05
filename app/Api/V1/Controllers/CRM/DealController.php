<?php

namespace App\Api\V1\Controllers\CRM;

use App\Api\V1\Controllers\Authentication\TokenController;
use App\CRM_Models\Deal;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DealController extends Controller
{
    public function form(Request $request)
    {
        $status = true;
        $id = $request->get('id');
        $user = TokenController::getUser();
        $current_company_id = TokenController::getCompanyId();
        if ($id === 'new') {
            $deal = new Deal();
            $deal->company_id = $current_company_id;
            $message = 'Record Added Successfully';
            $deal->created_by_id = $user->id;
            $status = true;
        } else {
            $message = 'Record Updated Successfully';
            $deal = Contacts::findOrFail($id);
        }
        if ($status) {
            $deal->first_name = $request->get('first_name');
            $deal->last_name = $request->get('last_name');
            $deal->deal_stage = $request->get('deal_stage');
            $deal->product_id = $request->get('product_id');

            $deal->deal_value = $request->get('deal_value');
            $deal->payment_status = $request->get('payment_status');
            $deal->expected_close_date = $request->get('expected_close_date');
            $deal->probability = $request->get('probability');
            $deal->type = $request->get('type');
            $deal->source = $request->get('source');
            $deal->campaign = $request->get('campaign');
            $deal->updated_by_id = $user->id;
            try {
                $deal->save();
            } catch (\Exception $e) {
             dd($e);
            }
            return response()->json([
                'status' => $status,
                'data' => $deal,
                'message' => $message
            ]);
        } else {
            return response()->json([
                'status' => $status,
                'message' => $message,
            ]);
        }
    }

    

    public function query()
    {
        $current_company = TokenController::getCompanyId();
        $query = DB::table('deals as d')
            ->select(
                'd.first_name','d.last_name','d.deal_stage','d.product_id','d.deal_value','d.payment_status','d.expected_close_date','d.probability','d.type','d.source','d.campaign'
            )
            ->where('d.company_id', $current_company);
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
            "first_name" => "d.first_name",
            "last_name" => "d.last_name",
            "deal_stage" => "d.deal_stage",
            "product_id" => "d.product_id",
            "deal_value" => "d.deal_value",
            "payment_status" => "d.payment_status",
            "expected_close_date" => "d.expected_close_date",
            "probability" => "d.probability",
            "type" => "d.type",
            "source" => "d.source",
            "campaign" => "d.campaign",
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
            $query = $query->orderBy('d.first_name', 'ASC');
        return $query;
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
            'message' => 'Contact List',
            'data' => $result
        ]);
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
            'message' => 'Deal Full List',
            'data' => $result
        ]);
    }

    public function show($id)
    {
        $query = Deal::find($id);
        $query->product;
        return response()->json([
            'status' => true,
            'status_code' => 200,
            'message' => 'Deal Show',
            'data' => $query
        ]);
    }
}
