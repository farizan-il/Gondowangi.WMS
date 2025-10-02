<?php

namespace App\Http\Middleware;

use Illuminate\Http\Request;
use Inertia\Middleware;

class HandleInertiaRequests extends Middleware
{
    protected $rootView = 'app';

    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    public function share(Request $request): array
    {
        return array_merge(parent::share($request), [
            'auth' => [
                'user' => $request->user() ? [
                    'id' => $request->user()->id,
                    'nik' => $request->user()->nik,
                    'nama_lengkap' => $request->user()->nama_lengkap,
                    'email' => $request->user()->email,
                    'departement' => $request->user()->departement,
                    'jabatan' => $request->user()->jabatan,
                    'status' => $request->user()->status,
                    'role' => $request->user()->role ? [
                        'id' => $request->user()->role->id,
                        'role_name' => $request->user()->role->role_name,
                    ] : null,
                ] : null,
            ],
        ]);
    }
}
