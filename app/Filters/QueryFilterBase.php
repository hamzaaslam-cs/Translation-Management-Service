<?php

namespace App\Filters;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use ReflectionMethod;

class QueryFilterBase
{
    public $used_as = 'object';

    protected $request;

    protected $builder;

    protected $hasDates = false;

    protected array $forbidden_filters = ['no_cache'];

    protected array $raw_filters = ['currency'];

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function hasApplicableFilters()
    {
        return $this->hasFilters(true);
    }

    public function hasFilters($skip_forbidden = false)
    {
        return $this->getFilters($skip_forbidden)['status'];
    }

    public function getFilters($skip_forbidden = false)
    {
        $return = [
            'filters' => [],
            'status' => false,
        ];
        foreach ($this->request->all() as $name => $value) {
            if ($skip_forbidden && in_array($name, $this->forbidden_filters)) {
                continue;
            }
            if (method_exists($this, $name)) {
                $return['filters'][$name] = $value;
                $return['status'] = true;
            }
        }

        return $return;
    }

    public function __call($method, $args)
    {
        if (isset($this->$method)) {
            return call_user_func_array($this->$method, $args);
        }

        return null;
    }

    public function apply(Builder $builder)
    {
        $this->builder = $builder;
        foreach ($this->request->all() as $name => $value) {
            if (in_array($name, $this->forbidden_filters)) {
                continue;
            }
            if ($this->used_as == 'raw' && in_array($name, $this->raw_filters)) {
                $name = $name.'_raw';
            }
            if (method_exists($this, $name)) {
                $reflection = new ReflectionMethod($this, $name);
                // if parameter is required
                if (! empty($reflection->getParameters())) {
                    // and value is not empty
                    if (isset($value)) {
                        $this->builder = $this->$name($value);
                    }
                    // if value is empty then skip...
                } // if no parameter is required
                else {
                    $this->builder = $this->$name();
                }
            }
        }

        return $this->builder;
    }

    // skip cache step...

    public function hasDateFilters()
    {
        return $this->hasDates;
    }

    public function hasOnly(string|array $filter_name)
    {
        $filter_name = Arr::wrap($filter_name);
        foreach ($this->getFilters(true)['filters'] as $name => $value) {
            if (! in_array($name, $filter_name)) {
                return false;
            }
        }

        return true;
    }

    public function has(string|array $filter_name)
    {
        $filter_name = Arr::wrap($filter_name);
        foreach ($this->getFilters(true)['filters'] as $name => $value) {
            if (in_array($name, $filter_name)) {
                return true;
            }
        }

        return false;
    }

    protected function no_cache()
    {
        return $this->builder;
    }
}
