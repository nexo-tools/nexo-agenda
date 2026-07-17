<?php

// Minimal Spanish validation messages for the rules the app uses today.
// The full generated set (laravel-lang + own strings) lands in the i18n phase.
return [

    'accepted' => 'Debes aceptar :attribute.',
    'confirmed' => 'La confirmación de :attribute no coincide.',
    'date' => ':attribute no es una fecha válida.',
    'email' => ':attribute debe ser un email válido.',
    'exists' => 'El valor de :attribute no es válido.',
    'in' => 'El valor de :attribute no es válido.',
    'integer' => ':attribute debe ser un número entero.',
    'lowercase' => ':attribute debe estar en minúsculas.',
    'max' => [
        'numeric' => ':attribute no debe ser mayor que :max.',
        'string' => ':attribute no debe superar los :max caracteres.',
    ],
    'min' => [
        'numeric' => ':attribute debe ser al menos :min.',
        'string' => ':attribute debe tener al menos :min caracteres.',
    ],
    'numeric' => ':attribute debe ser un número.',
    'required' => 'El campo :attribute es obligatorio.',
    'string' => ':attribute debe ser texto.',
    'unique' => ':attribute ya está en uso.',

    'attributes' => [
        'name' => 'nombre',
        'email' => 'email',
        'password' => 'contraseña',
        'business_name' => 'nombre del negocio',
        'category' => 'rubro',
        'city' => 'ciudad',
        'whatsapp_phone' => 'WhatsApp',
    ],

];
