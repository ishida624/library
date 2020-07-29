<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class UpdateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
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
            'Lv' => 'integer|between:1,3',
            'username' => 'regex:/[0-9a-zA-Z]{3,16}/',
            'password' => 'regex:/[0-9a-zA-Z]{8,20}/',
        ];
    }
    public function messages()
    {
        return [
            // 'Lv.required' => 'Lv should between 1 to 3',
            'Lv.integer' => 'Lv should between 1 to 3',
            'Lv.between' => 'Lv should between 1 to 3',
            // 'username.required' => 'username between 3 to 16 charactersand only 0-9,a-z,A-Z',
            'username.regex' => 'username between 3 to 16 charactersand only 0-9,a-z,A-Z',
            // 'password.required' => 'password should between 8 to 20 characters and only 0-9,a-z,A-Z',
            'password.regex' => 'password should between 8 to 20 characters and only 0-9,a-z,A-Z ',
        ];
    }
    protected function failedValidation(Validator $validator)
    {
        $message = $validator->errors()->getMessages();
        throw new HttpResponseException(response()->json(['message' => 'bad request', 'reason' => $message], 400));
    }
}
