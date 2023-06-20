<?php


namespace App\Services;


use App\Models\Customer;
use App\Models\OrderNote;
use App\Models\Staff;

class NoteService implements Service
{
    public function store(string $model, array $data)
    {
        return (new $model)::query()->create(
            [
                'order_id' => $data['order_id'] ?? null,
                $data['column'] => $data['id'],
                'subject_id' => getCurrentUser()->id,
                'subject_type' => Staff::class,
                'content' => $data['content'],
                'supplier_id' => $data['supplier_id'] ?? null,
                'is_public' => $data['is_public'] ?? null
            ]
        );
    }

    /**
     * @param  string  $orderId
     * @param  string  $supplierId
     * @param  bool  $isPublic
     * @return mixed
     */
    public function getOrderNote(string $orderId, string $supplierId, bool $isPublic)
    {
        return optional(
            OrderNote::query()->where(
                [
                    'subject_type' => $isPublic ? Customer::class : Staff::class,
                    'supplier_id' => $supplierId,
                    'order_id' => $orderId,
                    'is_public' => $isPublic
                ]
            )->orderByDesc('created_at')->first()
        )->content;
    }
}