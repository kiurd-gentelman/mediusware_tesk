<?php
namespace App\Http\Helpers;

use App\Models\ProductVariant;

class Helpers{
    public static function getVariantOne($id){
        return ProductVariant::find($id)->variant;
    }

    public static function getVariantTwo($id){
        return ProductVariant::find($id)->variant;
    }

    public static function getVariantThree($id){
        return ProductVariant::find($id)->variant;
    }

}
