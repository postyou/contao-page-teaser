<?php

declare(strict_types=1);

namespace Postyou\ContaoPageTeaserBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;

class PostyouContaoPageTeaserBundle extends Bundle
{
    public function getPath(): string
    {
        return \dirname(__DIR__);
    }
}
