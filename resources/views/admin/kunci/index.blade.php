@extends('layouts.admin')
@section('content')
<div class="card">
    <div class="card-header">
        Daftar Kunci Data Peminjaman
    </div>

    <div class="card-body">
        <div class="table-responsive">
        <!-- FORM UNTUK FILTER BERDASARKAN DATE RANGE -->
            <div class="row input-daterange">
                <div class="input-group col-md-4">
                    <input type="text" name="from_date" id="from_date" class="form-control" placeholder="From Date" readonly />
                </div>
                <div class="input-group col-md-4">
                    <input type="text" name="to_date" id="to_date" class="form-control" placeholder="To Date" readonly />
                </div>
                <div class="input-group col-md-4">
                    <div class="col-md-2">
                        <button type="button" name="filter" id="filter" class="btn btn-primary">Filter</button>
                    </div>
                    <div class="col-md-2">
                        <button type="button" name="refresh" id="refresh" class="btn btn-default">Refresh</button>
                    </div>
                </div>
            </div>
            <table class=" table table-bordered table-striped table-hover datatable datatable-kunci">
                <thead>
                    <tr>
                        <th>
                            ID
                        </th>
                        <th>
                            Kunci
                        </th>
                        <th>
                            Peminjam
                        </th>
                        <th>
                            Tanggal Dibuat
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

<script>
    $(function () {
        $('.input-daterange').datepicker({
            todayBtn:'linked',
            format:'yyyy-mm-dd',
            autoclose:true
        });

        load_data();

        function load_data(from_date = '', to_date = ''){
            alert(from_date);
                    //datatable ajax
            var table = $('.datatable-kunci').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: '{{ route('admin.kunci.index') }}',
                    data: {from_date:from_date, to_date:to_date},
                },
                columns: [
                    {data: 'id', name: 'id'},
                    {data: 'kunci', name: 'kunci'},
                    {data: 'nama', name: 'nama'},
                    {data: 'created_at', name: 'created_at'},
                ]
            });
        }

        $('#filter').click(function(){
            var from_date = $('#from_date').val();
            var to_date = $('#to_date').val();
            if(from_date != '' &&  to_date != '')
            {
                $('.datatable-kunci').DataTable().destroy();
                load_data(from_date, to_date);
            }
            else
            {
                alert('Both Date is required');
            }
        });

        $('#refresh').click(function(){
            $('#from_date').val('');
            $('#to_date').val('');
            $('.datatable-kunci').DataTable().destroy();
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
  }
  dtButtons.push(deleteButton)
@endcan

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

</script>
@endsection