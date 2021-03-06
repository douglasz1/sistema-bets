<li>
    <a href="{{ route('index') }}">
        <i class="fa fa-futbol-o sidebar-nav-icon"></i>
        <span class="sidebar-nav-mini-hide">Simulador</span>
    </a>
</li>
<li>
    <a href="{{ route('supervisor.sellers.index') }}">
        <i class="fa fa-users sidebar-nav-icon"></i>
        <span class="sidebar-nav-mini-hide">Usuários</span>
    </a>
</li>
<li>
    <a href="{{ route('supervisor.balance.index') }}">
        <i class="fa fa-dollar sidebar-nav-icon"></i>
        <span class="sidebar-nav-mini-hide">Saldo</span>
    </a>
</li>
<li>
    <a href="{{ route('supervisor.cashier.index') }}">
        <i class="fa fa-television sidebar-nav-icon"></i>
        <span class="sidebar-nav-mini-hide">Acompanhamento</span>
    </a>
</li>
<li>
    <a href="{{ route('supervisor.financial.index') }}">
        <i class="fa fa-fax sidebar-nav-icon"></i>
        <span class="sidebar-nav-mini-hide">Financeiro</span>
    </a>
</li>
<li>
    <a href="{{ route('supervisor.reports.analitico.index') }}">
        <i class="fa fa-files-o sidebar-nav-icon"></i>
        <span class="sidebar-nav-mini-hide">Relatório Analítico</span>
    </a>
</li>
<li>
    <a href="{{ route('rules') }}">
        <i class="fa fa-file-text-o sidebar-nav-icon"></i>
        <span class="sidebar-nav-mini-hide">Regras</span>
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
