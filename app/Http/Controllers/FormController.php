<?php

namespace App\Http\Controllers;

use App\Form;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Laravel\Lumen\Routing\Controller;

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
        $form->name = $request->get('name');
        $form->response_template = $request->get('response_template');
        $form->save();
        $this->createFieldsForForm($form, $request->get('fields'));

    }

    private function createFieldsForForm($form, $fields)
    {
        foreach ($fields as $field) {
            $newField = new Field();
            $newField->name = $field->name;
            $newField->max_length = $field->max_length;
            $newField->form()->associate($form);
            $newField->save();
        }
    }
}