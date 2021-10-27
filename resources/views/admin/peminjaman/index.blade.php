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
        {{ trans('global.list') }}
    </div>

    <div class="card-body">
        <div class="table-responsive">
        <!-- FORM UNTUK FILTER BERDASARKAN DATE RANGE -->
            <form action="{{ route('admin.peminjaman.rangeReport') }}" method="get">    
                <div class="input-group mb-3 col-md-3 float-right">
                        <button class="btn btn-secondary" type="submit">Filter</button>
                </div>
                <div class="input-group mb-3 col-md-3 float-right">
                    <!-- <input type="text" id="created_at" name="date" class="form-control"> -->
                    <input type="text" id="created_at" name="date" value="" class="form-control" />
                </div>
                <div class="input-group mb-3 col-md-3 float-right">
                        <div class="input-group-prepend">
                            <label class="input-group-text" for="admin_select">Admin</label>
                        </div>
                        <!-- <select class="custom-select" id="admin_select" name="admin_select"> -->
                            <!-- <option selected>Choose...</option>
                            @foreach($admins as $key => $admin)
                                <option value="{{ $admin->id }}">{{ $admin->name }}</option>
                            @endforeach
                        </select> -->
                </div>
                
            </form>
            <table class=" table table-bordered table-striped table-hover datatable datatable-peminjaman">
                <thead>
                    <tr>
                        <th width="10">

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
                    @foreach($peminjamans as $key => $peminjaman)
                        <tr data-entry-id="{{ $peminjaman->id }}">
                            <td>

                            </td>
                            <td>
                                {{ $peminjaman->nama ?? '' }}
                            </td>
                            <td>
                                {{ $peminjaman->email ?? '' }}
                            </td>
                            <td>
                                {{ $peminjaman->barang_pinjam ?? '' }}
                            </td>
                            <td>
                                {{ $peminjaman->tanggal_pinjam ?? '' }}
                            </td>
                            <td>
                                {{ $peminjaman->tanggal_kembali ?? '-' }}
                            </td>
                            <td>
                                {{ $peminjaman->status == 1 ? 'Sudah dikembalikan' : 'Belum dikembalikan'}}
                            </td>
                            <td>
                                {{ $admin[$key] ?? '' }}
                            </td>
                            <td>

                                @can('peminjaman_edit')
                                    <a class="btn btn-xs btn-info" href="{{ route('admin.peminjaman.show', $peminjaman->id) }}">
                                        {{ trans('global.show') }}
                                    </a>
                                @endcan

                                @can('peminjaman_edit')
                                    <a class="btn btn-xs btn-info" href="{{ route('admin.peminjaman.edit', $peminjaman->id) }}" style="display: {{ $peminjaman->status == 1 ? 'none' : '' }}">
                                        {{ trans('global.edit') }}
                                    </a>
                                @endcan

                                @can('peminjaman_pengembalian')
                                    <a class="btn btn-xs btn-warning" href="{{ route('admin.peminjaman.pengembalian', $peminjaman->id) }}" style="display: {{ $peminjaman->tanggal_kembali != NULL ? 'none' : '' }}">
                                        Pengembalian
                                    </a>
                                @endcan

                                @can('peminjaman_delete')
                                    <form action="{{ route('admin.peminjaman.destroy', $peminjaman->id) }}" method="POST" onsubmit="return confirm('{{ trans('global.areYouSure') }}');" style="display: inline-block;">
                                        <input type="hidden" name="_method" value="DELETE">
                                        <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                        <input type="submit" class="btn btn-xs btn-danger" value="{{ trans('global.delete') }}">
                                    </form>
                                @endcan
                            </td>

                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>


    </div>
</div>
@endsection
@section('scripts')
@parent
<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
<script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>

<link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />

<script>
    $(function () {
  let dtButtons = $.extend(true, [], $.fn.dataTable.defaults.buttons)
@can('peminjaman_delete')
  let deleteButtonTrans = '{{ trans('global.datatables.delete') }}'
  let deleteButton = {
    text: deleteButtonTrans,
    url: "{{ route('admin.peminjaman.massDestroy') }}",
    className: 'btn-danger',
    action: function (e, dt, node, config) {
      var ids = $.map(dt.rows({ selected: true }).nodes(), function (entry) {
          return $(entry).data('entry-id')
      });

      if (ids.length === 0) {
        alert('{{ trans('global.datatables.zero_selected') }}')

        return
      }

      if (confirm('{{ trans('global.areYouSure') }}')) {
        $.ajax({
          headers: {'x-csrf-token': _token},
          method: 'POST',
          url: config.url,
          data: { ids: ids, _method: 'DELETE' }})
          .done(function () { location.reload() })
      }
    }
  }
  dtButtons.push(deleteButton)
@endcan

  $.extend(true, $.fn.dataTable.defaults, {
    order: [[ 1, 'desc' ]],
    pageLength: 100,
  });
  $('.datatable-peminjaman:not(.ajaxTable)').DataTable({ buttons: dtButtons })
    $('a[data-toggle="tab"]').on('shown.bs.tab', function(e){
        $($.fn.dataTable.tables(true)).DataTable()
            .columns.adjust();
    });
})
$(document).ready(function() {
    
    let start = moment().startOf('month')
    let end = moment().endOf('month')

    //INISIASI DATERANGEPICKER
    $('#created_at').daterangepicker({
        startDate: start,
        endDate: end
    })
    
    

    
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
})

</script>
@endsection