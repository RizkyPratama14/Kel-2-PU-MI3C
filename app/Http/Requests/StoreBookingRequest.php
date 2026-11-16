<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Task T9: Form Request Validasi Booking.
 *
 * Semua pesan kesalahan menggunakan Bahasa Indonesia.
 */
class StoreBookingRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'unit_id'           => ['required', 'exists:cameras,id'],
            'mulai_at'          => ['required', 'date', 'after:now'],
            'selesai_at'        => ['required', 'date', 'after:mulai_at'],
            'lokasi_pickup'     => ['required', 'string', 'max:255'],
            'metode_pembayaran' => ['required', 'in:transfer_bank,e_wallet,cod,Transfer Bank,E-Wallet,COD,qris,QRIS'],
        ];
    }

    /**
     * Pesan kesalahan dalam Bahasa Indonesia.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'unit_id.required'           => 'Unit kamera wajib dipilih.',
            'unit_id.exists'             => 'Unit kamera yang dipilih tidak valid.',
            'mulai_at.required'          => 'Waktu mulai sewa wajib diisi.',
            'mulai_at.date'              => 'Format waktu mulai sewa tidak valid.',
            'mulai_at.after'             => 'Waktu mulai sewa harus setelah waktu sekarang.',
            'selesai_at.required'        => 'Waktu selesai sewa wajib diisi.',
            'selesai_at.date'            => 'Format waktu selesai sewa tidak valid.',
            'selesai_at.after'           => 'Waktu selesai sewa harus setelah waktu mulai.',
            'lokasi_pickup.required'     => 'Lokasi pengambilan wajib diisi.',
            'lokasi_pickup.string'       => 'Lokasi pengambilan harus berupa teks.',
            'lokasi_pickup.max'          => 'Lokasi pengambilan maksimal 255 karakter.',
            'metode_pembayaran.required' => 'Metode pembayaran wajib dipilih.',
            'metode_pembayaran.in'       => 'Metode pembayaran yang dipilih tidak valid. Pilih salah satu: Transfer Bank, E-Wallet, atau COD.',
        ];
    }
}
