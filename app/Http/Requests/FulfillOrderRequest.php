<?php

namespace App\Http\Requests;

use App\Enums\FulfillmentType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;

class FulfillOrderRequest extends FormRequest
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
     */
    public function rules(): array
    {
        return [
            'fulfillment_type' => ['required', new Enum(FulfillmentType::class)],
            'keys' => ['required_if:fulfillment_type,key', 'nullable', 'string'],
            'account_user' => ['required_if:fulfillment_type,account', 'nullable', 'string'],
            'account_pass' => ['required_if:fulfillment_type,account', 'nullable', 'string'],
            'account_link' => ['nullable', 'url'],
            'transaction_id' => ['required_if:fulfillment_type,topup', 'nullable', 'string'],
            'admin_note' => ['nullable', 'string'],
            'notify_email' => ['sometimes', 'boolean'],
            'notify_whatsapp' => ['sometimes', 'boolean'],
        ];
    }
}
