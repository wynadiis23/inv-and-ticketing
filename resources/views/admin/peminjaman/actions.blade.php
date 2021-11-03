@can('peminjaman_edit')
    <a class="btn btn-xs btn-info" href="{{ route('admin.peminjaman.edit', $row->id) }}">
        {{ trans('global.edit') }}
    </a>
    <a class="btn btn-xs btn-info" href="{{ route('admin.peminjaman.show', $row->id) }}">
        {{ trans('global.show') }}
    </a>
@endcan

@can('peminjaman_pengembalian')
    <a class="btn btn-xs btn-warning" href="{{ route('admin.peminjaman.pengembalian', $row->id) }}" style="display: {{ $row->tanggal_kembali != NULL ? 'none' : '' }}">
        Pengembalian
    </a>
@endcan

@can('peminjaman_delete')
    <form action="{{ route('admin.peminjaman.destroy', $row->id) }}" method="POST" onsubmit="return confirm('{{ trans('global.areYouSure') }}');" style="display: inline-block;">
        <input type="hidden" name="_method" value="DELETE">
        <input type="hidden" name="_token" value="{{ csrf_token() }}">
        <input type="submit" class="btn btn-xs btn-danger" value="{{ trans('global.delete') }}">
    </form>
@endcan