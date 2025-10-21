<?php

namespace App\Traits;

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Models\IncomingActivityLog;
use App\Models\QcActivityLog;
use App\Models\ReservationActivityLog;
use App\Models\ReturnActivityLog;
use App\Models\WarehouseActivityLog;
use App\Models\ActivityLog;

trait ActivityLogger
{
    /**
     * Log an activity.
     *
     * @param \Illuminate\Database\Eloquent\Model $model
     * @param string $action
     * @param array $data
     * @return void
     */
    public function logActivity($model, string $action, array $data = [])
    {
        $logModel = $this->getLogModelFor($model);

        if (!$logModel) {
            return;
        }

        $request = app(Request::class);

        $logData = [
            'user_id' => Auth::id(),
            'action' => $action,
            'description' => $data['description'] ?? null,
            'material_id' => $data['material_id'] ?? null,
            'batch_lot' => $data['batch_lot'] ?? null,
            'exp_date' => $data['exp_date'] ?? null,
            'qty_before' => $data['qty_before'] ?? 0,
            'qty_after' => $data['qty_after'] ?? 0,
            'bin_from' => $data['bin_from'] ?? null,
            'bin_to' => $data['bin_to'] ?? null,
            'reference_document' => $data['reference_document'] ?? null,
            'old_value' => $data['old_value'] ?? null,
            'new_value' => $data['new_value'] ?? null,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'device_info' => $this->getDeviceInfo($request),
        ];

        // Dynamically add the foreign key for the model
        $foreignKey = strtolower(class_basename($model)) . '_id';
        $logData[$foreignKey] = $model->id;

        $logModel::create($logData);
    }

    /**
     * Get the appropriate log model for a given model.
     *
     * @param \Illuminate\Database\Eloquent\Model $model
     * @return string|null
     */
    protected function getLogModelFor($model)
    {
        $modelClass = get_class($model);

        $map = [
            'App\Models\IncomingGood' => IncomingActivityLog::class,
            'App\Models\IncomingGoodsItem' => IncomingActivityLog::class,
            'App\Models\QCChecklist' => QcActivityLog::class,
            'App\Models\Reservation' => ReservationActivityLog::class,
            'App\Models\ReturnModel' => ReturnActivityLog::class,
            'App\Models\Warehouse' => WarehouseActivityLog::class,
            // Add other mappings here
        ];

        return $map[$modelClass] ?? ActivityLog::class;
    }

    /**
     * Get device information from the request.
     *
     * @param \Illuminate\Http\Request $request
     * @return string
     */
    protected function getDeviceInfo(Request $request)
    {
        // Simple device info, can be expanded
        return $request->header('User-Agent');
    }
}
