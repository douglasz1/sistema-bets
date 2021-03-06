<li>
    <a href="{{ route('index') }}">
        <i class="fa fa-futbol-o sidebar-nav-icon"></i>
        <span class="sidebar-nav-mini-hide">Simulador</span>
    </a>
</li>
@if(config('app.bolaoDisponivel'))
    <li>
        <a href="{{ route('admin.bolao.index') }}">
            <i class="fa fa-futbol-o sidebar-nav-icon"></i>
            <span class="sidebar-nav-mini-hide">Bolão</span>
        </a>
    </li>
@endif
<li>
    <a href="{{ route('admin.sellers.index') }}">
        <i class="fa fa-users sidebar-nav-icon"></i>
        <span class="sidebar-nav-mini-hide">Usuários</span>
    </a>
</li>
<li>
    <a href="{{ route('admin.balance.index') }}">
        <i class="fa fa-dollar sidebar-nav-icon"></i>
        <span class="sidebar-nav-mini-hide">Saldo</span>
    </a>
</li>
<li>
    <a href="{{ route('admin.cashier.index') }}">
        <i class="fa fa-television sidebar-nav-icon"></i>
        <span class="sidebar-nav-mini-hide">Acompanhamento</span>
    </a>
</li>
<li>
    <a href="{{ route('admin.financial.index') }}">
        <i class="fa fa-fax sidebar-nav-icon"></i>
        <span class="sidebar-nav-mini-hide">Financeiro</span>
    </a>
</li>
<li>
    <a href="{{ route('admin.reports.analitico.index') }}">
        <i class="fa fa-files-o sidebar-nav-icon"></i>
        <span class="sidebar-nav-mini-hide">Relatório Analítico</span>
    </a>
</li>
<li>
    <a href="{{ route('admin.quotations.index') }}">
        <i class="fa fa-list-alt sidebar-nav-icon"></i>
        <span class="sidebar-nav-mini-hide">Partidas</span>
    </a>
</li>
<li>
    <a href="{{ route('admin.results.index') }}">
        <i class="fa fa-keyboard-o sidebar-nav-icon"></i>
        <span class="sidebar-nav-mini-hide">Resultados</span>
    </a>
</li>
<li>
    <a href="{{ route('admin.goals.index') }}">
        <i class="fa fa-line-chart sidebar-nav-icon"></i>
        <span class="sidebar-nav-mini-hide">Meta</span>
    </a>
</li>
<li>
    <a href="{{ route('rules') }}">
        <i class="fa fa-file-text-o sidebar-nav-icon"></i>
        <span class="sidebar-nav-mini-hide">Regras</span>
    </a>
</li>
<li>
    <a href="javascript:" class="sidebar-nav-menu">
        <i class="fa fa-chevron-left sidebar-nav-indicator sidebar-nav-mini-hide"></i>
        <i class="fa fa-cogs sidebar-nav-icon"></i>
        <span class="sidebar-nav-mini-hide">Outros</span>
    </a>
    <ul>
        <li>
            <a href="{{ route('admin.jogos-manuais.index') }}">
                Jogos Manuais
            </a>
        </li>
        <li>
            <a href="{{ route('admin.auditoria.index') }}">
                Auditoria
            </a>
        </li>
        <li>
            <a href="{{ route('admin.expenses.index') }}">
                Gastos
            </a>
        </li>
        <li>
            <a href="{{ route('admin.payments.index') }}">
                Depósitos
            </a>
        </li>
        <li>
            <a href="{{ route('admin.leagues.index') }}">
                Ligas
            </a>
        </li>
        <li>
            <a href="{{ route('admin.quotations.categories.index') }}">
                Categ. cotações
            </a>
        </li>
        <li>
            <a href="{{ route('admin.cancelar-palpites.index') }}">
                Cancelar palpites
            </a>
        </li>
    </ul>
</li>
<li>
    <a href="{{ route('alterarSenha') }}">
        <i class="fa fa-key sidebar-nav-icon"></i>
        <span class="sidebar-nav-mini-hide">Alterar senha</span>
    </a>
</li>
<li>
    <a href="{{ route('download') }}">
        <i class="fa fa-android sidebar-nav-icon"></i>
        <span class="sidebar-nav-mini-hide">Baixar app</span>
    </a>
</li>
<li>
    <a href="{{ url('/logout') }}">
        <i class="fa fa-key sidebar-nav-icon"></i>
        <span class="sidebar-nav-mini-hide">Sair</span>
    </a>
</li>
