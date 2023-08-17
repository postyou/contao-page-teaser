<?php

declare(strict_types=1);

namespace Postyou\ContaoPageTeaserBundle\Service;

use Contao\ContentModel;
use Contao\CoreBundle\Image\Studio\Studio;
use Contao\CoreBundle\Security\ContaoCorePermissions;
use Contao\Model\Collection;
use Contao\ModuleModel;
use Contao\PageModel;
use Contao\StringUtil;
use Symfony\Component\Routing\Exception\ExceptionInterface;
use Symfony\Component\Security\Core\Security;
use Terminal42\PageimageBundle\PageimageHelper;

class PageTeaserService
{
    public function __construct(
        protected readonly PageimageHelper $pageImageHelper,
        protected readonly Studio $studio,
        protected readonly Security $security,
    ) {
    }

    /**
     * @param Collection<PageModel> $pages
     *
     * @return array<array<string, mixed>>
     */
    public function prepare(Collection $pages, ContentModel|ModuleModel $model): array
    {
        $items = [];

        foreach ($pages as $pageModel) {
            $pageModel->loadDetails();

            // PageModel->groups is an array after calling loadDetails()
            if (!$pageModel->protected || $model->showProtected || $this->security->isGranted(ContaoCorePermissions::MEMBER_IN_GROUPS, $pageModel->groups)) {
                // Get href
                $href = $this->getHref($pageModel);

                if (null === $href) {
                    continue;
                }

                $cssClass = ($pageModel->protected ? 'protected ' : '') . $pageModel->cssClass;
                $row = $pageModel->row();

                $row['title'] = StringUtil::specialchars($pageModel->title, true);
                $row['pageTitle'] = StringUtil::specialchars($pageModel->pageTitle, true);
                $row['class'] = $cssClass;
                $row['link'] = $pageModel->title;
                $row['href'] = $href;
                $row['rel'] = '';
                $row['target'] = '';
                $row['description'] = str_replace(["\n", "\r"], [' ', ''], (string) $pageModel->description);

                $rel = [];

                // Override the link target
                if ('redirect' === $pageModel->type && $pageModel->target) {
                    $rel[] = 'noreferrer';
                    $rel[] = 'noopener';

                    $row['target'] = ' target="_blank"';
                }

                // Set the rel attribute
                if ($rel !== []) {
                    $row['rel'] = ' rel="'.implode(' ', $rel).'"';
                }

                $items[] = $row;
            }
        }

        return $items;
    }

    protected function getHref(PageModel $pageModel): ?string
    {
        switch ($pageModel->type) {
            case 'redirect':
                $href = $pageModel->url;

                break;

            case 'root':
                // Overwrite the alias to link to the empty URL or language URL
                $pageModel->alias = 'index';
                $href = $pageModel->getFrontendUrl();

                break;

            case 'forward':
                if ($pageModel->jumpTo) {
                    $nextPage = PageModel::findPublishedById((int) $pageModel->jumpTo);
                } else {
                    $nextPage = PageModel::findFirstPublishedRegularByPid((int) $pageModel->id);
                }

                if ($nextPage instanceof PageModel) {
                    $href = $nextPage->getFrontendUrl();

                    break;
                }
                // no break

            default:
                try {
                    $href = $pageModel->getFrontendUrl();
                } catch (ExceptionInterface) {
                    $href = null;
                }

                break;
        }

        return $href;
    }
}
