<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Societie;
use App\Models\Regional;
use App\Models\Consultation;
use App\Models\Spot;
use App\Models\SpotVaccine;
use App\Models\Vaccine;
use App\Models\Vaccination;
class ConsulController extends Controller
{
    public function addconsul(Request $request){
        $token = $request->token;
        $user = Societie::where('login_tokens', $token)->first();
        if($user === null || $token === null){
            return response()
                ->json(['message' => 'Unauthorized user'], 401);
        }else{
           
            $user_id = $user->id;
            if($consultation = Consultation::create([
                'society_id' => $user_id,
                'status' => 'pending',
                'disease_history' => $request->desease_history,
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
        $user = Societie::where('login_tokens', $token)->first();
        if($user === null || $token === null){
            return response()
                ->json(['message' => 'Unauthorized user'], 401);
        }else{
            
            $user_id = $user->id;
            if($consultation = Consultation::where('society_id' , $user_id)){
                $result = $consultation->join('medicals', 'consultations.doctor_id', '=', 'medicals.id')->get();
                foreach($result as $result){
                    return response()
                    ->json(['id' => $result->id,
                            'status'=>$result->status,
                            'disease_history'=>$result->disease_history,
                            'current_symptoms'=>$result->current_symptoms,
                            'doctor_notes' => $result->doctor_notes,
                            'doctor'=>['DOCTOR'=>$result->name]
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
        $user = Societie::where('login_tokens', $token)->first();
        if($user == null || $token === null){
            return response()
                ->json(['message' => 'Unauthorized user'], 401);
        }else{
            $spots = Spot::where('regional_id' , $user->regional_id)
                            ->select('id','name','address','serve','capacity')
                            ->get()->toArray();
                for($x = 0; $x < count($spots); $x++){
                    $vaccine = SpotVaccine::where('spot_id',$spots[$x]['id'])
                                                ->join('Vaccines','Spot_Vaccines.vaccine_id','=','Vaccines.id')
                                                ->select('Vaccines.name')
                                                ->get();
                $data=[];
                for($y=0;$y < count($vaccine); $y++){
                    if(count($vaccine) >= 0){
                        $status = true;
                    }else{ $status = false;}
                    $data[$y]=array($vaccine[$y]['name'] => $status);
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
        $user = Societie::where('login_tokens', $token)->first();
        if($user === null || $token === null || $id === null){
            return response()
                ->json(['message' => 'Unauthorized user'], 401);
        }else{
            if($request->date === null){
                $date = date('Y-m-d');
            }else{$date = $request->date;}
            $count = Vaccination::where('spot_id' , $id)
                            ->where('date',$date)
                            ->where('vaccine_id',null)
                            ->count();
            $spots = Spot::where('id' , $id)
                            ->get();
                    return response()->json([
                            "date" => date('F d, Y', strtotime($date)),
                            'Spot' => ['id'=>$spots[0]['id'],'name'=>$spots[0]['name'],'address'=>$spots[0]['address'],'serve'=>$spots[0]['serve'],'capacity'=>$spots[0]['capacity']],
                            'vaccinations_count' => $count
                            ],200);
                        
                
        }
    }
    
}
