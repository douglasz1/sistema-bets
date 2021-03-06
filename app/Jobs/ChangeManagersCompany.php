<?php

namespace Bets\Jobs;

use Bets\Models\Bet;
use Bets\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class ChangeManagersCompany implements ShouldQueue
{
    use InteractsWithQueue, Queueable, SerializesModels;
    /**
     * @var User
     */
    private $manager;

    /**
     * Create a new job instance.
     *
     * @param User $manager
     */
    public function __construct(User $manager)
    {
        //
        $this->manager = $manager;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        /*
         * Update sellers company ID
         */
        User::where('user_id', $this->manager->id)
            ->update(['company_id' => $this->manager->company_id]);

        $sellers = User::where('user_id', $this->manager->id)
            ->select('id', 'user_id')
            ->get()->toArray();

        if (is_array($sellers)) {
            $sellers = array_merge($sellers, [$this->manager->toArray()]);
        }

        Bet::whereIn('seller_id', $sellers)
            ->update(['company_id' => $this->manager->company_id]);
    }
}
