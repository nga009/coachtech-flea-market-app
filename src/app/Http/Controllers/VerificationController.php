<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class VerificationController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function notice()
    {
        if (auth()->user()->hasVerifiedEmail()) {
            if (!auth()->user()->profile) {
                return redirect()->route('profile.create');
            }
            return redirect()->route('items.index');
        }
        
        return view('auth.email-verification-notice');
    }
}
