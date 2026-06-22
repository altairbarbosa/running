<?php

return [
    'required' => 'O campo :attribute é obrigatório.',
    'email' => 'Informe um endereço de e-mail válido.',
    'confirmed' => 'A confirmação de :attribute não corresponde.',
    'current_password' => 'A senha atual está incorreta.',
    'string' => 'O campo :attribute deve ser um texto.',
    'boolean' => 'O campo :attribute deve ser verdadeiro ou falso.',
    'numeric' => 'O campo :attribute deve ser um número.',
    'integer' => 'O campo :attribute deve ser um número inteiro.',
    'date' => 'O campo :attribute deve ser uma data válida.',
    'before' => 'O campo :attribute deve ser anterior a :date.',
    'unique' => 'Este :attribute já está em uso.',
    'image' => 'O campo :attribute deve ser uma imagem válida.',
    'uploaded' => 'Não foi possível enviar o arquivo de :attribute.',
    'min' => [
        'string' => 'O campo :attribute deve ter pelo menos :min caracteres.',
        'numeric' => 'O campo :attribute deve ser no mínimo :min.',
    ],
    'max' => [
        'string' => 'O campo :attribute não pode ter mais de :max caracteres.',
        'numeric' => 'O campo :attribute não pode ser maior que :max.',
        'file' => 'O arquivo de :attribute não pode ter mais de :max quilobytes.',
    ],
    'password' => [
        'letters' => 'A senha deve conter ao menos uma letra.',
        'mixed' => 'A senha deve conter letras maiúsculas e minúsculas.',
        'numbers' => 'A senha deve conter ao menos um número.',
        'symbols' => 'A senha deve conter ao menos um símbolo.',
    ],
    'attributes' => [
        'name' => 'nome',
        'email' => 'e-mail',
        'password' => 'senha',
        'current_password' => 'senha atual',
        'password_confirmation' => 'confirmação da senha',
    ],
];
