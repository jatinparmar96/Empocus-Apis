<?php

namespace App\Api\V1\Controllers\CRM;

use App\Api\V1\Controllers\Authentication\TokenController;
use App\Api\V1\Controllers\Masters\AddressController;
use App\CRM_Models\Lead;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class QuotationController extends Controller
{
    public function form(Request $request)
    {
        return response()->json([
            'data'=> $request->all()
        ]);
        $status = true;
        $id = $request->get('id');
        $user = TokenController::getUser();
        $current_company_id = TokenController::getCompanyId();
        if ($id === 'new') {
            $lead = new Lead();
            $lead->company_id = $current_company_id;
            $message = 'Record Added Successfully';
            $lead->created_by_id = $user->id;
        } else {
            $message = 'Record Updated Successfully';
            $lead = Lead::findOrFail($id);
        }
        if ($status) {
            $lead->first_name = $request->get('first_name');
            $lead->last_name = $request->get('last_name');
            $lead->email = $request->get('email');
            $lead->primary_contact_number = $request->get('primary_contact_number');
            $lead->department = $request->get('department');
            $lead->lead_status = $request->get('lead_status');
            $lead->company_name = $request->get('company_name');
            $lead->company_employee_number = $request->get('company_employee_number');
            $lead->company_annual_revenue = $request->get('company_annual_revenue');
            $lead->company_website = $request->get('company_website');
            $lead->company_phone = $request->get('company_phone');
            $lead->company_industry_type = $request->get('company_industry_type');
            $lead->company_business_type = $request->get('company_business_type');
            $lead->twitter_link = $request->get('twitter_link');
            $lead->facebook_link = $request->get('facebook_link');
            $lead->linkedin_link = $request->get('linkedin_link');
            $lead->deal_name = $request->get('deal_name');
            $lead->deal_value = $request->get('deal_value');
            $lead->deal_expected_close_date = $request->get('deal_expected_close_date');
            $lead->deal_product = $request->get('deal_product');
            $lead->source = $request->get('source');
            $lead->campaign = $request->get('campaign');
            $lead->medium = $request->get('medium');
            $lead->keyword = $request->get('keyword');
            $lead->updated_by_id = $user->id;
            try {
                $lead->save();
                AddressController::storeAddress($request, 'lead_', 'Lead', $lead->id);
            } catch (\Exception $e) {
                $status = false;
                $message = 'Something is wrong' . $e;
            }
            return response()->json([
                'status' => $status,
                'data' => $lead,
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
            'message' => 'Leads List',
            'data' => $result
        ]);
    }

    public function query()
    {
        $company_id = TokenController::getCompanyId();
        $query = DB::table('leads as l')
            ->leftJoin('users as u', 'l.created_by_id', 'u.id')
            ->leftJoin('raw_products as rp', 'l.deal_product', 'rp.id')
            ->select('l.id', 'l.first_name', 'l.last_name', 'l.email', 'l.company_name', 'l.primary_contact_number', 'l.department',
                'l.lead_status', 'l.company_employee_number', 'l.company_annual_revenue', 'l.company_website',
                'l.company_phone', 'l.company_industry_type', 'l.company_business_type', 'l.twitter_link', 'l.facebook_link',
                'l.linkedin_link', 'l.deal_name', 'l.deal_value', 'l.deal_expected_close_date', 'l.deal_product', 'l.source',
                'l.campaign', 'l.medium', 'l.keyword', 'l.created_by_id', 'l.updated_by_id')
            ->addSelect('u.display_name')
            ->addSelect('rp.product_display_name')
            ->where('l.company_id', '=', $company_id);
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
            "id" => "l.id",
            "first_name" => "l.first_name",
            "last_name" => "l.last_name",
            "email" => "l.email",
            "primary_contact_number" => "l.primary_contact_number",
            "department" => "l.department",
            "lead_status" => "l.lead_status",
            "company_employee_number" => "l.company_employee_number",
            "company_annual_revenue" => "l.company_annual_revenue",
            "company_website" => "l.company_website",
            "company_phone" => "l.company_phone",
            "company_industry_type" => "l.company_industry_type",
            "company_business_type" => "l.company_business_type",
            "twitter_link" => "l.twitter_link",
            "facebook_link" => "l.facebook_link",
            "linkedin_link" => "l.linkedin_link",
            "deal_name" => "l.deal_name",
            "deal_value" => "l.deal_value",
            "deal_expected_close_date" => "l.deal_expected_close_date",
            "deal_product" => "l.deal_product",
            "source" => "l.source",
            "campaign" => "l.campaign",
            "medium" => "l.medium",
            "keyword" => "l.keyword",
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
            $query = $query->orderBy('l.lead_status', 'ASC');
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
            'message' => 'Leads Full List',
            'data' => $result
        ]);
    }

    public function show($id)
    {
        $data['lead'] = $this->query()->where('l.id', $id)->get();
        $data['lead_addresses'] = AddressController::get_address_by_type($id, 'Lead');
        return response()->json([
            'status' => true,
            'status_code' => 200,
            'message' => 'Leads',
            'data' => $data
        ]);
    }
}
