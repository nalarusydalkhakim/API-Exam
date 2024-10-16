<?php

namespace Modules\LearningManagement\Http\Requests\School;

use Modules\LearningManagement\Http\Requests\BaseRequest;

class CreateFileRequest extends BaseRequest
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
     * @return array<string, mixed>
     */
    public function rules()
    {
        $rules = [
            'file' => 'required|mimetypes:image/jpeg,image/png,video/mp4,video/x-m4v,video/quicktime,video/x-msvideo,video/x-ms-wmv,video/x-flv,video/3gpp,application/pdf|max:100000'
        ];

        return $rules;
    }
}
