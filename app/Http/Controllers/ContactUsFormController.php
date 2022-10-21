<?php

namespace App\Http\Controllers;

use App\Http\Requests\SendContactUsMessageRequest;
use App\Mail\MessageFromWebsiteForm;
use Illuminate\Support\Facades\Mail;

class ContactUsFormController extends Controller
{
    public function sendContactUsMessage(SendContactUsMessageRequest $request)
    {
        Mail::to('remigijus136@gmail.com')->send(new MessageFromWebsiteForm($request->all(['email', 'text'])));
    }
}
