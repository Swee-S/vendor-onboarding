<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateVendorRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $vendorId = $this->route('vendor')->id;

        return [
            'business_name'       => ['required', 'string', 'max:255'],
            'business_type'       => ['required', Rule::in([
                                        'Individual', 'Proprietorship',
                                        'Partnership', 'Pvt Ltd', 'LLP'
                                      ])],
            'contact_person_name' => ['required', 'string', 'max:255'],
            'contact_email'       => ['required', 'email'],
            'contact_mobile'      => [
                'required',
                'regex:/^[6-9][0-9]{9}$/',
                Rule::unique('vendors', 'contact_mobile')
                    ->whereNotIn('status', ['rejected'])
                    ->ignore($vendorId), // ignore self
            ],
            'pan_number'          => [
                'required',
                'regex:/^[A-Z]{5}[0-9]{4}[A-Z]{1}$/',
                Rule::unique('vendors', 'pan_number')
                    ->whereNotIn('status', ['rejected'])
                    ->ignore($vendorId),
            ],
            'gst_number'          => [
                'nullable',
                'regex:/^[0-9]{2}[A-Z]{5}[0-9]{4}[A-Z]{1}[1-9A-Z]{1}Z[0-9A-Z]{1}$/',
                Rule::unique('vendors', 'gst_number')
                    ->whereNotIn('status', ['rejected'])
                    ->whereNotNull('gst_number')
                    ->ignore($vendorId),
            ],
            'address'             => ['required', 'string'],
            'city'                => ['required', 'string', 'max:100'],
            'state'               => ['required', 'string', 'max:100'],
            'pincode'             => ['required', 'regex:/^[1-9][0-9]{5}$/'],
            'account_holder_name' => ['required', 'string', 'max:255'],
            'account_number'      => ['required', 'string', 'min:9', 'max:18'],
            'ifsc_code'           => ['required', 'regex:/^[A-Z]{4}0[A-Z0-9]{6}$/'],
        ];
    }

    public function messages(): array
    {
        return [
            'contact_mobile.regex'  => 'Enter a valid 10-digit Indian mobile number (starting with 6-9).',
            'pan_number.regex'      => 'PAN must be in format: ABCDE1234F',
            'pan_number.unique'     => 'An active application with this PAN already exists.',
            'gst_number.regex'      => 'GST must be in format: 27ABCDE1234F1Z5',
            'gst_number.unique'     => 'An active application with this GST number already exists.',
            'contact_mobile.unique' => 'An active application with this mobile number already exists.',
            'pincode.regex'         => 'Enter a valid 6-digit Indian pincode.',
            'ifsc_code.regex'       => 'IFSC must be in format: SBIN0001234',
        ];
    }
}