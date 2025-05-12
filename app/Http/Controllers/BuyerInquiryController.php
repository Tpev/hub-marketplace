<?php

namespace App\Http\Controllers;

use App\Models\BuyerInquiry;
use Illuminate\Http\Request;

class BuyerInquiryController extends Controller
{
public function store(Request $request)
{
    $request->validate([
        'message' => 'required|string|max:1000',
        'name' => 'required|string|max:255',
        'email' => 'required|email|max:255',
		'g-recaptcha-response' => 'required|captcha',
    ]);

    BuyerInquiry::create($request->only('name', 'email', 'message'));

    return redirect()->back()->with('success', 'Your inquiry has been submitted!');
}
}