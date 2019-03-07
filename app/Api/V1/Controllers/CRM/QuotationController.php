<?php

namespace App\Api\V1\Controllers\CRM;

use App\Api\V1\Controllers\Authentication\TokenController;
use App\Api\V1\Controllers\Masters\AddressController;
use App\Api\V1\Controllers\CRM\QuotationProductsController;
use App\CRM_Models\Quotation;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;


class QuotationController extends Controller
{
    public function form(Request $request)
    {       
        $status = true;
        $id = $request->get('id');
        $user = TokenController::getUser();
        $current_company_id = TokenController::getCompanyId();
        DB::beginTransaction();
        if ($id === 'new') {
            $quotation = new Quotation();
            $quotation->company_id = $current_company_id;
            $message = 'Record Added Successfully';
            $quotation->created_by_id = $user->id;
        } else {
            $message = 'Record Updated Successfully';
            $quotation = Quotation::findOrFail($id);
        }
        if ($status) {
            $quotation->customer_id = $request->get('customer_id');
            $quotation->address_id = $request->get('address_id');
            $quotation->date = $request->get('date');
            $quotation->validity_date = $request->get('validity_date');
            $quotation->delivery_at = $request->get('delivery_at');
            $quotation->transporter_name = $request->get('transporter_name');
            $quotation->eway_bill_number = $request->get('eway_bill_number');
            $quotation->gross_amount = $request->get('gross_amount');
            $quotation->discount_type = $request->get('discount_type');
            $quotation->discount = $request->get('discount');
            $quotation->total = $request->get('total');
            $quotation->cgst = $request->get('cgst');
            $quotation->sgst = $request->get('sgst');
            $quotation->igst = $request->get('igst');
            $quotation->delivery_charges = $request->get('delivery_charges');
            $quotation->grand_total = $request->get('grand_total');
            $quotation->updated_by_id = $user->id;
            try {
                $quotation->save();
                $quotationProduct = new QuotationProductsController();
                foreach($request->products as $product)
                {
                    $quotationProduct->store($product);
                }
            } catch (\Exception $e) {
                DB::rollback();
                $status = false;
                $message = 'Something is wrong' . $e;
            }
            DB::commit();
            return response()->json([
                'status' => $status,
                'data' => $quotation,
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
            'message' => 'Quotation List',
            'data' => $result
        ]);
    }

    public function query()
    {
        $company_id = TokenController::getCompanyId();
        $query = DB::table('quotations as q')
            ->leftJoin('chart_of_accounts as c', 'q.customer_id','c.id')
            ->leftJoin('addresses as a', 'q.billing_address_id', 'a.id')
            ->leftJoin('addresses as bill_a','q.delivery_address_id','bill_a.id')
            ->select('q.quotation_date','q.quotation_validity_date','q.transporter_name','q.e_way_bill_no','q.gross_amt',
                'q.discount_type','q.discount_value','q.total_amt','q.cgst_amt','q.sgst_amt','q.igst_amt')
            ->addSelect('c.ca_company_name as customer_name')
            ->addSelect('a.city')
            ->addSelect('')
            ->where('q.company_id', '=', $company_id);
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
            $query = $query->orderBy('l.id', 'ASC');
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
            'message' => 'Quotation Full List',
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
