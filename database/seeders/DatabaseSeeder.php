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

        // Cria ou atualiza o usuário atendente
        $agent = User::updateOrCreate(
            ['email' => 'atendimento@claro.com.br'],
            [
                'name' => 'Lucas R.',
                'password' => Hash::make('Senha12345'),
                'role' => 'atendente',
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
    }
}
