<?php

namespace Bets\Http\Controllers\Web;

use Bets\Services\BetsService;
use Illuminate\Http\Request;
use Bets\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class BetsController extends Controller
{
    /**
     * @var BetsService
     */
    private $betsService;

    public function __construct(BetsService $betsService)
    {
        $this->betsService = $betsService;
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
                'bet_value' => 'required|numeric',
                'choices' => 'required',
            ],
            [
                'choices.required'  => 'Escolha ao menos um palpite',
                'bet_value.required' => 'O valor é obrigatório',
            ]
        );

        $validator->validate();

        $data = array();

        $data['bet_value'] = moneyUS($request->get('bet_value'));
        $data['quotations'] = $request->get('choices');

        try {
            $bet = $this->betsService->createWithoutSeller($data);

            return response()->json(['bet' => $bet]);

        } catch (\Exception $exception) {
            return response()->json([
                'choices' => [$exception->getMessage()],
            ], 400);
        }
    }

    public function printing($id)
    {
        try {
            $bet = $this->betsService->findById($id);

            return view('bets.ticket', compact('bet'));

        } catch (\Throwable $e) {
            return redirect('/')->with('error', 'Erro ao encontrar simulação.');
        }
    }

    public function pdf($apostaID)
    {
        $bet = $this->betsService->findById($apostaID);

        if (!$bet) {
            abort(404, 'Aposta não encontrada');
        }

        $arquivoPDF = storage_path("app/public/{$bet->id}.pdf");

        $viewBilhete = view('bets.pdf', compact('bet'));

        $pdf = new \Dompdf\Dompdf();
        $pdf->setPaper([0, 0, 300, 249.449]);

        $GLOBALS['bodyHeight'] = 0;

        $pdf->setCallbacks([
            'myCallbacks' => [
                'event' => 'end_frame', 'f' => function ($infos) {
                    $frame = $infos["frame"];
                    if (strtolower($frame->get_node()->nodeName) === "body") {
                        $padding_box = $frame->get_padding_box();
                        $GLOBALS['bodyHeight'] += $padding_box['h'];
                    }
                }
            ]
        ]);

        $pdf->loadHtml($viewBilhete);

        $pdf->render();

        unset($pdf);

        try {
            $pdf = new \Dompdf\Dompdf();
            $pdf->setPaper([0, 0, 300, $GLOBALS['bodyHeight'] - 40]);
            $pdf->loadHtml($viewBilhete);
	        $pdf->render();

            $output = $pdf->output();

            file_put_contents($arquivoPDF, $output);

            $im = new \Imagick();

	        $im->setResolution(150, 150);

            $im->readImage("{$arquivoPDF}[0]");

            $im->setImageFormat("png32");

	        $im->setImageCompressionQuality(100);

            unlink($arquivoPDF);

            header('Content-type: image/png');

            echo $im;

            $im->clear();

            $im->destroy();

            return '';
        } catch (\Throwable $e) {
            return \PDF::loadView('bets.pdf', compact('bet'))
                ->setPaper([0, 0, 249.449, $GLOBALS['bodyHeight'] + 5])
                ->stream("{$bet->id}.pdf");
        }
    }

    public function gerarImagem($apostaID)
    {
        $bet = $this->betsService->findById($apostaID);

        if (!$bet) {
            abort(404, 'Aposta não encontrada');
        }

        $arquivoPDF = storage_path("app/public/{$bet->id}.pdf");

        $viewBilhete = view('bets.pdf', compact('bet'));

        $pdf = new \Dompdf\Dompdf();
        $pdf->setPaper([0, 0, 300, 249.449]);

        $GLOBALS['bodyHeight'] = 0;

        $pdf->setCallbacks([
            'myCallbacks' => [
                'event' => 'end_frame', 'f' => function ($infos) {
                    $frame = $infos["frame"];
                    if (strtolower($frame->get_node()->nodeName) === "body") {
                        $padding_box = $frame->get_padding_box();
                        $GLOBALS['bodyHeight'] += $padding_box['h'];
                    }
                }
            ]
        ]);

        $pdf->loadHtml($viewBilhete);

        $pdf->render();

        unset($pdf);

        $pdf = new \Dompdf\Dompdf();
        $pdf->setPaper([0, 0, 300, $GLOBALS['bodyHeight']]);
        $pdf->loadHtml($viewBilhete);
        $pdf->render();

        $output = $pdf->output();

        file_put_contents($arquivoPDF, $output);

        $im = new \Imagick();

        $im->setResolution(150, 150);

        try {
            $im->readImage("{$arquivoPDF}[0]");
        } catch (\ImagickException $e) {
            return 'erro ao gerar imagem';
        }

        $im->setImageFormat("png32");

        $im->setImageCompressionQuality(100);

        unlink($arquivoPDF);

        header('Content-type: image/png');

        echo $im;

        $im->clear();

        $im->destroy();

        return '';
    }

    public function gerarPDF($apostaID)
    {
        $bet = $this->betsService->findById($apostaID);

        if (!$bet) {
            abort(404, 'Aposta não encontrada');
        }

        $viewBilhete = view('bets.pdf', compact('bet'));

        $pdf = new \Dompdf\Dompdf();
        $pdf->setPaper([0, 0, 300, 249.449]);

        $GLOBALS['bodyHeight'] = 0;

        $pdf->setCallbacks([
            'myCallbacks' => [
                'event' => 'end_frame', 'f' => function ($infos) {
                    $frame = $infos["frame"];
                    if (strtolower($frame->get_node()->nodeName) === "body") {
                        $padding_box = $frame->get_padding_box();
                        $GLOBALS['bodyHeight'] += $padding_box['h'];
                    }
                }
            ]
        ]);

        $pdf->loadHtml($viewBilhete);

        $pdf->render();

        unset($pdf);

        $pdf = new \Dompdf\Dompdf();
        $pdf->setPaper([0, 0, 300, $GLOBALS['bodyHeight']]);
        $pdf->loadHtml($viewBilhete);
        $pdf->render();

        $pdf->stream("{$bet->id}.pdf");

        return '';
    }
}
