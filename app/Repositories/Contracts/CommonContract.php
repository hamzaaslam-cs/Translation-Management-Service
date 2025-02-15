<?php

namespace App\Repositories\Contracts;

use App\Filters\QueryFilterBase;

interface CommonContract
{
    public function all(?QueryFilterBase $filters = null);

    public function find($id);

    public function store(array $data);

    public function update(array $data, $id);

    public function destroy($id);
}
