<?php

namespace App\Api\V1\Controllers\CRM;

use App\Api\V1\Controllers\Authentication\TokenController;
use App\CRM_Models\Task;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TaskController extends Controller
{
    public function form(Request $request)
    {
        $status = true;
        $id = $request->get('id');
        $user = TokenController::getUser();
        $current_company_id = TokenController::getCompanyId();
        if ($id === 'new') {
            $task = new Task();
            $task->company_id = $current_company_id;
            $message = 'Record Added Successfully';
            $task->created_by_id = $user->id;
            $status = true;
        } else {
            $message = 'Record Updated Successfully';
            $task = Task::findOrFail($id);
        }
        if ($status) {
            $task->title = $request->get('title');
            $task->due_date = $request->get('due_date');
            $task->due_time = $request->get('due_time');
            $task->task_type = $request->get('task_type');
            $task->outcome = $request->get('outcome');
            $task->description = $request->get('description');
            $task->contact_id = $request->get('contact_id');
            $task->updated_by_id = $user->id;
            try {
                $task->save();
            } catch (\Exception $e) {
                $status = false;
                $message = 'Something is wrong' . $e;
            }
            return response()->json([
                'status' => $status,
                'data' => $task,
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
            'message' => 'Task List',
            'data' => $result
        ]);
    }

    public function query()
    {
        $current_company = TokenController::getCompanyId();
        $query = DB::table('crm_tasks as t')
            ->select('t.title', 't.due_date', 't.due_time', 't.task_type', 't.outcome', 't.description')
            ->where('t.company_id', $current_company);

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
            "id" => "t.id",
            "title" => "t.title",
            "due_date" => "t.due_date",
            "due_time" => "t.due_time",
            "task_type" => "t.task_type",
            "outcome" => "t.outcome",
            "description" => "t.description"
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
            $query = $query->orderBy('t.title', 'ASC');
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
            'message' => 'Task Full List',
            'data' => $result
        ]);
    }

    public function show($id)
    {
        $query = $this->query();
        $query = $this->search($query);
        $query = $this->sort($query);
        $result = $query->where('t.id', $id)->first();
        return response()->json([
            'status' => true,
            'status_code' => 200,
            'message' => 'Task Show List',
            'data' => $result
        ]);
    }
}
