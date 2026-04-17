<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class ContactFormController extends Controller
{
    public function send(Request $request)
    {
        // Honeypot check
        if ($request->filled('website')) {
            abort(403);
        }
        
        $request->validate([
            'name'    => 'required|string|min:3|max:100',
            'email'   => 'required|email|max:255',
            'message' => 'required|string|min:20|max:2000',
        ]);

        if (preg_match('/https?:\/\//i', $request->message)) {
            abort(403);
        }

        Mail::raw(
            "Name: {$request->name}\n"
            ."Email: {$request->email}\n\n"
            ."Message:\n{$request->message}",
            function ($m) {
                $m->to('noreply@thealawites.com')
                  ->subject('New Contact Message');
            }
        );

        return redirect()->route('thank.you', app()->getLocale());
    }
}
