<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateMessage extends FormRequest
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
            'receiver_id' => 'required|exists:users,id',
            'message' => 'nullable|string',
            'file' => 'nullable|file|max:10240',
        ];
    }

    public function validated($key = null, $default = null)
    {
        $data = parent::validated();
        $data['sender_id'] = auth()->id();
        if ($this->hasFile('file')) {
            $data['file_path'] = $this->file('file')->store('chat_files');
        }
        return $data;
    }
}
