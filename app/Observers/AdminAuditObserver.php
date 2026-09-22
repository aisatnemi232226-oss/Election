<?php

namespace App\Observers;

use App\Support\AuditLogger;
use Illuminate\Database\Eloquent\Model;

class AdminAuditObserver
{
    protected function shouldLog(): bool
    {
        return auth()->check() && (auth()->user()->is_admin ?? false);
    }

    protected function actorId(): ?int
    {
        return auth()->check() ? (int) auth()->id() : null;
    }

    protected function modelLabel(Model $model): string
    {
        return class_basename($model);
    }

    public function created(Model $model): void
    {
        if (!$this->shouldLog()) {
            return;
        }

        AuditLogger::log(
            request(),
            $this->actorId(),
            'AUDIT',
            'INSERT',
            $this->modelLabel($model) . ' Created',
            'Created ' . strtolower($this->modelLabel($model)) . ' #' . $model->getKey(),
            $model->getTable(),
            (int) $model->getKey(),
            null,
            $model->getAttributes()
        );
    }

    public function updated(Model $model): void
    {
        if (!$this->shouldLog()) {
            return;
        }

        $changes = $model->getChanges();
        if (empty($changes)) {
            return;
        }

        if (count($changes) === 1 && array_key_exists('deleted_at', $changes)) {
            return;
        }

        if (
            count($changes) === 1
            && array_key_exists('status', $changes)
            && request()
            && request()->route()
            && in_array(request()->route()->getName(), ['admin.candidates.approve', 'admin.candidates.reject'], true)
        ) {
            return;
        }

        $old = [];
        foreach (array_keys($changes) as $key) {
            $old[$key] = $model->getOriginal($key);
        }

        AuditLogger::log(
            request(),
            $this->actorId(),
            'AUDIT',
            'UPDATE',
            $this->modelLabel($model) . ' Updated',
            'Updated ' . strtolower($this->modelLabel($model)) . ' #' . $model->getKey(),
            $model->getTable(),
            (int) $model->getKey(),
            $old,
            $changes
        );
    }

    public function deleted(Model $model): void
    {
        if (!$this->shouldLog()) {
            return;
        }

        if (method_exists($model, 'isForceDeleting') && $model->isForceDeleting()) {
            return;
        }

        AuditLogger::log(
            request(),
            $this->actorId(),
            'AUDIT',
            'DELETE',
            $this->modelLabel($model) . ' Deleted',
            'Deleted ' . strtolower($this->modelLabel($model)) . ' #' . $model->getKey(),
            $model->getTable(),
            (int) $model->getKey(),
            $model->getAttributes(),
            null
        );
    }

    public function restored(Model $model): void
    {
        if (!$this->shouldLog()) {
            return;
        }

        AuditLogger::log(
            request(),
            $this->actorId(),
            'AUDIT',
            'UPDATE',
            $this->modelLabel($model) . ' Restored',
            'Restored ' . strtolower($this->modelLabel($model)) . ' #' . $model->getKey(),
            $model->getTable(),
            (int) $model->getKey(),
            null,
            $model->getAttributes()
        );
    }

    public function forceDeleted(Model $model): void
    {
        if (!$this->shouldLog()) {
            return;
        }

        AuditLogger::log(
            request(),
            $this->actorId(),
            'AUDIT',
            'DELETE',
            $this->modelLabel($model) . ' Permanently Deleted',
            'Permanently deleted ' . strtolower($this->modelLabel($model)) . ' #' . $model->getKey(),
            $model->getTable(),
            $model->getKey() ? (int) $model->getKey() : null,
            $model->getAttributes(),
            null
        );
    }
}
