<?php

namespace App\Services;

use App\Base;
use App\Http\Requests\SearchRequest;
use App\Schema\Schema;
use Illuminate\Database\Eloquent\Builder;

class DataTable
{
    public function formatResponse(SearchRequest $request, Schema $schema)
    {
        $base = app(Base::class);
        $fields = $schema->keyed();

        $queryBuilder = $request->makeQueryBuilder();
        $requestedColumns = [];
        $columnReverseMap = [];
        $columnOrderMap = [];
        foreach ($request->columns as $k => $v) {
            // Check that only valid column names are requested
            if (!isset($fields[$v['data']])) {
                throw new \RuntimeException('Invalid column name requested: ' . $v['data']);
            }
            $field = $fields[$v['data']];

            $columnReverseMap[$field->getViewColumn()] = $field->key;
            $requestedColumns[$k] = $field->getViewColumn();
            $columnOrderMap[$k] = $field->getColumn();
        }

        // Always include the id column
        if (!in_array($schema->primaryId, array_values($requestedColumns))) {
            $requestedColumns[] = $schema->primaryId;
            $columnReverseMap[$schema->primaryId] = $schema->primaryId;
        }

        $queryBuilder->select(array_values($requestedColumns));

        foreach ($request->get('order', []) as $order) {
            // Check that only valid column names are requested
            if (!isset($requestedColumns[(int) $order['column']])) {
                throw new \RuntimeException('Invalid order by requested: ' . $order['column']);
            }
            $col = $columnOrderMap[(int) $order['column']];
            $dir = ($order['dir'] == 'asc') ? 'asc' : 'desc';

            $queryBuilder->orderByRaw("$col $dir");  //  NULLS LAST");
        }

        $recordCount = $this->getRecordCount($base, $request, $queryBuilder, $schema->costLimit);

        $queryBuilder->skip($request->start);
        $queryBuilder->take($request->length + 1);

        $data = $queryBuilder->get()
            ->map(function ($row) use ($columnReverseMap) {
                $out = [];
                foreach ($row->toArray() as $k => $v) {
                    if (is_array($v)) {
                        $v = implode(', ', $v);
                    }
                    if (isset($columnReverseMap[$k])) {
                        $out[$columnReverseMap[$k]] = $v;
                    }
                }

                return $out;
            });

        $reachedEnd = (count($data) < $request->length + 1);
        if (!$reachedEnd) {
            $data->pop();
        }

        $unknownCount = false;
        if ($recordCount === -1) {
            $recordCount = $request->start + $request->length;
            if ($reachedEnd) {
                $recordCount = $request->start + count($data);
            } else {
                $unknownCount = true;
                $recordCount += $request->length;
            }
        }

        return [
            'data' => $data,
            'count' => $recordCount,
            'unknownCount' => $unknownCount,
        ];
    }

    /**
     * Get record count estimates #postgres_specific
     * Returns -1 if $costLimit is set and the cost of the query overshoots this value.
     *
     * @param Base $base
     * @param SearchRequest $request
     * @param Builder $queryBuilder
     * @param int $costLimit
     *
     * @return int
     */
    protected function getRecordCount(Base $base, SearchRequest $request, Builder $queryBuilder, int $costLimit = 0): int
    {
        $viewCls = $base->getClass('RecordView');
        $view = (new $viewCls())->getTable();

        if ($costLimit) {
            if (!count($request->getQueryParts())) {
                // count(*) can be very slow for large tables. We can do with a much faster estimate generated by
                // the autovacuum daemon that runs regularly.
                $res = \DB::select(
                    'SELECT reltuples::bigint FROM pg_catalog.pg_class WHERE relname = ?',
                    [$view]
                );

                return (int) $res[0]->reltuples;
            }

            $plan = json_decode(\DB::select(
                'explain (format json, timing false) ' . $queryBuilder->toSql(),
                $queryBuilder->getBindings()
            )[0]->{'QUERY PLAN'});
            $cost = (int) $plan[0]->Plan->{'Total Cost'};
            if ($cost > $costLimit) {
                return -1;
            }
        }

        // Get exact count
        return (int) $queryBuilder->count();
    }
}
