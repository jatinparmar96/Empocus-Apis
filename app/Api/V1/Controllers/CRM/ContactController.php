<?php

namespace App\Api\V1\Controllers\CRM;

use App\Api\V1\Controllers\Authentication\TokenController;
use App\Api\V1\Controllers\Masters\AddressController;
use App\Api\V1\Controllers\Masters\CA_ContactsController;
use App\CRM_Models\Contacts;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ContactController extends Controller
{
    public function form(Request $request)
    {
        $status = true;
        $id = $request->get('id');
        $user = TokenController::getUser();
        $current_company_id = TokenController::getCompanyId();
        if ($id === 'new') {
            $contact = new Contacts();
            $contact->company_id = $current_company_id;
            $message = 'Record Added Successfully';
            $contact->created_by_id = $user->id;
            $status = true;
        } else {
            $message = 'Record Updated Successfully';
            $contact = Contacts::findOrFail($id);
        }
        if ($status) {
            $contact->first_name = $request->get('first_name');
            $contact->last_name = $request->get('last_name');
            $contact->email = $request->get('email');
            $contact->primary_contact_number = $request->get('primary_contact_number');
            $contact->job_title = $request->get('job_title');
            $contact->department = $request->get('department');
            $contact->work_telephone_number = $request->get('work_telephone_number');
            $contact->work_mobile_number = $request->get('work_mobile_number');
            $contact->status = $request->get('status');
            $contact->business_type = $request->get('business_type');
            $contact->facebook_link = $request->get('facebook_link');
            $contact->twitter_link = $request->get('twitter_link');
            $contact->linkedin_link = $request->get('linkedin_link');
            $contact->source = $request->get('source');
            $contact->campaign = $request->get('campaign');
            $contact->medium = $request->get('medium');
            $contact->keyword = $request->get('keyword');
            $contact->account_id = $request->get('account_id');
            $contact->updated_by_id = $user->id;
            try {
                $contact->save();
                AddressController::storeAddress($request, 'contact_', 'Contact', $contact->id);
            } catch (\Exception $e) {
                $status = false;
                $message = 'Something is wrong' . $e;
            }
            return response()->json([
                'status' => $status,
                'data' => $contact,
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
            'message' => 'Contact List',
            'data' => $result
        ]);
    }

    public function query()
    {
        $current_company = TokenController::getCompanyId();
        $query = DB::table('crm_account_contacts as c')
            ->leftJoin('accounts as a', 'a.id', '=', 'c.account_id')
            ->select('c.first_name', 'c.last_name', 'c.email', 'c.primary_contact_number', 'c.job_title',
                'c.department', 'c.work_telephone_number', 'c.work_mobile_number', 'c.status', 'c.business_type',
                'c.facebook_link', 'c.twitter_link', 'c.linkedin_link', 'c.source', 'c.campaign', 'c.medium',
                'c.keyword')
            ->addSelect('a.account_name')
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
            "first_name" => "c.first_name",
            "last_name" => "c.last_name",
            "email" => "c.email",
            "primary_contact_number" => "c.primary_contact_number",
            "job_title" => "c.job_title",
            "department" => "c.department",
            "work_telephone_number" => "c.work_telephone_number",
            "work_mobile_number" => "c.work_mobile_number",
            "status" => "c.status",
            "business_type" => "c.business_type",
            "facebook_link" => "c.facebook_link",
            "twitter_link" => "c.twitter_link",
            "linkedin_link" => "c.linkedin_link",
            "source" => "c.source",
            "campaign" => "c.campaign",
            "medium" => "c.medium",
            "keyword" => "c.keyword"
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
            $query = $query->orderBy('c.first_name', 'ASC');
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
            'message' => 'Contact Full List',
            'data' => $result
        ]);
    }

    public function show($id)
    {
        $query = $this->query();
        $query = $this->search($query);
        $query = $this->sort($query);
        $result = $query->where('c.id', $id)->first();
        return response()->json([
            'status' => true,
            'status_code' => 200,
            'message' => 'Contact Show List',
            'data' => $result
        ]);
    }
}
