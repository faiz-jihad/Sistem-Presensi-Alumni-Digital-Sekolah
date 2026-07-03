<?php

namespace App\Console\Commands;

use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;

#[Signature('app:check-expired-tokens')]
#[Description('Command description')]
class CheckExpiredTokens extends Command
{
    /**
     * Execute the console command.
     */
    public function handle()
    {
        //
    }
}
