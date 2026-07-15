<?php

namespace App\Http\Requests\Builder;

use Illuminate\Foundation\Http\FormRequest;

class CoupleEventRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'quote_text' => ['nullable', 'string'],
            'quote_source' => ['nullable', 'string', 'max:255'],
            'maps_link' => ['nullable', 'url', 'max:500'],
            'streaming_link' => ['nullable', 'url', 'max:500'],

            'akad.date' => ['nullable', 'date'],
            'akad.time' => ['nullable'],
            'akad.place' => ['nullable', 'string', 'max:255'],
            'akad.address' => ['nullable', 'string'],

            'resepsi.date' => ['nullable', 'date'],
            'resepsi.time' => ['nullable'],
            'resepsi.place' => ['nullable', 'string', 'max:255'],
            'resepsi.address' => ['nullable', 'string'],

            'groom.full_name' => ['required', 'string', 'max:255'],
            'groom.nickname' => ['nullable', 'string', 'max:255'],
            'groom.father_name' => ['nullable', 'string', 'max:255'],
            'groom.mother_name' => ['nullable', 'string', 'max:255'],
            'groom.photo' => ['nullable', 'image', 'max:2048'],

            'bride.full_name' => ['required', 'string', 'max:255'],
            'bride.nickname' => ['nullable', 'string', 'max:255'],
            'bride.father_name' => ['nullable', 'string', 'max:255'],
            'bride.mother_name' => ['nullable', 'string', 'max:255'],
            'bride.photo' => ['nullable', 'image', 'max:2048'],
        ];
    }
}