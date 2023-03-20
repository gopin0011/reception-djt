<?php

namespace App\Http\Controllers;

use App\Models\Gate;
use App\Models\Guest;
use App\Models\Room;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Str;
use PDF;

class GuestController extends Controller
{
    public function today()
    {
        $user = Auth::user()->role;
        $rooms = Room::all();
        $gates = Gate::all();
        return view('pages.guest.today', compact('rooms', 'gates', 'user'));
    }

    public function booking()
    {
        return view('pages.guest.book');
    }

    public function dataBooking(Request $request)
    {
        $data = Guest::where("kode", 'like', "BOOKED-" . "%")->get();
        if ($request->ajax()) {
            $allData = DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('action', function ($row) {
                    $btn = '<a href="javascript:void(0)" data-toggle="tooltip" data-id="' . $row->id . '" data-original-title="Edit" class="edit btn btn-primary btn-sm editData"><i class="fa fa-edit"></i></a>';
                    $btn .= '&nbsp;&nbsp;';
                    $btn .= '<a href="javascript:void(0)" data-toggle="tooltip" data-id="' . $row->id . '" data-original-title="Delete" class="delete btn btn-danger btn-sm deleteData"><i class="fa fa-trash"></i></a>';
                    return $btn;
                })
                ->addColumn('tanggal', function ($row) {
                    $data = Str::substr($row->kode, 7);
                    return $data;
                })
                ->rawColumns(['action','tanggal'])
                ->make(true);
            return $allData;
        }
    }

    public function editBooking($id)
    {
        $data = Guest::find($id);
        return response()->json($data);
    }

    public function postBooking(Request $request)
    {
        $time = now()->format('Ymdhis');
        Guest::updateOrCreate(
            ['id' => $request->data_id],
            [
                'kode' => 'BOOKED-' . $request->tanggal,
                'tamu' => $request->tamu,
                'asal' => $request->asal,
                'bertemu' => $request->bertemu,
                'tujuan' => $request->tujuan,
                'datang' => '-',
                'pulang' => '-',
                'gerbang' => '1',
                'ruangan' => '1',
                'sekuriti' => '1',
                'suhu' => '-',
                'acc' => '0',
            ]
        );
        // return response()->json(['success'=>'Data telah berhasil disimpan']);

        $url = 'https://fcm.googleapis.com/fcm/send';
        $FcmToken = User::select('device_token')->where('role', '1')->orWhere('role', '2')->whereNotNull('device_token')->pluck('device_token')->all();
        $serverKey = 'AAAAisHqdek:APA91bHRB-qtTq2DxSHJXwJUEXdry-l8n9eBCOgvN820RmWzJelUm9h34JWWR5E5JbaoRIYnmp_7ZGAuNjeB6rwc3CN876ukt8MOC2ulrPeglC5k-LKtwXpEPcAcsFEtGArvXNcOR6lO';

        $pesan = [
            "registration_ids" => $FcmToken,
            "notification" => [
                "title" => 'DJT Reception System',
                "body" => 'Ada booking tamu baru.',
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
        } else {
            return response()->json(['success' => 'Data telah berhasil disimpan']);
        }
        curl_close($ch);
    }

    public function destroyBooking($id)
    {
        Guest::find($id)->delete();
        return response()->json(['success' => 'Data telah berhasil dihapus']);
    }

    public function all()
    {
        return view('pages.guest.all');
    }

    public function todayData(Request $request)
    {
        $date = now()->format('Y-m-d');
        $data = Guest::where('kode', 'BOOKED-' . Carbon::now()->format('Y-m-d'))->orWhere('kode','like', Carbon::now()->format('ymd').'%')->get();
        if ($request->ajax()) {
            $allData = DataTables::of($data)
                ->addIndexColumn()
                // ->addColumn('tamu', function($row){
                //     $data = $row->kode;
                //     $x = asset('storage/tamu/'.$data.'.jpg');
                //     $tamu = '<a href="'.$x.'" target="_blank"><div style="max-height:140px; overflow:hidden">'. $row->tamu .'</div></a>';
                //     return $tamu ;
                // })
                ->addColumn('gerbang', function ($row) {
                    $gerbang = $row->gerbang;
                    $data = Gate::find($gerbang);
                    return $data->name;
                })
                ->addColumn('ruangan', function ($row) {
                    $ruangan = $row->ruangan;
                    $x = Room::find($ruangan);
                    $data = $x->name;

                    return $data;
                })
                ->addColumn('acc', function ($row) {
                    $acc = $row->acc;
                    if ($acc == '-') {
                        if (Str::contains($row->kode, "BOOKED")) {
                            $data = '<a href="javascript:void(0)" class="btn btn-secondary btn-sm"></a>';
                        } else {
                            $data = '<a href="javascript:void(0)" class="btn btn-warning btn-sm"></a>';
                            if (Auth::user()->role <> 3) {
                                $data .= '&nbsp;&nbsp;';
                                $data .= '<a href="javascript:void(0)" data-toggle="tooltip" data-id="' . $row->id . '" data-original-title="Approve" class="approve btn btn-primary btn-sm approveData">APP</a>';
                            }
                        }
                    } else if ($acc == '1') {
                        $data = '<a href="javascript:void(0)" class="btn btn-success btn-sm"></a>';
                    } else if ($acc == '0') {
                        $data = '<a href="javascript:void(0)" class="btn btn-primary btn-sm"></a>';
                    } else {
                        $data = '<a href="javascript:void(0)" class="btn btn-danger btn-sm"></a>';
                    }
                    return $data;
                })
                ->addColumn('foto', function ($row) {
                    if (Str::contains($row->kode, "BOOKED")) {
                        $tamu = '';
                    } else {
                        $data = $row->kode;
                        $x = asset('storage/tamu/' . $data . '.jpg');
                        $tamu = '<a href="' . $x . '" target="_blank"><i class="fa fa-image"></i></a>';
                    }
                    return $tamu;
                })
                ->addColumn('edit', function ($row) {
                    if (Auth::user()->role <> 3) {
                        $data = '<a href="javascript:void(0)" data-toggle="tooltip" data-id="' . $row->id . '" data-original-title="Edit" class="edit btn btn-primary btn-sm editData">Edit</a>';
                    } else {
                        $data = '';
                    }
                    return $data;
                })
                ->rawColumns(['gerbang', 'ruangan', 'acc', 'foto', 'edit'])
                ->make(true);
            return $allData;
        }
    }

    public function allData(Request $request)
    {
        $data = Guest::orderBy('created_at')->get();
        if ($request->ajax()) {
            $allData = DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('gerbang', function ($row) {
                    $gerbang = $row->gerbang;
                    $data = Gate::find($gerbang);
                    return $data->name;
                })
                ->addColumn('ruangan', function ($row) {
                    $ruangan = $row->ruangan;
                    $data = Room::find($ruangan);
                    return $data->name;
                })
                ->addColumn('acc', function ($row) {
                    $acc = $row->acc;
                    if ($acc == '-') {
                        if (Str::contains($row->kode, "BOOKED")) {
                            $data = '<a href="javascript:void(0)" class="btn btn-secondary btn-sm"></a>';
                        } else {
                            $data = '<a href="javascript:void(0)" class="btn btn-warning btn-sm"></a>';
                        }
                    } else if ($acc == '1') {
                        $data = '<a href="javascript:void(0)" class="btn btn-success btn-sm"></a>';
                    } else if ($acc == '0') {
                        $data = '<a href="javascript:void(0)" class="btn btn-primary btn-sm"></a>';
                    } else {
                        $data = '<a href="javascript:void(0)" class="btn btn-danger btn-sm"></a>';
                    }
                    return $data;
                })
                ->addColumn('foto', function ($row) {
                    if (Str::contains($row->kode, "BOOKED")) {
                        $tamu = '';
                    } else {
                        $data = $row->kode;
                        $x = asset('storage/tamu/' . $data . '.jpg');
                        $tamu = '<a href="' . $x . '" target="_blank"><i class="fa fa-image"></i></a>';
                    }
                    return $tamu;
                })
                  ->addColumn('tanggal', function ($row) {
                    if (Str::contains($row->kode, "BOOKED")) {
                        $tanggal = Str::substr($row->kode, 7);
                    } else {
                        $data = $row->kode;
                        $tanggal = Carbon::parse(Str::substr($row->kode, 0,6))->format('Y-m-d');
                    }
                    return $tanggal;
                })
                ->rawColumns(['gerbang', 'ruangan', 'acc', 'foto','tanggal'])
                ->make(true);
            return $allData;
        }
    }

    public function approveData($id)
    {
        Guest::updateOrCreate(
            ['id' => $id],
            [
                'acc' => '0'
            ]
        );
        // return response()->json(['success'=>'Data telah berhasil disimpan']);

        $url = 'https://fcm.googleapis.com/fcm/send';
        $FcmToken = User::select('device_token')->where('role', '1')->orWhere('role', '2')->whereNotNull('device_token')->pluck('device_token')->all();
        $serverKey = 'AAAAisHqdek:APA91bHRB-qtTq2DxSHJXwJUEXdry-l8n9eBCOgvN820RmWzJelUm9h34JWWR5E5JbaoRIYnmp_7ZGAuNjeB6rwc3CN876ukt8MOC2ulrPeglC5k-LKtwXpEPcAcsFEtGArvXNcOR6lO';

        $data = [
            "registration_ids" => $FcmToken,
            "notification" => [
                "title" => 'DJT Reception System',
                "body" => 'Ada tamu baru yang menunggu persetujuan',
                "sound" => true
            ]
        ];

        $encodedData = json_encode($data);

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
        }
        // Close connection
        curl_close($ch);
        // return response()->json(['success'=>'Data telah berhasil ditambah']);
    }

    public function reportsearch()
    {
        return view('pages.reports.pickmonth');
    }

    public function reportprint(Request $request)
    {
        // dd($request->date );
        // $date = Carbon::parse($tanggal)->format('Y-F');
        $data = DB::table('guests')
            ->join('rooms', 'rooms.id', '=', 'guests.ruangan')
            ->join('gates', 'gates.id', '=', 'guests.gerbang')
            ->join('users', 'users.id', '=', 'guests.sekuriti')
            ->where('guests.updated_at', 'like', $request->date . "-%")
            // ->select('guests.kode as kode')->get();
            ->select('guests.kode as kode', 'guests.tamu as tamu', 'guests.asal as asal', 'guests.bertemu as bertemu', 'guests.tujuan as tujuan', 'guests.datang as datang', 'guests.pulang as pulang', 'guests.suhu as suhu', 'gates.name as gerbang', 'rooms.name as ruangan', 'users.name as sekuriti', 'guests.acc as acc', 'guests.updated_at as tanggal')->orderBy('kode', 'desc')->get();
        // dd($data);
        $periode = Carbon::parse($request->date . '-01')->format('F Y');
        $pdf = PDF::loadview('pages.reports.print', compact('data', 'periode'))->setPaper('a4', 'landscape');
        return $pdf->stream('laporan-bulanan');
    }

    public function editData($id)
    {
        $data = Guest::find($id);
        return response()->json($data);
    }

    public function selectRoom(Request $request)
    {

        Guest::updateOrCreate(
            ['id' => $request->data_id],
            [
                'ruangan' => $request->ruangan,
                'tamu' => $request->tamu,
                'asal' => $request->asal,
                'bertemu' => $request->bertemu,
                'gerbang' => $request->gerbang,
                'tujuan' => $request->tujuan
            ]
        );
        return response()->json(['success' => 'Data telah berhasil disimpan']);
    }
}
