@extends('adminlte::page')

@section('title', 'DJT Reception System')

@section('content_header')
<div class="container">
    <div class="row">
        <div class="col float-left">
            <h5><small><i class="fa fa-user"></i></small> <strong>Ganti Data</strong></h5>
        </div>
    </div>
</div>
@stop

@section('content')
{{-- form method="POST" action="{{ route('recmats.updates', ['id' => $data->id]) }}"> --}}
<form method="POST" action="{{ route('data.store') }}">
    @csrf
    <div class="card">
        <div class="card-body">
            <div class="input-group mb-3 mt-3">
                <div class="col-12">
                    <input type="text" value="{{ $data }}" class="form-control" placeholder="Masukkan nama Anda" name="name" id="name" required oninvalid="this.setCustomValidity('Nama harus diisi')" oninput="setCustomValidity('')">
                </div>
            </div>

            <div class="col-12 mt-3">
                <button type="submit" class="btn btn-primary btn-block">Ubah Data</button>
            </div>
        </div>
    </div>
</form>
{{-- @include('sweetalert::alert') --}}
@stop

@section('css')
<meta name="csrf-token" content="{{ csrf_token() }}">
{{-- <link href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.0.1/css/bootstrap.min.css" rel="stylesheet"> --}}
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/v/bs5/dt-1.12.1/datatables.min.css"/>
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.12.1/css/jquery.dataTables.min.css"/>
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/rowreorder/1.2.8/css/rowReorder.dataTables.min.css"/>
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/responsive/2.3.0/css/responsive.dataTables.min.css"/>
@stop

@section('js')
<script src="https://code.jquery.com/jquery-3.5.1.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.0/jquery.validate.js"></script>
<script src="https://cdn.datatables.net/1.11.4/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
<script src="https://cdn.datatables.net/1.11.4/js/dataTables.bootstrap5.min.js"></script>
<script src="https://cdn.datatables.net/v/bs5/dt-1.12.1/datatables.min.js"></script>
<script src="https://code.jquery.com/jquery-3.5.1.js"></script>
<script src="https://cdn.datatables.net/1.12.1/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/rowreorder/1.2.8/js/dataTables.rowReorder.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.3.0/js/dataTables.responsive.min.js"></script>
@stop
