<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Regional;
use App\Models\Consultation;
use App\Models\Spot;
use App\Models\VaccineAvaillable;
use App\Models\RegVaccination;

use Validator;

class ConsulController extends Controller
{
    public function addconsul(Request $request){
        $token = $request->token;
        $user = User::where('remember_token', $token)->first();
        if($user === null){
            return response()
                ->json(['message' => 'Unauthorized user'], 401);
        }else{
            $validator = Validator::make($request->all(),[
                'desease_history' => 'required',
                'current_symptoms' => 'required'
            ]);
    
            if($validator->fails()){
                return response()->json($validator->errors());       
            }
            $user_id = $user->id;
            if($consultation = Consultation::create([
                'user_id' => $user_id,
                'status' => 'pending',
                'desease_history' => $request->desease_history,
                'current_symptoms' => $request->current_symptoms,
                'doctor_notes' => 'OK',
                'doctor_id' => '1'
             ])){
                return response()
                ->json(['message' => 'Request consultation sent'],200);
                }else{
                    return response()
                    ->json(['message' => 'Request consultation fail'],401);
                }
        }
    }

    public function getconsul(Request $request){
        $token = $request->token;
        $user = User::where('remember_token', $token)->first();
        if($user === null || $token === null){
            return response()
                ->json(['message' => 'Unauthorized user'], 401);
        }else{
            
            $user_id = $user->id;
            if($consultation = Consultation::where('user_id' , $user_id)){
                $result = $consultation->join('doctors', 'consultations.doctor_id', '=', 'doctors.id')->get();
                foreach($result as $result){
                    return response()
                    ->json(['id' => $result->id,
                            'status'=>$result->status,
                            'disease_history'=>$result->desease_history,
                            'current_symptoms'=>$result->current_symptoms,
                            'doctor_notes' => $result->doctor_notes,
                            'doctor'=>['DOCTOR'=>$result->doctor_name]
                         ],200);
                    }
                }else{
                    return response()
                    ->json(['message' => 'Unauthorized user'],401);
                }
        }
    }

    public function getspot(Request $request){
        $token = $request->token;
        $user = User::where('remember_token', $token)->first();
        if($user == null || $token === null){
            return response()
                ->json(['message' => 'Unauthorized user'], 401);
        }else{
            $spots = Spot::where('regional_id' , $user->regional_id)
                            ->select('id','spot_name as name','address','serve','capacity')
                            ->get()->toArray();
                for($x = 0; $x < count($spots); $x++){
                    $vaccine = VaccineAvaillable::where('spot_id',$spots[$x]['id'])
                                                ->join('Vaccines','Vaccine_availlables.vaccine_id','=','Vaccines.id')
                                                ->select('Vaccines.vaccine_name','Vaccine_availlables.status')
                                                ->get();
                $data=[];
                for($y=0;$y < count($vaccine); $y++){
                    if($vaccine[$y]['status'] === 1){
                        $status = true;
                    }else{ $status = false;}
                    $data[$y]=array($vaccine[$y]['vaccine_name'] => $status);
                }
               
                       $cek = ['available_vaccines'=>$data];
                       $spots[$x] = array_merge($spots[$x],$cek);
                } 
                    return response()->json([
                            "Spots" => $spots
                            ],200);                     
        } 
    }


    public function getspotdetail(Request $request,$id){
        $token = $request->token;
        $user = User::where('remember_token', $token)->first();
        if($user === null || $token === null || $id === null){
            return response()
                ->json(['message' => 'Unauthorized user'], 401);
        }else{
            if($request->date === null){
                $date = date('Y-m-d');
            }else{$date = $request->date;}
            $count = RegVaccination::where('spot_id' , $id)
                            ->where('vaccin_date',$date)
                            ->where('status','Waiting')
                            ->count();
            $spots = Spot::where('id' , $id)
                            ->get();
                    return response()->json([
                            "date" => date('F d, Y', strtotime($date)),
                            'Spot' => ['id'=>$spots[0]['id'],'name'=>$spots[0]['spot_name'],'address'=>$spots[0]['address'],'serve'=>$spots[0]['serve'],'capacity'=>$spots[0]['capacity']],
                            'vaccinations_count' => $count
                            ],200);
                        
                
        }
    }

}
