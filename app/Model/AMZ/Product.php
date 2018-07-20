<?php

namespace App;

use Peak\MixCode;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $table = 'product';

    public $timestamps = false;

    protected $fillable = [
        'id',
        'name',
        'mfn',
        'category_id',
        'photo_id',
        'photo_album',
        'width',
        'length',
        'height',
        'weight',
        'weight',
        'material',
        'created_at',
		'unit',
		'vendor_id'
    ];


    ### 创建省厂商编号 谨慎修改！

	public function setMfnAttribute ($val)
	{
		if ( $this->id) {
			unset ($this->attributes['mfn']);
		} else {
			$this->attributes['mfn'] = $val ?: self::makeMfn();
		}
	}


	/**
	 * 创建生产商编号
	 * */
	static function makeMfn ()
	{
		$str = str_shuffle(config('sku.mfn.str'));
		$str = substr($str,0,3);
//		$str = strtolower($str);

		return config('sku.mfn.year').substr(config('sku.mfn.str'), config('sku.mfn.month')-1,1).'-'.$str;
	}


    ### SKU生成，谨慎修改！

    protected $appends = [
    	'amz_sku',
	    'b2b_sku',
    ];


	static private function makeSku()
	{

	}



	public function getAmzSkuAttribute ($val)
	{
		MixCode::config(
			config('sku.amazon'),
			2,
			[
				0 => 'FlipCode',
				2 => 'CaecarCode',
			]
		);
		return MixCode::encode($this->mfn);
	}




    public function getB2bSkuAttribute ($val)
    {
	    MixCode::config(
		    config('sku.b2b'),
		    1,
		    [
			    0 => 'FlipCode' ,
			    3 => 'CaecarCode' ,
		    ]
	    );
		return MixCode::encode($this->mfn);
    }




}
