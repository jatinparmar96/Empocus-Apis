<?php

namespace App\Api\V1\Controllers\Masters;

use App\Api\V1\Controllers\Authentication\TokenController;
use App\Http\Controllers\Controller;
use App\Model\LeadStatus;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Input;

class LeadStatusMasterController extends Controller
{
    public function form(Request $request)
    {
        $status = true;
        $id = $request->get('id');
        $user = TokenController::getUser();
        $current_company_id = TokenController::getCompanyId();
        if ($id === 'new') {
            $count = LeadStatus::Where('unit_name', Input::get('unit_name'))
                ->Where('company_id', $current_company_id)
                ->count();
            if ($count > 0) {
                $status = false;
                $message = 'Kindly fill up form Correctly !!';
                $error['unit_name'] = 'Name already exist !!';
            } else {
                $message = 'Record Added Successfully';
                $l_status = new LeadStatus();
                $l_status->created_by_id = $user->id;
            }
        } else {
            $message = 'Record Updated Successfully';
            $l_status = LeadStatus::findOrFail($id);
        }

        if ($status) {
            $l_status->company_id = $current_company_id;
            $l_status->status = Input::get('lead_status');
            $l_status->updated_by_id = $user->id;
            try {
                $l_status->save();
            } catch (\Exception $e) {
                $status = false;
                $message = 'Something is wrong. Kindly Contact Admin';
            }

            return response()->json([
                'status' => $status,
                'data' => $l_status,
                'message' => $message
            ]);
        } else {
            return response()->json([
                'status' => $status,
                'message' => $message,
                'error' => $error,
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
            'message' => 'Lead Status List',
            'data' => $result
        ]);
    }

    public function query()
    {
        $current_company_id = TokenController::getCompanyId();
        $query = DB::table('lead_statuses as l_status')
            ->select('l_status.id', 'l_status.status')
            ->where('l_status.company_id', $current_company_id);
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
            "id" => "l_status.id",
            "status" => "l_status.status",
            "is_active" => "l_status.is_active"
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
            $query = $query->orderBy('l_status.id', 'ASC');

        return $query;
    }
    
    public function full_list()
    {
        $query = $this->query();
        $query = $this->search($query);
        $query = $this->sort($query);
        $query = $query->Where('l_status.is_active', true);
        $result = $query->get();
        return response()->json([
            'status' => true,
            'status_code' => 200,
            'message' => 'Lead Status Full List',
            'data' => $result
        ]);
    }

    public function show(Request $request, $id)
    {
        $query = $this->query();
        $result = $query->Where('uom.id', $id)->first();
        return response()->json([
            'status' => true,
            'status_code' => 200,
            'message' => 'UOM',
            'data' => $result
        ]);
    }

}
