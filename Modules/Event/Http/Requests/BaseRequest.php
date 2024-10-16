<?php

namespace Modules\Event\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class BaseRequest extends FormRequest
{
    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(
            response()->json([
                'code'      => 422,
                'message'   => $validator->getMessageBag()->first(),
                'errors'    => $validator->getMessageBag()
            ], JsonResponse::HTTP_UNPROCESSABLE_ENTITY)
        );
    }
}
