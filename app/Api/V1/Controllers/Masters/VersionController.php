<?php

namespace App\Api\V1\Controllers\Masters;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Model\UnitOfMeasurement;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\DB;

use App\Api\V1\Controllers\Authentication\TokenController;

class VersionController extends Controller
{
    
    public function get_version()
    {
        $query = DB::table('version')->select('version_number')->first();
        return response()->json([
            'status' => true,
            'status_code' => 200,
            'message' => 'App Version',
            'data' => $query
            ]);          
    }

}
