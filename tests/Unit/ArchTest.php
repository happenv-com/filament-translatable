<?php

declare(strict_types=1);

arch()->preset()->php();

arch()->preset()->security();

arch('no debugging calls')
    ->expect(['dd', 'dump', 'ray', 'var_dump'])
    ->not->toBeUsed();
