<?php

namespace App\Http\Controllers;

use App\Models\Gate;
use App\Models\Guest;
use App\Models\PasswordReset;
use App\Models\Room;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Str;
use App\Rules\MatchOldPassword;
use Illuminate\Support\Facades\Hash;

class ApiController extends Controller
{
    //AUTH
    public function register(Request $request){
        $validator = Validator::make($request->all(), [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8'],
        ]);
        if($validator->fails()){
            return response()->json([
            'success' => false,
            'message' => $validator->errors(),
            ], 401);
        }
        $input = $request->all();
        $input['password'] = bcrypt($input['password']);
        $user = User::create($input);
        // $success['token'] = $user->createToken('appToken')->accessToken;
        $success = $user->createToken('RegToken')->plainTextToken;
        return response()->json([
            'success' => true,
            'token' => $success,
            'token_type' => 'Bearer',
            'user' => $user
        ]);
    }

    public function login(){
        if(Auth::attempt(['email' => request('email'), 'password' => request('password')])){
            $user = Auth::user();
            // $success['token'] = $user->createToken('appToken')->accessToken;
            $success = $user->createToken('LoginToken')->plainTextToken;
            return response()->json([
            'success' => true,
            'token' => $success,
            'token_type' => 'Bearer',
            'user' => $user,
            ]);
        } else{
            return response()->json([
            'success' => false,
            'message' => 'Invalid Email or Password',
            ], 401);
        }
    }

    public function updatetoken(Request $request, $id){
        $user = User::find($id);
        $user->update(['device_token'=> $request->device_token]);
        return response()->json(['message'=>'Success','data'=>$user]);
    }

    public function logout(Request $request){
        try {
            $request->user()->currentAccessToken ()->delete() ;
            return response()->json (['status '=> 'success', 'message'=> "Logout successfully!", 'data'=> []]);
        }catch (\Exception $e) {
            return response () ->json (['status '=> 'false', 'message'=>$e->getMessage(), 'data' => []],500);
        }
    }

    public function forgotPassword(Request $request){
        try{
            $user = User::where('email',$request->email)->get();
            if(count($user) > 0){
                $token = Str::random(64);
                $domain = URL::to('/');
                $url = $domain.'/password/reset/'.$token.'?email='.$request->email;

                $data['url'] = $url;
                $data['email'] = $request->email;
                $data['title'] = "Lupa Password";
                $data['body'] = "Klik tautan di bawah, untuk atur ulang password.";

                Mail::send('notify.forgot_password',['data'=>$data],function($message) use ($data){
                    $message->to($data['email'])->subject($data['title']);
                });

                $datetime = Carbon::now()->format('Y-m-d H:i:s');
                PasswordReset::updateOrCreate(
                    ['email' => $request->email],
                    [
                        'email' => $request->email,
                        'token' => bcrypt($token),
                        'created_at' => $datetime
                    ]
                );
                return response()->json(['success'=>true,'msg'=>'Cek email, untuk tautan pengaturan ulang password.']);
            }else{
                return response()->json(['success'=>false,'msg'=>'User tidak ditemukan!']);
            }
        }catch (\Exception $e){
            return response()->json(['success'=>false,'msg'=>$e->getMessage()]);
        }
    }

    //ACCOUNT
    public function profil($id){
        return response()->json(User::where('id',$id)->get());
    }

    public function passwordupdate(Request $request, $id){
        // dd($request);
        $user = User::find($id);
        $validate = $request->validate([
            'old' => ['required', new MatchOldPassword],
            'new' => ['required', 'string', 'min:8'],
            'conf' => ['same:new'],
        ]);

        if($validate)
        {
            $user->update(['password'=> Hash::make($request->new)]);
            return response()->json(['message'=>'Success','data'=>$user]);
        }else{
            // return response()->json(['message'=>'Failed','data'=>[]]);
        }
    }

    public function profilupdate(Request $request, $id){
        $user = User::find($id);
        $user->update($request->all());
        return response()->json(['message'=>'Success','data'=>$user]);
    }

    //GUEST
    public function dashboard(){
        $date = now()->format('Y-m-d');
        $month = now()->format('-m-');
        $today = count(Guest::where([["created_at",'like', $date."%"],["kode",'not like', 'BOOKED-'."%"]])->get());
        $waiting = count(Guest::where([["created_at",'like', $date."%"],['acc','-']])->orWhere([["created_at",'like', $date."%"],['acc','0']])->get());
        $now = count(Guest::where([["created_at",'like', $date."%"],['acc','1'],['pulang','-']])->get());
        $all = count(Guest::all());
 $booking = count(Guest::where('kode','like','BOOKED-'.Carbon::now()->format('Y-m-d'))->get());
        $noroom = count(Guest::where([["created_at",'like', $date."%"],['acc','<>','-'],['ruangan','1']])->get());
        $data = array(
            $today,
            $waiting,
            $now,
            $all,
            $noroom,
            $booking
          );
        return response()->json($data);
    }

    public function gates(){
        $data = Gate::where('id','<>',1)->get();
        return response()->json($data);
    }

    public function rooms(){
        $data = Room::get();
        return response()->json($data);
    }

    public function store(Request $request){
        $input = $request->all();
        Guest::create($input);
    }

    public function upload(Request $request){
        if($request->file('image')){
            $idTamu = $request->kode;
            $request->file('image')->storeAs('tamu',$idTamu.'.jpg');
            return response()->json(['success'=>'Data telah berhasil ditambah']);
        }else{
            return response()->json(['failed'=>'Data telah ada']);
        }
    }

    public function appAll(){
        $data = Guest::where('acc','<>','1')->orderBy('updated_at','desc')->get();
        return response()->json($data);
    }

    public function appBooking(){
          $data = Guest::where('kode', 'BOOKED-' . Carbon::now()->format('Y-m-d'))->get();
        return response()->json($data);
    }
    
    public function appToday(){
        $date = now()->format('Y-m-d');
        $data = Guest::where([["created_at",'like', $date."%"],["kode",'not like', 'BOOKED-'."%"]])->orderBy('created_at','desc')->get();
        return response()->json($data);
    }

    public function appMonth(){
        $date = now()->format('Y-m');
        $data = Guest::where("created_at",'like', $date."%")->orderBy('created_at','desc')->get();
        return response()->json($data);
    }

    public function appNow(){
        $date = now()->format('Y-m-d');
        $data = Guest::where([["created_at",'like', $date."%"],['acc','1'],['pulang','-']])->orderBy('created_at','desc')->get();
        return response()->json($data);
    }

    public function appWaiting(){
        $date = now()->format('Y-m-d');
        $data = Guest::where([["created_at",'like', $date."%"],['acc','-']])->orWhere([["created_at",'like', $date."%"],['acc','0']])->orderBy('created_at','desc')->get();
        return response()->json($data);
    }

    public function appNoRoom(){
        $date = now()->format('Y-m-d');
        $data = Guest::where([["created_at",'like', $date."%"],['acc','<>','-'],['ruangan','1']])->orderBy('created_at','desc')->get();
        return response()->json($data);
    }

    public function appDetail($id){
        $data = DB::table('guests')
        ->join('gates', 'gates.id','=','guests.gerbang')
        ->join('users', 'users.id','=','guests.sekuriti')
        ->join('rooms', 'rooms.id','=','guests.ruangan')
        ->where('guests.id',$id)
        ->select('guests.id as id','guests.kode as kode','guests.tamu as tamu','guests.asal as asal','guests.bertemu as bertemu','guests.tujuan as tujuan','guests.datang as datang','guests.pulang as pulang','guests.suhu as suhu','gates.name as gerbang','users.name as sekuriti','rooms.name as ruangan','guests.created_at as created_at','guests.acc as acc')
        ->get();
        return response()->json($data);
    }

    public function appPulang(Request $request,$id){
        $data = Guest::find($id);
        $data->update($request->all());
        return response()->json(['success'=>'Data telah berhasil disimpan'.$data]);
    }

    public function update(Request $request,$id){
        $data = Guest::find($id);
        $data->update($request->all());
        return response()->json(['success'=>'Data telah berhasil disimpan'.$data]);
    }

    public function appApprove(Request $request,$id){
        $data = Guest::find($id);
        $data->update($request->all());

        $url = 'https://fcm.googleapis.com/fcm/send';
        $FcmToken = User::select('device_token')->where('role','2')->whereNotNull('device_token')->pluck('device_token')->all();
        $serverKey = 'AAAAisHqdek:APA91bHRB-qtTq2DxSHJXwJUEXdry-l8n9eBCOgvN820RmWzJelUm9h34JWWR5E5JbaoRIYnmp_7ZGAuNjeB6rwc3CN876ukt8MOC2ulrPeglC5k-LKtwXpEPcAcsFEtGArvXNcOR6lO';

        $pesan = [
            "registration_ids" => $FcmToken,
            "notification" => [
                "title" => 'DJT Reception System',
                "body" => 'Tamu telah diterima, segera pilih ruangan',
                "sound" => true
            ]
        ];

        $encodedData = json_encode($pesan);

        $headers = [
            'Authorization:key=' . $serverKey,
            'Content-Type: application/json',
        ];

        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $encodedData);
        // Execute post
        $result = curl_exec($ch);
        if ($result === FALSE) {
            die('Curl failed: ' . curl_error($ch));
        }else{
            return response()->json(['success'=>'Data telah berhasil disimpan'.$data]);
        }
        // Close connection
        curl_close($ch);
        // return response()->json(['success'=>'Data telah berhasil disimpan'.$data]);
    }

    public function appRuangan(Request $request,$id){
        $data = Guest::find($id);
        $data->update($request->all());
        $url = 'https://fcm.googleapis.com/fcm/send';
        $FcmToken = User::select('device_token')->where('role','3')->whereNotNull('device_token')->pluck('device_token')->all();
        $serverKey = 'AAAAisHqdek:APA91bHRB-qtTq2DxSHJXwJUEXdry-l8n9eBCOgvN820RmWzJelUm9h34JWWR5E5JbaoRIYnmp_7ZGAuNjeB6rwc3CN876ukt8MOC2ulrPeglC5k-LKtwXpEPcAcsFEtGArvXNcOR6lO';

        $pesan = [
            "registration_ids" => $FcmToken,
            "notification" => [
                "title" => 'DJT Reception System',
                "body" => 'Persilakan tamu untuk masuk',
                "sound" => true
            ]
        ];

        $encodedData = json_encode($pesan);

        $headers = [
            'Authorization:key=' . $serverKey,
            'Content-Type: application/json',
        ];

        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
        // Disabling SSL Certificate support temporarly
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $encodedData);
        // Execute post
        $result = curl_exec($ch);
        if ($result === FALSE) {
            die('Curl failed: ' . curl_error($ch));
        }else{
            return response()->json(['success'=>'Data telah berhasil disimpan'.$data]);
        }
        // Close connection
        curl_close($ch);
        // return response()->json(['success'=>'Data telah berhasil disimpan'.$data]);
    }
}
