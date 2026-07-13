<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Solicitation;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        \Illuminate\Support\Facades\Schema::disableForeignKeyConstraints();

        // Cria ou atualiza o usuário admin
        User::updateOrCreate(
            ['email' => 'adminprisma@claro.com.br'],
            [
                'name' => 'Mauro Filho',
                'password' => Hash::make('Senha123'),
                'role' => 'admin',
                'email_verified_at' => now(),
            ]
        );

        // Cria ou atualiza o usuário administrador/cliente
        $user = User::updateOrCreate(
            ['email' => 'mauro@claro.com.br'],
            [
                'name' => 'Mauro Filho',
                'password' => Hash::make('Senha123456'),
                'role' => 'user',
                'email_verified_at' => now(),
            ]
        );

        // Trunca e Cria Perfis de Acesso
        \App\Models\AccessProfile::truncate();

        $ap1 = \App\Models\AccessProfile::create([
            'name' => 'ATENDENTE N1',
            'nivel_n1' => true,
            'nivel_n2' => false,
            'fila' => true,
        ]);

        $ap2 = \App\Models\AccessProfile::create([
            'name' => 'ATENDENTE N2',
            'nivel_n1' => false,
            'nivel_n2' => true,
            'fila' => true,
        ]);

        $ap3 = \App\Models\AccessProfile::create([
            'name' => 'ATENDENTE N1 + N2',
            'nivel_n1' => true,
            'nivel_n2' => true,
            'fila' => true,
        ]);

        $apPromocoes = \App\Models\AccessProfile::create([
            'name' => 'ATENDENTE N1 - PROMOÇÕES',
            'nivel_n1' => true,
            'nivel_n2' => false,
            'fila' => true,
        ]);

        $apTecnico = \App\Models\AccessProfile::create([
            'name' => 'TÉCNICO N2 - PROMOÇÕES',
            'nivel_n1' => false,
            'nivel_n2' => true,
            'fila' => true,
        ]);

        // Cria ou atualiza o usuário atendente
        $agent = User::updateOrCreate(
            ['email' => 'atendimento@claro.com.br'],
            [
                'name' => 'Lucas R.',
                'password' => Hash::make('Senha12345'),
                'role' => 'atendente',
                'access_profile_id' => $ap1->id,
                'phone' => '(31) 12345-6789',
                'login' => 'F123456',
                'status' => 'ativo',
                'email_verified_at' => now(),
            ]
        );

        // Cria ou atualiza outros usuários atendentes para o grid
        User::updateOrCreate(
            ['email' => 'jose@claro.com.br'],
            [
                'name' => 'JOSÉ DA SILVA',
                'password' => Hash::make('Senha123'),
                'role' => 'atendente',
                'access_profile_id' => $apPromocoes->id,
                'phone' => '(31) 12345-6789',
                'login' => 'F123456',
                'status' => 'ausente',
                'email_verified_at' => now(),
            ]
        );

        User::updateOrCreate(
            ['email' => 'joao@claro.com.br'],
            [
                'name' => 'JOÃO DA SILVA',
                'password' => Hash::make('Senha123'),
                'role' => 'atendente',
                'access_profile_id' => $apTecnico->id,
                'phone' => '(31) 12345-6789',
                'login' => 'F123456',
                'status' => 'ativo',
                'email_verified_at' => now(),
            ]
        );

        User::updateOrCreate(
            ['email' => 'jose.inativo@claro.com.br'],
            [
                'name' => 'JOSÉ DA SILVA',
                'password' => Hash::make('Senha123'),
                'role' => 'atendente',
                'access_profile_id' => $apPromocoes->id,
                'phone' => '(31) 12345-6789',
                'login' => 'F123456',
                'status' => 'inativo',
                'email_verified_at' => now(),
            ]
        );

        User::updateOrCreate(
            ['email' => 'carlos@claro.com.br'],
            [
                'name' => 'CARLOS DA SILVA',
                'password' => Hash::make('Senha123'),
                'role' => 'atendente',
                'access_profile_id' => $apPromocoes->id,
                'phone' => '(31) 12345-6789',
                'login' => 'F123456',
                'status' => 'ativo',
                'email_verified_at' => now(),
            ]
        );

        // Usuário Atendente N2
        User::updateOrCreate(
            ['email' => 'lucia@claro.com.br'],
            [
                'name' => 'Lúcia M.',
                'password' => Hash::make('Senha123'),
                'role' => 'atendente',
                'access_profile_id' => $ap2->id,
                'phone' => '(31) 12345-6789',
                'login' => 'F123456',
                'status' => 'ativo',
                'email_verified_at' => now(),
            ]
        );

        // Usuário Atendente N1 + N2
        User::updateOrCreate(
            ['email' => 'roberto@claro.com.br'],
            [
                'name' => 'Roberto G.',
                'password' => Hash::make('Senha123'),
                'role' => 'atendente',
                'access_profile_id' => $ap3->id,
                'phone' => '(31) 12345-6789',
                'login' => 'F123456',
                'status' => 'ativo',
                'email_verified_at' => now(),
            ]
        );

        // Limpa solicitações antigas antes de criar novas (garante idempotência)
        Solicitation::where('user_id', $user->id)->delete();

        // Solicitação 1
        Solicitation::create([
            'user_id' => $user->id,
            'title' => 'Promoção de Combo Multi não aparece no painel',
            'description' => 'Olá! A promoção de Combo Multi não está aparecendo no meu painel do Dashboard. Já tentei aplicar os filtros de Linha de Negócio mas mesmo assim o indicador não aparece. Outros colegas da minha regional conseguem visualizar normalmente, então não sei se é algo no meu acesso ou se estou fazendo algo errado na hora de filtrar.',
            'status' => 'aberta',
            'ticket_number' => '123456789',
            'created_at' => '2026-06-08 01:02:03',
        ]);

        // Solicitação 2
        Solicitation::create([
            'user_id' => $user->id,
            'title' => 'Promoção M-Play não constando nas metas',
            'description' => 'Boa tarde! Percebi que os indicadores da promoção M-Play não estão aparecendo na tela de Metas, mas o produto está ativo no meu canal. Já verifiquei os filtros de período e linha de negócio e mesmo assim ele não aparece. Gostaria de entender se isso é um problema de configuração ou se a promoção realmente não entra no cálculo das metas.',
            'status' => 'em_replica',
            'ticket_number' => '123456789',
            'created_at' => '2026-06-08 01:02:03',
        ]);

        // Solicitação 3
        Solicitation::create([
            'user_id' => $user->id,
            'title' => 'Dúvida sobre vigência da oferta Claro BOX',
            'description' => 'Oi, gostaria de saber até quando a oferta do Claro BOX está vigente para apresentar nos pontos de venda. Nas minhas últimas visitas alguns parceiros me questionaram sobre isso e fiquei na dúvida se a promoção ainda está ativa ou se já foi encerrada. Quero garantir que estou passando a informação correta para as lojas.',
            'status' => 'resolvida',
            'ticket_number' => '123456789',
            'created_at' => '2026-06-08 01:02:03',
        ]);

        // Seed Presets
        \App\Models\Preset::truncate();
        for ($i = 1; $i <= 6; $i++) {
            \App\Models\Preset::create([
                'shortcut' => '/Mensagem inicial',
                'title' => 'Mensagem Inicial ' . $i,
                'text' => "Olá, meu nome é [FIRSTNAME] e eu iniciarei seu atendimento.\n\nUm momento enquanto eu analiso as informações - qualquer dúvida é só me chamar, me mantenho à disposição.",
                'user_id' => null, // Global created by admin
            ]);
        }

        // Seed Tags
        \App\Models\Tag::truncate();
        $colors = ['#E27D18', '#DA291C', '#007BFF', '#28A745', '#6F42C1', '#17A2B8', '#FD7E14', '#E83E8C'];
        for ($i = 1; $i <= 8; $i++) {
            \App\Models\Tag::create([
                'name' => 'Tag ' . $i,
                'color' => $colors[$i - 1] ?? '#CCCCCC',
            ]);
        }

        // Seed Activity Logs
        \App\Models\ActivityLog::truncate();
        $carlos = \App\Models\User::where('email', 'carlos@claro.com.br')->first();
        $joao = \App\Models\User::where('email', 'joao@claro.com.br')->first();

        $logsData = [
            [
                'activity' => 'Exclusão',
                'type' => 'USUÁRIO',
                'user_id' => $carlos?->id,
                'user_name' => 'CARLOS DA SILVA',
                'pdv' => 'XPTO001',
                'details' => 'Exclusão do usuário ID 15 no sistema pelo painel administrativo.',
                'created_at' => '2026-04-02 09:30:00'
            ],
            [
                'activity' => 'Importação',
                'type' => 'INDICADORES',
                'user_id' => $joao?->id,
                'user_name' => 'JOÃO DA SILVA',
                'pdv' => 'XPTO001',
                'details' => 'Importação da planilha de indicadores comerciais referentes ao mês corrente.',
                'created_at' => '2026-04-02 09:30:00'
            ],
            [
                'activity' => 'Importação',
                'type' => 'DOCUMENTO',
                'user_id' => $carlos?->id,
                'user_name' => 'CARLOS DA SILVA',
                'pdv' => 'XPTO001',
                'details' => 'Importação de documento PDF de termo de aceite de parceria comercial.',
                'created_at' => '2026-04-02 09:30:00'
            ],
            [
                'activity' => 'Exclusão',
                'type' => 'FAVORITO',
                'user_id' => $joao?->id,
                'user_name' => 'JOÃO DA SILVA',
                'pdv' => 'XPTO001',
                'details' => 'Remoção do ponto de venda favorito ID 89 das preferências.',
                'created_at' => '2026-04-02 09:30:00'
            ],
            [
                'activity' => 'Importação',
                'type' => 'DOCUMENTO',
                'user_id' => $joao?->id,
                'user_name' => 'JOÃO DA SILVA',
                'pdv' => 'XPTO001',
                'details' => 'Importação da imagem do contrato social assinado em formato PNG.',
                'created_at' => '2026-04-02 09:30:00'
            ],
            [
                'activity' => 'Atualização',
                'type' => 'FAVORITOS',
                'user_id' => $carlos?->id,
                'user_name' => 'CARLOS DA SILVA',
                'pdv' => 'XPTO001',
                'details' => 'Atualização da lista de favoritos, ordenação e inclusão de nova categoria.',
                'created_at' => '2026-04-02 09:30:00'
            ],
            [
                'activity' => 'Atualização',
                'type' => 'PERFIL',
                'user_id' => $joao?->id,
                'user_name' => 'JOÃO DA SILVA',
                'pdv' => 'XPTO001',
                'details' => 'Atualização de foto de perfil e contato telefônico nas configurações do usuário.',
                'created_at' => '2026-04-02 09:30:00'
            ],
            [
                'activity' => 'Criação',
                'type' => 'PDV',
                'user_id' => $joao?->id,
                'user_name' => 'JOÃO DA SILVA',
                'pdv' => 'XPTO001',
                'details' => 'Criação do Ponto de Venda XPTO001 com endereço físico e telefone associados.',
                'created_at' => '2026-04-02 09:30:00'
            ],
            [
                'activity' => 'Exclusão',
                'type' => 'USUÁRIO',
                'user_id' => $carlos?->id,
                'user_name' => 'CARLOS DA SILVA',
                'pdv' => 'XPTO001',
                'details' => 'Exclusão de conta temporária de prestador de serviços ID 421.',
                'created_at' => '2026-04-02 09:30:00'
            ],
            [
                'activity' => 'Criação',
                'type' => 'FAVORITOS',
                'user_id' => $joao?->id,
                'user_name' => 'JOÃO DA SILVA',
                'pdv' => 'XPTO001',
                'details' => 'Criação do grupo de favoritos "Minhas Regionais" para acesso rápido.',
                'created_at' => '2026-04-02 09:30:00'
            ],
            [
                'activity' => 'Atualização',
                'type' => 'FAVORITOS',
                'user_id' => $joao?->id,
                'user_name' => 'JOÃO DA SILVA',
                'pdv' => 'XPTO001',
                'details' => 'Atualização das permissões de compartilhamento do grupo de favoritos.',
                'created_at' => '2026-04-02 09:30:00'
            ],
        ];

        $activities = ['Criação', 'Atualização', 'Exclusão', 'Importação'];
        $types = ['USUÁRIO', 'INDICADORES', 'DOCUMENTO', 'FAVORITO', 'PERFIL', 'PDV', 'FAVORITOS'];
        $usersList = [
            ['id' => $carlos?->id, 'name' => 'CARLOS DA SILVA'],
            ['id' => $joao?->id, 'name' => 'JOÃO DA SILVA']
        ];
        $pdvs = ['XPTO001', 'XPTO002', 'XPTO003'];

        \Illuminate\Support\Facades\DB::transaction(function () use ($logsData, $activities, $types, $usersList, $pdvs) {
            foreach ($logsData as $log) {
                \App\Models\ActivityLog::create($log);
            }

            for ($i = 12; $i <= 200; $i++) {
                $userRand = $usersList[array_rand($usersList)];
                $actRand = $activities[array_rand($activities)];
                $typeRand = $types[array_rand($types)];
                $pdvRand = $pdvs[array_rand($pdvs)];

                \App\Models\ActivityLog::create([
                    'activity' => $actRand,
                    'type' => $typeRand,
                    'user_id' => $userRand['id'],
                    'user_name' => $userRand['name'],
                    'pdv' => $pdvRand,
                    'details' => "Ação de {$actRand} no módulo de {$typeRand} para o PDV {$pdvRand}.",
                    'created_at' => now()->subMinutes($i * 5),
                ]);
            }
        });

        \Illuminate\Support\Facades\Schema::enableForeignKeyConstraints();
    }
}
