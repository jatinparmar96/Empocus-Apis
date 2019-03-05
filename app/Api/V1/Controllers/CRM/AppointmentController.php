<?php

namespace App\Api\V1\Controllers\CRM;

use App\Api\V1\Controllers\Authentication\TokenController;
use App\CRM_Models\Appointment;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AppointmentController extends Controller
{
    public function form(Request $request)
    {
        $status = true;
        $id = $request->get('id');
        $user = TokenController::getUser();
        $current_company_id = TokenController::getCompanyId();
        if ($id === 'new') {
            $appointment = new Appointment();
            $appointment->company_id = $current_company_id;
            $message = 'Record Added Successfully';
            $appointment->created_by_id = $user->id;
            $status = true;
        } else {
            $message = 'Record Updated Successfully';
            $appointment = Appointment::findOrFail($id);
        }
        if ($status) {
            $appointment->title = $request->get('title');
            $appointment->start_date = $request->get('start_date');
            $appointment->start_time = $request->get('start_time');
            $appointment->end_date = $request->get('end_date');
            $appointment->end_time = $request->get('end_time');
            $appointment->outcome = $request->get('outcome');
            $appointment->location = $request->get('location');
            $appointment->latitude = $request->get('latitude');
            $appointment->longitude = $request->get('longitude');
            $appointment->description = $request->get('description');
            $appointment->updated_by_id = $user->id;
            try {
                $appointment->save();
            } catch (\Exception $e) {
                $status = false;
                $message = 'Something is wrong' . $e;
            }
            return response()->json([
                'status' => $status,
                'data' => $appointment,
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
        $query = DB::table('appointments as ap')
            ->select('ap.id','ap.title','ap.start_date','ap.start_time','ap.end_date','ap.end_time','ap.outcome','ap.location','ap.latitude','ap.longitude','ap.description')
            ->where('ap.company_id', $current_company);
        return $query;
    }

    public function TableColumn()
    {
        $TableColumn = array(
            "id" => "ap.id",
            "title" => "ap.title",
            "start_date	" => "ap.start_date",
            "start_time" => "ap.start_time",
            "end_date" => "ap.end_date",
            "end_time	" => "ap.end_time",
            "outcome" => "ap.outcome",
            "location" => "ap.location",
            "latitude" => "ap.latitude",
            "longitude" => "ap.longitude",
            "description" => "ap.description",
            "created_by_id" => "ap.created_by_id",
            "updated_by_id" => "ap.updated_by_id",
        );
        return $TableColumn;
    }

    public function sort($query)
    {
        $sort = \Request::get('sort');
        if (!empty($sort)) {
            $TableColumn = $this->TableColumn();
            $query = $query->orderBy($TableColumn[key($sort)], $sort[key($sort)]);
        } else
            $query = $query->orderBy('ap.title', 'ASC');
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

    //use Helpers;
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
        $result = $query->where('ap.id', $id)->get();
        return response()->json([
            'status' => true,
            'status_code' => 200,
            'message' => 'Account Show List',
            'data' => $result
        ]);
    }
}
