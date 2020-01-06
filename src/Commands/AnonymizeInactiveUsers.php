<?php

namespace Dialect\Gdpr\Commands;

use Carbon\Carbon;
use Illuminate\Console\Command;

class AnonymizeInactiveUsers extends Command
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $signature = 'gdpr:anonymizeInactiveUsers';
    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Anonymize inactive users';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        if (! config('gdpr.enabled')) {
            return;
        }

        $model = config('gdpr.settings.user_model_fqn', 'App\User');
        $user = new $model();
        $anonymizableUsers = $user::where('last_activity', '!=', null)->where('isAnonymized', 0)->where('last_activity', '<=', carbon::now()->subMonths(config('gdpr.settings.ttl')))->get();

        foreach ($anonymizableUsers as $user) {
            $user->anonymize();
            $user->update([
                'isAnonymized' => true,
            ]);
        }
    }
}
