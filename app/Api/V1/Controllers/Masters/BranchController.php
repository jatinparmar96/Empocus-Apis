<?php

namespace App\Api\V1\Controllers\Masters;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Input;
use JWTAuth;
use App\Model\Branch;
use App\Model\Bank;
use Illuminate\Support\Facades\DB;
use App\Model\Address;
use App\Api\V1\Controllers\Authentication\TokenController;

class BranchController extends Controller
{
    public function form(Request $request,$company_id=0)
    {
        $status = true;
        $current_company_id = $company_id;
        if($company_id === 0)
        {
           $current_company_id = TokenController::getCompanyId();
        }
        $id = $request->get('id');
        if($id === 'new')
        {
          
            $count = Branch::where('name',$request->get('branch_name'))
                            ->where('company_id',$current_company_id)
                            ->count();
           
            if($count>0)
            {
              
               $status = false;
               $message = 'Kindly Fill up the form Correctly !!';
               $error['branch_name']= 'Branch Name already Exits';
            }
            else
            {
               
                $message = 'New Branch created successfully!!';
                $branch = new Branch();
                $branch->company_id = $current_company_id;
                $branch->created_by_id =TokenController::getUser()->id;
            }
           
        }
        else
        {
           
            $message = 'Branch updated successfully!!';
            $branch = findOrFail($id);
        }
        if($status)
        {
          

            if($company_id !== 0)
            {
                $branch->name = 'Head Office';
            }
            else
            {
                $branch->name = $request->get('branch_name');
            }
            $branch->code = $request->get('branch_code');
            $branch->gst_number = $request->get('branch_gst_number');   
            $branch->updated_by_id = TokenController::getUser()->id;
            try
            {
                $branch->save();
            }
            catch(\Exception $e)
            {
                $status = false;
                $message='Something is wrong'. $e;
            }
            if ($company_id != 0) 
            {
                return $branch; 
            }
            else
            {
                try
                {
                    $bank = Bank::findOrFail($request->get('branch_bank_id'));
                    $bank->type = 'Branch';
                    $bank->type_id = $branch->id;
                    $bank->save();
                    AddressController::storeAddress($request,'branch_','Branch',$branch->id);
                    $branch = $this->query()->where('b.id',$branch->id)->first();
                }
                catch(\Exception $e)
                {
                    $status = false;
                    $message='Something is wrong'.$e;
                }
                return response()->json([
                    'status'=>$status,
                    'data'=>$branch,
                    'message'=>$message
                ]);
           
            }
        }
        else
        {
            return response()->json([
                'status'=>$status,
                'message'=>$message,
                'error'=>$error
            ]);
        }
    }
  

    public function query()
    {
        $current_company_id  = TokenController::getCompanyId();
        $query = DB::table('company_branches as b')
                    ->join('addresses as a','b.id','a.type_id')
                    ->join('banks as ba','b.id','ba.type_id')
                    ->select(
                    'b.id','b.name','b.gst_number','b.code'
                    )
					->addSelect('a.block_no','a.road_name','a.landmark','a.country','a.city','a.state','a.pincode')
                    ->addSelect('ba.id','ba.bank_name','ba.account_name','ba.account_no','ba.ifsc_code')
                    ->where('b.company_id',$current_company_id);
        return $query;
    }

    public function TableColumn()
    {         
        $TableColumn = array(
                       "id"=>"b.id",
                       "name"=>"b.name",
                       "gst_number"=>"b.gst_number",
                       "code"=>"b.code",
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
           $query = $query->orderBy('b.name', 'ASC');
           
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
                'message' => 'Branch List',
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
                'message' => 'Branch Full List',
                'data' => $result
                ]);
    }
}
