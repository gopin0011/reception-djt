<?php

namespace App\Http\Controllers;

use App\Models\Gate;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;

class GateController extends Controller
{
    public function index(){
        return view('pages.gate.index');
    }

    public function showData(Request $request){
        $data = Gate::where('id','<>',1)->get();
        if($request->ajax()){
            $allData = DataTables::of($data)
            ->addIndexColumn()
            ->addColumn('action', function($row){
                $btn = '<a href="javascript:void(0)" data-toggle="tooltip" data-id="' .$row->id. '" data-original-title="Edit" class="edit btn btn-primary btn-sm editData"><i class="fa fa-edit"></i></a>';
                $btn .= '&nbsp;&nbsp;';
                $btn.= '<a href="javascript:void(0)" data-toggle="tooltip" data-id="' .$row->id. '" data-original-title="Delete" class="delete btn btn-danger btn-sm deleteData"><i class="fa fa-trash"></i></a>';
                return $btn;
            })
            ->rawColumns(['action'])
            ->make(true);
            return $allData;
        }
    }

    public function edit($id)
    {
        $data = Gate::find($id);
        return response()->json($data);
    }

    public function store(Request $request){

        Gate::updateOrCreate(
            ['id'=>$request->data_id],
            ['name'=>$request->name,
            'note'=>$request->note
            ]
        );
        return response()->json(['success'=>'Data telah berhasil disimpan']);
    }

    public function destroy($id){
        Gate::find($id)->delete();
        return response()->json(['success'=>'Data telah berhasil dihapus']);
    }
}
