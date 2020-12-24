<?php

/**
 * Redis storage engine for cache (cluster support)
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     s.nakano <s.nakano@guppy.co.jp>
 * @link          https://github.com/SNakano/cakephp-redis-cluster
 * @package       RedisCluster.Lib.Cache.Engine
 * @license       http://www.opensource.org/licenses/mit-license.php MIT License
 */

 App::uses('RedisEngine', 'Cache/Engine');

/**
 * Redis storage engine for cache (cluster support)
 *
 * @package       Cacke.Cache.Engine
 */
class RedisClusterEngine extends RedisEngine
{
    /**
     * Initialize the Cache Engine
     *
     * Called automatically by the cache frontend
     * To reinitialize the settings call Cache::engine('EngineName', [optional] settings = array());
     *
     * @param array $settings array of setting for the engine
     * @return bool True if the engine has been successfully initialized, false if not
     */
    public function init($settings = array())
    {
        if (!class_exists('RedisCluster')) {
            return false;
        }
        parent::init(
            array_merge(array(
                'engine' => 'RedisCluster',
                'prefix' => Inflector::slug(APP_DIR) . '_',
                'servers' => ['127.0.0.1:6379'],
                'password' => false,
                'timeout' => 0,
                'read_timeout' => 0,
                'persistent' => false,
                'slave_failover' => RedisCluster::FAILOVER_NONE,
            ), $settings)
        );
        return $this->_connect();
    }

    /**
     * Connects to a Redis server
     *
     * @return bool True if Redis server was connected
     */
    protected function _connect()
    {
        try {
            $this->_Redis = new RedisCluster(
                NULL,
                $this->settings['servers'],
                $this->settings['timeout'],
                $this->settings['read_timeout'],
                $this->settings['persistent'],
                $this->settings['password']
            );
        } catch (RedisException $e) {
            return false;
        }

        return $this->_Redis->setOption(RedisCluster::OPT_SLAVE_FAILOVER, $this->settings['slave_failover']);
    }

    /**
     * Write data for key into cache.
     *
     * @param string $key Identifier for the data
     * @param mixed $value Data to be cached
     * @param int $duration How long to cache the data, in seconds
     * @return bool True if the data was successfully cached, false on failure
     */
    public function write($key, $value, $duration)
    {
        if (!is_int($value)) {
            $value = serialize($value);
        }
        if ($duration === 0) {
            return $this->_Redis->set($key, $value);
        }

        return $this->_Redis->setex($key, $duration, $value);
    }

    /**
     * Delete all keys from the cache
     *
     * @param bool $check Whether or not expiration keys should be checked. If
     *   true, no keys will be removed as cache will rely on redis TTL's.
     * @return bool True if the cache was successfully cleared, false otherwise
     */
    public function clear($check)
    {
        if ($check) {
            return true;
        }
        $keys = $this->_Redis->keys($this->settings['prefix'] . '*');
        $this->_Redis->del($keys);

        return true;
    }
}
