<?php
class Route{
	private static $config = [
			'index' => 1,
			'news' => 6,
			'cat' => 5,
			'hits' => 13
		];

	public static function current(){
		global $id;
		return array_search($id, self::$config);
	}

	public static function link($name){
		return getRealLinkURL('pages:'.self::$config[$name]);
	}

	public static function idByName($name){
		return self::$config[$name];
	}

	public static function nameById($id){
		return array_search($id, self::$config);
	}
}