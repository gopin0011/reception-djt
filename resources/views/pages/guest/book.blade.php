@extends('adminlte::page')

@section('title', 'DJT Reception System')

@section('content_header')
<div class="container">
    <div class="row">
        <div class="col float-left">
            <h5><i class="fa fa-user-plus"></i> <strong>Booking Tamu</strong></h5>
        </div>
        <div class="col">
            <a type="button" href="javascript:void(0)" id="createNewData" class="btn btn-primary float-right">+ Tambah</a>
        </div>
    </div>
</div>
<div class="modal fade" id="ajaxModal" arial-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="modalHeading"></h4>
            </div>
            <div class="modal-body">
                <form id="dataForm" name="dataForm" class="form-horizontal">
                    <input type="hidden" name="data_id" id="data_id">
                    <div class="form-group">
                        Tamu: <br>
                        <input type="text" class="form-control" id="tamu" name="tamu"
                        placeholder="Masukkan nama tamu" value="" required>
                    </div>
                    <div class="form-group">
                        Asal: <br>
                        <input type="text" class="form-control" id="asal" name="asal"
                        placeholder="Masukkan asal tamu" value="" required>
                    </div>
                    <div class="form-group">
                        Bertemu: <br>
                        <input type="text" class="form-control" id="bertemu" name="bertemu"
                        placeholder="Bertemu dengan" value="" required>
                    </div>
                    <div class="form-group">
                        Tujuan: <br>
                        <input type="text" class="form-control" id="tujuan" name="tujuan"
                        placeholder="Tujuan bertemu" value="" required>
                    </div>
                    <div class="form-group">
                        Tanggal Datang: <br>
                        <input type="date" class="form-control" id="tanggal" name="tanggal"
                        placeholder="Tanggal" value="" required>
                    </div>
                    <button type="submit" class="btn btn-primary" id="btnSave" value="create">Simpan</button>
                </form>
            </div>
        </div>
    </div>
</div>
@stop

@section('content')
<table class="table table-striped data-table display nowrap" width="100%">
    <thead>
        <tr>
            <th width="50px">#</th>
            <th>Tamu</th>
            <th>Institusi</th>
            <th>Bertemu</th>
            <th>Tujuan</th>
            <th>Tanggal</th>
            <th width="60px"></th>
        </tr>
    </thead>
    <tbody></tbody>
</table>
@stop

@section('css')
<meta name="csrf-token" content="{{ csrf_token() }}">
{{-- <link href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.0.1/css/bootstrap.min.css" rel="stylesheet"> --}}
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/v/bs5/dt-1.12.1/datatables.min.css"/>
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.12.1/css/jquery.dataTables.min.css"/>
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/rowreorder/1.2.8/css/rowReorder.dataTables.min.css"/>
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/responsive/2.3.0/css/responsive.dataTables.min.css"/>
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/buttons/2.2.3/css/buttons.dataTables.min.css"/>
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
<script src="https://cdn.datatables.net/buttons/2.2.3/js/dataTables.buttons.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
<script src="https://cdn.datatables.net/buttons/2.2.3/js/buttons.html5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.2.3/js/buttons.print.min.js"></script>

<script type="text/javascript">
    $(function(){
        $.ajaxSetup({
            headers:{'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')}
        });
        var table = $(".data-table").DataTable({
            // lengthMenu:[[10,25,50,-1],['10', '25', '50', 'Show All']],
            // dom: 'Blfrtip',
            // buttons: [
            //     'excel'
            // ],
            // rowReorder: {
            //     selector: 'td:nth-child(2)'
            // },
            responsive: true,
            serverSide:true,
            processing:true,
            ajax: '{!! route('data.booking') !!}',
            columnDefs: [
                {
                    searchable: false,
                    orderable: false,
                    targets: 0,
                },
            ],
            order: [[1, 'asc']],
                columns:[
                    {data:'DT_RowIndex',name:'DT_RowIndex'},
                    {data:'tamu',name:'tamu'},
                    {data:'asal',name:'asal'},
                    {data:'bertemu',name:'bertemu'},
                    {data:'tujuan',name:'tujuan'},
                    {data:'tanggal',name:'tanggal'},
                    {data:'action',name:'action'},
                ]
        });

        $("#createNewData").click(function(){
            $("#data_id").val('');
            $("#dataForm").trigger("reset");
            $("#modalHeading").html("Tambah Data");
            $("#ajaxModal").modal('show');
        });

        $("#btnSave").click(function(e){
            e.preventDefault();
            $(this).html('Save');

            $.ajax({
                type:"POST",
                url:"{{route('post.booking')}}",
                data:$("#dataForm").serialize(),
                dataType:'json',
                success:function(data){
                    $("#dataForm").trigger("reset");
                    $("#ajaxModal").modal('hide');
                    table.draw();
                },
                error:function(data){
                    console.log('Error',data);
                    $("#btnSave").html('Simpan');
                }
            });
        });

        $('body').on('click','.deleteData', function(){
            var data_id = $(this).data("id");
            if(confirm("Apakah Anda yakin?"))
            {
                $.ajax({
                    type:"DELETE",
                    url:"{{route('guest.booking')}}"+"/"+data_id,
                    success:function(data){
                        table.draw();
                    },
                    error:function(data){
                        console.log('Error',data);
                    }
                });
            }else{
                return false;
            }
        });

        $('body').on('click','.editData', function(){
            var data_id = $(this).data("id");
            $.get("{{route('guest.booking')}}"+"/"+data_id+"/edit",function(data){
                $("#modalHeading").html("Ubah Data");
                $("#ajaxModal").modal('show');
                $("#data_id").val(data.id);
                $("#tamu").val(data.tamu);
                $("#asal").val(data.asal);
                $("#bertemu").val(data.bertemu);
                $("#tanggal").val(data.kode.substring(7));
                $("#tujuan").val(data.tujuan);
            });
        });
    });
    </script>

@stop
