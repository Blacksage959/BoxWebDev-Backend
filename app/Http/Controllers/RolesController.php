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
use App\Role;

class RolesController extends Controller
{
  public function __construct()
  {
    $this->middleware("jwt.auth" , ["only" => ["index","show","update","store","destroy"]]);
  }

  public function index()
  {
    $user = Auth::user();
    if($user->roleID != 1)
      {
        return Response::json(["error" => "Not allowed."]);
      }

    $roles = Role::all();
    return Response::json($roles);
  }

  public function store(Request $request)
  {
    $rules = [
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
    $role = new Role;
    $role->name = $request->input('name');
    $role->save();

    return Response::json(["success" => "Congrats, You did it."]);
  }


  public function update($id, Request $request)
  {
    $user = Auth::user();
    if($user->roleID != 1)
      {
        return Response::json(["error" => "Not allowed."]);
      }
    $rules = [
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

    $role = Role::find($id);
      $role->name = $request->input('name');
    $role->save();

    return Response::json(["success" => "Congrats, You did it."]);

  }


  public function show($id)
  {

    $user = Auth::user();
    if($user->roleID != 1)
      {
        return Response::json(["error" => "Not allowed."]);
      }

    $role = Role::find($id);
      return Response::json($role);
  }


  public function destroy($id)
  {

    $user = Auth::user();
    if($user->roleID != 1)
      {
        return Response::json(["error" => "Not allowed."]);
      }

    $role = Role::find($id);
    $role->delete();
      return Response::json(["success" => "Deleted Role."]);

  }
}
