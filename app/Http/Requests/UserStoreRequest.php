<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class UserStoreRequest extends FormRequest {
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\Rule|array|string>
     */
    public function rules(): array {
        return [
            'name' => 'required|max:255',
            'surname' => 'required|max:255',
            'birth_date' => 'required|date',
            'email' => 'required|unique:users',
            'password' => 'required',
            'fiscal_code' => 'nullable|regex:/^[A-Z]{6}\d{2}[A-Z]\d{2}[A-Z]\d{3}[A-Z]$/i'
        ];
    }

    public function messages(): array {
        return [
            'name.required' => 'Il nome è richiesto',
            'surname.required' => 'Il cognome è richiesto',
            'birth_date.required' => 'La data di nascita è richiesta',
            'email.required' => "L'email è richiesto",
            'password.required' => 'La password è richiesta',
            'fiscal_code.regex' => 'Codice fiscale formalmente errato'
        ];
    }

    public function failedValidation(Validator $validator) {

        throw new HttpResponseException(response()->json([
            'success' => false,
            'message' => 'Validation errors',
            'data' => $validator->errors()

        ]));

    }
}
