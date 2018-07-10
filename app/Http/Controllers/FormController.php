<?php

namespace App\Http\Controllers;

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
                'fields' => 'required|array',
                'fields.*' => 'required|array',
                'fields.*.name' => 'required|string',
                'fields.*.max_length' => 'required|integer|min:1',
            ]);
        } catch (ValidationException $e) {
            return response()->json(['error' => $e->errors()], 422);
        }

        $form = new Form();
        $form->_id = Uuid::generate(4)->string;
        $form->name = $request->get('name');
        $form->response_template = $request->get('response_template');
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
            $newField->max_length = $field['max_length'];
            $newField->created_by = Auth::user();
            $newField->updated_by = Auth::user();
            $newField->form()->associate($form);
            $newField->save();
        }
    }

    public function getForm($formId)
    {
        $form = Form::loadFromUuid($formId);

        if ($form->user->id !== Auth::user()->id) {
            return response()->json(['error' => 'Invalid Form ID'], 500);
        }

        return response()->json($form, 200);
    }

    public function getAllForms()
    {
        $forms = Auth::user()->forms()->get();

        return response()->json(['forms' => $forms], 200);
    }
}