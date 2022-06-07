<?php

namespace Khamsolt\Orchid\Files\Enums;

enum Mode: string
{
    case SINGLE = 'single';
    case MULTIPLE = 'multiple';
    case RESTRICTED = 'restricted';
}
