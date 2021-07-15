@extends('layouts.app')

@section('content')

    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Products</h1>
    </div>


    <div class="card">
        <form action="{{route('search')}}" method="post" class="card-header" >
            @csrf
            <div class="form-row justify-content-between">
                <div class="col-md-2">
                    <input type="text" name="title" placeholder="Product Title" class="form-control">
                </div>
                <div class="col-md-2">
                    <select name="variant" id="" class="form-control">

                    </select>
                </div>

                <div class="col-md-3">
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text">Price Range</span>
                        </div>
                        <input type="text" name="price_from" aria-label="First name" placeholder="From" class="form-control">
                        <input type="text" name="price_to" aria-label="Last name" placeholder="To" class="form-control">
                    </div>
                </div>
                <div class="col-md-2">
                    <input type="date" name="date" placeholder="Date" class="form-control">
                </div>
                <div class="col-md-1">
                    <button type="submit" class="btn btn-primary float-right"><i class="fa fa-search"></i></button>
                </div>
            </div>
        </form>

        <div class="card-body">
            <div class="table-response">
                <table class="table">
                    <thead>
                    <tr>
                        <th>#</th>
                        <th>Title</th>
                        <th>Description</th>
                        <th width="450px">Variant</th>
                        <th width="150px">Action</th>
                    </tr>
                    </thead>

                    <tbody>

                    @forelse($products as $key=>$product)
                        @php
                        $min_price = 0;
                        $max_price = 0;
                        @endphp
                    <tr>
                        <td>{{++$key}}</td>
                        <td>{{$product->title}} <br> Created at : {{$product->created_at}}</td>
                        <td>{{$product->description}}</td>
                        <td>
                            <dl class="row mb-0" style="height: 80px; overflow: hidden" id="variant">
                                <dt class="col-sm-3 pb-0">
                                    @foreach($product->variantPrice as $variant_price)

                                        @php

                                            if ($min_price ==0 ) {
                                                $min_price = $variant_price->price;

                                            }
                                            if ($max_price ==0 ) {
                                                $max_price = $variant_price->price;
                                            }
                                            if ($min_price>$variant_price->price) {
                                                $min_price = $variant_price->price;
                                            }
                                            if ($max_price<$variant_price->price) {
                                                $max_price = $variant_price->price;
                                            }
                                        @endphp


                                        {{($variant_price->product_variant_one)? Helpers::getVariantOne($variant_price->product_variant_one).'/':''}}
                                        {{ ($variant_price->product_variant_two)? Helpers::getVariantTwo($variant_price->product_variant_two).'/':''}}
                                        {{ ($variant_price->product_variant_three)? Helpers::getVariantThree($variant_price->product_variant_three).'/':''}}
                                        {{--                                @dump($variant_price)--}}
                                        {{--                                SM/ Red/ V-Nick--}}
                                        <br>
                                    @endforeach
                                </dt>
                                <dd class="col-sm-9">
                                    <dl class="row mb-0">
                                        <dt class="col-sm-4 pb-0">Price :{{ number_format($min_price,2) }} - {{ number_format($max_price,2) }}</dt>
                                        <dd class="col-sm-8 pb-0">InStock : {{ number_format(50,2) }}</dd>
                                    </dl>
                                </dd>
                            </dl>
                            <button onclick="$('#variant').toggleClass('h-auto')" class="btn btn-sm btn-link">Show more</button>
                        </td>
                        <td>
                            <div class="btn-group btn-group-sm">
                                <a href="{{ route('product.edit', 1) }}" class="btn btn-success">Edit</a>
                            </div>
                        </td>
                    </tr>
                        @empty
                        <td>No data found</td>
                    @endforelse

                    </tbody>

                </table>
            </div>

        </div>
{{--        @dump($products)--}}
        <div class="card-footer">
            <div class="row justify-content-between">
                <div class="col-md-6">
                    <p>Showing 1 to {{$products->currentPage() * $products->perPage()}} out of {{$products->total()}}</p>
                </div>
                <div class="col-md-2">

                    {{ $products->links() }}
                </div>
            </div>
        </div>
    </div>

@endsection
