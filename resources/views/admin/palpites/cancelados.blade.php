@extends('layouts.admin')

@section('content')
    <div class="btn-group btn-group-full p-b">
        <a class="btn btn-primary" href="{{ route('admin.cancelar-palpites.index') }}">
            Cancelar palpites
        </a>
        <a class="btn btn-primary active" href="{{ route('admin.cancelar-palpites.cancelados') }}">
            Palpites cancelados
        </a>
    </div>
    <div class="table-responsive">
        <table class="table table-vcenter table-borderless table-striped table-hover">
            <thead>
            <tr>
                <td>
                    Cód. Aposta
                </td>
                <td>
                    <b>Liga</b>
                </td>
                <td>
                    <b>Partida</b>
                </td>
                <td>
                    <b>Data</b>
                </td>
                <td>
                    <b>Cancelado em</b>
                </td>
            </tr>
            </thead>
            <tbody>
            @forelse($palpites as $palpite)
                <tr>
                    <td>{{ $palpite->bet->id }}</td>
                    <td>{{ "{$palpite->match->league->country->name}: {$palpite->match->league->name}" }}</td>
                    <td>{{ "{$palpite->match->home_team} x {$palpite->match->away_team}" }}</td>
                    <td>{{ $palpite->match->match_date->format('d/m H:i') }}</td>
                    <td>{{ $palpite->updated_at->format('d/m H:i') }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="3" class="text-center">
                        <b>Nenhuma partida encontrada</b>
                    </td>
                </tr>
            @endforelse
            </tbody>
        </table>
    </div>
    {{ $palpites->links() }}
@endsection
