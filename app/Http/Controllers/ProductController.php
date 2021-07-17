<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\ProductVariantPrice;
use App\Models\Variant;
use http\Env\Response;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Http\Response|\Illuminate\View\View
     */
    public function index()
    {
        $products = Product::with('variantPrice')->paginate(5);
//        $products = Product:: select('products.id' ,'products.title','products.description','product_variants.variant' )
//        ->leftJoin('product_variants','products.id' ,'=' ,'product_variants.product_id')
//        ->paginate();
//        dd($products);

        return view('products.index',compact('products'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Http\Response|\Illuminate\View\View
     */
    public function create()
    {
        $variants = Variant::all();
        return view('products.create', compact('variants'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
//        dd($request->all());
        $validated = $request->validate([
            'title'=>'required',
            'sku'=>'required|unique:products',
            'description'=>'required',
        ]);

        $exception = DB::transaction(function() use ($request) {
            $product_variant_one = array();
            $product_variant_two = array();
            $product_variant_three = array();
            // try...catch
            try {

                $insert_product = new Product();
                $insert_product->title = $request->title;
                $insert_product->sku =$request->sku;
                $insert_product->description =$request->description;
                $insert_product->save();

                if ($request->product_variant) {

                    foreach ($request->product_variant_prices as $key=>$product_variant_price){

                        $product_variant_price_array = explode("/", $product_variant_price['title']);
                        foreach ($product_variant_price_array as $key1=>$item){
                            if ($item) {
                                $insert_product_variant = new ProductVariant();
                                $insert_product_variant->variant = $product_variant_price_array[$key1];
                                $insert_product_variant->variant_id = $request->product_variant[$key1]['option'];
                                $insert_product_variant->product_id = $insert_product->id;
                                $insert_product_variant->save();

                                if ($request->product_variant[$key1]['option'] == 1) {
                                    $product_variant_one[] = $insert_product_variant->id;
//                                    dump(';product_varient_one id boshbe');
                                }
                                elseif ($request->product_variant[$key1]['option'] == 2){
                                    $product_variant_two[] = $insert_product_variant->id;
//                                    dump(';product_varient_two');
                                }
                                else{
                                    $product_variant_three[] = $insert_product_variant->id;
//                                    dump(';product_varient_three');
                                }
                            }
                        }
                    }
                    if ($request->product_variant_prices) {
                        $count = max(count($product_variant_one) , count($product_variant_two) , count($product_variant_three));
                        for ($i=0 ;$i<$count ; $i++){
                            $insert_product_variant_price = new ProductVariantPrice();
                            if (array_key_exists($i,$product_variant_one)) {
                                $insert_product_variant_price->product_variant_one = $product_variant_one[$i];
                            }
                            if (array_key_exists($i,$product_variant_two)){
                                $insert_product_variant_price->product_variant_two = $product_variant_two[$i];
                            }
                            if (array_key_exists($i,$product_variant_three)){
                                $insert_product_variant_price->product_variant_three = $product_variant_three[$i];
                            }
                            $insert_product_variant_price->price = $product_variant_price['price'];
                            $insert_product_variant_price->stock = $product_variant_price['stock'];
                            $insert_product_variant_price->product_id = $insert_product->id;
                            $insert_product_variant_price->save();
                        }
                    }

//                    dump($product_variant_one);
//                    dump($product_variant_two);
//                    dump($product_variant_three);
//                    dd($request->product_variant_prices);
                }

            }
            catch(Exception $e) {
                return $e;
            }

        });
//
//        dd(3);
        return response()->json(200);

    }


    /**
     * Display the specified resource.
     *
     * @param \App\Models\Product $product
     * @return \Illuminate\Http\Response
     */
    public function show($product)
    {

    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param \App\Models\Product $product
     * @return \Illuminate\Http\Response
     */
    public function edit(Product $product)
    {
        $product_variant_price = $product->variantPrice;
        $product_variant = $product->productVariant;
//        dump($product_variant_price);
//        dd($product);
        $variants = Variant::all();
        return view('products.edit', compact('variants','product','product_variant','product_variant_price'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Product $product
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Product $product)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\Models\Product $product
     * @return \Illuminate\Http\Response
     */
    public function destroy(Product $product)
    {
        //
    }

    public function search(Request $request){
        $search = $request->input('title');
        $products = Product::with('variantPrice')
            ->where('title' ,'LIKE',"%{$search}%")
            ->paginate(5);
        return view('products.index',compact('products'));
    }
    public function variant_delete($id){

        $varient = ProductVariantPrice::find($id);
        $varient->delete();
        return response()->json(200);
    }

    public function uploadImage(Request $request){
//        dd($request->all());
        $imageName = $request->file->getClientOriginalName();
//        dd($imageName);
        $path= $request->file->move(public_path('images'), $imageName);
        return response()->json($path);
    }

    public function deleteImage(Request $request){
        if ($request->name){
            unlink(public_path('/images'), $request->name);
        }
        dd($request->all());
    }
}

