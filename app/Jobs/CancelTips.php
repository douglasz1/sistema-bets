<?php

namespace Bets\Jobs;

use Bets\Models\Tip;
use Bets\Services\TipsService;
use Illuminate\Bus\Queueable;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class CancelTips implements ShouldQueue
{
    use InteractsWithQueue, Queueable, SerializesModels, DispatchesJobs;

    /**
     * @var Tip
     */
    private $tip;

    /**
     * Create a new job instance.
     *
     * @param Tip $tip
     */
    public function __construct(Tip $tip)
    {
        $this->tip = $tip;
    }

    /**
     * Execute the job.
     *
     * @param TipsService $tipsService
     * @return void
     */
    public function handle(TipsService $tipsService)
    {
        $tipsService->cancel($this->tip->id);
    }
}
