<?php

namespace Peak\Model\Content;

use Illuminate\Support\ServiceProvider;

class Provider extends ServiceProvider
{

	public function boot()
	{
		// 创建迁移
		$this->publishes(
			[
				__DIR__.'/publish/migration.php' => database_path('migrations/2018_09_16_170327_peak_content.php'),
			],
			'migration'
		);
	}


	public function register ()
	{

	}





}