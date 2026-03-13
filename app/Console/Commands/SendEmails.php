<?php

namespace App\Console\Commands;

use App\Constants\RoleUser;
use App\Mail\TicketWeeklyStatus;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class SendEmails extends Command
{
    protected $signature = 'send:emails';

    protected $description = 'Send ticket status email (all tickets by status) to all admins';

    public function handle(): int
    {
        $admins = User::query()->where('role', RoleUser::ADMIN)->get();
        if ($admins->isEmpty()) {
            $this->warn('No admin users found.');
            return self::FAILURE;
        }
        foreach ($admins as $admin) {
            Mail::to($admin->email)->send(new TicketWeeklyStatus());
            $this->info("Sent to {$admin->email}");
        }
        $this->info('Done.');
        return self::SUCCESS;
    }
}
