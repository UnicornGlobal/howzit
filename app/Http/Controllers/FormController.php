<?php

namespace App\Http\Controllers;

use App\Credentials;
use App\Field;
use App\Form;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Laravel\Lumen\Routing\Controller;
use Webpatser\Uuid\Uuid;

class FormController extends Controller
{
    public function addForm(Request $request)
    {
        try {
            $this->validate($request, [
                'name' => 'required|string',
                'response_template' => 'required|string',
                'credentials_id' => ['required', 'exists:credentials,_id'],
                'fields' => 'required|array|min:1',
                'fields.*' => 'required|array',
                'fields.*.name' => 'required|string',
                'fields.*.label' => 'required|string',
                'fields.*.type' => 'required|string',
                'fields.*.required' => 'required|boolean',
                'fields.*.min_length' => 'required|integer|min:0',
                'fields.*.max_length' => 'required|integer|min:1',
                'fields.*.regex' => 'nullable|string',
            ]);
        } catch (ValidationException $e) {
            return response()->json(['error' => $e->errors()], 422);
        }

        $credentials = Credentials::loadFromUuid($request->get('credentials_id'));

        if ($credentials->user->id !== Auth::user()->id) {
            return response()->json(['error' => 'Invalid Credentials ID'], 422);
        }

        $form = new Form();
        $form->_id = Uuid::generate(4)->string;
        $form->name = $request->get('name');
        $form->response_template = $request->get('response_template');
        $form->credentials()->associate($credentials);
        $form->created_by = Auth::user();
        $form->updated_by = Auth::user();
        $form->save();
        $this->createFieldsForForm($form, $request->get('fields'));

        return response()->json(['form_id' => $form->_id], 201);
    }

    private function createFieldsForForm($form, $fields)
    {
        foreach ($fields as $field) {
            $newField = new Field();
            $newField->_id = Uuid::generate(4)->string;
            $newField->name = $field['name'];
            $newField->label = $field['label'];
            $newField->type = $field['type'];
            $newField->min_length = $field['min_length'];
            $newField->max_length = $field['max_length'];
            $newField->regex = isset($field['regex']) ? $field['regex'] : null;
            $newField->required = $field['required'];
            $newField->created_by = Auth::user();
            $newField->updated_by = Auth::user();
            $newField->form()->associate($form);
            $newField->save();
        }
    }

    public function getForm($formId)
    {
        $form = Form::where('_id', $formId)->with('credentials', 'fields')->first();

        if (empty($form) || $form->user->id !== Auth::user()->id) {
            return response()->json(['error' => 'Invalid Form ID'], 500);
        }

        return response()->json($form, 200);
    }

    public function getAllForms()
    {
        $forms = Form::where('created_by', Auth::user()->id)->with('fields')->get();

        return response()->json(['forms' => $forms], 200);
    }

    public function getFormConfig($formId)
    {
        $form = Form::where('_id', $formId)->with('fields')->first();

        if (empty($form) || $form->user->id !== Auth::user()->id) {
            return response()->json(['error' => 'Invalid Form ID'], 500);
        }

        $form->makeHidden(['_id', 'response_template', 'created_at', 'updated_at']);

        $form->fields->each(function ($field) {
            $field->makeHidden(['_id', 'created_at', 'updated_at', ]);
        });

        return response()->json($form, 200);

    }
}