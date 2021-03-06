<?php


namespace Bets\Services\Bolao;


use Bets\Models\Bolao\Aposta;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class AcompanhamentoService
{
    /**
     * @var Aposta
     */
    private $aposta;
    private $dataFinal;
    private $dataInicial;
    private $vendedor;
    private $gerente;
    private $empresa;

    public function __construct(Aposta $aposta)
    {
        $this->aposta = $aposta;
    }

    public function buscar($carregarRelacionamentos = true)
    {
        $query = $this->aposta->newQuery();

        $dataInicial = $this->dataInicial ?? Carbon::now()->toDateString();
        $dataFinal = $this->dataFinal ?? Carbon::now()->toDateString();

        $query->whereDate('bolao_apostas.created_at', '>=', $dataInicial);
        $query->whereDate('bolao_apostas.created_at', '<=', $dataFinal);

        if (!is_null($this->empresa)) {
            $query->whereIn('bolao_apostas.empresa_id', $this->empresa);
        }

        if (!is_null($this->vendedor)) {
            $query->where('vendedor_id', $this->vendedor);
        }

        if (isset($this->gerente) && !isset($this->vendedor)) {
            $query->select('users.id', 'users.user_id', 'bolao_apostas.*')
                ->join('users', function ($join) {
                    $join->on('users.user_id', '=', DB::raw("{$this->gerente}"))
                        ->orOn('users.id', '=', DB::raw("{$this->gerente}"));
                })
                ->whereRaw("bolao_apostas.vendedor_id = users.id");
        }

        $query->orderByDesc('bolao_apostas.created_at');

        if ($carregarRelacionamentos) {
            $query->with([
                'vendedor' => function ($query) {
                    $query->select('name', 'id', 'user_id', 'company_id')
                        ->with([
                            'manager' => function ($query) {
                                $query->select('name', 'id')->with('roles');
                            },
                            'roles'
                        ]);
                },
                'vendedor.company' => function ($query) {
                    $query->select('name', 'id');
                }
            ]);
        }

        return $query->get();
    }

    public function pesquisar($apostaId)
    {
        $query = $this->aposta->newQuery();

        if (!is_null($this->empresa)) {
            $query->whereIn('empresa_id', $this->empresa);
        }

        $query->where('id', 'like', "{$apostaId}%");

        $query->with([
            'vendedor' => function ($query) {
                return $query->select('name', 'id', 'profit_percentage', 'manager_commission', 'user_id', 'company_id')
                    ->with([
                        'manager' => function ($query) {
                            return $query->select('name', 'id', 'profit_percentage', 'manager_commission')
                                ->with('roles');
                        },
                        'roles'
                    ]);
            },
            'vendedor.company'
        ]);

        $query->orderBy('created_at', 'DESC');

        return $query->take(10)->get();
    }

    /**
     * @param mixed $dataFinal
     * @return AcompanhamentoService
     */
    public function setDataFinal($dataFinal)
    {
        $this->dataFinal = $dataFinal;
        return $this;
    }

    /**
     * @param mixed $dataInicial
     * @return AcompanhamentoService
     */
    public function setDataInicial($dataInicial)
    {
        $this->dataInicial = $dataInicial;
        return $this;
    }

    /**
     * @param mixed $vendedor
     * @return AcompanhamentoService
     */
    public function setVendedor($vendedor)
    {
        $this->vendedor = $vendedor;
        return $this;
    }

    /**
     * @param mixed $gerente
     * @return AcompanhamentoService
     */
    public function setGerente($gerente)
    {
        $this->gerente = $gerente;
        return $this;
    }

    /**
     * @param mixed $empresa
     * @return AcompanhamentoService
     */
    public function setEmpresa($empresa)
    {
        if (!is_null($empresa)) {
            if (is_array($empresa)) {
                $this->empresa = $empresa;
            } else {
                $this->empresa = [$empresa];
            }
        }
        return $this;
    }
}
