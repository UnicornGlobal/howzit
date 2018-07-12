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
        $this->actingAs($this->user)->post(
            sprintf('api/public/forms/%s/response', 'c1a440fe-0843-4da2-8839-e7ec6faee2c9'),
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

    public function testProcessInvalidForm()
    {
        $this->actingAs($this->user)->post(
            sprintf('api/public/forms/%s/response', 'c1a440fe-0843-4da2-8839-e7ec6faee2c9'),
            [
                'name' => 2,
                'email' => 'kinghog@hogs.com',
            ]
        );

        $result = json_decode($this->response->getContent());
        $this->assertResponseStatus(422);
        $this->assertEquals('The product field is required.', $result->error->product[0]);
        $this->assertEquals('The name must be a string.', $result->error->name[0]);
        $this->assertEquals('The name must be between 2 and 120 characters.', $result->error->name[1]);
    }

    public function testGetResponses()
    {
        $this->actingAs($this->user)->get(sprintf('api/forms/%s/responses', 'c1a440fe-0843-4da2-8839-e7ec6faee2c9'));
        $result = json_decode($this->response->getContent());
        $this->assertResponseStatus(200);
        $this->assertEquals('128010d0-a816-4322-bc23-2e431232cd5b', $result->responses[0]->_id);
        $this->assertNotEmpty($result->responses[0]->created_at);
        $this->assertNotEmpty($result->responses[0]->updated_at);

        // Response formatted as array of responses for the form, containing the answer, with the relevant field name attached
        $this->assertCount(3, $result->responses[0]->response_elements);
        $this->assertEquals('tabbs', $result->responses[0]->response_elements[0]->answer);
        $this->assertEquals('product', $result->responses[0]->response_elements[0]->field->name);
        $this->assertNotEmpty($result->responses[0]->response_elements[0]->created_at);
        $this->assertNotEmpty($result->responses[0]->response_elements[0]->updated_at);

        $this->assertEquals('testuser@yahoo.com', $result->responses[0]->response_elements[1]->answer);
        $this->assertEquals('email', $result->responses[0]->response_elements[1]->field->name);
        $this->assertNotEmpty($result->responses[0]->response_elements[1]->created_at);
        $this->assertNotEmpty($result->responses[0]->response_elements[1]->updated_at);

        $this->assertEquals('Tom Smith', $result->responses[0]->response_elements[2]->answer);
        $this->assertEquals('name', $result->responses[0]->response_elements[2]->field->name);
        $this->assertNotEmpty($result->responses[0]->response_elements[2]->created_at);
        $this->assertNotEmpty($result->responses[0]->response_elements[2]->updated_at);
    }

    public function testGetInvalidFormResponses()
    {
        $this->actingAs($this->user)->get(sprintf('api/forms/%s/responses', 'abc123'));
        $result = json_decode($this->response->getContent());

        $this->assertResponseStatus(500);
        $this->assertEquals('Invalid Form ID', $result->error);
    }
}