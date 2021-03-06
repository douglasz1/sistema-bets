<li class="active">
    <a>
        <i class="fa fa-dollar sidebar-nav-icon"></i>
        <span class="sidebar-nav-mini-hide">
            Saldo: R$
            {{ moneyBR(auth()->user()->balance) }}
        </span>
    </a>
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
    <a href="{{ route('seller.clients.index') }}">
        <i class="fa fa-user-o sidebar-nav-icon"></i>
        <span class="sidebar-nav-mini-hide">Gerenciar clientes</span>
    </a>
</li>
<li>
    <a href="{{ route('bets.reprints') }}">
        <i class="fa fa-print sidebar-nav-icon"></i>
        <span class="sidebar-nav-mini-hide">Segunda via</span>
    </a>
</li>
<li>
    <a href="{{ route('seller.cashier.index') }}">
        <i class="fa fa-television sidebar-nav-icon"></i>
        <span class="sidebar-nav-mini-hide">Bilhetes</span>
    </a>
</li>
<li>
    <a href="{{ route('seller.financial.index') }}">
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
