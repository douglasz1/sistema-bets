@extends('layouts.admin')

@section('content')
    <div class="block full">
        <div class="block-title">
            <h2><i class="fa fa-bars"></i> Resultados de partidas</h2>
        </div>

        <div class="table-responsive">
            <table class="table table-vcenter table-condensed table-striped table-hover">
                <thead>
                <tr>
                    <td width="120px"><b>Data</b></td>
                    <td><b>Liga</b></td>
                    <td><b>Partida</b></td>
                    <td width="90px"><b>1&deg; tempo</b></td>
                    <td width="90px"><b>2&deg; tempo</b></td>
                    <td width="90px"><b>Final</b></td>
                </tr>
                </thead>
                <tbody>
                @forelse($results as $result)
                    <tr>
                        <td>
                            {{ $result->match_date->format('d/m H:i') }}
                        </td>
                        <td>{{ $result->league->name }}</td>
                        <td>
                            {{ $result->home_team }}
                            <br>
                            {{ $result->away_team }}
                        </td>
                        <td>
                            {{ $result->home_1st . ' x ' . $result->away_1st }}
                        </td>
                        <td>
                            {{ $result->home_2nd . ' x ' . $result->away_2nd }}
                        </td>
                        <td>
                            {{ $result->home_final . ' x ' . $result->away_final }}
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center">
                            <strong>Nenhum resultado encontrado</strong>
                        </td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
