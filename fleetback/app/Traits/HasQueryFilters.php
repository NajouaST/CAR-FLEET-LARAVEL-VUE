<?php

namespace App\Traits;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

trait HasQueryFilters
{
    public function applyQueryParameters($query, Request $request)
    {
        $table = $query->getModel()->getTable(); // dynamically detect table (e.g., "modeles")

        // 1. Filtering
        foreach ($request->all() as $key => $value) {
            if (strpos($key, '__') !== false) {
                $parts = explode('__', $key);

                if (count($parts) === 2) {
                    // Normal field: field__operator
                    [$field, $operator] = $parts;
                    $this->applyOperator($query, $field, $operator, $value);
                } elseif (count($parts) === 3) {
                    // Relation field: relation__field__operator
                    [$relation, $field, $operator] = $parts;
                    $query->whereHas($relation, function($q) use ($field, $operator, $value) {
                        $this->applyOperator($q, $field, $operator, $value);
                    });
                }
            }
        }

        // 2. Sorting
        $sortField = $request->input('sortField');
        $sortOrder = $request->input('sortOrder', 1);

        if ($sortField) {
            if (strpos($sortField, '.') !== false) {
                // Example: marque.name
                [$relation, $field] = explode('.', $sortField);

                $relationModel = $query->getModel()->$relation()->getRelated(); // get relation model
                $relationTable = $relationModel->getTable();
                $foreignKey = $query->getModel()->$relation()->getQualifiedForeignKeyName(); // e.g. modeles.marque_id
                $ownerKey = $relationModel->getKeyName(); // usually "id"

                // Apply subquery sort (no join needed, avoids select collision)
                $query->orderBy(
                    DB::table($relationTable)
                        ->select($field)
                        ->whereColumn($relationTable.'.'.$ownerKey, $foreignKey)
                        ->limit(1),
                    $sortOrder == 1 ? 'asc' : 'desc'
                );

            } else {
                // Normal column on main table
                $query->orderBy($table.'.'.$sortField, $sortOrder == 1 ? 'asc' : 'desc');
            }
        }

        // 3. Pagination (PrimeVue: `first` + `rows`)
        $perPage = (int) $request->input('rows', 10);
        $offset = (int) $request->input('first', 0);

        $total = $query->count();
        $results = $query->skip($offset)->take($perPage)->get();

        return [
            'data' => $results,
            'totalRecords' => $total,
        ];
    }

    private function applyOperator($query, $field, $operator, $value)
    {
        switch ($operator) {
            case 'contains':
                $query->where($field, 'like', "%{$value}%");
                break;
            case 'notContains':
                $query->where($field, 'not like', "%{$value}%");
                break;
            case 'startsWith':
                $query->where($field, 'like', "{$value}%");
                break;
            case 'endsWith':
                $query->where($field, 'like', "%{$value}");
                break;
            case 'equals':
                is_array($value)
                    ? $query->whereIn($field, $value)
                    : $query->where($field, $value);
                break;
            case 'notEquals':
                is_array($value)
                    ? $query->whereNotIn($field, $value)
                    : $query->where($field, '!=', $value);
                break;
            case 'in':
                $query->whereIn($field, (array) $value);
                break;
            case 'lt':
                $query->where($field, '<', $value);
                break;
            case 'lte':
                $query->where($field, '<=', $value);
                break;
            case 'gt':
                $query->where($field, '>', $value);
                break;
            case 'gte':
                $query->where($field, '>=', $value);
                break;
            case 'dateIs':
                $query->whereDate($field, '=', date('Y-m-d', strtotime($value)));
                break;
            case 'dateIsNot':
                $query->whereDate($field, '!=', date('Y-m-d', strtotime($value)));
                break;
            case 'dateBefore':
                $query->whereDate($field, '<', date('Y-m-d', strtotime($value)));
                break;
            case 'dateAfter':
                $query->whereDate($field, '>', date('Y-m-d', strtotime($value)));
                break;
        }
    }
}
