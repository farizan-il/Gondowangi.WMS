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
        $user = $request->user();
        $permissions = [];

        if ($user && $user->role) {
            $permissions = $user->role->permissions()
                ->pluck('permission_name')
                ->toArray();
        }

        return array_merge(parent::share($request), [
            'auth' => [
                'user' => $user ? [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'nik' => $user->nik,
                    'departement' => $user->departement,
                    'jabatan' => $user->jabatan,
                    'role' => $user->role ? [
                        'id' => $user->role->id,
                        'name' => $user->role->role_name,
                    ] : null,
                ] : null,
            ],
            'permissions' => $permissions,
            'flash' => [
                'success' => fn () => $request->session()->get('success'),
                'error' => fn () => $request->session()->get('error'),
            ],
            'pendingReturnCount' => $user ? \App\Models\ReturnModel::where('status', 'Pending Approval')->count() : 0,
            'pendingPickingCount' => $user ? \App\Models\ReservationRequest::whereIn('status', ['Submitted', 'Reserved', 'In Progress', 'Ready to Pick', 'Short-Pick'])
                ->whereHas('reservations', function ($q) {
                    $q->where('qty_reserved', '>', 0);
                })->count() : 0,
        ]);
    }
}
