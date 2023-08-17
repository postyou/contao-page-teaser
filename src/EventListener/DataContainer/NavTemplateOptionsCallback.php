<?php

declare(strict_types=1);

namespace Postyou\ContaoPageTeaserBundle\EventListener\DataContainer;

use Contao\Controller;
use Contao\CoreBundle\DependencyInjection\Attribute\AsCallback;

#[AsCallback('tl_content', 'fields.navigationTpl.options')]
class NavTemplateOptionsCallback
{
    /**
     * @return string[]
     */
    public function __invoke(): array
    {
        return Controller::getTemplateGroup('nav_');
    }
}
