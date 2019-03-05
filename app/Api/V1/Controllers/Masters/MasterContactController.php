<?php

namespace App\Api\V1\Controllers\Masters;

use App\Api\V1\Controllers\Authentication\TokenController;
use App\Model\MasterContacts;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MasterContactController extends Controller{

    public static function form($request,$type,$type_id)
    {
        $request = collect($request);
        $id = $request->get('contact_id');
        if($id === 'new')
        {
            $contact = new MasterContacts();
            $contact->created_by_id = TokenController::getUser()->id;
            $contact->company_id=TokenController::getCompanyId();
        }
        else
        {
            $contact = MasterContacts::findOrFail($id);
        }
        $contact->type = $type;
        $contact->type_id = $type_id;
        $contact->first_name  = $request->get('first_name');
        $contact->last_name = $request->get('last_name');
        $contact->mobile_number  = $request->get('mobile_number');
        $contact->alternate_mobile_number  = $request->get('alternate_mobile_number');
        $contact->email = $request->get('email');
        $contact->designation  = $request->get('designation');
        $contact->branch  = $request->get('branch');
        $contact->updated_by_id = TokenController::getUser()->id;
        try
        {
            $contact->save();
        }
        catch(\Exception $e )
        {
            throw new \Exception($e);
        }
        return;
    }
    public static function get_contacts_by_type($id,$type)
    {
        $query = DB::table('master_contacts as a')
            ->select('a.id as contact_id','a.first_name','a.last_name','a.email','a.mobile_number','a.alternate_mobile_number','a.designation','a.branch')
            ->where('a.type_id', $id)
            ->where('a.type', $type)->get();
        return $query;
    }
}