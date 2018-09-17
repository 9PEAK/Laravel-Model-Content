<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class PeakContent extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
		DB::statement('CREATE TABLE IF NOT EXISTS `9peak_content` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `type` tinyint(3) UNSIGNED NOT NULL COMMENT \'模块类型\',
  `title` varchar(300) NOT NULL COMMENT \'标题\',
  `cover` text COMMENT \'封面图片\',
  `album` text COMMENT \'相册\',
  `summary` text COMMENT \'摘要\',
  `content` text NOT NULL COMMENT \'正文\',
  `datetime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT \'更新日期\',
  `status` tinyint(2) DEFAULT NULL COMMENT \'状态\',
  `category_id` int(10) UNSIGNED NOT NULL DEFAULT \'0\' COMMENT \'分类\',
  `view` int(10) UNSIGNED NOT NULL DEFAULT \'0\' COMMENT \'浏览次数\',
  PRIMARY KEY (`id`),
  KEY `category_id` (`category_id`),
  KEY `datetime` (`datetime`),
  KEY `title` (`title`),
  KEY `type` (`type`),
  KEY `status` (`status`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('9peak_content');
    }
}
