<?php

namespace App\api\V1\Controllers\CRM;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\CRM_Models\QuotationProducts;

class QuotationProductsController extends Controller
{
    public function store($product)
    {
        if($product->id)
        {
            $newProduct = QuotationProducts::findOrFail($product->id);
        }
        else
        {
            $newProduct = new QuotationProducts();
        }
        $newProduct->product_id = $product->id;
        $newProduct->rate= $product->rate;
        $newProduct->quantity = $product->quantity;
        $newProduct->gst = $product->gst;
        $newProduct->total = $product->total;
        try
        {
            $newProduct->save();
        }
        catch(\Exception $e)
        {
            throw new \Exception($e);
        }
        return true;
    }
}
