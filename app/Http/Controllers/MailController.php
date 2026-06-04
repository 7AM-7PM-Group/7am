<?php

namespace App\Http\Controllers;

use App\Mail\Order\Order;
use App\Mail\Order\Paid;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class MailController extends Controller
{
    public function sendTestEmail()
    {
        $business = \App\Models\Business::lastest()->first();
        Mail::to($business->user->email)->queue(new \App\Mail\Business\Accept($business->id));
        // return view('mail.order.order');
    }
}
