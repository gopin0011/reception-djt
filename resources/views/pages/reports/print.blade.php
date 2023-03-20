<!DOCTYPE html>
<html>
<head>
	<title>Laporan Kehadiran Tamu</title>
</head>
<body>
	<style type="text/css">
		table tr td,
		table tr th{
			font-size: 9pt;
		}

        table {
            border-left: 0.01em solid #ccc;
            border-right: 0;
            border-top: 0.01em solid #ccc;
            border-bottom: 0;
            border-collapse: collapse;
        }
        table td,
        table th {
            border-left: 0;
            border-right: 0.01em solid #ccc;
            border-top: 0;
            border-bottom: 0.01em solid #ccc;
        }

        th, td {
            padding: 5px;
        }

        @page { margin: 75px; }
        body { margin: 0px; }

        /* p.big {
            line-height: 1.2;
        } */
	</style>
	<center>
		<h4>Laporan Kehadiran Tamu PT. Dwida Jaya Tama - {{$periode}}</h4>
	</center>
    <br>
	<table class='table table-bordered'>
		<thead>
			<tr>
				<th width="20px">#</th>
				<th>Foto</th>
				<th>Tamu</th>
				<th>Institusi</th>
				<th>Suhu</th>
				<th>Bertemu</th>
				<th>Tujuan</th>
				<th>Tanggal</th>
				<th>Datang</th>
				<th>Pulang</th>
				<th>Pintu Akses</th>
				<th>Ruangan</th>
				<th>Sekuriti</th>
				<th>Status</th>
			</tr>
		</thead>
		<tbody>
			@php $i=1 @endphp
			@foreach($data as $p)
			<tr>
				<td>{{$i++}}.</td>
				<td>
				    @if(file_exists("storage/tamu/".$p->kode.".jpg")) 
  <img width="100px" alt=""  height="100px" src="{{ public_path("storage/tamu/".$p->kode.".jpg") }}">
@else
  <img width="100px" alt=""  height="100px" src="{{ public_path("icon.png") }}">
@endif
				    
				    </td>
				<td>{{$p->tamu}}</td>
				<td>{{$p->asal}}</td>
				<td>{{$p->suhu}}</td>
				<td>{{$p->bertemu}}</td>
				<td>{{$p->tujuan}}</td>
				<td>{{date('d-M-y', strtotime($p->tanggal))}}</td>
				<td>{{$p->datang}}</td>
				<td>{{$p->pulang}}</td>
				<td>{{$p->gerbang}}</td>
				<td>{{$p->ruangan}}</td>
				<td>{{$p->sekuriti}}</td>
				<td>
                    @if ($p->acc == '-')
                        Menunggu konfirmasi
                    @elseif ($p->acc == '1')
                        Diterima
                    @else
                        Ditolak
                    @endif
                </td>
			</tr>
			@endforeach
		</tbody>
	</table>
</body>
</html>
