<?php

namespace App\Api\V1\Controllers\Masters;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Model\Bank;
use Tymon\JWTAuth\Facades\JWTAuth;
use App\Api\V1\Controllers\Authentication\TokenController;
use Illuminate\Support\Facades\DB;

class BillOfMaterialMasterController extends Controller
{
   public function form(Request $request)
   {
        dd($request->all());
   }
   public function query()
   {

   }
   public function list()
   {

   }
}
