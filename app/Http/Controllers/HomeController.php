<?php

namespace Bets\Http\Controllers;

use Bets\Services\CompaniesService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class HomeController extends Controller
{
    private $companiesService;

    public function __construct(CompaniesService $companiesService)
    {
        $this->companiesService = $companiesService;
    }

    public function index()
    {
        if (auth()->user()->hasRoles('admin')) {
            return view('admin.simulador.index');
        }

        return view('home');
    }

    public function rules()
    {
        $company = $this->companiesService->first();
        $rules = $company->rules;

        return view('rules', compact('company', 'rules'));
    }

    public function balance()
    {
        return view('balance');
    }

    public function download()
    {
        $file = env('APK_URL', 'javascript:');
        $file = storage_path("app/public/{$file}");

        if (file_exists($file)) {
            header('Content-Description: File Transfer');
            header('Content-Type: application/vnd.android.package-archive');

            # header('Content-Type: application/octet-stream');
            # header('Content-Type: application/force-download');

            header('Content-Disposition: attachment; filename=' . basename($file));
            header('Content-Transfer-Encoding: binary');
            header('Expires: 0');
            header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
            header('Pragma: public');
            header('Content-Length: ' . filesize($file));

            if (ob_get_contents()) ob_end_clean();

            flush();

            readfile($file);

            exit;
        }
    }

    public function alterarSenha()
    {
        $usuario = auth()->user();
        return view('alterarSenha', compact('usuario'));
    }

    public function atualizarSenha(Request $request)
    {
        $validacao = validator()->make($request->all(), [
            'old_password' => 'required',
            'password' => 'required|confirmed|min:3'
        ]);

        try {
            $validacao->validate();

            $senhaAtual = auth()->user()->getAuthPassword();

            if (!Hash::check($request->get('old_password'), $senhaAtual)) {
                return redirect()->back()->with('error', 'A senha atual não está correta');
            }

            auth()->user()->update([
                'password' => bcrypt($request->get('password'))
            ]);

            return redirect()
                ->back()
                ->with('success', 'Senha atualizada com sucesso');

        } catch (\Throwable $exception) {
            return redirect()->back()
                ->withInput()
                ->withErrors($validacao)
                ->with('error', "Erro ao atualizar.");
        }
    }
}
