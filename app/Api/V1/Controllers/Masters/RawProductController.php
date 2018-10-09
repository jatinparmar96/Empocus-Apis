<?php

namespace App\Api\V1\Controllers\Masters;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Model\RawProduct;
use Illuminate\Support\Facades\Input;
use Tymon\JWTAuth\Facades\JWTAuth;
use App\Api\V1\Controllers\Authentication\TokenController;
use Illuminate\Support\Facades\DB;


class RawProductController extends Controller
{
    public function form(Request $request)
    {
        $status = true;
        $id = $request->get('id');
        $user = TokenController::getUser();
        $current_company_id = TokenController::getCompanyId();
        if($id == 'new')
        {
            $count = RawProduct::where('product_name',$request->get('raw_product_name'))
                                ->where('company_id',$current_company_id)
                                ->count();
            if($count>0)
            {
                $status = false;
                $message = 'Please fill the form correctly!!!';
                $error['product_name'] = 'Raw Product with this name Already Exists';
            }
            else
            {
                $raw = new RawProduct();
                $raw->company_id = $current_company_id;
                $message = "Record added Successfully";
                $raw->created_by_id = $user->id;
            }
            
        }
        else
        {
            $message = 'Record Updated Successfully';
            $raw = RawProduct::findOrFail($id);
        }
        if($status)
        {
            $raw->product_name = $request->get('raw_product_name');
            $raw->product_display_name = $request->get('raw_product_display_name');
            $raw->product_code = $request->get('raw_product_code');
            $raw->product_uom = $request->get('raw_product_uom');
            $raw->product_conv_uom = $request->get('raw_product_conv_uom');
            $raw->conv_factor = $request->get('raw_product_conv_factor');
            $raw->batch_type = $request->get('raw_product_batch_type');
            $raw->stock_ledger = $request->get('raw_product_maintain_stock_ledger');
            $raw->product_rate_pick = $request->get('raw_product_rate_pick_from');
            $raw->product_purchase_rate = $request->get('raw_product_purchase_rate');
            $raw->mrp_rate = $request->get('raw_product_mrp_rate');
            $raw->sales_rate = $request->get('raw_product_sales_rate');
            $raw->gst_rate = $request->get('raw_product_gst_slot');
            $raw->max_level = $request->get('raw_product_max_level');
            $raw->min_level = $request->get('raw_product_min_level');
            $raw->description = $request->get('raw_product_description');
            $raw->product_category = $request->get('raw_product_category');
            $raw->product_hsn = $request->get('raw_product_hsn');
            $raw->updated_by_id = $user->id;
            try
            {
                $raw->save();
            }
            catch(\Exception $e)
            {
                $status = false;
                $message = 'Something is wrong. Kindly Contact Admin'.$e;
            }
            $raw = $this->query()->where('rp.id',$raw->id)->first();
            return response()->json([
                'status' => $status,
                'data' => $raw,
                'message'=>$message
                ]);
        }
        else
        {
            return response()->json([
                'status' => $status,                
                'message'=>$message,
                'error' => $error,
                ]);
        }
       

    }

    public function query()
    {

        $current_company_id = TokenController::getCompanyId();
        $query = DB::table('raw_products as rp')
                ->leftJoin('unit_of_measurements as uom1','rp.product_uom','uom1.id')
                ->leftJoin('unit_of_measurements as uom2','rp.product_conv_uom','uom2.id')
                ->leftJoin('taxes as t','rp.gst_rate','t.id')
                ->leftJoin('product_categories as pc','rp.product_category','pc.id')
                ->select(
                'rp.id','rp.product_name','rp.product_display_name','rp.product_code','rp.conv_factor','rp.batch_type','rp.stock_ledger','rp.product_rate_pick','rp.product_purchase_rate','rp.mrp_rate','rp.sales_rate','rp.gst_rate','rp.max_level','rp.min_level','rp.product_hsn','rp.description'
                )
                ->addSelect('uom1.unit_name')
                ->addSelect('uom2.unit_name as conversion_uom')
                ->addSelect('t.tax_name','t.tax_rate')
                ->addSelect('pc.product_category_name')
                ->where('rp.company_id',$current_company_id);
        return $query;
    }

    public function TableColumn()
    {         
        $TableColumn = array(
                       "id"=>"rp.id",
                       "product_name"=>"rp.product_name",                       
                       "product_display_name"=>"rp.product_display_name",
                       "product_code"=>"rp.product_code",                       
                       "conv_factor"=>"rp.conv_factor",
                       "batch_type"=>"rp.batch_type",
                       "stock_ledger"=>"rp.stock_ledger",
                       "product_rate_pick"=>"rp.product_rate_pick",
                       "product_purchase_rate"=>"rp.product_purchase_rate",
                       "mrp_rate"=>"rp.mrp_rate",
                       "sales_rate"=>"rp.sales_rate",
                       "gst_rate"=>"rp.gst_rate",
                       "max_level"=>"rp.max_level",
                       "min_level"=>"rp.min_level",
                       "description"=>"rp.description",
                       );
        return $TableColumn;
    }

    public function sort($query)
    {
       $sort = \Request::get('sort');
       if(!empty($sort))
        {
            $TableColumn = $this->TableColumn();
            $query = $query->orderBy($TableColumn[key($sort)], $sort[key($sort)]);
        }
        else
           $query = $query->orderBy('rp.product_display_name', 'ASC');
           
        return $query;      
    }

    public function search($query)
    {      
        $search = \Request::get('search');
        if(!empty($search))
        {
            $TableColumn = $this->TableColumn();
            foreach($search as $key=>$searchvalue)
            { 
                if($searchvalue !== '') 
                    $query =  $query->Where($TableColumn[$key], 'LIKE', '%'.$searchvalue.'%');
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
                'message' => 'Raw Product List',
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
                'message' => 'Raw Product Full List',
                'data' => $result
                ]);
    }
}
