<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class UpdateUserRequest extends FormRequest
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
            'number'  => 'sometimes|string|max:255',
            'address' => 'sometimes|string|max:255',
            'email'   => 'sometimes|string|max:255|unique:users,email,' . Auth::id(),
            'name'    => 'sometimes|string|max:255',
            'avatar'  => 'sometimes|image|max:5096',
        ];
    }

    public function messages(): array
    {
        return [
            'email.unique' => 'This email is already in use by another account.',
            'avatar.image' => 'Avatar must be a valid image file.',
            'avatar.max'   => 'Avatar size cannot exceed 5MB.',
        ];
    }

}
