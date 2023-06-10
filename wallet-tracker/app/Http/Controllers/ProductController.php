<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Illuminate\Support\Facades\Auth;

use App\Models\Product;
use App\Models\Warehouse;
use App\Models\Role;

class ProductController extends Controller
{
    public function index(Request $request, string $code)
    {
        // check method
        $method = $request->method();

        // khusus method GET
        if ($method != "GET") {
            return redirect(Route('home'));
        }

        if (!$code) {
            return redirect(Route('home'));
        }

        // inisiasi user yang sedang aktif
        $user = Auth::user();

        // check apakah terdapat warehouse dengan kode yang dikirimkan
        $warehouse = Warehouse::where('code', $code)->first();

        if (!$warehouse) {
            return redirect(route('home'));
        }

        // jika user yang sedang login terdaftar di warehouse
        $userHasAccess = Role::whereHas('warehouse', function($q) use ($warehouse) {
            $q->where('code', $warehouse->code);
        })->whereHas('users', function($q) use ($user) {
            $q->where('username', $user->username);
        })->first();

        // Jika user tidak memiliki akses
        if (!$userHasAccess) {
            return redirect(route('home'));
        }

        $producttypes = $warehouse->producttypes;

        // return view terkait
        return view('product.index', [
            'warehouse' => $warehouse,
            'producttypes' => $producttypes,
        ]);
    }

    public function edit(Request $request, string $code)
    {
        // check method
        $method = $request->method();

        // khusus method GET
        if ($method != "GET") {
            return redirect(Route('home'));
        }

        if (!$code) {
            return redirect(Route('home'));
        }

        // inisiasi user yang sedang aktif
        $user = Auth::user();

        // check apakah terdapat warehouse dengan kode yang dikirimkan
        $warehouse = Warehouse::where('code', $code)->first();

        // jika terdapat warehouse, maka tampilkan view
        if (!$warehouse) {
            return redirect(route('home'));
        }

        // jika user yang sedang login terdaftar di warehouse
        $userHasAccess = Role::whereHas('warehouse', function($q) use ($warehouse) {
            $q->where('code', $warehouse->code);
        })->whereHas('users', function($q) use ($user) {
            $q->where('username', $user->username);
        })->first();

        // Jika user tidak memiliki akses
        if (!$userHasAccess) {
            return redirect(route('home'));
        }

        $producttypes = $warehouse->producttypes;

        // return view terkait
        return view('product.edit', [
            'warehouse' => $warehouse,
            'producttypes' => $producttypes,
        ]);
    }
}
