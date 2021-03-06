<?php

use Bets\Models\League;
use Illuminate\Database\Seeder;

class LeaguesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $leagues = [
            [
                'id' => 1,
                'name' => 'Copa do Mundo'
            ],

            [
                'id' => 3,
                'name' => 'Inglaterra - Premier Lg.',
            ],

            [
                'id' => 4,
                'name' => 'França - Ligue 1',
            ],

            [
                'id' => 5,
                'name' => 'Alemanha - Bundesliga',
            ],

            [
                'id' => 6,
                'name' => 'Itália - Série A',
            ],

            [
                'id' => 7,
                'name' => 'Campeonato Espanhol',
            ],

            [
                'id' => 8,
                'name' => 'Liga dos Campeões',
            ],

            [
                'id' => 19,
                'name' => 'França - Ligue 2',
            ],

            [
                'id' => 21,
                'name' => 'Holanda - Eredivisie',
            ],

            [
                'id' => 26,
                'name' => 'Bélgica - Divisão 1A',
            ],

            [
                'id' => 27,
                'name' => 'Suíça - Superliga',
            ],

            [
                'id' => 28,
                'name' => 'Inglaterra - Championship',
            ],

            [
                'id' => 29,
                'name' => 'Alemanha - Bundesliga 2',
            ],

            [
                'id' => 30,
                'name' => 'Itália - Serie B',
            ],

            [
                'id' => 31,
                'name' => 'Espanha - Segunda',
            ],

            [
                'id' => 32,
                'name' => 'Portugal - Primeira Liga',
            ],

            [
                'id' => 33,
                'name' => 'Escócia - Premiership',
            ],

            [
                'id' => 35,
                'name' => 'Áustria - Bundesliga',
            ],

            [
                'id' => 36,
                'name' => 'França - Taça',
            ],

            [
                'id' => 37,
                'name' => 'Turquia - Superliga',
            ],

            [
                'id' => 38,
                'name' => 'Grécia - Superliga',
            ],

            [
                'id' => 40,
                'name' => 'França - Taça da Liga',
            ],

            [
                'id' => 44,
                'name' => 'Inglaterra - FA Cup',
            ],

            [
                'id' => 47,
                'name' => 'Espanha - Taça do Rei',
            ],

            [
                'id' => 50,
                'name' => 'Itália - Taça',
            ],

            [
                'id' => 53,
                'name' => 'França - National',
            ],

            [
                'id' => 55,
                'name' => 'Alemanha - Taça',
            ],

            [
                'id' => 79,
                'name' => 'Amigáveis - Selecções',
            ],

            [
                'id' => 88,
                'name' => 'Dinamarca - Superligaen',
            ],

            [
                'id' => 122,
                'name' => 'Camp. da Europa',
            ],

            [
                'id' => 124,
                'name' => 'Turquia - Taça',
            ],

            [
                'id' => 132,
                'name' => 'Áustria - Taça',
            ],

            [
                'id' => 133,
                'name' => 'Grécia - Taça',
            ],

            [
                'id' => 138,
                'name' => 'Portugal - Taça',
            ],

            [
                'id' => 145,
                'name' => 'Suécia - Allsvenskan',
            ],

            [
                'id' => 146,
                'name' => 'Finlândia - Veikkausliiga',
            ],

            [
                'id' => 147,
                'name' => 'Rússia - Premier Lg.',
            ],

            [
                'id' => 156,
                'name' => 'Noruega - Eliteserien',
            ],

            [
                'id' => 187,
                'name' => 'Brasileirão Serie A',
            ],

            [
                'id' => 197,
                'name' => 'Copa Libertadores',
            ],

            [
                'id' => 198,
                'name' => 'França - Supertaça',
            ],

            [
                'id' => 201,
                'name' => 'Copa do Brasil',
            ],

            [
                'id' => 213,
                'name' => 'Espanha - Supertaça',
            ],

            [
                'id' => 220,
                'name' => 'Rep. Checa - 1.ª Liga',
            ],

            [
                'id' => 221,
                'name' => 'Polónia - Ekstraklasa',
            ],

            [
                'id' => 228,
                'name' => 'Supertaça Europeia',
            ],

            [
                'id' => 229,
                'name' => 'Inglaterra - League 1',
            ],

            [
                'id' => 230,
                'name' => 'Argentina - Primera',
            ],

            [
                'id' => 432,
                'name' => 'México - Primeira',
            ],

            [
                'id' => 462,
                'name' => 'Suíça - Taça',
            ],

            [
                'id' => 475,
                'name' => 'Dinamarca - Taça',
            ],

            [
                'id' => 488,
                'name' => 'Rússia - Taça',
            ],

            [
                'id' => 492,
                'name' => 'Noruega - 1.ª Divisão',
            ],

            [
                'id' => 493,
                'name' => 'Suécia - Superettan',
            ],

            [
                'id' => 500,
                'name' => 'Bélgica - Divisão 1B',
            ],

            [
                'id' => 503,
                'name' => 'Japão - J-League',
            ],

            [
                'id' => 504,
                'name' => 'EUA - MLS',
            ],

            [
                'id' => 512,
                'name' => 'Irlanda - Premier League',
            ],

            [
                'id' => 523,
                'name' => 'Amigáveis - Clubes',
            ],

            [
                'id' => 531,
                'name' => 'Croácia - 1.ª Divisão',
            ],

            [
                'id' => 532,
                'name' => 'Alemanha - Region. Norte',
            ],

            [
                'id' => 536,
                'name' => 'Dinamarca - 1.ª Divisão',
            ],

            [
                'id' => 537,
                'name' => 'Inglaterra - League 2',
            ],

            [
                'id' => 539,
                'name' => 'Holanda - Eerste',
            ],

            [
                'id' => 548,
                'name' => 'Bulgária - 1.ª Liga',
            ],

            [
                'id' => 549,
                'name' => 'Eslovénia - 1.ª Divisão',
            ],

            [
                'id' => 551,
                'name' => 'Escócia - Championship',
            ],

            [
                'id' => 552,
                'name' => 'Roménia - Liga 1',
            ],

            [
                'id' => 553,
                'name' => 'Hungria - NB 1',
            ],

            [
                'id' => 559,
                'name' => 'Eslováquia - 1.ª Divisão',
            ],

            [
                'id' => 565,
                'name' => 'Suécia - Taça',
            ],

            [
                'id' => 786,
                'name' => 'Bulgária - Taça',
            ],

            [
                'id' => 939,
                'name' => 'Portugal - Segunda Liga',
            ],

            [
                'id' => 1146,
                'name' => 'Polónia - Taça',
            ],

            [
                'id' => 1238,
                'name' => 'Rep. Checa - Taça',
            ],

            [
                'id' => 1300,
                'name' => 'Eslováquia - Taça',
            ],

            [
                'id' => 1347,
                'name' => 'Ucrânia - Taça',
            ],

            [
                'id' => 1377,
                'name' => 'Chipre - Taça',
            ],

            [
                'id' => 1378,
                'name' => 'Jogos Olímpicos',
            ],

            [
                'id' => 1379,
                'name' => 'Jogos Olímpicos F.',
            ],

            [
                'id' => 1547,
                'name' => 'Alemanha - Supertaça',
            ],

            [
                'id' => 1570,
                'name' => 'Japão - Taça da Liga',
            ],

            [
                'id' => 1690,
                'name' => 'Suíça - Liga Challenge',
            ],

            [
                'id' => 1711,
                'name' => 'Alemanha - 3. Liga',
            ],

            [
                'id' => 1749,
                'name' => 'Polónia - 1.ª Liga',
            ],

            [
                'id' => 1772,
                'name' => 'Taça Sul-Americana',
            ],

            [
                'id' => 1874,
                'name' => 'Austrália - A-League',
            ],

            [
                'id' => 1875,
                'name' => 'Áustria - 1.ª Divisão',
            ],

            [
                'id' => 1877,
                'name' => 'Irl. do Norte - Premiership',
            ],

            [
                'id' => 1882,
                'name' => 'Escócia - League 1',
            ],

            [
                'id' => 1883,
                'name' => 'Escócia - League 2',
            ],

            [
                'id' => 2009,
                'name' => 'Camp. Mundo - Ásia',
            ],

            [
                'id' => 2010,
                'name' => 'Camp. Mundo - Europa',
            ],

            [
                'id' => 2011,
                'name' => 'Camp. Mundo - Am. N.',
            ],

            [
                'id' => 2012,
                'name' => 'Camp. do Mundo - Oceân.',
            ],

            [
                'id' => 2013,
                'name' => 'Camp. Mundo - A. Sul',
            ],

            [
                'id' => 2015,
                'name' => 'CONCACAF - Liga dos Campeões',
            ],

            [
                'id' => 2113,
                'name' => 'Camp. Europa Sub-21 - Qual.',
            ],

            [
                'id' => 2146,
                'name' => 'AFC - Liga dos Campeões',
            ],

            [
                'id' => 2175,
                'name' => 'Eslovénia - Taça',
            ],

            [
                'id' => 2262,
                'name' => 'Roménia - Taça',
            ],

            [
                'id' => 2486,
                'name' => 'Brasil - Carioca',
            ],

            [
                'id' => 2487,
                'name' => 'Brasil - Gaúcho',
            ],

            [
                'id' => 2488,
                'name' => 'Brasil - Mineiro',
            ],

            [
                'id' => 2489,
                'name' => 'Brasil - Paulista',
            ],

            [
                'id' => 2511,
                'name' => 'Israel - Taça',
            ],

            [
                'id' => 2597,
                'name' => 'Equador - Primera A',
            ],

            [
                'id' => 2598,
                'name' => 'Uruguai - Primera',
            ],

            [
                'id' => 2645,
                'name' => 'Gales - Premier League',
            ],

            [
                'id' => 2646,
                'name' => 'Colômbia - Copa',
            ],

            [
                'id' => 2745,
                'name' => 'Hungria - Taça',
            ],

            [
                'id' => 3102,
                'name' => 'Egipto - 1.ª Liga',
            ],

            [
                'id' => 3142,
                'name' => 'Marrocos - GNF I',
            ],

            [
                'id' => 3143,
                'name' => 'Argélia - 1.ª Divisão',
            ],

            [
                'id' => 3297,
                'name' => 'Bielorrússia - 1.ª Liga',
            ],

            [
                'id' => 3298,
                'name' => 'China - Super League',
            ],

            [
                'id' => 3299,
                'name' => 'Estónia - Meistriliiga',
            ],

            [
                'id' => 3300,
                'name' => 'Irlanda - 1.ª Divisão',
            ],

            [
                'id' => 3301,
                'name' => 'Japão - J-League 2',
            ],

            [
                'id' => 3302,
                'name' => 'Peru - 1.ª Division',
            ],

            [
                'id' => 3303,
                'name' => 'Rússia - 1.ª Divisão',
            ],

            [
                'id' => 3304,
                'name' => 'Coreia do Sul - K-League',
            ],

            [
                'id' => 3453,
                'name' => 'Liga Europa',
            ],

            [
                'id' => 3454,
                'name' => 'Brasileirão Serie B',
            ],

            [
                'id' => 3570,
                'name' => 'Camp. da Europa Sub-19 F.',
            ],

            [
                'id' => 3584,
                'name' => 'Chile - 1.ª Divisão',
            ],

            [
                'id' => 4135,
                'name' => 'Liga dos Campeões F.',
            ],

            [
                'id' => 4188,
                'name' => 'Inglaterra - EFL Trophy',
            ],

            [
                'id' => 4219,
                'name' => 'Rep. Checa - 2.ª Divisão',
            ],

            [
                'id' => 4414,
                'name' => 'Taça Asiática - Qual.',
            ],

            [
                'id' => 4624,
                'name' => 'Kuwait - Premier League',
            ],

            [
                'id' => 4625,
                'name' => 'Turquia - 1.ª Liga',
            ],

            [
                'id' => 4626,
                'name' => 'Catar - Star League',
            ],

            [
                'id' => 4627,
                'name' => 'África do Sul - 1.ª Liga',
            ],

            [
                'id' => 4666,
                'name' => 'EAU - 1.ª Liga',
            ],

            [
                'id' => 5036,
                'name' => 'Arábia Saudita - 1.ª Liga',
            ],

            [
                'id' => 5037,
                'name' => 'Inglaterra - National',
            ],

            [
                'id' => 5490,
                'name' => 'Jordânia - Pro League',
            ],

            [
                'id' => 5499,
                'name' => 'Argentina - Primera B Nacional',
            ],

            [
                'id' => 5500,
                'name' => 'Colômbia - Primera A',
            ],

            [
                'id' => 5537,
                'name' => 'Bahrein - League',
            ],

            [
                'id' => 5630,
                'name' => 'Amigáveis - Selecções F.',
            ],

            [
                'id' => 5631,
                'name' => 'Amigáveis - Sub-21',
            ],

            [
                'id' => 5632,
                'name' => 'Amigáveis - Sub-20',
            ],

            [
                'id' => 5637,
                'name' => 'Noruega - Supertaça',
            ],

            [
                'id' => 5642,
                'name' => 'Paraguai - 1.ª Division',
            ],

            [
                'id' => 5707,
                'name' => 'Bósnia - 1.ª Liga',
            ],

            [
                'id' => 5708,
                'name' => 'Costa Rica - 1.ª Division',
            ],

            [
                'id' => 5711,
                'name' => 'Venezuela - 1.ª División',
            ],

            [
                'id' => 5753,
                'name' => 'Bielorrússia - Taça',
            ],

            [
                'id' => 7159,
                'name' => 'Sérvia - Copa',
            ],

            [
                'id' => 7439,
                'name' => 'Singapura - S-League',
            ],

            [
                'id' => 7443,
                'name' => 'Vietname - V-League',
            ],

            [
                'id' => 7445,
                'name' => 'Roménia - 2.ª Liga',
            ],

            [
                'id' => 7556,
                'name' => 'Amigáveis - Sub-18',
            ],

            [
                'id' => 7846,
                'name' => 'Lituânia - A Lyga',
            ],

            [
                'id' => 7855,
                'name' => 'Austrália - Brisbane PL',
            ],

            [
                'id' => 7856,
                'name' => 'Austrália - NPL Victoria',
            ],

            [
                'id' => 7916,
                'name' => 'Camp. do Mundo F. - Europa',
            ],

            [
                'id' => 7978,
                'name' => 'Austrália - NPL NSW',
            ],

            [
                'id' => 8405,
                'name' => 'Azerbaijão - 1.ª Liga',
            ],

            [
                'id' => 8406,
                'name' => 'Moldávia - 1.ª Divisão',
            ],

            [
                'id' => 9948,
                'name' => 'Europeu F. Sub-17 - Qual.',
            ],

            [
                'id' => 14478,
                'name' => 'Malta - Prem. League',
            ],

            [
                'id' => 14531,
                'name' => 'Brasil - Cearense',
            ],

            [
                'id' => 14550,
                'name' => 'Brasil - Baiano',
            ],

            [
                'id' => 14677,
                'name' => 'Amigáveis - Sub-17',
            ],

            [
                'id' => 14738,
                'name' => 'Brasil - Pernambucano',
            ],

            [
                'id' => 14759,
                'name' => 'Torneio de Viareggio',
            ],

            [
                'id' => 14896,
                'name' => 'Macedónia - 1.ª Liga',
            ],

            [
                'id' => 15221,
                'name' => 'Camp. da Europa - Elite',
            ],

            [
                'id' => 15510,
                'name' => 'Uzebequistão - PFL',
            ],

            [
                'id' => 15578,
                'name' => 'Irão - Liga Pro',
            ],

            [
                'id' => 15820,
                'name' => 'Argentina - Copa',
            ],

            [
                'id' => 15823,
                'name' => 'Bolívia - Liga',
            ],

            [
                'id' => 15859,
                'name' => 'Albânia - Taça',
            ],

            [
                'id' => 15922,
                'name' => 'Itália - Taça Primavera',
            ],

            [
                'id' => 16133,
                'name' => 'Áustria - Regionalliga',
            ],

            [
                'id' => 16135,
                'name' => 'Bélgica - Liga de Reservas',
            ],

            [
                'id' => 16142,
                'name' => 'Chile - Taça',
            ],

            [
                'id' => 16143,
                'name' => 'Chile - 2.ª Divisão',
            ],

            [
                'id' => 16147,
                'name' => 'Dinamarca - 2.ª Divisão',
            ],

            [
                'id' => 16149,
                'name' => 'Estónia - 2.ª Divisão',
            ],

            [
                'id' => 16188,
                'name' => 'Coreia do S. - National Champ.',
            ],

            [
                'id' => 16189,
                'name' => 'Coreia do Sul - 2.ª Divisão',
            ],

            [
                'id' => 16509,
                'name' => 'Croácia - 2.ª Divisão',
            ],

            [
                'id' => 16541,
                'name' => 'Alemanha - Bundesliga Fem.',
            ],

            [
                'id' => 16553,
                'name' => 'Islândia - Taça da Liga',
            ],

            [
                'id' => 16582,
                'name' => 'Omã - 1.ª Divisão',
            ],

            [
                'id' => 16597,
                'name' => 'Suécia - 2.ª Div. Norra Götaland',
            ],

            [
                'id' => 16598,
                'name' => 'Suécia - 2.ª Div. Norra Svealand',
            ],

            [
                'id' => 16599,
                'name' => 'Suécia - 2.ª Div. Norrland',
            ],

            [
                'id' => 16604,
                'name' => 'Turquia - Lig 2 Beyaz Grup',
            ],

            [
                'id' => 16614,
                'name' => 'Inglaterra - Isthmian Premier',
            ],

            [
                'id' => 16692,
                'name' => 'Austrália - PL Oeste',
            ],

            [
                'id' => 17145,
                'name' => 'Inglaterra - National Norte',
            ],

            [
                'id' => 17146,
                'name' => 'Inglaterra - National Sul',
            ],

            [
                'id' => 17211,
                'name' => 'Alemanha - Liga Regional Nordeste',
            ],

            [
                'id' => 17212,
                'name' => 'Alemanha - Liga Regional Sudoeste',
            ],

            [
                'id' => 17213,
                'name' => 'Alemanha - Reg. Oeste',
            ],

            [
                'id' => 17214,
                'name' => 'Alemanha - Liga Regional Bavaria',
            ],

            [
                'id' => 17217,
                'name' => 'Austrália - PL Sul',
            ],

            [
                'id' => 17872,
                'name' => 'Rússia - 2.ª Divisão Sul',
            ],

            [
                'id' => 17873,
                'name' => 'Rússia - 2.ª Divisão Oeste',
            ],

            [
                'id' => 17988,
                'name' => 'Rep. Checa - Liga Sub-21',
            ],

            [
                'id' => 18853,
                'name' => 'Argentina - Primera B Metro',
            ],

            [
                'id' => 19119,
                'name' => 'Taça MX',
            ],

            [
                'id' => 19149,
                'name' => 'Colômbia - Primera B',
            ],

            [
                'id' => 19523,
                'name' => 'Austrália - Queensland PL',
            ],

            [
                'id' => 19619,
                'name' => 'Int. Champions Cup',
            ],

            [
                'id' => 19658,
                'name' => 'Brasileirão Serie C',
            ],

            [
                'id' => 19707,
                'name' => 'Itália - Lega Pro Girone A',
            ],

            [
                'id' => 19708,
                'name' => 'Itália - Lega Pro Girone C',
            ],

            [
                'id' => 19709,
                'name' => 'Itália - Lega Pro Girone B',
            ],

            [
                'id' => 20098,
                'name' => 'CONCACAF Sub-17',
            ],

            [
                'id' => 20166,
                'name' => 'Inglaterra - Northern Premier',
            ],

            [
                'id' => 20167,
                'name' => 'Inglaterra - Southern Premier',
            ],

            [
                'id' => 20169,
                'name' => 'Brasil - Copa do Nordeste',
            ],

            [
                'id' => 20674,
                'name' => 'Jamaica - Premier League',
            ],

            [
                'id' => 20701,
                'name' => 'Brasil - Primeira Liga',
            ],

            [
                'id' => 20759,
                'name' => 'Brasil - Paulista A3',
            ],

            [
                'id' => 20952,
                'name' => 'EUA - USL',
            ],

            [
                'id' => 21127,
                'name' => 'Inglaterra - Premier Lg. 2',
            ],

            [
                'id' => 21440,
                'name' => 'Brasil Sao Paulo Youth Cup',
            ],

            [
                'id' => 21530,
                'name' => 'Malásia Premier League',
            ],

            [
                'id' => 21767,
                'name' => 'Brasil - Campeonato Brasileiro F.',
            ],

            [
                'id' => 21793,
                'name' => 'Argentina - Liga de Reservas B',
            ],

            [
                'id' => 21794,
                'name' => 'Áustria - Landesliga',
            ],

            [
                'id' => 21795,
                'name' => 'Sérvia - Liga de Reservas',
            ],

            [
                'id' => 21839,
                'name' => 'Estónia - II Liiga',
            ],

            [
                'id' => 21840,
                'name' => 'Irl. do Norte - Intermediate Taça',
            ],
        ];

        foreach ($leagues as $league) {
            factory(League::class)->create($league);
        }
    }
}
