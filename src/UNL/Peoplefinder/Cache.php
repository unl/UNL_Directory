<?php

class UNL_Peoplefinder_Cache
{
	protected $fastCache;

	protected $fastLifetime;

	protected $slowCache;

	public static function factory($options = [])
	{
		$defaultOptions = [
			'memcache_host' => 'localhost',
			'memcache_port' => 11211,
			'fast_lifetime' => 86400, //default to one day,
			'slow_storage' => realpath(__DIR__ . '/../../..') . '/tmp/',
		];

		$options = array_merge($defaultOptions, (array) $options);

		$fastCache = new Memcached;
        $fastCache->addServer($options['memcache_host'], $options['memcache_port']);

        $slowCache = new UNL_Cache_Lite([
            'cacheDir' => $options['slow_storage'],
            'hashedDirectoryLevel' => 2,
            'hashedDirectoryUmask' => 0770,
            'automaticSerialization' => true,
            'lifeTime' => null, // forever
        ]);

        return new self($fastCache, $options['fast_lifetime'], $slowCache);
	}

	public function __construct(Memcached $fastCache, $fastLifetime, UNL_Cache_Lite $slowCache)
	{
		$this->fastCache = $fastCache;
		$this->fastLifetime = (int) $fastLifetime;
		$this->slowCache = $slowCache;
	}

	public function get($key)
	{
		return $this->fastCache->get($key);
	}

	public function getSlow($key)
	{
		return $this->slowCache->get($key);
	}

	public function set($key, $value, $expires = false)
	{
		if (!$expires) {
			$expires = time() + $this->fastLifetime;
		}

		$this->fastCache->set($key, $value, $expires);
		$this->slowCache->save($value, $key);
	}

	public function remove($key)
	{
		$this->fastCache->delete($key);
		$this->slowCache->remove($key, 'default', true);
	}
}
