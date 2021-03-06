<?php

namespace Bets\Jobs;

use Bets\Models\Bet;
use Bets\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class ChangeSellersCompany implements ShouldQueue
{
    use InteractsWithQueue, Queueable, SerializesModels;
    /**
     * @var User
     */
    private $seller;

    /**
     * Create a new job instance.
     *
     * @param User $seller
     */
    public function __construct(User $seller)
    {
        $this->seller = $seller;
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
        Bet::where('seller_id', $this->seller->id)
            ->update(['company_id' => $this->seller->company_id]);
    }
}
