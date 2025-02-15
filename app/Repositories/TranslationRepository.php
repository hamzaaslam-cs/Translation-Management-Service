<?php

namespace App\Repositories;

use App\Filters\QueryFilterBase;
use App\Models\Translation;

class TranslationRepository implements Contracts\CommonContract
{
    public function all(?QueryFilterBase $filters = null)
    {
        $translations = Translation::query();
        if (! empty($filters) && $filters->hasFilters()) {
            $translations = $translations->filter($filters);
        }
        return $translations->simplePaginate(config('app.paginate'));
    }

    public function find($id)
    {
        return Translation::findOrFail($id);
    }

    public function store(array $data)
    {
        return Translation::create($data);
    }

    public function update(array $data, $id)
    {
        $translation = Translation::findOrFail($id);
        $translation->update($data);

        return $translation;
    }

    public function destroy($id)
    {
        return Translation::where('id', '=', $id)->delete();
    }
}
