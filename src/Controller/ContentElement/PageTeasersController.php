<?php

declare(strict_types=1);

namespace Postyou\ContaoPageTeaserBundle\Controller\ContentElement;

use Contao\ContentModel;
use Contao\CoreBundle\Controller\ContentElement\AbstractContentElementController;
use Contao\CoreBundle\DependencyInjection\Attribute\AsContentElement;
use Contao\Model\Collection;
use Contao\PageModel;
use Contao\StringUtil;
use Contao\Template;
use Postyou\ContaoPageTeaserBundle\Service\PageTeaserService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

#[AsContentElement(category: 'links')]
class PageTeasersController extends AbstractContentElementController
{
    public function __construct(
        protected readonly PageTeaserService $pageTeaserService,
    ) {
    }

    protected function getResponse(Template $template, ContentModel $model, Request $request): Response
    {
        /** @var array<int> $ids */
        $ids = StringUtil::deserialize($model->pages, true);

        // Get all active pages and also include root pages if the language is added to the URL
        $pageModels = PageModel::findPublishedRegularByIds($ids, ['includeRoot' => true]);

        // Return if there are no pages
        if (!$pageModels instanceof Collection) {
            return new Response();
        }

        $template->data = [
            'items' => $this->pageTeaserService->prepare($pageModels, $model),
            'inheritPageImage' => $model->inheritPageImage,
            'imgSize' => $model->size,
        ];

        return $template->getResponse();
    }
}
