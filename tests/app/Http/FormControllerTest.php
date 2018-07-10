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
        $mock = __DIR__ . '/../../../resources/views/mail/confirmaccount.blade.php';
        $template = file_get_contents($mock);
        $this->actingAs($this->user)->post('api/forms', [
            'name' => 'Formed Form',
            'response_template' => $template,
            'credentials_id' => '2d6adc3f-6b0c-4b3f-a303-6b694f4776f0',
            'fields' => [
                [
                    'name' => 'email',
                    'max_length' => 56,
                ],
                [
                    'name' => 'product',
                    'max_length' => 32,
                ],
                [
                    'name' => 'message',
                    'max_length' => 512,
                ]
            ]
        ]);
        $result = json_decode($this->response->getContent());
        $this->assertResponseStatus(201);

        $this->assertObjectHasAttribute('form_id', $result);
    }

    public function testGetAllForms()
    {
        $this->actingAs($this->user)->get('api/forms');
        $result = json_decode($this->response->getContent());
        $this->assertResponseStatus(200);

        $this->assertObjectHasAttribute('forms', $result);
        $this->assertEquals('A well formed Form', $result->forms[0]->name);
        $this->assertNotEmpty($result->forms[0]->response_template);
        $this->assertNotEmpty($result->forms[0]->created_at);
        $this->assertNotEmpty($result->forms[0]->updated_at);

        foreach ($result->forms[0]->fields as $field) {
            $this->assertNotEmpty($field->_id);
            $this->assertNotEmpty($field->name);
            $this->assertObjectHasAttribute('regex', $field);
            $this->assertNotEmpty($field->max_length);
            $this->assertNotEmpty($field->created_at);
            $this->assertNotEmpty($field->updated_at);
        }
    }
}