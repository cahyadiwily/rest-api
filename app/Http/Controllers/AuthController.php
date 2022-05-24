<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Societie;
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

        $user = Societie::where('id_card_number', $request['id_card_number'])->where('password', $request['password']);
        if($user->update(['login_tokens'=>md5($request['id_card_number'])])){
            $profil = $user->join('regionals', 'societies.regional_id', '=', 'regionals.id')->get();
            foreach($profil as $userku){
            return response()
            ->json(['name' => $userku->name,
                    'born_date'=>$userku->born_date,
                    'gender'=>$userku->gender,
                    'address'=>$userku->address,
                    'token' => $userku->login_tokens,
                    'regional'=>['id'=>$userku->regional_id,'province'=>$userku->province,'district'=>$userku->district]
                 ],200);
            }
        }else{
            return response()
                ->json(['message' => 'ID Card Number or Password Incorrect'], 401);
        }
        
    }

    public function logout(Request $request){
        $token = $request->token;
        $user = Societie::where('login_tokens', $token)->first();
        if($user === null || $token === null){
            return response()
                ->json(['message' => 'Invalid Token'], 401);
        }else{
                if($user->update(['login_tokens'=>null])){
                return response()
                ->json(['message' => 'Logout Success'],200);
                }else{
                    return response()
                    ->json(['message' => 'Logout Unsuccess'],401);
                }
        }
    }
}
