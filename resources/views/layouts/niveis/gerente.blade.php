<li class="active">
    <a class="sidebar-nav-menu">
        <i class="fa fa-chevron-left sidebar-nav-indicator sidebar-nav-mini-hide"></i>
        <i class="fa fa-dollar sidebar-nav-icon"></i>
        <span class="sidebar-nav-mini-hide">Saldo</span>
    </a>
    <ul>
        <li>
            <a>
                <i class="fa fa-money sidebar-nav-icon"></i>
                Limite: R$ {{ moneyBR(auth()->user()->limit) }}
            </a>
        </li>
        <li>
            <a>
                <i class="fa fa-database sidebar-nav-icon"></i>
                Saldo: R$ {{ moneyBR(auth()->user()->balance) }}
            </a>
        </li>
    </ul>
</li>
<li>
    <a href="{{ route('index') }}">
        <i class="fa fa-futbol-o sidebar-nav-icon"></i>
        <span class="sidebar-nav-mini-hide">Jogos</span>
    </a>
</li>
<li>
    <a href="{{ route('bets.validate') }}">
        <i class="fa fa-ticket sidebar-nav-icon"></i>
        <span class="sidebar-nav-mini-hide">Validar PIN</span>
    </a>
</li>
<li>
    <a href="{{ route('bets.reprints') }}">
        <i class="fa fa-print sidebar-nav-icon"></i>
        <span class="sidebar-nav-mini-hide">Segunda via</span>
    </a>
</li>
<li>
    <a href="{{ route('manager.cashier.summary') }}">
        <i class="fa fa-television sidebar-nav-icon"></i>
        <span class="sidebar-nav-mini-hide">Acompanhamento</span>
    </a>
</li>
<li class="hide">
    <a href="{{ route('manager.financial.summary') }}">
        <i class="fa fa-calculator sidebar-nav-icon"></i>
        <span class="sidebar-nav-mini-hide">Financeiro</span>
    </a>
</li>
<li>
    <a href="{{ route('rules') }}">
        <i class="fa fa-file-text-o sidebar-nav-icon"></i>
        <span class="sidebar-nav-mini-hide">Regras</span>
    </a>
</li>
<li>
    <a href="{{ route('leagues.toprint') }}">
        <i class="fa fa-table sidebar-nav-icon"></i>
        <span class="sidebar-nav-mini-hide">Tabelas</span>
    </a>
</li>
<li class="hide">
    <a href="{{ route('results') }}">
        <i class="fa fa-android sidebar-nav-icon"></i>
        <span class="sidebar-nav-mini-hide">Resultados</span>
    </a>
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
<li class="sidebar-separator">
    <span class="sidebar-nav-mini-hide">Gerência</span>
</li>
<li>
    <a href="{{ route('manager.sellers.index') }}">
        <i class="fa fa-users sidebar-nav-icon"></i>
        <span class="sidebar-nav-mini-hide">Operadores</span>
    </a>
</li>
@if(env('MANAGER_SEND_BALANCE', false))
    <li>
        <a href="{{ route('manager.balance.index') }}">
            <i class="fa fa-dollar sidebar-nav-icon"></i>
            <span class="sidebar-nav-mini-hide">Saldo</span>
        </a>
    </li>
@endif
<li>
    <a href="{{ route('manager.cashier.monitoring') }}">
        <i class="fa fa-television sidebar-nav-icon"></i>
        <span class="sidebar-nav-mini-hide">Acompanhamento</span>
    </a>
</li>
<li>
    <a href="{{ route('manager.financial.general') }}">
        <i class="fa fa-calculator sidebar-nav-icon"></i>
        <span class="sidebar-nav-mini-hide">Financeiro</span>
    </a>
</li>
@if(env('MANAGER_SEND_PAYMENT', true))
    <li>
        <a href="{{ route('manager.payments.index') }}">
            <i class="fa fa-share sidebar-nav-icon"></i>
            <span class="sidebar-nav-mini-hide">Depósitos</span>
        </a>
    </li>
@endif
