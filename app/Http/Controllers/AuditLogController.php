<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;

class AuditLogController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:audit.view');
    }

    public function index()
    {
        $logs = AuditLog::with('user')->latest()->paginate(50);

        $actionLabels = [
            'authorization.denied' => 'Authorization denied',
            'driver.created' => 'Driver created',
            'driver.updated' => 'Driver updated',
            'driver.deactivated' => 'Driver deactivated',
            'user.created' => 'User created',
            'user.updated' => 'User updated',
            'user.deactivated' => 'User deactivated',
            'user.password_reset' => 'User password reset',
            'user.registration_requested' => 'Registration requested',
            'user.registration_approved' => 'Registration approved',
            'user.registration_rejected' => 'Registration rejected',
            'user.initial_admin_assigned' => 'Initial administrator assigned',
            'operational.created' => 'Record created',
            'operational.updated' => 'Record updated',
            'operational.deleted' => 'Record deleted',
        ];

        $logs->getCollection()->transform(function (AuditLog $log) use ($actionLabels) {
            $log->action_label = $actionLabels[$log->action]
                ?? ucwords(str_replace(['.', '_'], ' ', $log->action));
            $log->formatted_details = $this->formatDetails($log->details);

            return $log;
        });

        return view('audit.index', compact('logs'));
    }

    private function formatDetails($details): array
    {
        if (is_string($details)) {
            $decoded = json_decode($details, true);
            $details = json_last_error() === JSON_ERROR_NONE ? $decoded : ['Details' => $details];
        }

        if (!is_array($details)) {
            return [];
        }

        $formatted = [];
        foreach ($details as $key => $value) {
            $label = is_string($key) ? ucwords(str_replace(['_', '.'], ' ', $key)) : 'Details';

            if (is_array($value)) {
                $value = implode(', ', array_map(function ($item) {
                    if (is_array($item)) {
                        return $this->formatNestedDetails($item);
                    }

                    return $this->formatDetailValue($item);
                }, $value));
            } else {
                $value = $this->formatDetailValue($value);
            }

            $formatted[] = ['label' => $label, 'value' => $value];
        }

        return $formatted;
    }

    private function formatNestedDetails(array $details): string
    {
        $parts = [];
        foreach ($details as $key => $value) {
            $label = is_string($key) ? ucwords(str_replace(['_', '.'], ' ', $key)) : 'Details';
            $parts[] = $label.': '.$this->formatDetailValue($value);
        }

        return implode('; ', $parts);
    }

    private function formatDetailValue($value): string
    {
        if ($value === null || $value === '') {
            return '—';
        }

        if (is_bool($value)) {
            return $value ? 'Yes' : 'No';
        }

        if (!is_scalar($value)) {
            return '—';
        }

        $roles = [
            'it_manager' => 'IT Manager',
            'fleet_supervisor' => 'Fleet Supervisor',
            'driver' => 'Driver',
            'finance_manager' => 'Finance Manager',
            'general_manager' => 'General Manager',
        ];

        $stringValue = (string) $value;
        if (isset($roles[$stringValue])) {
            return $roles[$stringValue];
        }

        return $stringValue;
    }
}
