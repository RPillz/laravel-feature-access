<?php

namespace RPillz\FeatureAccess\Commands;

use Illuminate\Console\Command;

class FeatureAccessCommand extends Command
{
    public $signature = 'featureaccess';

    public $description = 'My command';

    public function handle(): int
    {
        $this->comment('All done');

        return self::SUCCESS;
    }
}
