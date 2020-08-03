<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class BookRequest extends FormRequest
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
            'bookname' => 'required|max:60'
        ];
    }
    public function messages()
    {
        return [
            'bookname' => 'bookname can not null and can not over 20 characters.',
            // 'bookname.max' => 'bookname can not null and can not over 20 characters'
        ];
    }
    protected function failedValidation(Validator $validator)
    {
        $message = $validator->errors()->getMessages();
        throw new HttpResponseException(response()->json(['message' => 'bad request', 'reason' => $message['bookname']['0']], 400));
    }
}
