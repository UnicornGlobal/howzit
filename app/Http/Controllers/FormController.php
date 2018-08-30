<?php

namespace App\Http\Controllers;

use App\Credentials;
use App\Field;
use App\Form;
use App\Token;
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
                'owner_email' => 'required|email',
                'fields' => 'required|array|min:1',
                'fields.*' => 'required|array',
                'fields.*.name' => 'required|string',
                'fields.*.label' => 'required|string',
                'fields.*.type' => 'required|string',
                'fields.*.required' => 'required|boolean',
                'fields.*.min_length' => 'required|integer|min:0',
                'fields.*.max_length' => 'required|integer|min:1',
                'fields.*.regex' => 'nullable|string',
                'fields.*.order_index' => 'required|integer|min:0',
            ]);
        } catch (ValidationException $e) {
            return response()->json(['error' => $e->errors()], 422);
        }

        $form = new Form();
        $form->_id = Uuid::generate(4)->string;
        $form->owner_email = $request->get('owner_email');
        $form->name = $request->get('name');
        $form->created_by = Auth::user()->id;
        $form->updated_by = Auth::user()->id;
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
            $newField->order_index = $field['order_index'];
            $newField->created_by = Auth::user()->id;
            $newField->updated_by = Auth::user()->id;
            $newField->form()->associate($form);
            $newField->save();
        }
    }

    public function getForm($formId)
    {
        $form = Form::where('_id', $formId)->with('fields')->first();

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

    public function getFormConfig(Request $request, $formId)
    {
        $form = Form::loadFromUuid($formId);

        $form->makeHidden(['_id', 'response_template', 'created_at', 'updated_at', 'owner_email']);

        $form->fields->each(function ($field) {
            $field->makeHidden(['_id', 'created_at', 'updated_at']);
        });

        $token = new Token();
        $token->_id = Uuid::generate(4)->string;
        $token->form_id = $form->id;
        $token->user_ip = $request->ip();
        $token->user_agent = $request->userAgent();

        $token->save();

        $config = [
            'form' => $form->toArray(),
            'token' => $token->_id,
        ];

        return response()->json($config, 200);
    }
}
