<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\Admin\Client\ClientCollection;
use App\Models\Client;
use Illuminate\Http\Request;

class ClientController extends Controller
{
    public function index() {
        $clients = Client::orderBy('id','desc')->get();
        return new ClientCollection($clients);
    }
}
