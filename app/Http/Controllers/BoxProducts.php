<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Purifier;
use Hash;
use Response;
use App\Product;
use JWTAuth;
use File;
use Auth;

class BoxProducts extends Controller

{
  public function __construct()
  {
    $this->middleware("jwt.auth" , ["only" => ["update","store","destroy"]]);
  }

  public function index()
  {
    $products = Product::all();
    return Response::json($products);
  }

  public function store(Request $request)
  {
    $rules = [

      "image" => 'required',
      "price" => 'required',
      "description" => 'required',
      "name" => 'required',

  ];

    $validator = Validator::make(Purifier::clean($request->all()), $rules);
      if($validator->fails())
        {
          return Response::json(["error" => "You need to fill out all fields."]);
        }

        $user = Auth::user();
        if($user->roleID != 1)
          {
            return Response::json(["error" => "Not allowed."]);
          }

    $product = new Product;

    $image = $request->file('image');
    $imageName = $image->getClientOriginalName();
    $image->move('storage/', $imageName);
    $product->images = $request->root().'/storage/'.$imageName;

      $product->price = $request->input('price');
      $product->description = $request->input('description');
      $product->name = $request->input('name');

      $product->save();
    return Response::json(["success" => "Congrats, You did it."]);
  }


  public function update($id, Request $request)
  {

    $rules = [

      "image" => 'required',
      "price" => 'required',
      "description" => 'required',
      "name" => 'required',

  ];


    $validator = Validator::make(Purifier::clean($request->all()), $rules);
      if($validator->fails())
        {
          return Response::json(["error" => "You need to fill out all fields."]);
        }

        $user = Auth::user();
        if($user->roleID != 1)
          {
            return Response::json(["error" => "Not allowed."]);
          }


    $product = Product::find($id);

    $image = $request->file('image');
    $imageName = $image->getClientOriginalName();
    $image->move('storage/', $imageName);
    $product->images = $request->root().'/storage/'.$imageName;

      $product->price = $request->input('price');
      $product->description = $request->input('description');
      $product->name = $request->input('name');

      $product->save();

    return Response::json(["success" => "Congrats, You did it."]);

  }


  public function show($id)
  {
    $product = Product::find($id);

    return Response::json($product);
  }


  public function destroy($id)
  {

    $user = Auth::user();
    if($user->roleID != 1)
      {
        return Response::json(["error" => "Not allowed."]);
      }


    $product = Product::find($id);

    $product->delete();

    return Response::json(["success" => "Deleted product."]);

  }

}
