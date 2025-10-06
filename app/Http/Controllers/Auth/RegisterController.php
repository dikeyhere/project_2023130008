<?php

namespace App\Http\Requests;

    use Illuminate\Foundation\Http\FormRequest;

    class RegisterRequest extends FormRequest
    {
        public function authorize()
        {
            return true;
        }

        public function rules()
        {
            return [
                'name' => 'required|string|max:255',
                'email' => 'required|string|email|max:255|unique:users',
                'password' => 'required|string|min:8|confirmed',
                'role' => 'required|in:admin,ketua_tim,anggota_tim',  // Validasi role
            ];
        }

        public function messages()  // Opsional: Custom message error
        {
            return [
                'role.required' => 'Role harus dipilih.',
                'role.in' => 'Role tidak valid. Pilih Admin, Ketua Tim, atau Anggota Tim.',
            ];
        }
    }
