<?php

namespace App\CRM_Models;

use Illuminate\Database\Eloquent\Model;

class Deal extends Model
{
    //
   public function product()
   {
       return $this->belongsTo('App\Model\RawProduct', 'product_id', 'id');
   }
}
