<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ReservationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string',
            'name_kana' => 'required|string',
            'birth_date' => 'required|date',
            'gender' => 'required',
            'phone' => 'required|regex:/^\d+$/',
            'email' => 'nullable|email|confirmed',
            'reservation_slot_id' => 'required',
            'symptoms_start' => 'nullable|string',
            'symptoms_type' => 'nullable|array',
            'symptoms_other' => 'nullable|string',
            'past_disease_flag' => 'nullable|string',
            'past_disease_detail' => 'nullable|string',
            'allergy_flag' => 'nullable|string',
            'allergy_detail' => 'nullable|string',
            'notes' => 'nullable|string',
        ];
    }
}
