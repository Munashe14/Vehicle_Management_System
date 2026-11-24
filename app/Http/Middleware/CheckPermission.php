<?php

namespace App\Http\Middleware;

use App\Models\AuditLog;
use Closure;
use Illuminate\Http\Request;

class CheckPermission
{
    public function handle(Request $request, Closure $next, string $permission)
    {
        $user = $request->user();

        if ($user && $user->hasPermission($permission)) {
            return $next($request);
        }

        if ($user) {
            AuditLog::create([
                'user_id' => $user->id,
                'action' => 'authorization.denied',
                'subject_type' => 'route',
                'details' => [
                    'permission' => $permission,
                    'route' => $request->route() ? $request->route()->uri() : null,
                    'method' => $request->method(),
                ],
                'ip_address' => $request->ip(),
            ]);
        }

        abort(403, 'You are not authorized to perform this action.');
    }
}
