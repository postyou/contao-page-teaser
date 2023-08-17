<?php

declare(strict_types=1);

namespace Postyou\ContaoPageTeaserBundle\EventListener;

use Contao\CoreBundle\DependencyInjection\Attribute\AsHook;
use Contao\CoreBundle\File\Metadata;
use Contao\CoreBundle\Image\Studio\Studio;
use Contao\FrontendTemplate;
use Contao\Module;
use Contao\PageModel;
use Contao\Template;
use Terminal42\PageimageBundle\PageimageHelper;

#[AsHook('parseTemplate')]
class ParseTemplateListener
{
    public function __construct(
        protected readonly PageimageHelper $pageImageHelper,
        protected readonly Studio $studio,
    ) {
    }

    public function __invoke(Template $template): void
    {
        if (!$template instanceof FrontendTemplate) {
            return;
        }

        if ('ce_page_teasers' !== $template->getName() && !str_starts_with($template->getName(), 'nav_')) {
            return;
        }

        $template->getTeaserImage = function (int $pageId) use ($template): ?array {
            $pageModel = PageModel::findById($pageId);

            if (!$pageModel instanceof PageModel) {
                return null;
            }

            if ($template->module instanceof Module) {
                $inherit = $template->module->inheritPageImage;
                $imgSize = $template->module->imgSize;
            } else {
                $inherit = $template->inheritPageImage;
                $imgSize = $template->imgSize;
            }

            $image = $this->pageImageHelper->getOneByPageAndIndex($pageModel, inherit: (bool) $inherit);

            if (null === $image) {
                return null;
            }

            $figure = $this->studio->createFigureBuilder()
                ->setSize($imgSize) // @phpstan-ignore-line
            ;

            if ($pageModel->pageImageOverwriteMeta) {
                $figure->setOverwriteMetadata(new Metadata([
                    Metadata::VALUE_ALT => $image['alt'],
                    Metadata::VALUE_TITLE => $image['title'],
                    Metadata::VALUE_URL => $image['href'],
                ]));
            }

            return $figure->from($image['uuid'])->build()->getLegacyTemplateData();
        };
    }
}
