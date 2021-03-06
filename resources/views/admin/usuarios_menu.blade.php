<div class="btn-group btn-group-full p-b">
    <a class="btn btn-primary {{ Route::currentRouteName() === 'admin.sellers.index' ? 'active' : '' }}" href="{{ route('admin.sellers.index') }}">
        Operadores
    </a>
    <a class="btn btn-primary {{ Route::currentRouteName() === 'admin.managers.index' ? 'active' : '' }}" href="{{ route('admin.managers.index') }}">
        Gerentes
    </a>
    <a class="btn btn-primary {{ Route::currentRouteName() === 'admin.supervisors.index' ? 'active' : '' }}" href="{{ route('admin.supervisors.index') }}">
        Supervisores
    </a>
    <a class="btn btn-primary {{ Route::currentRouteName() === 'admin.companies.index' ? 'active' : '' }}" href="{{ route('admin.companies.index') }}">
        Empresas
    </a>
    <a class="btn btn-primary {{ Route::currentRouteName() === 'admin.technical.index' ? 'active' : '' }}" href="{{ route('admin.technical.index') }}">
        Técnicos
    </a>
</div>
