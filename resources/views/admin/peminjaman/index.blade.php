@extends('layouts.admin')
@section('content')
@can('peminjaman_create')
    <div style="margin-bottom: 10px;" class="row">
        <div class="col-lg-12">
            <a class="btn btn-success" href="{{ route("admin.peminjaman.create") }}">
                {{ trans('global.add') }}
            </a>
        </div>
    </div>
@endcan
<div class="card">
    <div class="card-header">
        List Peminjaman Inventory
    </div>

    <div class="card-body">
        <div class="table-responsive">
        <!-- FORM UNTUK FILTER BERDASARKAN DATE RANGE -->
            <div class="container">
                <div class="row">
                    <div class="row input-daterange">
                        <div class="col">
                                <div class="input-group">
                                    <input type="text" name="from_date" id="from_date" class="form-control" placeholder="From Date" readonly />    
                                </div>
                        </div>
                        <div class="col">
                            <div class="input-group">
                                <input type="text" name="to_date" id="to_date" class="form-control" placeholder="To Date" readonly />
                            </div>
                        </div>
                    </div>
                    <div class="col">
                        <div class="input-group col">
                            <select id="filter_status" name="filter_status" class="form-control">
                                <option value="" >All</option>
                                <option value="0">Belum dikembalikan</option>
                                <option value="1">Sudah dikembalikan</option>
                            </select>
                        </div>
                    </div>
                    <div class=col>
                        <div class="input-group">
                            <div class="col-md-2">
                                <button type="button" name="filter" id="filter" class="btn btn-primary">Filter</button>
                            </div>
                            <div class="col-md-2">
                                <button type="button" name="refresh" id="refresh" class="btn btn-default">Refresh</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <br>
            <!-- <form action="{{ route('admin.peminjaman.rangeReport') }}" method="get">
                <div class="input-group mb-3 col-md-3 float-right">
                    <button class="btn btn-secondary" type="submit">Filter</button>
                    <button class="btn btn-primary" type="submit">Refresh</button>
                </div>    
                <div class="input-group mb-3 col-md-3 float-right"> -->
                    <!-- <input type="text" id="created_at" name="date" class="form-control"> -->
                    <!-- <input type="text" id="created_at" name="date" value="" class="form-control" />
                </div> -->
                
                <!-- <div class="input-group mb-3 col-md-3 float-right">
                        <div class="input-group-prepend">
                            <label class="input-group-text" for="admin_select">Admin</label>
                        </div> -->
                        <!-- <select class="custom-select" id="admin_select" name="admin_select"> -->
                            <!-- <option selected>Choose...</option>
                            @foreach($admins as $key => $admin)
                                <option value="{{ $admin->id }}">{{ $admin->name }}</option>
                            @endforeach
                        </select> -->
                <!-- </div> -->
                
            <!-- </form> -->
            <table class=" table table-bordered table-striped table-hover datatable datatable-peminjaman">
                <thead>
                    <tr>
                        <th>
                            ID
                        </th>
                        <th>
                            Nama Peminjam
                        </th>
                        <th>
                            Email Peminjam
                        </th>
                        <th>
                            Barang yang Dipinjam
                        </th>
                        <th>
                            Tanggal Peminjaman
                        </th>
                        <th>
                            Tanggal Kembali
                        </th>
                        <th>
                            Status Pengembalian
                        </th>
                        <th>
                            Admin
                        </th>
                        
                        <th>
                            Aksi
                        </th>
                    </tr>
                </thead>
                <tbody>
                </tbody>
            </table>
        </div>


    </div>
</div>
@endsection
@section('scripts')
@parent
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.8.0/css/bootstrap-datepicker.css" />
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.8.0/js/bootstrap-datepicker.js"></script>
<!-- <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script> -->
<script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>

<!-- <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" /> -->

<script>
    $(function () {
        $('.input-daterange').datepicker({
            todayBtn:'linked',
            format:'yyyy-mm-dd',
            autoclose:true
        });

        load_data();

        function load_data(from_date = '', to_date = '', filter_status = ''){
                    //datatable ajax
            var table = $('.datatable-peminjaman').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: '{{ route('admin.peminjaman.index') }}',
                    data: {from_date:from_date, to_date:to_date, filter_status:filter_status},
                },
                columns: [
                    {data: 'id', name: 'id'},
                    {data: 'nama', name: 'nama'},
                    {data: 'email', name: 'email'},
                    {data: 'barang_pinjam', name: 'barang_pinjam'},
                    {data: 'tanggal_pinjam', name: 'tanggal_pinjam'},
                    {data: 'DT_RowData.tanggal_kembali', name: 'tanggal_kembali'},
                    {data: 'DT_RowData.status', name: 'status'},
                    {data: 'name', name: 'name'},
                    {data: 'action', name: 'action', orderable: false, searchable: false},
                ]
            });
        }

        $('#filter').click(function(){
            var filter_status = $('#filter_status').val();
            var from_date = $('#from_date').val();
            var to_date = $('#to_date').val();
            if(from_date != '' &&  to_date != '')
            {
                alert(filter_status);
                $('.datatable-peminjaman').DataTable().destroy();
                load_data(from_date, to_date, filter_status);
            }
            else
            {
                alert(filter_status);
                // alert('Both Date is required');
                $('.datatable-peminjaman').DataTable().destroy();
                load_data(from_date, to_date, filter_status);
            }
        });

        $('#refresh').click(function(){
            $('#from_date').val('');
            $('#to_date').val('');
            $('#filter_status').val('');
            $('.datatable-peminjaman').DataTable().destroy();
            load_data();
        });

//   let dtButtons = $.extend(true, [], $.fn.dataTable.defaults.buttons)
// @can('peminjaman_delete')
//   let deleteButtonTrans = '{{ trans('global.datatables.delete') }}'
//   let deleteButton = {
//     text: deleteButtonTrans,
//     url: "{{ route('admin.peminjaman.massDestroy') }}",
//     className: 'btn-danger',
//     action: function (e, dt, node, config) {
//       var ids = $.map(dt.rows({ selected: true }).nodes(), function (entry) {
//           return $(entry).data('entry-id')
//       });

//       if (ids.length === 0) {
//         alert('{{ trans('global.datatables.zero_selected') }}')

//         return
//       }

//       if (confirm('{{ trans('global.areYouSure') }}')) {
//         $.ajax({
//           headers: {'x-csrf-token': _token},
//           method: 'POST',
//           url: config.url,
//           data: { ids: ids, _method: 'DELETE' }})
//           .done(function () { location.reload() })
//       }
//     }
//   }
//   dtButtons.push(deleteButton)
// @endcan

//   $.extend(true, $.fn.dataTable.defaults, {
//     order: [[ 1, 'desc' ]],
//     pageLength: 100,
//   });
//   $('.datatable-peminjaman:not(.ajaxTable)').DataTable({ buttons: dtButtons })
//     $('a[data-toggle="tab"]').on('shown.bs.tab', function(e){
//         $($.fn.dataTable.tables(true)).DataTable()
//             .columns.adjust();
//     });
})
// $(document).ready(function() {
    
    
    
    

    
    // $('input[name="date"]').daterangepicker({
    //   autoUpdateInput: true,
    //   locale: {
    //       cancelLabel: 'Clear'
    //   }
    // });

    // $('input[name="date"]').on('apply.daterangepicker', function(ev, picker) {
    //     $(this).val(picker.startDate.format('MM/DD/YYYY') + ' - ' + picker.endDate.format('MM/DD/YYYY'));
    // });

    // $('input[name="date"]').on('cancel.daterangepicker', function(ev, picker) {
    //     $(this).val('');
    // });
// })

</script>
@endsection