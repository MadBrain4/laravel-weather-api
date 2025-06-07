<?php
    return [
        'roles' => [
            'superadmin',
            'admin',
            'user',
        ],

        // Roles que pueden ser asignados por superadmin
        'assignable' => [
            'admin',
            'user',
        ],
    ];