<?php

namespace App\Http\Controllers;

use App\Mail\ContactMessage;
use App\Mail\ContactNotification;
use App\Models\User;
use App\Models\Mail as MailModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Rules\Recaptcha;

class MessagesController extends Controller
{
    public function list() {

	}

	public function forward(Request $request) {

		$data = $request->validate([
			'email' => ['max:256', 'required', 'email'],
			'subject' => ['max:256', 'required'],
			'message' => [
				'required',
				function($attribute, $value, $fail) {
					if(str_word_count($value) <= 1) {
						$fail('The :attribute field must have more than one word.');
					}
				}
			],
			'g-recaptcha-response' => [new Recaptcha]
		]);

		$users = User::where('role', 'admin')->orWhere('role', 'postmaster')->get();
		
		MailModel::create($data);

		foreach($users as $user) {
			Mail::to($user->email)->send(New ContactMessage($data));
		}
		Mail::to($data['email'])->send(New ContactNotification($data));

		return redirect()->route('index')->with([
			'flash' => __('flash.mail.success'),
			'flash-type' => 'success'
		]);
	}

	public function log() {
		$mails = MailModel::get();
		return view('other.maillog', compact('mails'));
	}
}
