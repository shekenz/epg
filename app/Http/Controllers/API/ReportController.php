<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use App\Mail\Report;
use App\Mail\SystemError;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    
	public function mail(Request $request)
	{

		$data = json_decode($request->getContent());

		if(!empty($data))
		{
			Mail::to('aureltrotebas@icloud.com')->send(new Report($data));
		}
	}

}
