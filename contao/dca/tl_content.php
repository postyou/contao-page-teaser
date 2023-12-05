<?php

declare(strict_types=1);

use Doctrine\DBAL\Platforms\MySQLPlatform;

$GLOBALS['TL_DCA']['tl_content']['palettes']['page_teasers'] = '
    {type_legend},type,headline;
    {teasers_legend},pages,showProtected,useChildPages;
    {image_legend},size,inheritPageImage;
    {template_legend:hide},customTpl,navigationTpl;
    {protected_legend:hide},protected;
    {expert_legend:hide},guests,cssID;
    {invisible_legend:hide},invisible,start,stop
';

$GLOBALS['TL_DCA']['tl_content']['fields']['pages'] = [
    'exclude' => true,
    'inputType' => 'pageTree',
    'foreignKey' => 'tl_page.title',
    'eval' => ['multiple' => true, 'fieldType' => 'checkbox', 'mandatory' => true, 'isSortable' => true],
    'relation' => ['type' => 'hasMany', 'load' => 'lazy'],
    'sql' => ['type' => 'blob', 'notnull' => false, 'length' => MySQLPlatform::LENGTH_LIMIT_BLOB],
];

$GLOBALS['TL_DCA']['tl_content']['fields']['inheritPageImage'] = [
    'exclude' => true,
    'inputType' => 'checkbox',
    'eval' => ['tl_class' => 'w50 m12'],
    'sql' => ['type' => 'string', 'length' => 1, 'fixed' => true, 'default' => ''],
];

$GLOBALS['TL_DCA']['tl_content']['fields']['useChildPages'] = [
    'exclude' => true,
    'inputType' => 'checkbox',
    'eval' => ['tl_class' => 'w50'],
    'sql' => ['type' => 'string', 'length' => 1, 'fixed' => true, 'default' => ''],
];

$GLOBALS['TL_DCA']['tl_content']['fields']['showProtected'] = [
    'exclude' => true,
    'inputType' => 'checkbox',
    'sql' => ['type' => 'string', 'length' => 1, 'fixed' => true, 'default' => ''],
];

$GLOBALS['TL_DCA']['tl_content']['fields']['navigationTpl'] = [
    'inputType' => 'select',
    'eval' => ['tl_class' => 'w50', 'includeBlankOption' => true, 'chosen' => true],
    'sql' => ['type' => 'string', 'length' => 64, 'default' => '', 'customSchemaOptions' => ['collation' => 'ascii_bin']],
];
