<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Client;
use Illuminate\Support\Carbon;

class ClientsController extends Controller
{

    public function list() {

			$clients = Client::all();
			return view('clients.list', compact('clients'));

		}

		public function csv(Request $request) {

			$client = Client::all();

			$fileName = 'clients_'.Carbon::now()->toDateString().'.csv';

			$headers = array(
				'Content-type'        => 'text/csv',
				'Content-Disposition' => 'attachment; filename='.$fileName,
				'Pragma'              => 'no-cache',
				'Cache-Control'       => 'must-revalidate, post-check=0, pre-check=0',
				'Expires'             => '0'
			);

			$columns = ['email','firstname', 'lastname'];

			$callback = function() use($client, $columns) {
				$file = fopen('php://output', 'w');
				fputcsv($file, $columns, ';');
				foreach ($client as $client) {
					fputcsv(
						$file,
						[
							$client->email,
							$client->firstname,
							$client->lastname,
						],
						';'
					);
				}

				fclose($file);
			};
			
			return response()->stream($callback, 200, $headers);

		}
}
