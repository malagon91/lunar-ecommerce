<?php

namespace Lunar\Http\Controllers;

use DB;
use Auth;

use Lunar\Store\Product;
use Lunar\Store\Category;

use Illuminate\Http\Request;

class SearchController extends Controller
{
    public function query(Request $request)
    {	
    	$query = $request->input('query');

    	$products = Product::where(DB::raw('name'), 'LIKE', "%{$query}%")
    		->orWhere('description', 'LIKE', "%{$query}%")
    		->orWhere('sku', 'LIKE', "%{$query}%")
    		->get();

    	$categories = Category::where(DB::raw('name'), 'LIKE', "%{$query}%")
    		->get();

        return view('front.search.query')->with('products', $products)->with('categories', $categories);
    }
}
