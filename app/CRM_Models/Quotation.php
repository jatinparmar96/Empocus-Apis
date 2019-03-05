<?php

namespace App\CRM_Models;

use Illuminate\Database\Eloquent\Model;

class Quotation extends Model
{
    protected $table = 'quotations';

    public function products()
    {
        return $this->belongsToMany('App\CRM_Models\QuotationProducts', 'products', 'id');
    }
}
