<?php

namespace App\Models;

use App\Models\Traits\StaffRelation;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * Class ReportUserTakeCareCustomer
 * @package App\Models
 *
 * @property string $id
 * @property string $user_id
 * @property bool $status
 */
class ReportUserTakeCareCustomer extends BaseModel
{
    use HasFactory, StaffRelation;
}
