<?php

return [
    'catalog' => [
        'Geral' => [
            'dashboard.view' => 'Visualizar painel',
        ],
        'Alunos' => [
            'members.view' => 'Visualizar alunos',
            'members.manage' => 'Cadastrar e editar alunos',
        ],
        'Treinos' => [
            'workouts.view' => 'Visualizar treinos',
            'workouts.manage' => 'Criar, editar e remover treinos',
            'exercises.manage' => 'Gerenciar exercícios e grupos musculares',
        ],
        'Loja' => [
            'shop.view' => 'Visualizar loja',
            'shop.order' => 'Realizar pedidos',
            'shop.manage' => 'Gerenciar produtos',
        ],
        'Financeiro' => [
            'billing.view-own' => 'Visualizar as próprias mensalidades',
            'plans.manage' => 'Gerenciar planos',
            'memberships.manage' => 'Gerenciar matrículas',
            'billing.manage' => 'Gerenciar cobranças e pagamentos',
        ],
        'Administração' => [
            'users.manage' => 'Gerenciar usuários',
            'permissions.manage' => 'Gerenciar grupos de permissões',
        ],
    ],
    'defaults' => [
        'admin' => ['*'],
        'trainer' => ['dashboard.view', 'members.view', 'members.manage', 'workouts.view', 'workouts.manage', 'exercises.manage', 'shop.view'],
        'member' => ['dashboard.view', 'workouts.view', 'shop.view', 'shop.order', 'billing.view-own'],
    ],
];
