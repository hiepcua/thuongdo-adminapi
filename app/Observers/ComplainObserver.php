<?php

namespace App\Observers;

use App\Models\Complain;
use App\Models\ReportComplain;
use App\Services\ReportService;

class ComplainObserver
{
    public function updated(Complain $complain)
    {
        if(($old = $complain->getOriginal('status')) != $complain->status)
        {
            (new ReportService())->inDecrementByOrganization(ReportComplain::class, $complain->status, $old, 1, ['customer_id' => $complain->customer_id]);
        }
    }
}
