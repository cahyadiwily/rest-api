<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Regional;
use Validator;
use Auth;
class AuthController extends Controller
{
    public function login(Request $request)
    {
        
        $validator = Validator::make($request->all(), [
            'id_card_number' => 'required',
            'password' => 'required'
        ]);
        if ($validator->fails())
        {
            return response()
                ->json(['message' => 'ID Card Number or Password required'], 401);
        }

        $user = User::where('id_card_number', $request['id_card_number'])->where('password', $request['password']);
        if($user->update(['remember_token'=>md5($request['id_card_number'])])){
            $profil = $user->join('regionals', 'users.regional_id', '=', 'regionals.id')->get();
            foreach($profil as $userku){
            return response()
            ->json(['name' => $userku->name,
                    'born_date'=>$userku->born_date,
                    'gender'=>$userku->gender,
                    'address'=>$userku->address,
                    'token' => $userku->remember_token,
                    'regional'=>['id'=>$userku->regional_id,'province'=>$userku->province,'district'=>$userku->district]
                 ],200);
            }
        }else{
            return response()
                ->json(['message' => 'ID Card Number or Password Incorrect'], 401);
        }
        
    }

    public function profile(Request $request){
        $token = $request->token;
        $user = User::where('remember_token', $token)->first();
        if($user === null){
            return response()
                ->json(['message' => 'Unauthorized User token'], 401);
        }else{
                $profil = $user->join('regionals', 'users.regional_id', '=', 'regionals.id')->get();
                foreach($profil as $userku){
                return response()
                ->json(['name' => $userku->name,
                        'born_date'=>$userku->born_date,
                        'gender'=>$userku->gender,
                        'address'=>$userku->address,
                        'token' => $userku->remember_token,
                        'regional'=>['id'=>$userku->regional_id,'province'=>$userku->province,'district'=>$userku->district]
                     ],200);
                }
        }
    }

    public function logout(Request $request){
        $token = $request->token;
        $user = User::where('remember_token', $token)->first();
        if($user === null){
            return response()
                ->json(['message' => 'Invalid Token'], 401);
        }else{
                if($user->update(['remember_token'=>null])){
                return response()
                ->json(['message' => 'Logout Success'],200);
                }else{
                    return response()
                    ->json(['message' => 'Logout Unsuccess'],401);
                }
        }
    }

}
