<?php

namespace Bets\Jobs;

use Bets\Models\Bet;
use Bets\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class CalculateBetStatus implements ShouldQueue
{
    use InteractsWithQueue, Queueable, SerializesModels;

    /**
     * @var Bet
     */
    private $bet;

    /**
     * Create a new job instance.
     *
     * @param Bet $bet
     */
    public function __construct(Bet $bet)
    {
        $this->bet = $bet;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        if ($this->bet->tips->where('status', '<>', 'canceled')->count() === $this->bet->tips->where('status', 'win')->count()) {

            if ($this->bet->tips->where('status', 'canceled')->count() === $this->bet->tips->count()) {
                $this->bet->commission = 0;
            }

            $this->bet->status = 'win';
            $this->bet->save();

            if ($this->bet->seller_id !== null) {
                $prize = $this->bet->prize;

                $seller = app(User::class)->find($this->bet->seller_id);

                if ($prize + $seller->balance < $seller->limit) {
                    $seller->increment('balance', $prize);
                } else {
                    $seller->balance = $seller->limit;
                    $seller->save();
                }
            }
        }
    }
}
