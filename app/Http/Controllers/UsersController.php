<?php

namespace App\Http\Controllers;


use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Purifier;
use Hash;
use Response;
use App\User;
use Auth;
use JWTAuth;
use File;

class UsersController extends Controller
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

    $users = User::all();
    return Response::json($users);
  }

  public function signUp(Request $request)
    {
      $rules=[
        "email"=>"required",
        "username"=>"required",
        "password"=>"required",

    ];

      $validator=Validator::make(Purifier::clean($request->all()),$rules);
        if($validator->fails())
          {
              return Response::json(["error"=>"You need all fields."]);
          }

    $check = User::where("email","=",$request->input("email"))->orWhere("name","=",$request->input("username"))->first();
      if(!empty($check))
        {
          return Response::json(["error"=>"User already exists."]);
        }


    $user = new User;
      $user->name = $request->input("username");
      $user->password = Hash::make($request->input("password"));
      $user->email = $request->input("email");
      $user->roleID = 2;
      $user->save();
    return Response::json(["success"=>"Thankyou for signing up."]);
  }



  public function signIn(Request $request)
    {
      $rules=[
        "email"=>"required",
        "password"=>"required",
      ];

    $validator = Validator::make(Purifier::clean($request->all()),$rules);
      if($validator->fails())
        {
          return Response::json(["error"=>"Your inputs were either invalid or do not exist."]);
        }


    $email = $request->input("email");
    $password = $request->input("password");

    $cred = compact("email","password",["email","password"]);

    $token = JWTAuth::attempt($cred);
    return Response::json(compact("token"));
}




  public function update($id, Request $request)
  {

    $rules=[
      "email"=>"required",
      "username"=>"required",
      "password"=>"required",
      "roleID"=>"required",
  ];

    $validator = Validator::make(Purifier::clean($request->all()), $rules);
      if($validator->fails())
        {
            return Response::json(["error" => "You need to fill out all fields."]);
        }

  $user = User::find($request->input('userID'));
      if(empty($user))
        {
          return Response::json(["error" => "User does not exist."]);
        }

        $user = Auth::user();
        if($user->roleID != 1)
          {
            return Response::json(["error" => "Not allowed."]);
          }

  $user = User::find($id);
      $user->name = $request->input("username");
      $user->password = Hash::make($request->input("password"));
      $user->email = $request->input("email");
      $user->roleID = 2;
    $user->save();

    return Response::json(["success" => "Congrats, You did it."]);

  }





  public function show()
  {

    $user = Auth::user();
    if($user->roleID != 1)
      {
        return Response::json(["error" => "Not allowed."]);
      }


    $user = User::find($user->id);
    return Response::json($user);
  }





  public function destroy($id)
  {

    $user = Auth::user();
    if($user->roleID != 1)
      {
        return Response::json(["error" => "Not allowed."]);
      }

    $user = User::find($id);
    $user->delete();

    return Response::json(["success" => "Deleted User."]);
  }
}
