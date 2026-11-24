<?php

namespace App\Observers;

use App\Models\AuditLog;
use Illuminate\Database\Eloquent\Model;

class OperationalAuditObserver
{
    public function created(Model $model): void
    {
        $this->record($model, 'created', array_keys($model->getAttributes()));
    }

    public function updated(Model $model): void
    {
        $this->record($model, 'updated', array_keys($model->getChanges()));
    }

    public function deleted(Model $model): void
    {
        $this->record($model, 'deleted', []);
    }

    private function record(Model $model, string $event, array $fields): void
    {
        AuditLog::create([
            'user_id' => auth()->id(),
            'action' => 'operational.'.$event,
            'subject_type' => get_class($model),
            'subject_id' => $model->getKey(),
            'details' => ['fields' => array_values(array_diff($fields, ['password', 'remember_token']))],
            'ip_address' => request()->ip(),
        ]);
    }
}
