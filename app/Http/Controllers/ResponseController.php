<?php

namespace App\Http\Controllers;


use App\Form;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class ResponseController extends Controller
{
    public function processFormResponse(Request $request, $formId)
    {
        $form = Form::loadFromUuid($formId);
        $fields = $form->fields->toArray();
        $validationArray = $this->getValidationArray($fields);
        try {
            $this->validate($request, $validationArray);
        } catch (ValidationException $e) {
            return response()->json(['error' => $e->errors()]);
        }

        return true;
    }

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
                case 'text':
                    $rules[] = 'string';
                    break;
                case 'string':
                    $rules[] = 'string';
                    break;
                case 'integer':
                    $rules[] = 'integer';
                    break;
                case 'float':
                    $rules[] = 'float';
                    break;
                case 'email':
                    $rules[] = 'email';
                    break;
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