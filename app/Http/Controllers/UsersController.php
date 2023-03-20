<?php

namespace App\Http\Controllers;

use App\Models\PasswordReset;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\DataTables;
use App\Rules\MatchOldPassword;
use Illuminate\Support\Facades\Hash;

class UsersController extends Controller
{
    //DATA
    public function changedata(){
        $data = Auth::user()->name;
        return view('pages.user.changedata', compact('data'));
    }

    public function storedata(Request $request){

            User::find(auth()->user()->id)->update(['name'=> $request->name]);
            return redirect(route('home'));
    }

    //PASSWORD
    public function changepassword(){
        return view('pages.user.changepassword');
    }

    public function storepassword(Request $request){
        $validate = $request->validate([
            'old' => ['required', new MatchOldPassword],
            'new' => ['required', 'string', 'min:8'],
            'conf' => ['same:new'],
        ]);

        if($validate)
        {
            User::find(auth()->user()->id)->update(['password'=> Hash::make($request->new)]);
            // toast('Password telah berhasil diubah','success');
            Auth::logout();
            return redirect(route('login'));
        }
        else{
            // toast('Gagal ubah password','error');
            return redirect(route('changepassword'));
        }
    }



    //DATA
    public function index(){
        return view('pages.user.index');
    }

    public function showData(Request $request){
        $data = User::where('id','<>',1)->get();
        if($request->ajax()){
            $allData = DataTables::of($data)
            ->addIndexColumn()
            ->addColumn('role',function($row){
                if($row->role ==0)
                {
                    $role = "Developer";
                }else if($row->role ==1)
                {
                    $role = "Manajemen";
                }else if($row->role ==2)
                {
                    $role = "GA";
                }else if($row->role ==3)
                {
                    $role = "Security";
                }else if($row->role ==4)
                {
                    $role = "Front Office";
                }else{
                    $role = "IT";
                }
                return $role;
            })
            ->addColumn('action', function($row){
                $btn = '<a href="javascript:void(0)" data-toggle="tooltip" data-id="' .$row->id. '" data-original-title="Edit" class="edit btn btn-primary btn-sm editData"><i class="fa fa-edit"></i></a>';
                $btn .= '&nbsp;&nbsp;';
                $btn.= '<a href="javascript:void(0)" data-toggle="tooltip" data-id="' .$row->id. '" data-original-title="Delete" class="delete btn btn-danger btn-sm deleteData"><i class="fa fa-trash"></i></a>';
                return $btn;
            })
            ->rawColumns(['role','action'])
            ->make(true);
            return $allData;
        }
    }

    public function edit($id)
    {
        $data = User::find($id);
        return response()->json($data);
    }

    public function store(Request $request){

        if($request->data_id == "")
            {
                User::updateOrCreate(
                    ['id'=>$request->data_id],
                    ['name'=>$request->name,
                    'email'=>$request->email,
                    'role'=>$request->role,
                    'password'=>bcrypt("123guest*#"),
                    ]
                );
            }else{
                User::updateOrCreate(
                    ['id'=>$request->data_id],
                    ['name'=>$request->name,
                    'email'=>$request->email,
                    'role'=>$request->role,
                    ]
                );
            }
            return response()->json(['success'=>'Data telah berhasil disimpan']);
        }


    public function destroy($id){
        User::find($id)->delete();
        return response()->json(['success'=>'Data telah berhasil dihapus']);
    }
}
