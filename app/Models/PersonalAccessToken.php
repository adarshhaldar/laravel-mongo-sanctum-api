<?php

namespace App\Models;

use MongoDB\Laravel\Eloquent\DocumentModel;
use Laravel\Sanctum\PersonalAccessToken as SanctumPersonalAccessToken;


class PersonalAccessToken extends SanctumPersonalAccessToken
{
    use DocumentModel;

    protected $connection = 'mongodb';
    protected $table = 'personal_access_tokens';
    protected $primaryKey = '_id';
    protected $keyType = 'string';
}
