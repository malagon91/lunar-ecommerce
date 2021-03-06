<?php

namespace Lunar\Http\Controllers;

use Stripe\Stripe;
use Stripe\Charge;

use Session;
use Auth;

use Lunar\Wishlist;
use Lunar\Store\Order;
use Lunar\Store\Cart;
use Lunar\Store\Product;
use Lunar\Store\Country;
use Lunar\Store\Category;
use Lunar\Address;

use Illuminate\Http\Request;

class CatalogController extends Controller
{
    public function index()
	{
		$products = Product::all()->take(3);

		return view('home')->with('products', $products);
	}

    public function greatdetail()
    {
    	$products = Product::all();
        $categories = Category::all();

    	return view('front.catalog.great-detail')->with('products', $products)->with('categories', $categories);
    }

    public function detail($slug){

        $product = Product::where('slug', '=', $slug)->first();
        //$category = Category::subCategory()->where('category_id', 'category_id')->where('slug', '!=' , $product->slug)->take(4)->inRandomOrder()->get();

    	return view('front.catalog.detail')->with('product', $product);
    }

    public function filterCategory($name)
    {
        
        $categories = Category::all();
        $category = Category::where('name', '=', $name)->first();

        return view('front.catalog.filter')->with('category', $category)->with('categories', $categories);
    }

    public function cart()
    {
    	if (!Session::has('cart')) {
    		return view('front.checkout.cart');
    	}

    	$oldCart = Session::get('cart');
    	$cart = new Cart($oldCart);

    	return view('front.checkout.cart')->with('products', $cart->items)->with('totalPrice', $cart->totalPrice);
    }

    public function addCart(Request $request, $id)
    {
    	$product = Product::find($id);
    	$oldCart = Session::has('cart') ? Session::get('cart') : null;

    	$cart = new Cart($oldCart);
    	$cart->add($product, $product->id);

    	$request->session()->put('cart', $cart);

    	//dd( $request->session()->get('cart') );

    	return redirect()->back();

    }

    public function substractOne($id)
    {
        $oldCart = Session::has('cart') ? Session::get('cart') : null;

        $cart = new Cart($oldCart);
        $cart->substractOne($id);

        if(count($cart->items) > 0){
            Session::put('cart', $cart);    
        }else{
            Session::forget('cart');
        }

        return redirect()->route('cart');
    }

    public function addMore($id)
    {
        $oldCart = Session::has('cart') ? Session::get('cart') : null;

        $cart = new Cart($oldCart);
        $cart->addMore($id);

        Session::put('cart', $cart);

        return redirect()->route('cart');
    }

    public function deleteItem($id)
    {
        $oldCart = Session::has('cart') ? Session::get('cart') : null;

        $cart = new Cart($oldCart);
        $cart->deleteItem($id);

        if(count($cart->items) > 0){
            Session::put('cart', $cart);    
        }else{
            Session::forget('cart');
        }

        return redirect()->route('cart');
    }

    /* CHECKOUT */

    public function checkout()
    {
    	if (!Session::has('cart')) {
    		return view('front.checkout.cart');
    	}

    	$oldCart = Session::get('cart');
    	$cart = new Cart($oldCart);
    	$total = $cart->totalPrice;

        if(Auth::check()){
            $user = Auth::user();

            $orders = Auth::user()->orders;

            $orders->transform(function($order, $key){
                // Cart es el nombre de la columna en la base de datos.
                $order->cart = unserialize($order->cart);
                return $order;
            });

            $addresses = Address::where('user_id', Auth::user()->id)->get();

            $countries = Country::all();

           return view('front.checkout.index')->with('total', $total)->with('orders', $orders)->with('user', $user)->with('addresses', $addresses)->with('countries', $countries);
        }

    	return view('front.checkout.index')->with('total', $total);
    }

    public function postCheckout(Request $request)
    {
    	if (!Session::has('cart')) {
    		return redirect()->view('front.checkout.cart');
    	}

    	$oldCart = Session::get('cart');
    	$cart = new Cart($oldCart);

    	Stripe::setApiKey('sk_test_tGBJk6Js27V4nwigVtf2WPnr');

    	try {
    		$charge = Charge::create(array(
			  "amount" => $cart->totalPrice * 100,
			  "currency" => "mxn",
			  "source" => $request->input('stripeToken'), 
			  "description" => "Test Payment Successfull"
			  ));

    		// GUARDAR LA ORDEN

    		$order = new Order();

    		$order->cart = serialize($cart);
    		$order->street = $request->input('street');
    		$order->street_num = $request->input('street_num');
    		$order->country = $request->input('country');
    		$order->state = $request->input('state');
    		$order->postal_code = $request->input('postal_code');
    		$order->city = $request->input('city');
            $order->country = $request->input('country');
    		$order->phone = $request->input('phone');

    		$order->client_name = $request->input('client_name');
    		$order->payment_id = $charge->id;

    		// Identificar al usuario para guardar sus datos.
    		Auth::user()->orders()->save($order);

    	} catch(\Excepton $e) {
    			return redirect()->route('checkout')->with('error', $e->getMessage() );
    	}

    		Session::forget('cart');

             alert()->success('Your purchase was succesfully completed, take a look at your order summary on "Your Profile".', 'Success!')->persistent("Ok, thanks!");

    		return redirect()->route('index')->with('success', "Your purchase was done succesfully!");
    }
}
