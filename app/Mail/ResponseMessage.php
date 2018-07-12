<?php

namespace App\Mail;

use App\Response;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;

class ResponseMessage extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(Response $response)
    {
        $data = $this->prepareResponse($response);
    }

    public function build()
    {

    }

    private function prepareResponse(Response $response)
    {
        $fields = '';

        $response->responseElements->each(function ($element) use ($fields) {
            $key = $element->field->label;
            $value = $element->answer;
            $fields = sprintf("%s%s: %s\n", $fields, $key, $value);
        });
    }
}