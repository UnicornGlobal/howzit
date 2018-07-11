<?php

use App\User;
use Laravel\Lumen\Testing\DatabaseTransactions;

class CredentialsControllerTest extends TestCase
{
    use DatabaseTransactions;

    private $user;

    public function setUp()
    {
        parent::setUp();
        $this->user = User::loadFromUuid('4BFE1010-C11D-4739-8C24-99E1468F08F6');
    }

    public function testGetCredentials()
    {
        $this->actingAs($this->user)->get('api/users/credentials', ['Debug-Token' => env('DEBUG_TOKEN')]);
        $result = json_decode($this->response->getContent());
        $this->assertResponseStatus(200);
        $this->assertObjectHasAttribute('credentials', $result);
        $this->assertEquals('2d6adc3f-6b0c-4b3f-a303-6b694f4776f0', $result->credentials[0]->_id);
        $this->assertEquals('seeded creds', $result->credentials[0]->name);
        $this->assertEquals('mailgun_username', $result->credentials[0]->username);
        $this->assertEquals('test@howzit.com', $result->credentials[0]->mail_from_address);
        $this->assertEquals('Howzit Seed', $result->credentials[0]->mail_from_name);
        $this->assertEquals('howzit', $result->credentials[0]->domain);
        $this->assertNotNull($result->credentials[0]->created_at);
        $this->assertNotNull($result->credentials[0]->updated_at);
    }

    public function testGetSingleCredential()
    {
        $this->actingAs($this->user)->get(sprintf('api/users/credentials/%s', '2d6adc3f-6b0c-4b3f-a303-6b694f4776f0'));
        $result = json_decode($this->response->getContent());
        $this->assertResponseStatus(200);
        $this->assertEquals('2d6adc3f-6b0c-4b3f-a303-6b694f4776f0', $result->_id);
        $this->assertEquals('seeded creds', $result->name);
        $this->assertEquals('mailgun_username', $result->username);
        $this->assertEquals('test@howzit.com', $result->mail_from_address);
        $this->assertEquals('Howzit Seed', $result->mail_from_name);
        $this->assertEquals('howzit', $result->domain);
        $this->assertNotNull($result->created_at);
        $this->assertNotNull($result->updated_at);
    }

    public function testGetInvalidCredentials()
    {
        $this->actingAs($this->user)->get(sprintf('api/users/credentials/%s', '123'));
        $result = json_decode($this->response->getContent());
        $this->assertResponseStatus(500);
        $this->assertEquals('Invalid Credentials ID', $result->error);
    }

    public function testGetCredsWithWrongUser()
    {
        $wrongUser = User::loadFromUuid('5FFA95F4-5EB4-46FB-94F1-F2B27254725B');
        $this->actingAs($wrongUser)->get(sprintf('api/users/credentials/%s', '2d6adc3f-6b0c-4b3f-a303-6b694f4776f0'));
        $result = json_decode($this->response->getContent());
        $this->assertResponseStatus(500);
        $this->assertEquals('Invalid Credentials ID', $result->error);
    }

    public function testAddCredentials()
    {
        $this->actingAs($this->user)->post('api/users/credentials', [
            'name' => 'test creds',
            'username' => 'testuser',
            'secret' => 'super secret secret',
            'domain' => 'test',
            'mail_from_address' => 'mailer@test.com',
            'mail_from_name' => 'Test Unicorn',
            'provider_id' => '6a6cfa4d-a8ce-40cb-aee2-ffe4dbb80aec'
        ], ['Debug-Token' => env('DEBUG_TOKEN')]);
        $result = json_decode($this->response->getContent());
        $this->assertResponseStatus(201);
        $this->assertObjectHasAttribute('credentials_id', $result);

        $this->actingAs($this->user)->get(sprintf('api/users/credentials/%s', $result->credentials_id));
        $result = json_decode($this->response->getContent());
        $this->assertResponseStatus(200);
        $this->assertEquals('test creds', $result->name);
        $this->assertEquals('testuser', $result->username);
        $this->assertEquals('mailer@test.com', $result->mail_from_address);
        $this->assertEquals('Test Unicorn', $result->mail_from_name);
        $this->assertEquals('test', $result->domain);
        $this->assertNotNull($result->created_at);
        $this->assertNotNull($result->updated_at);
    }

    public function testAddInvalidCredentials()
    {
        $this->actingAs($this->user)->post('api/users/credentials', [
            'name' => 'test creds',
            'username' => 'testuser',
            'domain' => 'test',
            'mail_from_address' => 'mailer@test.com',
            'mail_from_name' => 'Test Unicorn',
            'provider_id' => '6a6cfa4d-a8ce-40cb-aee2-ffe4dbb80aec'
        ], ['Debug-Token' => env('DEBUG_TOKEN')]);
        $result = json_decode($this->response->getContent());
        $this->assertResponseStatus(422);
        $this->assertEquals('The secret field is required.', $result->error->secret[0]);
    }

    public function testEditCredentials()
    {
        $this->actingAs($this->user)->put(sprintf('api/users/credentials/%s', '2d6adc3f-6b0c-4b3f-a303-6b694f4776f0'), [
            'name' => 'Updated Creds',
            'username' => 'new_username',
            'secret' => 'new secret secret',
            'domain' => 'new domain',
            'mail_from_address' => 'new@howzit.com',
            'mail_from_name' => 'Fresh New Unicorn',
        ], ['Debug-Token' => env('DEBUG_TOKEN')]);
        $result = json_decode($this->response->getContent());
        $this->assertResponseStatus(202);
        $this->assertTrue($result->success);
        $this->actingAs($this->user)->get(sprintf('api/users/credentials/%s', '2d6adc3f-6b0c-4b3f-a303-6b694f4776f0'));
        $result = json_decode($this->response->getContent());
        $this->assertResponseStatus(200);
        $this->assertEquals('2d6adc3f-6b0c-4b3f-a303-6b694f4776f0', $result->_id);
        $this->assertEquals('Updated Creds', $result->name);
        $this->assertEquals('new_username', $result->username);
        $this->assertEquals('new@howzit.com', $result->mail_from_address);
        $this->assertEquals('Fresh New Unicorn', $result->mail_from_name);
        $this->assertEquals('new domain', $result->domain);
        $this->assertNotNull($result->created_at);
        $this->assertNotNull($result->updated_at);
    }

    public function testInvalidCredentialEdit()
    {
        $this->actingAs($this->user)->put(sprintf('api/users/credentials/%s', '2d6adc3f-6b0c-4b3f-a303-6b694f4776f0'), [
            'name' => 'Updated Creds',
            'username' => 'new_username',
            'secret' => 'new secret secret',
            'domain' => 'new domain',
            'mail_from_name' => 'Fresh New Unicorn',
        ], ['Debug-Token' => env('DEBUG_TOKEN')]);
        $result = json_decode($this->response->getContent());
        $this->assertResponseStatus(422);
        $this->assertEquals('The mail from address field is required.', $result->error->mail_from_address[0]);
    }
}
