<?php

declare(strict_types=1);

namespace Postyou\ContaoPageTeaserBundle\Controller\ContentElement;

use Contao\ContentModel;
use Contao\CoreBundle\Controller\ContentElement\AbstractContentElementController;
use Contao\CoreBundle\DependencyInjection\Attribute\AsContentElement;
use Contao\CoreBundle\Routing\ScopeMatcher;
use Contao\Model\Collection;
use Contao\PageModel;
use Contao\StringUtil;
use Contao\Template;
use Postyou\ContaoPageTeaserBundle\Service\PageTeaserService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;

#[AsContentElement(category: 'links', template: 'ce_page_teasers')]
class PageTeasersController extends AbstractContentElementController
{
    public function __construct(
        protected readonly PageTeaserService $pageTeaserService,
        protected readonly RequestStack $requestStack,
        protected readonly ScopeMatcher $scopeMatcher
    ) {}

    protected function getResponse(Template $template, ContentModel $contentModel, Request $request): Response
    {
        /** @var array<int> $ids */
        $ids = StringUtil::deserialize($contentModel->pages, true);

        if ($contentModel->useChildPages) {
            $arrPages = [];
            foreach ($ids as $id) {
                $pageCollection = PageModel::findByPid($id, ['includeRoot' => true]);

                if ($pageCollection instanceof Collection) {
                    array_push($arrPages, ...$pageCollection->getModels());
                }
            }

            $pageModels = new Collection($arrPages, 'tl_page');
        } else {
            // Get all active pages and also include root pages if the language is added to the URL
            $pageModels = PageModel::findPublishedRegularByIds($ids, ['includeRoot' => true]);
        }

        // Return if there are no pages
        if (!$pageModels instanceof Collection) {
            return new Response();
        }

        $template->navigationTpl = 'nav_teaser';

        if ($contentModel->navigationTpl) {
            $request = $this->requestStack->getCurrentRequest();

            // Use the custom template unless it is a back end request
            if (!$request || !$this->scopeMatcher->isBackendRequest($request)) {
                $template->navigationTpl = $contentModel->navigationTpl;
            }
        }

        $currentPage = $this->getPageModel();

        $template->data = [
            'items' => $this->pageTeaserService->prepare($pageModels, $contentModel, $currentPage),
            'inheritPageImage' => $contentModel->inheritPageImage,
            'imgSize' => $contentModel->size,
        ];

        return $template->getResponse();
    }
}
