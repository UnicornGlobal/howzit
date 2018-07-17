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

        if ($token->used || $form->id !== $token->form->id) {
            Log::warning(sprintf('Invalid token for response: User: %s', Auth::user()->id));
            // Generic error response
            return response()->json(['error' => 'Server error'], 500);
        }

        // Create the response record
        $response = new Response();
        $response->form()->associate($form);
        $response->_id = Uuid::generate(4)->string;
        $response->created_by = Auth::user()->id;
        $response->updated_by = Auth::user()->id;
        $response->save();

        $token->response()->associate($response);
        // Invalidate the token
        $token->used = true;
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
                'created_by' => Auth::user()->id,
                'updated_by' => Auth::user()->id,
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

        $cleanedSender = preg_replace(
            "/(\t|\n|\v|\f|\r| |\xC2\x85|\xc2\xa0|\xe1\xa0\x8e|\xe2\x80[\x80-\x8D]|\xe2\x80\xa8|\xe2\x80\xa9|"
                    ."\xe2\x80\xaF|\xe2\x81\x9f|\xe2\x81\xa0|\xe3\x80\x80|\xef\xbb\xbf)+/",
            "_",
            $response->form->name
        );

        Mail::raw($fields, function ($message) use ($response, $cleanedSender) {
            $message->from(sprintf('%s@howzit.com', $cleanedSender));
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

        if ($form->user->id !== Auth::user()->id) {
            return response()->json(['error' => 'Invalid Form ID'], 500);
        }

        $responses = $form->responses()->with('responseElements', 'responseElements.field:id,name')->get();

        return response()->json(['responses' => $responses], 200);
    }
}
