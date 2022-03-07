<?php

namespace App\Utils;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use function in_array;

class RequestSearchQuery
{
    /**
     * @var Request
     */
    private $request;

    /**
     * @var Builder
     */
    private $query;

    public function __construct(Request $request, Builder $query, $searchables = [])
    {
        $this->request = $request;
        $this->query = $query;

        $this->initializeQuery($searchables);
    }

    private function getLocalizedColumn(Model $model, $column)
    {
        if (property_exists($model, 'translatable') && in_array($column, $model->translatable, true)) {
            $locale = app()->getLocale();

            return "$column->$locale";
        }

        return $column;
    }

    /**
     * @param array $searchables
     */
    public function initializeQuery($searchables = [])
    {
        $model = $this->query->getModel();
        if ($column = $this->request->get('column')) {
            $this->query->orderBy(
                $this->getLocalizedColumn($model, $column),
                $this->request->get('direction') ?? 'asc'
            );
        }

        if ($search = $this->request->get('search')) {
            $this->query->where(function (Builder $query) use ($model, $searchables, $search) {
                foreach ($searchables as $key => $searchableColumn) {
                    $query->orWhere(
                        $this->getLocalizedColumn($model, $searchableColumn), 'like', "%{$search}%"
                    );
                }
            });
        }
    }

    /**
     * @param $columns
     *
     * @return LengthAwarePaginator
     */
    public function result($columns)
    {
        return $this->query->paginate($this->request->get('perPage'), $columns);
    }

    /**
     * @param       $columns
     * @param  array  $headings
     * @param       $fileName
     *
     * @return BinaryFileResponse
     */
    public function export($columns, array $headings, $fileName)
    {
        $currentDate = date('dmY-His');

        return Excel::download(
            new DataTableExport($headings, $this->query, $columns),
            "$fileName-export-$currentDate.xlsx"
        );
    }
}
