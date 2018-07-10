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
        $template = file_get_contents('/home/fergus/UnicornGlobal/howzit/resources/views/mail/confirmaccount.blade.php');
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
}