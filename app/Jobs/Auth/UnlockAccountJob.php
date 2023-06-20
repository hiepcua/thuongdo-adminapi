<?php

namespace App\Jobs\Auth;

use App\Constants\AuthConstant;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class UnlockAccountJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $_user;

    /**
     * Create a new job instance.
     *
     * @param  User  $user
     */
    public function __construct(User $user)
    {
        $this->delay(now()->addMinutes(AuthConstant::ACCOUNT_BLOCKED_BY_MINUTES));
        $this->_user = $user;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $this->_user->status = AuthConstant::STATUS_UNLOCK;
        $this->_user->blocked_at = null;
        $this->_user->save();
    }
}
