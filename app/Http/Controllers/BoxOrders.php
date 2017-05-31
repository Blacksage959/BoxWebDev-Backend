<?php

namespace App\Http\Controllers;


use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Purifier;
use Hash;
use Response;
use App\Order;
use JWTAuth;
use File;
use Auth;
use App\Product;

class BoxOrders extends Controller

{
  public function __construct()
  {
    $this->middleware("jwt.auth" , ["only" => ["index","show"]]);
  }


  public function index()
  {

    $user = Auth::user();
    if($user->roleID != 1)
      {
        return Response::json(["error" => "Not allowed."]);
      }

    $orders = Order::join("users","orders.userID","=","users.ID")
                     ->join("products","orders.productID","=","products.id")
                     ->select("orders.id","orders.amount","orders.totalPrice","users.name","products.name")
                     ->orderBy("orders.id","desc")
                    -> get();

    return Response::json($orders);
  }

  public function store(Request $request)
  {
    $rules = [
      'productID' => 'required',

    ];


    $validator = Validator::make(Purifier::clean($request->all()), $rules);
      if($validator->fails())
        {
            return Response::json(["error" => "You need to fill out all fields."]);
        }

      $product = Product::find($request->input('productID'));

      if(empty($product))
      {
        return Response::json(["error" => "Product does not exist."]);
      }

      if($product->availability == 0)
      {
        return Response::json(["error" => "Product not available."]);
      }

    $order = new Order;
      $order->userID = Auth::user()->id;
      $order->productID = $request->input('productID');

    $order->save();

    return Response::json(["success" => "Congrats, You did it."]);

  }


  public function update($id, Request $request)
  {

    $rules = [
      'productID' => 'required',

    ];

    $validator = Validator::make(Purifier::clean($request->all()), $rules);
      if($validator->fails())
        {
            return Response::json(["error" => "You need to fill out all fields."]);
        }

        $product = Product::find($request->input('productID'));

        if(empty($product))
        {
          return Response::json(["error" => "Product does not exist."]);
        }

        if($product->availability == 0)
        {
          return Response::json(["error" => "Product not available."]);
        }



    $order = Order::find($id);
      $order->userID = Auth::user()->id;
        if($user->roleID != 1 || $user->id != $order->userID)
          {
            return Response::json(["error" => "Not allowed."]);
          }
      $order->productID = $request->input('productID');

    $order->save();

    return Response::json(["success" => "Congrats, You did it."]);

  }


  public function show($id)
  {
    $user = Auth::user();
    if($user->roleID != 1)
      {
        return Response::json(["error" => "Not allowed."]);
      }

    $order = Order::find($id);
    return Response::json($order);
  }


  public function destroy($id)
  {

    $order = Order::find($id);
    $user = Auth::user();
    if($user->roleID != 1 || $user->id != $order->userID)
      {
        return Response::json(["error" => "Not allowed."]);
      }
    $order->delete();

    return Response::json(["success" => "Deleted order."]);
  }

}
