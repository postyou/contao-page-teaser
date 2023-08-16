<?php

declare(strict_types=1);

namespace Postyou\ContaoPageTeaserBundle\ContaoManager;

use Contao\CoreBundle\ContaoCoreBundle;
use Contao\ManagerPlugin\Bundle\BundlePluginInterface;
use Contao\ManagerPlugin\Bundle\Config\BundleConfig;
use Contao\ManagerPlugin\Bundle\Parser\ParserInterface;
use Postyou\ContaoPageTeaserBundle\PostyouContaoPageTeaserBundle;

class Plugin implements BundlePluginInterface
{
    public function getBundles(ParserInterface $parser): array
    {
        return [
            BundleConfig::create(PostyouContaoPageTeaserBundle::class)
                ->setLoadAfter([ContaoCoreBundle::class]),
        ];
    }
}
