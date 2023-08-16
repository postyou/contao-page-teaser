<?php

declare(strict_types=1);

use Contao\CoreBundle\DataContainer\PaletteManipulator;

PaletteManipulator::create()
    ->addLegend('teasers_legend', 'nav_legend')
    ->addField('imgSize', 'teasers_legend', PaletteManipulator::POSITION_APPEND)
    ->addField('inheritPageImage', 'teasers_legend', PaletteManipulator::POSITION_APPEND)
    ->applyToPalette('customnav', 'tl_module')
    ->applyToPalette('navigation', 'tl_module')
;
