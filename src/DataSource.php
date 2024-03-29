<?php
namespace Ericli1018\LaravelKendoUiDatasource;

use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\DB;

class DataSource
{
	protected $app;
	protected $input;
	protected $columns;
	protected $table_name;
	protected $sortKey;
	protected $filterKey;
	protected $stringOps = [
		'eq' => 'like',
		'neq' => 'not like',
		'doesnotcontain' => 'not like',
		'contains' => 'like',
		'startswith' => 'like',
        'endswith' => 'like',
        'isnull' => 'is',
        'isnotnull' => 'is not',
        'isempty' => '=',
        'isnotempty' => '!=',
        'isnullorempty' => '=',
        'isnotnullorempty' => '!='
	];
	protected $numberOps = [
		'eq' => '=',
		'gt' => '>',
		'gte' => '>=',
		'lt' => '<',
		'lte' => '<=',
        'neq' => '!=',
        'isnull' => 'is',
        'isnotnull' => 'is not',
	];

	public function __construct(Application $app, array $input, array $columns, $table_name)
	{
		$this->app = $app;
		$this->input = $input;
		$this->columns = $columns;
		$this->table_name = $table_name === null ? '' : "`$table_name`" . '.';
		$this->sortKey = config('laravel-kendo-ui-datasource.sortKey');
		$this->filterKey = config('laravel-kendo-ui-datasource.filterKey');
	}

	private function sort($query, $d)
	{
		if( ! is_array($d))
			$this->app->abort(400, 'sort is not array');

		foreach($d as $f)
		{
			if( ! is_array($f))
				$this->app->abort(400, 'sort item is not object.');

			if( ! isset($this->columns[$f['field']]))
				$this->app->abort(400, $f['field'] . ' field not set.');

			if( ! isset($f['dir']) or ! in_array($f['dir'], ['asc', 'desc'], true))
				$this->app->abort(400, $f['field'] . ' field sort dir wrong.');
			
			$field = null;
			if (is_array($this->columns[$f['field']])) 
			{
				if (count($this->columns[$f['field']]) > 1) // table name
					$field = '`' . $this->columns[$f['field']][1] . '`.`' . $f['field'] . '`';
				else 
					$field = $this->table_name . '`' . $f['field'] . '`';
			} 
			else 
			{
				$field = $this->table_name . '`' . $f['field'] . '`';
			}
			$query->orderBy(DB::raw($field), $f['dir']);
		}
	}

	private function filterField($query, $d, $logic)
	{
		if( ! isset($d['field']) or ! isset($this->columns[$d['field']]))
			$this->app->abort(400, $d['field'] . ' field not set.');

		$columnType = null;
		$columnName = null; 
		if (is_array($this->columns[$d['field']])) 
		{
			if (count($this->columns[$d['field']]) > 0) 
			{
				$columnType = $this->columns[$d['field']][0];

				if (count($this->columns[$d['field']]) > 1) 
					$columnName = '`' . $this->columns[$d['field']][1] . '`.`' . $d['field'] . '`';
				else
					$columnName = $this->table_name . '`' . $d['field'] . '`';
			}
			else
				$this->app->abort(400, $d['field'] . ' field not set.');
		} 
		else 
		{
			$columnType = $this->columns[$d['field']];
			$columnName = $this->table_name . '`' . $d['field'] . '`';
		}

		if($columnType === 'string')
		{
			if( ! isset($d['operator']) or ! isset($this->stringOps[$d['operator']]))
				$this->app->abort(400, $d['field'] . ' field not set operator.');

				$value = "";
				if ('isnull' == $d['operator']) {
					$query->whereNull(DB::raw($columnName));
				} else if ('isnotnull' == $d['operator']) {
					$query->whereNotNull(DB::raw($columnName));
				} else if ('isnullorempty' == $d['operator']) {
					$query->whereNull(DB::raw($columnName));
					$query->orWhere(DB::raw($columnName), $this->stringOps[$d['operator']], $value, $logic);
				} else if ('isnotnullorempty' == $d['operator']) {
					$query->whereNotNull(DB::raw($columnName));
					$query->orWhere(DB::raw($columnName), $this->stringOps[$d['operator']], $value, $logic);
				} else {
					if ('isempty' != $d['operator'] && 'isnotempty' != $d['operator']) {
						if( ! isset($d['value']) or ! is_string($d['value']))
							$this->app->abort(400, $d['field'] . ' field not set value.');
					}
	
					$value = $d['value'];
					if($d['operator'] === 'contains' or $d['operator'] === 'doesnotcontain')
						$value = "%$value%";
					else if($d['operator'] === 'startswith')
						$value = "$value%";
					else if($d['operator'] === 'endswith')
						$value = "%$value";
	
					$query->where(DB::raw($columnName), $this->stringOps[$d['operator']], $value, $logic);
				}
		}
		else if($columnType === 'number')
		{
			if( ! isset($d['operator']) or ! isset($this->numberOps[$d['operator']]))
				$this->app->abort(400, $d['field'] . ' field not set operator.');

			if ('isnull' == $d['operator']) {
				$query->whereNull(DB::raw($columnName));
			} else if ('isnotnull' == $d['operator']) {
				$query->whereNotNull(DB::raw($columnName));
			} else {
				if( ! isset($d['value']) or ! is_numeric($d['value']))
					$this->app->abort(400, $d['field'] . ' field not set value.');

				$query->where(DB::raw($columnName), $this->numberOps[$d['operator']], $d['value'], $logic);
			}
		}
		else if($columnType === 'boolean')
		{
			if( ! isset($d['operator']))
				$this->app->abort(400, $d['field'] . ' field not set operator.');

			if( ! isset($d['value']))
				$this->app->abort(400, $d['field'] . ' field not set value.');

			$query->where(DB::raw($columnName), $d['value'] === 'true' ? '!=' : '=', 0, $logic);		
		}
		else if($columnType === 'date')
		{
			if( ! isset($d['operator']) or ! isset($this->numberOps[$d['operator']]))
				$this->app->abort(400, $d['field'] . ' field not set operator.');

			try {
				$value = new \DateTime($d['value']);
			}
			catch(\Exception $e)
			{
				$this->app->abort(400, $d['field'] . ' field value is not datetime.');
			}
			if ('isnull' == $d['operator']) {
                $query->whereNull(DB::raw($columnName));
            } else if ('isnotnull' == $d['operator']) {
                $query->whereNotNull(DB::raw($columnName));
            } else {
                $query->where(DB::raw($columnName), $this->numberOps[$d['operator']], $value, $logic);
            }
		}
		else {
			$this->app->abort(500, $d['field'] . ' field type not support.');
		}
	}

	private function filter($query, $d)
	{
		$filter_r = function($query, $d, $depth, $logic) use(&$filter_r)
		{
			if($depth >= 32)
				$this->app->abort(400, 'filter recursive limit(depth=32).');

			if( ! is_array($d))
				$this->app->abort(400, 'filter input is not object.');

			if(isset($d['filters']) and is_array($d['filters']))
			{
				if( ! isset($d['logic']) or ! in_array($d['logic'], ['and', 'or'], true))
					$this->app->abort(400, 'filter logic only support "and" or "or".');

				$query->where(function($query) use ($d, $depth, $filter_r)
				{
					foreach($d['filters'] as $f)
						$filter_r($query, $f, $depth + 1, $d['logic']);
				}, null, null, $logic);
			}
			else {
				$this->filterField($query, $d, $logic);
			}
		};

		$filter_r($query, $d, 0, 'and');
	}

	public function execute($query, $count_col = '*')
	{
		if(isset($this->input[$this->sortKey]) and is_array($this->input[$this->sortKey]))
			$this->sort($query, $this->input[$this->sortKey]);

		if(isset($this->input[$this->filterKey]) and is_array($this->input[$this->filterKey]))
			$this->filter($query, $this->input[$this->filterKey]);

		$total = $query->count(DB::raw($count_col));

		if(isset($this->input['skip']))
			$query->skip(@intval($this->input['skip']));
		else
			$query->skip(0);

		if(isset($this->input['take']))
			$query->take(@intval($this->input['take']));
		else
			$query->take(1000); // default get 1000 rows

		return $total;
	}

}
