<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Kunci;
use App\Peminjaman;
use Gate;
use Symfony\Component\HttpFoundation\Response;
use DataTables;
use Illuminate\Support\Facades\DB;

class KunciController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        //
        abort_if(Gate::denies('peminjaman_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        if ($request->ajax()) {
            
            if(!empty($request->from_date)) {
                $data = DB::table('kunci')
                            ->join('peminjaman', 'kunci.peminjaman_id', '=', 'peminjaman.id')
                            ->whereBetween(\DB::raw("DATE_FORMAT(kunci.created_at, '%Y-%m-%d')"), array($request->from_date, $request->to_date))
                            ->select('kunci.*', 'peminjaman.nama')
                            ->get();
                // $data = Kunci::select('*')
                //     ->whereBetween(\DB::raw("DATE_FORMAT(created_at, '%Y-%m-%d')"), array($request->from_date, $request->to_date))
                //     ->get();
            } else {
                // $data = Kunci::select('*');
                $data = DB::table('kunci')
                            ->join('peminjaman', 'kunci.peminjaman_id', '=', 'peminjaman.id')
                            // ->whereBetween(\DB::raw("DATE_FORMAT(created_at, '%Y-%m-%d')"), array($request->from_date, $request->to_date))
                            ->select('kunci.*', 'peminjaman.nama')
                            ->get();
            }
            // $data[0]->nama="mamamama";
            // dd($data);
            return Datatables::of($data)
                    ->addIndexColumn()
                    // ->addColumn('peminjam', function(Kunci $kunci) {
                    //     $peminjam = Peminjaman::findOrFail($kunci->peminjaman_id)->nama;
                    //     return $peminjam;
                    // })
                    ->make(true);

        } else {
            // dd('mamang');   
        }

        return view('admin.kunci.index');
        // foreach ($users as $table2record) {
        //     // echo $table2record->id; //access table2 data
        //     echo $table2record->peminjaman->email; //access table1 data
        // }
        // dd($users);
        
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
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
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
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
    }

    public function bikinKunci($idPeminjaman)
    {
        $kunci = new Kunci;
        $key = md5(uniqid(rand(), true));

        $kunci->kunci = $key;
        $kunci->peminjaman_id = $idPeminjaman;
        $kunci->save();
    }
}
