<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SendRegisterInvitationRequest extends FormRequest
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
            'name' => 'required|unique:mysql.users,name|unique:mysql.invitations,name',
            'email' => 'required|email:rfc,dns|unique:mysql.users,email|unique:mysql.invitations,email',
        ];
    }
}
