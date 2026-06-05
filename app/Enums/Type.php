<?php

namespace App\Enums;

enum Type: int
{
    CASE STORE = 1;
    CASE WAREHOUSE = 2;

    CASE IN = 3;
    CASE OUT = 4;
    CASE TRANSFER = 5;
    CASE RETURN = 6;
}
