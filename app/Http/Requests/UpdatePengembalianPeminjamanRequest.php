<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Gate;
use Symfony\Component\HttpFoundation\Response;

class UpdatePengembalianPeminjamanRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        abort_if(Gate::denies('peminjaman_pengembalian'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            //
            'nama'  => 'required',
            'email' => [
                'required',
            ],
            'barang_pinjam' => [
                'required',
            ],
            'tanggal_pinjam' => [
                'required',
                'date',
            ],
            'image' => 'image|file|max:1024',
            'tanggal_kembali' => [
                'date',
                'after_or_equal:tanggal_pinjam',
            ],
        ];
    }
}
