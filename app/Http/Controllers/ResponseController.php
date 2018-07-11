<?php

namespace App\Http\Controllers;


use App\Form;
use App\Response;
use App\ResponseElement;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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
            return response()->json(['error' => $e->errors()]);
        }

        // Create the response record
        $response = new Response();
        $response->form()->associate($form);
        $response->_id = Uuid::generate(4)->string;
        $response->created_by = Auth::user()->id;
        $response->updated_by = Auth::user()->id;
        $response->save();

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

        return response()->json(['success' => true], 201);
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
                    $rules[] = 'float';
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
}