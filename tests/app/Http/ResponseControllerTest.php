<?php

use App\User;
use Laravel\Lumen\Testing\DatabaseTransactions;

class ResponseControllerTest extends TestCase
{
    use DatabaseTransactions;

    private $user;

    public function setUp()
    {
        parent::setUp();
        $this->user = User::loadFromUuid('4BFE1010-C11D-4739-8C24-99E1468F08F6');
    }

    public function testProcessFormResponse()
    {
        $this->actingAs($this->user)->post(sprintf('api/public/forms/%s/response', 'c1a440fe-0843-4da2-8839-e7ec6faee2c9'),
            [
                'name' => 'King Hog',
                'email' => 'kinghog@hogs.com',
                'product' => 'tabbs',
            ]
        );

        $result = json_decode($this->response->getContent());
        $this->assertResponseStatus(201);
        $this->assertTrue($result->success);
    }
}
