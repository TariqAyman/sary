<?php
// Copyright
declare(strict_types=1);

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\MessageBag;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

abstract class ApiController extends Controller
{
    /**
     * Send success data
     *
     * @param $data
     * @return JsonResponse
     */
    protected function success($data = []): JsonResponse
    {
        $data = [
            'data' => $data,
        ];

        return $this->send(Response::HTTP_OK, $data);
    }

    /**
     * Send bad request data
     *
     * @param array $data
     * @return JsonResponse
     */
    protected function badRequest($data): JsonResponse
    {
        if ($data instanceof MessageBag) {
            $errors = [];


            foreach ($data->messages() as $input => $messagesList) {
                $errors[] = [
                    'key' => $input,
                    'message' => $messagesList[0]
                ];
            }

            $data = [
                'errors' => $errors
            ];

        } elseif (is_string($data)) {
            $data = [
                'error' => $data,
            ];
        } elseif (is_array($data)) {
            $data = [
                'errors' => $data
            ];
        }

        return $this->send(Response::HTTP_BAD_REQUEST, $data);
    }

    /**
     * Send not found request data
     *
     * @param array $data
     */
    protected function notFound($data): JsonResponse
    {
        if ($data instanceof MessageBag) {
            $errors = [];

            foreach ($data->messages() as $input => $messagesList) {
                $errors[$input] = $messagesList[0];
            }

            $data = [
                'errors' => $errors
            ];
        } elseif (is_string($data)) {
            $data = [
                'error' => $data,
            ];
        }

        return $this->send(Response::HTTP_NOT_FOUND, $data);
    }

    /**
     * Unauthorized data
     *
     * @param string $message
     * @return JsonResponse
     */
    protected function unauthorized(string $message): JsonResponse
    {
        $message = ['error' => $message];

        return $this->send(Response::HTTP_UNPROCESSABLE_ENTITY, $message);
    }

    /**
     * Send Response
     *
     * @param int $statusCode
     * @param array $message
     * @return JsonResponse
     */
    protected function send(int $statusCode, array $message): JsonResponse
    {
        return response()->json($message, $statusCode);
    }

    /**
     * Sends a Successful response with a given code
     *
     * @param $data
     * @param $code
     * @return JsonResponse
     */
    protected function successResponse($data, $code = 200): JsonResponse
    {
        return response()->json($data, $code);
    }

    /**
     * Sends an error code with a given code
     *
     * @param $message
     * @param $code
     * @return JsonResponse
     */
    protected function errorResponse($message, $code = 400): JsonResponse
    {
        return response()->json(['message' => $message], $code);
    }

    /**
     * Sends an errors code with a given code
     *
     * @param $message
     * @param $code
     * @return JsonResponse
     */
    protected function errorsResponse($message, $code = 400): JsonResponse
    {
        return response()->json(['message' => $message], $code);
    }

    /**
     * Sends a json with a collection of data with a 200 http code as default
     *
     * @param \Illuminate\Support\Collection $collection
     * @param int $code
     * @return JsonResponse
     */
    protected function showAll(Collection $collection, $code = 200): JsonResponse
    {
        return $this->successResponse(['data' => $collection], $code);
    }

    /**
     * sends a json response with only one result
     *
     * @param \Illuminate\Database\Eloquent\Model $instance
     * @param int $code
     * @return JsonResponse
     */
    protected function showOne(Model $instance, $code = 200): JsonResponse
    {
        return $this->successResponse(['data' => $instance], $code);
    }

    /**
     * @param Request $request
     * @param array $validation_data
     * @param array|null $message
     */
    protected function validator(Request $request, array $validation_data, array $message = null)
    {
        $validator = isset($message) ? Validator::make($request->all(), $validation_data, $message) : Validator::make($request->all(), $validation_data);

        if ($validator->fails()) {

            if ($validator->errors() instanceof MessageBag) {
                $errors = [];

                foreach ($validator->errors()->toArray() as $input => $messagesList) {
                    $errors[] = [
                        'key' => $input,
                        'message' => $messagesList[0]
                    ];
                }

                $data = ['errors' => $errors];

            }


            response()->json($data, Response::HTTP_BAD_REQUEST)
                ->header('Content-Type', 'application/json')
                ->send();
            die();

        }
        return null;
    }
}
