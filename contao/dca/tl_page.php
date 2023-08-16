<?php

declare(strict_types=1);

use Contao\CoreBundle\DataContainer\PaletteManipulator;
use Doctrine\DBAL\Platforms\MySQLPlatform;

$pm = PaletteManipulator::create()
    ->addLegend('teaser_legend', 'meta_legend', PaletteManipulator::POSITION_AFTER, true)
    ->addField('teaser', 'teaser_legend', PaletteManipulator::POSITION_APPEND)
;

foreach ($GLOBALS['TL_DCA']['tl_page']['palettes'] as $name => $palette) {
    if ('__selector__' === $name) {
        continue;
    }

    $pm->applyToPalette($name, 'tl_page');
}

unset($name, $palette, $pm);

$GLOBALS['TL_DCA']['tl_page']['fields']['teaser'] = [
    'exclude' => true,
    'search' => true,
    'inputType' => 'textarea',
    'eval' => ['rte' => 'tinyMCE', 'tl_class' => 'clr', 'helpwizard' => true],
    'explanation' => 'insertTags',
    'sql' => ['type' => 'text', 'notnull' => false, 'length' => MySQLPlatform::LENGTH_LIMIT_TEXT],
];
