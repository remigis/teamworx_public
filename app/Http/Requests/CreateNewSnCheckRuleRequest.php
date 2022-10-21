<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * @property $rule
 * @property $group_id
 */
class CreateNewSnCheckRuleRequest extends FormRequest
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
        $this->rule = strtoupper($this->rule);
        return [
            'group_id' => 'required|exists:mysql.sn_look_over_groups,id',
            'rule' => ['required', 'string', Rule::unique('sn_look_over_rules')->where(function ($query) {
                return $query->where('rule', $this->rule)
                    ->where('sn_look_over_group_id', $this->group_id);
            })],
        ];
    }
}
