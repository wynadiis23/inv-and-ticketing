<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\StorePeminjamanRequest;
use App\Http\Requests\UpdatePeminjamanRequest;
use App\Http\Requests\UpdatePengembalianPeminjamanRequest;
use App\User;
use App\Peminjaman;
use App\Kunci;
use App\Http\Controllers\Admin\KunciController;
use Gate;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;
use Carbon\Carbon;
use DataTables;


class PeminjamanController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        //
        $user = Auth()->user();
        abort_if(Gate::denies('peminjaman_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $admins = User::all();
        // $peminjamans = Peminjaman::all();
        // //get admin name yang input peminjaman
        // foreach($peminjamans as $peminjaman)
        // {
        //     $admin[] = User::findOrFail($peminjaman->user_id)->name;
        // }
        // // dd($admin[0]);
        // return view('admin.peminjaman.index', compact('peminjamans', 'admin', 'admins'));
        
        if ($request->ajax()) {
            // dd($request->all());
            if(!empty($request->from_date) && $request->filter_status !== null) { //awal date 0 dan status ''
                // dd('mamang');
                $data = Peminjaman::select('*')
                    ->whereBetween('tanggal_pinjam', array($request->from_date, $request->to_date))
                    ->where('status', '=', $request->filter_status)
                    ->get();
            } else if(empty($request->from_date) && $request->filter_status !== null) { //date 0 status 0 atau 1
                // dd($request->filter_status);
                $data = Peminjaman::select('*')
                    ->where('status', '=', $request->filter_status)
                    ->get();
            }else {
                $data = Peminjaman::select('*');
            }
            // $data[0]->nama="mamamama";
            // dd($data);
            return Datatables::of($data)
                    ->addIndexColumn()
                    ->addColumn('admin', function(Peminjaman $peminjaman) {
                        $admin = User::findOrFail($peminjaman->user_id)->name;
                        return $admin;
                    })
                    ->addColumn('action', function($row){
                        //    $btn = '<a href="peminjaman/'.$row->id.'/edit" class="edit btn btn-primary btn-sm">Edit</a>';
                        //    $btn = $btn. '<a href="peminjaman/'.$row->id.'/edit" class="edit btn btn-secondary btn-sm">Edit</a>';
                        //     return $btn;
                        return view('admin.peminjaman.actions', compact('row'));
                    })
                    ->setRowData([
                        'tanggal_kembali' => '{{ $tanggal_kembali == null ? "-" : $tanggal_kembali}}',
                        'status' => '{{ $status == 0 ? "belum dikembalikan" : "sudah dikembalikan" }}',
                    ])
                    ->rawColumns(['action'])
                    ->make(true);

        } else {
            // dd('mamang');   
        }
        return view('admin.peminjaman.index', compact('admins'));
        // return "mamang";
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
        
        // return $tampung;
        return view ('admin.peminjaman.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StorePeminjamanRequest $request)
    {
        //
        $idUser = Auth::user()->id;

        // return $request->file('image')->store('peminjaman-inventaris-gambar');
        
        $peminjaman = new Peminjaman;
        
        $peminjaman->nama = $request->nama;
        $peminjaman->email = $request->email;
        $peminjaman->tanggal_pinjam = $request->tanggal_pinjam;
        $peminjaman->tanggal_kembali = $request->tanggal_kembali;
        
        $barang_pinjam = $request->barang_pinjam;
        $hasil = implode(';',$barang_pinjam);
        $peminjaman->barang_pinjam = $hasil;
        $peminjaman->user_id = $idUser;

        if($request->file('image')) 
        {
            $peminjaman->photo_path = $request->file('image')->store('peminjaman-inventaris-gambar');
        }

        if($peminjaman->save()) {
            $kunci = new KunciController;
            $kunci->bikinKunci(Peminjaman::latest()->first()->id);
            return redirect()->route('admin.peminjaman.index')->with(['success' => 'Tambah Data Peminjaman '.$peminjaman->email.' berhasil!']);
        } else {
            return redirect()->route('admin.peminjaman.index')->with(['error' => 'Tambah Data Peminjaman Error']);
        }

        
        // return "mamang";
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
        //
        abort_if(Gate::denies('peminjaman_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $peminjaman = Peminjaman::findorFail($id);
        return view('admin.peminjaman.show', compact('peminjaman'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
        abort_if(Gate::denies('peminjaman_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $peminjaman = Peminjaman::findorFail($id);
        // dd($peminjaman);
        return view('admin.peminjaman.edit', compact('peminjaman'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(UpdatePeminjamanRequest $request, $id)
    {
        $idUser = Auth::user()->id;
        $peminjaman = Peminjaman::find($id);
        
        $peminjaman->nama = $request->nama;
        $peminjaman->email = $request->email;
        $peminjaman->barang_pinjam = $request->barang_pinjam;
        $peminjaman->tanggal_pinjam = $request->tanggal_pinjam;
        $peminjaman->tanggal_kembali = $request->tanggal_kembali;
        $peminjaman->user_id = $idUser;
        
        $peminjaman->save();

        // return redirect()->route('admin.peminjaman.index')->withStatus('Your ticket has been submitted, we will be in touch. You can view ticket status');
        return redirect()->route('admin.peminjaman.index')->with(['success' => 'Edit Data Peminjaman '.$peminjaman->email.' berhasil!']);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
        $peminjaman = Peminjaman::findOrFail($id);

        if($peminjaman->delete()) {
            return redirect()->route('admin.peminjaman.index')->with(['success' => 'Hapus Data Peminjaman berhasil!']);
        } else {
            return redirect()->route('admin.peminjaman.index')->with(['error' => 'Hapus Data Peminjaman error!']);
        }
    }

    public function upload(Request $request)
    {
        if($request->hasFile('photo')) {
            dd($request);
        }
    }

    public function rangeReport(Request $request)
    {
        $admins = User::all();
        //INISIASI 30 HARI RANGE SAAT INI JIKA HALAMAN PERTAMA KALI DI-LOAD
        //KITA GUNAKAN STARTOFMONTH UNTUK MENGAMBIL TANGGAL 1
        $start = Carbon::now()->startOfMonth()->format('Y-m-d');
        //DAN ENDOFMONTH UNTUK MENGAMBIL TANGGAL TERAKHIR DIBULAN YANG BERLAKU SAAT INI
        $end = Carbon::now()->endOfMonth()->format('Y-m-d');

        //JIKA USER MELAKUKAN FILTER MANUAL, MAKA PARAMETER DATE AKAN TERISI
        // dd($request->all());
        if (request()->date != '') {
            //MAKA FORMATTING TANGGALNYA BERDASARKAN FILTER USER
            $date = explode(' - ' ,request()->date);
            $start = Carbon::parse($date[0])->format('Y-m-d');
            $end = Carbon::parse($date[1])->format('Y-m-d');
            // dd($end);
        }

        //BUAT QUERY KE DB MENGGUNAKAN WHEREBETWEEN DARI TANGGAL FILTER
        $peminjamans = Peminjaman::whereBetween('tanggal_pinjam', [$start, $end])->get();
        // dd($peminjamans[0]);
        return view('admin.peminjaman.index', compact('peminjamans', 'admins'));
    }

    public function pengembalian($id)
    {
        abort_if(Gate::denies('peminjaman_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $peminjaman = Peminjaman::findOrFail($id);
        // dd($validated);
        return view('admin.peminjaman.pengembalian', compact('peminjaman'));
    }

    public function pengembalianUpdate(UpdatePengembalianPeminjamanRequest $request, $id)
    {
        // dd($id);
        $peminjaman = Peminjaman::findOrFail($id);
        $kunci = Kunci::where('peminjaman_id', $id)->first();
        // dd($kunci->kunci);
        if($request->key == $kunci->kunci)
        {
            $peminjaman->tanggal_kembali = $request->tanggal_kembali;
            $peminjaman->status = 1;
            if($peminjaman->save())
            {
                //update kunci
                $updateKunci = Kunci::where('kunci', $request->key)->first();
                $updateKunci->status = 1;

                if($updateKunci->save())
                {
                    return redirect()->route('admin.peminjaman.index')->with(['success' => 'Pengembalian '.$peminjaman->email.' berhasil!']);
                }
            }
        } else {
            return redirect()->route('admin.peminjaman.index')->with(['error' => 'Pengembalian '.$peminjaman->email.' tidak berhasil. Kunci Salah!']);
        }
        dd($request->all());
    }
}
