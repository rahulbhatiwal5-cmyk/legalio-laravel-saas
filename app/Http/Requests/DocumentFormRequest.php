<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class DocumentFormRequest extends FormRequest
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
            'title' => 'required|string|unique:documents,title',
            's.lug' => 'required|string|unique:documents,slug',
            'short_description' => 'required',
            // 'document_button_text' => 'required',
            'long_description' => 'required',
            'img_heading.*' => 'required',
            'img_description.*' => 'required',
            'img_description_second.*' => 'required',
            // 'doc_price' => 'required',
            'category_id' => 'required',
            // 'legal_heading' => 'required',
            // 'legal_description' => 'required',
            // 'legal_btn_text' => 'required',
        ];
    }

    public function messages()
    {
        return [
            'category_id.required' => 'The category field is required.',
            'img_heading.*.required' => 'The heading field is required.',
            'img_description.*.required' => 'The description here field is required.',
            'field_image.*.required' => 'The image is required',
            'img_description_second.*.required' => 'The description here field is required.',
            // 'doc_price.required' => 'The price field is required.',
            // 'legal_heading.required' => 'The heading field is required.',
            // 'legal_description.required' => 'The description here field is required.',
            // 'legal_btn_text.required' => 'The button label field is required.',
            // 'legal_doc_image.required' => 'The legal document image field is required.',
        ];
    }

}
