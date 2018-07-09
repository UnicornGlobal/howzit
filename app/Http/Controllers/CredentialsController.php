<?php

namespace App\Http\Controllers;


use App\Credentials;
use App\Provider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Validation\ValidationException;
use Webpatser\Uuid\Uuid;

class CredentialsController extends Controller
{
    public function addCredentials(Request $request)
    {
        try {
            $this->validate($request, [
                'name' => 'required|string',
                'username' => 'required|string',
                'secret' => 'required|string',
                'domain' => 'required|string',
                'provider_id' => ['required', 'exists:providers'],
                'mail_from_address' => 'required|string',
                'mail_from_name' => 'required|string',
            ]);
        } catch (ValidationException $e) {
            return response()->json(['error' => $e->errors()], 422);
        }

        $provider = Provider::loadFromUuid($request->get('provider_id'));

        $newCreds = new Credentials();
        $newCreds->_id = Uuid::generate(4)->string;
        $newCreds->username = $request->get('username');
        $newCreds->secret = Crypt::encrypt($request->get('secret'));
        $newCreds->domain = $request->get('domain');
        $newCreds->mail_from_address = $request->get('mail_from_address');
        $newCreds->mail_from_name = $request->get('mail_from_name');
        $newCreds->created_by = Auth::user()->id;
        $newCreds->updated_by = Auth::user()->id;

        $newCreds->user()->associate(Auth::user());
        $newCreds->provider()->associate($provider);
        $newCreds->save();

        return response()->json(['credentials_id' => $newCreds->_id], 201);
    }

    public function getCredentials()
    {

    }

    public function editCredentials(Request $request)
    {
        try {
            $this->validate($request, [

            ]);
        } catch (\Exception $e) {

        }
    }

    public function deleteCredentials($credentialId)
    {

    }
}