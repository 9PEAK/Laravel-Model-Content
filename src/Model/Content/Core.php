<?php

namespace Peak\Model\Content;

class Core
{
	protected $table = '9peak_content';
	public $timestamps = false;

	protected $guarded = [
		'type' => 'type',
		'id' => 'id',
	];


	protected $hidden = [
		'type',
	];


	protected static function boot()
	{
		parent::boot();

		static::addGlobalScope('type', function (Builder $builder) {
			$builder->where('type', static::TYPE);
		});

		static::saving(function($model) {
			$model->type = static::TYPE;
		});
	}



	### scope查询

	/**
	 * 搜索标题
	 * */
	public function scopeWhereTitle ($query, $name, $like=false)
	{
		return $like ? $query->where('title', 'like', '%'.$name.'%') : $query->where('title', $name);
	}


	/**
	 * 搜索状态
	 * */
	public function scopeWhereStatus ($query, $status)
	{
		return $query->where('status', $status);
	}


	/**
	 * 检索分类
	 * */
	public function scopeWhereCategory ($query, $categoryId)
	{
		return $query->where('category_id', $categoryId);
	}




}
