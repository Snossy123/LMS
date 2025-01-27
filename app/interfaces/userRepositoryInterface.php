<?php

namespace App\Interfaces;

interface userRepositoryInterface
{
    public function attempt(array $credentials):bool;
}
