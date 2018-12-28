<?php
namespace components\Traits;

use Illuminate\Pagination\LengthAwarePaginator as Paginator;
use Illuminate\Support\Collection;

/**
 * Trait PaginatorTrait
 * @package components\Traits
 */
trait PaginatorTrait
{

    /**
     * @param $items
     * @param int $perPage
     * @param null $page
     * @param array $options
     * @return Paginator
     */
    protected function paginate($items, $perPage = 15, $page = null, $options = [])
    {
        $page = $page ?: (Paginator::resolveCurrentPage() ?: 1);
        $items = $items instanceof Collection ? $items : Collection::make($items);
        return new Paginator($items->forPage($page, $perPage), $items->count(), $perPage, $page, $options);
    }

    /**
     * @param $data
     * @return array
     */
    protected function makeElements($data)
    {
        $window = \Illuminate\Pagination\UrlWindow::make($data, 3);

        return array_filter([
            $window['first'],
            is_array($window['slider']) ? '...' : null,
            $window['slider'],
            is_array($window['last']) ? '...' : null,
            $window['last'],
        ]);
    }

    /**
     * @param $request
     */
    protected function paginatorResolver($request = false)
    {
        $request = $request ?: $this->app->request;

        Paginator::currentPathResolver(function () use ($request) {
            return $request->getResourceUri();
        });

        Paginator::currentPageResolver(function ($pageName = 'page') use ($request) {
            return $request->get($pageName, 1);
        });
    }

}