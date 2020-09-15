<?php

namespace App\Http\Controllers;

use App\Models\Address;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AddressesController extends Controller
{
    //
    public function index(  ) {
        $addresses = \request()->user()->addresses;
        return view('users.addresses',compact('addresses'));
    }
}
