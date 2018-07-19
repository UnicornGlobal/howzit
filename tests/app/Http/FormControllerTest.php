<?php

use App\User;
use Laravel\Lumen\Testing\DatabaseTransactions;

class FormControllerTest extends TestCase
{
    use DatabaseTransactions;

    private $user;

    public function setUp()
    {
        parent::setUp();
        $this->user = User::loadFromUuid('4BFE1010-C11D-4739-8C24-99E1468F08F6');
    }

    public function testAddForm()
    {
        $this->actingAs($this->user)->post('api/forms', [
            'name' => 'Formed Form',
            'owner_email' => 'test@howzit.com',
            'fields' => [
                [
                    'name' => 'email',
                    'type' => 'email',
                    'label' => 'Email Address',
                    'required' => true,
                    'min_length' => 7,
                    'max_length' => 56,
                    'regex' => '/(^[a-zA-Z0-9_.+-]+@[a-zA-Z0-9-]+\.[a-zA-Z0-9-.]+$)/',
                    'order_index' => 2,
                ],
                [
                    'name' => 'product',
                    'type' => 'text',
                    'label' => 'Product',
                    'required' => true,
                    'min_length' => 3,
                    'regex' => null,
                    'max_length' => 15,
                    'order_index' => 1,
                ],
                [
                    'name' => 'message',
                    'type' => 'text',
                    'label' => 'Your message',
                    'required' => true,
                    'min_length' => 10,
                    'max_length' => 512,
                    'regex' => null,
                    'order_index' => 3,
                ]
            ]
        ]);
        $result = json_decode($this->response->getContent());
        $this->assertResponseStatus(201);

        $this->assertObjectHasAttribute('form_id', $result);
    }

    public function testAddInvalidForm()
    {
        $this->actingAs($this->user)->post('api/forms', [
            'name' => 'Formed Form',
        ]);
        $result = json_decode($this->response->getContent());
        $this->assertResponseStatus(422);

        $this->assertEquals('The owner email field is required.', $result->error->owner_email[0]);
        $this->assertEquals('The fields field is required.', $result->error->fields[0]);
    }

    public function testGetAllForms()
    {
        $this->actingAs($this->user)->get('api/forms');
        $result = json_decode($this->response->getContent());
        $this->assertResponseStatus(200);

        $this->assertObjectHasAttribute('forms', $result);
        $this->assertEquals('A well formed Form', $result->forms[0]->name);
        $this->assertNotEmpty($result->forms[0]->created_at);
        $this->assertNotEmpty($result->forms[0]->updated_at);

        foreach ($result->forms[0]->fields as $field) {
            $this->assertNotEmpty($field->_id);
            $this->assertNotEmpty($field->name);
            $this->assertObjectHasAttribute('regex', $field);
            $this->assertNotEmpty($field->max_length);
            $this->assertNotEmpty($field->min_length);
            $this->assertNotEmpty($field->created_at);
            $this->assertNotEmpty($field->updated_at);
            $this->assertNotEmpty($field->required);
            $this->assertNotEmpty($field->type);
            $this->assertNotEmpty($field->label);
        }
    }

    public function testGetSingleForm()
    {
        $this->actingAs($this->user)->get(sprintf('api/forms/%s', 'c1a440fe-0843-4da2-8839-e7ec6faee2c9'));
        $result = json_decode($this->response->getContent());
        $this->assertResponseStatus(200);

        $this->assertEquals('A well formed Form', $result->name);
        $this->assertNotEmpty($result->created_at);
        $this->assertNotEmpty($result->updated_at);

        foreach ($result->fields as $field) {
            $this->assertNotEmpty($field->_id);
            $this->assertNotEmpty($field->name);
            $this->assertObjectHasAttribute('regex', $field);
            $this->assertNotEmpty($field->max_length);
            $this->assertNotEmpty($field->min_length);
            $this->assertNotEmpty($field->created_at);
            $this->assertNotEmpty($field->updated_at);
            $this->assertNotEmpty($field->required);
            $this->assertNotEmpty($field->type);
            $this->assertNotEmpty($field->label);
        }
    }

    public function testGetInvalidForm()
    {
        $this->actingAs($this->user)->get(sprintf('api/forms/%s', 'abcd'));
        $result = json_decode($this->response->getContent());
        $this->assertResponseStatus(500);
        $this->assertEquals('Invalid Form ID', $result->error);

        $wrongUser = User::loadFromUuid('5FFA95F4-5EB4-46FB-94F1-F2B27254725B');
        $this->actingAs($wrongUser)->get(sprintf('api/forms/%s', 'c1a440fe-0843-4da2-8839-e7ec6faee2c9'));
        $result = json_decode($this->response->getContent());
        $this->assertResponseStatus(500);
        $this->assertEquals('Invalid Form ID', $result->error);
    }

    public function testGetFormConfig()
    {
        $this->actingAs($this->user)->get(sprintf('public/forms/%s', 'c1a440fe-0843-4da2-8839-e7ec6faee2c9'));
        $result = json_decode($this->response->getContent());
        $this->assertResponseOk();
        $this->assertEquals('A well formed Form', $result->form->name);
        $this->assertObjectHasAttribute('token', $result);
        $this->assertNotEmpty($result->token);

        foreach ($result->form->fields as $field) {
            $this->assertCount(8, (array)$field);
            $this->assertNotEmpty($field->name);
            $this->assertObjectHasAttribute('regex', $field);
            $this->assertNotEmpty($field->max_length);
            $this->assertNotEmpty($field->min_length);
            $this->assertNotEmpty($field->required);
            $this->assertNotEmpty($field->type);
            $this->assertNotEmpty($field->label);
            $this->assertNotEmpty($field->order_index);
        }
    }
}
