<?php

namespace App\Services;

use App\Http\Requests\DataTableRequest;
use Illuminate\Database\Eloquent\Builder;

class DataTableProvider
{
    public function processRequest(DataTableRequest $request)
    {
        list($queryBuilder, $colMap) = $request->makeQueryBuilderAndColumnMap();

        // Count *before* we apply limit!
        $recordCount = $this->getRecordCount($request, $queryBuilder);

        // Then get data
        $data = $queryBuilder
            ->skip($request->start)
            ->take($request->length + 1)
            ->get()
            ->map(function ($row) use ($colMap) {
                $out = [];
                foreach ($row->toArray() as $k => $v) {
                    if (is_array($v)) {
                        $v = implode(', ', $v);
                    }
                    if (isset($colMap[$k])) {
                        $out[$colMap[$k]] = $v;
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
     * @param DataTableRequest $request
     * @param Builder $queryBuilder
     *
     * @return int
     */
    protected function getRecordCount(DataTableRequest $request, Builder $queryBuilder): int
    {
        $base = $request->getBase();
        $viewCls = $base->getClass('RecordView');
        $view = (new $viewCls())->getTable();
        $costLimit = $base->config('costLimit');

        if ($costLimit) {
            if (!count($request->parseQuery())) {
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
