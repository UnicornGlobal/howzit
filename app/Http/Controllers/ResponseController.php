<?php

namespace App\Http\Controllers;

use App\Form;
use App\Response;
use App\ResponseElement;
use App\Token;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\ValidationException;
use Webpatser\Uuid\Uuid;

class ResponseController extends Controller
{
    /**
     * Process a response to a form
     *
     * @param Request $request
     * @param $formId
     * @return \Illuminate\Http\JsonResponse
     * @throws \Exception
     */
    public function processFormResponse(Request $request, $formId)
    {
        $form = Form::loadFromUuid($formId);

        // Validate the response
        $validationArray = $this->getValidationArray($form->fields);
        try {
            $this->validate($request, $validationArray);
        } catch (ValidationException $e) {
            return response()->json(['error' => $e->errors()], 422);
        }

        $token = Token::where('_id', $request->get('token'))->with('form')->first();

        if (empty($token)) {
            Log::warning('Invalid token for response');
            return response()->json(['error' => 'Server error'], 500);
        }

        if ($token->used) {
            Log::warning('Reused token for response');
            // Generic error response
            return response()->json(['error' => 'Server error'], 500);
        }

        if ($form->id !== $token->form->id) {
            Log::warning('User attempting to cross-user token for response');
            // Generic error response
            return response()->json(['error' => 'Server error'], 500);
        }

        if ($token->user_ip !== $request->ip()) {
            Log::warning(
                sprintf('User attempting to use token from new IP: Old IP: %s, new IP: %s', $token->user_ip, $request->ip())
            );
            return response()->json(['error' => 'Server error'], 500);
        }

        if ($token->user_agent !== $request->userAgent()) {
            Log::warning('User attempting to access token from different agent');
            return response()->json(['error' => 'Server error'], 500);
        }

        // Create the response record
        $response = new Response();
        $response->form()->associate($form);
        $response->_id = Uuid::generate(4)->string;
        $response->created_by = 1;
        $response->updated_by = 1;
        $response->save();

        $token->response()->associate($response);
        // Invalidate the token
        $token->used = true;
        $token->used_at = Carbon::now();
        $token->save();

        // Add each of the response elements
        $elements = [];
        // Mass insert doesn't support timestamps
        $now = Carbon::now();
        foreach ($form->fields as $field) {
            $element = [
                'response_id' => $response->id,
                'field_id' => $field->id,
                'answer' => $request->get($field->name),
                'created_by' => 1,
                'updated_by' => 1,
                'created_at' => $now,
                'updated_at' => $now,
            ];
            $elements[] = $element;
        }
        ResponseElement::insert($elements);

        $this->emailResponseToOwner($response);

        return response()->json(['success' => true], 201);
    }

    private function emailResponseToOwner($response)
    {
        $fields = '';

        foreach ($response->responseElements as $element) {
            $key = $element->field->label;
            $value = $element->answer;
            $fields = sprintf("%s%s: %s\n", $fields, $key, $value);
        };

        Mail::raw($fields, function ($message) use ($response) {
            $message->from($response->form->email_alias);
            $message->to($response->form->owner_email);
        });
    }

    /**
     * Maps the form's fields to Illuminate's validation rules
     *
     * @param $fields
     * @return array
     */
    private function getValidationArray($fields)
    {
        $validationArray = [];
        $validationArray['token'] = ['required', 'string'];
        foreach ($fields as $field) {
            $key = $field->name;
            $rules = [];

            if ($field->required) {
                $rules[] = 'required';
            };
            switch ($field->type) {
                case 'integer':
                    $rules[] = 'integer';
                    break;
                case 'float':
                    $rules[] = 'numeric';
                    break;
                case 'email':
                    $rules[] = 'email';
                    break;
                default:
                    $rules[] = 'string';
            }

            if (!empty($field->regex)) {
                $rules[] = sprintf('regex:%s', $field->regex);
            }
            $rules[] = sprintf('between:%d,%d', $field->min_length, $field->max_length);
            $validationArray[$key] = $rules;
        }
        return $validationArray;
    }

    public function getResponsesForForm($formId)
    {
        $form = Form::loadFromUuid($formId);

        $responses = $form->responses()->with('responseElements', 'responseElements.field:id,name')->get();

        return response()->json(['responses' => $responses], 200);
    }
}
