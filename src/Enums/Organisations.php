<?php

namespace App\Enums;

enum Organisations: string
{
    case CANTON = 'canton';
    case PRIVATE = 'private';
    case COMMERCIAL = 'commercial';
    case NGO = 'ngo';
}
