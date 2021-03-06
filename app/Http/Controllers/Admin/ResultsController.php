<?php

namespace Bets\Http\Controllers\Admin;

use Bets\Http\Controllers\Controller;
use Bets\Jobs\{CalculateBetStatus, CancelTips};
use Bets\Models\Tip;
use Bets\Services\ResultsService;
use Bets\Http\Requests\ResultsUpdateRequest;
use Illuminate\Http\Request;

class ResultsController extends Controller
{
    /**
     * @var ResultsService
     */
    private $resultsService;

    public function __construct(ResultsService $resultsService)
    {
        $this->resultsService = $resultsService;
    }

    public function index()
    {
        $resultados = \Bets\Models\Tip::query()
            ->whereDate('created_at', '>=', now()->subDays(2))
            ->groupBy(['match_id', 'bet_slug', 'choice_slug'])
            ->where('status', 'pending')
            ->with([
                'match', 'match.league',
                'match.league.country', 'match.sport',
            ])
            ->get();

        return view('admin.results.index', compact('resultados'));
    }

    public function salvar(Request $request)
    {
        $mensagem = '';

        $status = $request->get('status');

        $query = Tip::query();

        $query->where('match_id', $request->get('evento'));
        $query->where('bet_slug', $request->get('mercado'));
        $query->where('choice_slug', $request->get('palpite'));

        $apostas = $query->pluck('bet_id');

        if ($status === 'vencedor') {
            $query->update(['status' => 'win']);

            $apostas = \Bets\Models\Bet::whereIn('id', $apostas->toArray())->get();

            foreach ($apostas as $aposta) {
                dispatch((new CalculateBetStatus($aposta))->onQueue('betStatus'));
            }

            $mensagem = 'Palpites marcados como vencedores. As apostas serão calculadas em breve...';
        } elseif ($status === 'perdedor') {
            $query->update(['status' => 'lose']);

            \Bets\Models\Bet::whereIn('id', $apostas->toArray())
                ->update(['status' => 'lose']);

            $mensagem = 'Palpites marcados como perdedores. As apostas serão calculadas em breve...';
        } elseif ($status === 'cancelar') {
            $query->update(['status' => 'canceled']);

            $palpites = $query->get();

            foreach ($palpites as $palpite) {
                dispatch((new CancelTips($palpite))->onQueue('betStatus'));
            }

            $mensagem = 'Palpites cancelados. As apostas serão calculadas em breve...';
        }

        return redirect()->back()->with('success', $mensagem);
    }

    public function store(Request $request)
    {
        $data = $request->get('results');

        foreach ($data as $key => $value) {
            if ($value['home_1st'] === '' || $value['away_1st'] === '') continue;
            if ($value['home_2nd'] === '' || $value['away_2nd'] === '') continue;
            if ($value['home_final'] === '' || $value['away_final'] === '') continue;

            $value['status'] = 'finished';
            $this->resultsService->update($value, $key);
        }

        return redirect()->route('admin.results.index')->with('success', 'Resutados cadastrados com sucesso. Os ganhadores serão calculados em breve...');
    }

    public function edit($id)
    {
        $result = $this->resultsService->findById($id);

        return view('admin.results.edit', compact('result'));
    }

    public function update(ResultsUpdateRequest $request, $id)
    {
        $data = $request->all();

        $this->resultsService->update($data, $id);

        $message = ($data['status'] === 'finished') ? 'Os ganhadores serão calculados em breve...' : '';

        return redirect()->route('admin.results.index')->with('success', 'Resutado cadastrado com sucesso. ' . $message);
    }

    public function cancel($id)
    {
        $this->resultsService->update(array('status' => 'canceled'), $id);

        return redirect()->route('admin.results.index')->with('success', 'Partida cancelada com sucesso!');
    }
}
