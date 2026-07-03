<?php

namespace App\Console\Commands;

use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;

#[Signature('app:send-absence-notification')]
#[Description('Command description')]
class SendAbsenceNotification extends Command
{
    /**
     * Execute the console command.
     */
    public function handle()
    {
        //
    }
}
