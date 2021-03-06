<li>
    <a href="{{ route('index') }}">
        <i class="fa fa-futbol-o sidebar-nav-icon"></i>
        <span class="sidebar-nav-mini-hide">Simulador</span>
    </a>
</li>
@if(config('app.bolaoDisponivel'))
    <li>
        <a href="{{ route('technical.bolao.index') }}">
            <i class="fa fa-futbol-o sidebar-nav-icon"></i>
            <span class="sidebar-nav-mini-hide">Bolão</span>
        </a>
    </li>
@endif
<li>
    <a href="javascript:" class="sidebar-nav-menu">
        <i class="fa fa-chevron-left sidebar-nav-indicator sidebar-nav-mini-hide"></i>
        <i class="fa fa-users sidebar-nav-icon"></i>
        <span class="sidebar-nav-mini-hide">Usuários</span>
    </a>
    <ul>
        <li>
            <a href="{{ route('technical.sellers.index') }}">
                Cambistas
            </a>
        </li>
        <li>
            <a href="{{ route('technical.managers.index') }}">
                Gerentes
            </a>
        </li>
        <li>
            <a href="{{ route('technical.companies.index') }}">
                Empresas
            </a>
        </li>
    </ul>
</li>
<li>
    <a href="{{ route('technical.balance.index') }}">
        <i class="fa fa-dollar sidebar-nav-icon"></i>
        <span class="sidebar-nav-mini-hide">Saldo</span>
    </a>
</li>
<li>
    <a href="{{ route('technical.cashier.index') }}">
        <i class="fa fa-television sidebar-nav-icon"></i>
        <span class="sidebar-nav-mini-hide">Acompanhamento</span>
    </a>
</li>
<li>
    <a href="{{ route('technical.quotations.index') }}">
        <i class="fa fa-calculator sidebar-nav-icon"></i>
        <span class="sidebar-nav-mini-hide">Cotações</span>
    </a>
</li>
<li>
    <a href="{{ route('technical.matches.index') }}">
        <i class="fa fa-list-alt sidebar-nav-icon"></i>
        <span class="sidebar-nav-mini-hide">Partidas</span>
    </a>
</li>
<li>
    <a href="{{ route('technical.results.index') }}">
        <i class="fa fa-keyboard-o sidebar-nav-icon"></i>
        <span class="sidebar-nav-mini-hide">Resultados</span>
    </a>
</li>
<li>
    <a href="javascript:" class="sidebar-nav-menu">
        <i class="fa fa-chevron-left sidebar-nav-indicator sidebar-nav-mini-hide"></i>
        <i class="fa fa-cogs sidebar-nav-icon"></i>
        <span class="sidebar-nav-mini-hide">Outros</span>
    </a>
    <ul>
        <li class="hide">
            <a href="{{ route('technical.expenses.index') }}">
                Gastos
            </a>
        </li>
        <li>
            <a href="{{ route('technical.quotations.categories.index') }}">
                Categ. cotações
            </a>
        </li>
        <li>
            <a href="{{ route('technical.leagues.index') }}">
                Ligas
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
