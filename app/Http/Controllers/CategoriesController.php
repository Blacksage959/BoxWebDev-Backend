<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Purifier;
use Hash;
use Response;
use App\Category;
use JWTAuth;
use File;
use Auth;
use App\Product;

class CategoriesController extends Controller
{
  public function __construct()
  {
    $this->middleware("jwt.auth" , ["only" => ["update","store","destroy"]]);
  }
  public function index()
  {
    $categories = Category::all();
    return Response::json($categories);

  }

  public function store(Request $request)
  {
    $user = Auth::user();
    if($user->roleID != 1)
      {
        return Response::json(["error" => "Not allowed."]);
      }
    $rules = [
      'name' => 'required',

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

    $category = new Category;
      $category->name = $request->input('name');
      $category->save();
    return Response::json(["success" => "Congrats, You did it."]);
  }


  public function update($id, Request $request)
  {

    $rules = [
      'name' => 'required',

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

    $category = Category::find($id);
      $category->name = $request->input('name');
      $category->save();
    return Response::json(["success" => "Congrats, You did it."]);

  }


  public function show($id)
  {

    $category = Category::find($id);
    $products = Product::where("categoryID", "=" , $id)->get();
    return Response::json(["category"=> $category,"products"=>$products]);
  }


  public function destroy($id)
  {

    $user = Auth::user();
    if($user->roleID != 1)
      {
        return Response::json(["error" => "Not allowed."]);
      }


    $category = Category::find($id);
    $category->delete();
  return Response::json(["success" => "Deleted category."]);

  }

}
