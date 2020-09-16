<?php

namespace App\Http\Controllers;

use App\Http\Requests\AddressRequest;
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

    public function create() {
        $address = new Address();
        return view('users.create_and_edit_address',compact('address'));
    }

    public function store( AddressRequest $request ) {
        $request->user()->addresses()->create($request->only([
            'province',
            'city',
            'district',
            'address',
            'contact_name',
            'contact_phone',
        ]));
        return redirect()->route('addresses.index');
    }

    public function destroy(Address $address) {

        $this->authorize('destroy',$address);
        $address->delete();
        return response()->json(['message'=>'删除成功'],204);
    }

    public function edit(Address $address) {
        return view('users.create_and_edit_address', compact('address'));
    }

    public function update(Address $address, AddressRequest $request) {
        $this->authorize('update',$address);
        $address->update($request->only([
            'province',
            'city',
            'district',
            'strict',
            'contact_name',
            'contact_phone',
        ]));
        return redirect()->route('addresses.index');
    }
}
