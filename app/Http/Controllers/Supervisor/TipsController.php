<?php

namespace Bets\Http\Controllers\Supervisor;

use Bets\Http\Controllers\Controller;
use Bets\Services\TipsService;
use Illuminate\Http\Request;

class TipsController extends Controller
{
    /**
     * @var TipsService
     */
    private $tipsService;

    public function __construct(TipsService $tipsService)
    {
        $this->tipsService = $tipsService;
    }

    public function cancel($id)
    {
        $this->tipsService->cancel($id);

        return redirect()->back()->with('success', 'Palpite cancelado com sucesso');
    }
}
