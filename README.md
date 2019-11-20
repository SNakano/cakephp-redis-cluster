# CakePHP Redis Cluster Cache Engine

[![Latest Stable Version](https://poser.pugx.org/snakano/cakephp-redis-cluster-cache/v/stable)](https://packagist.org/packages/snakano/cakephp-redis-cluster-cache)
[![Total Downloads](https://poser.pugx.org/snakano/cakephp-redis-cluster-cache/downloads)](https://packagist.org/packages/snakano/cakephp-redis-cluster-cache)
[![License](https://poser.pugx.org/snakano/cakephp-redis-cluster-cache/license)](https://packagist.org/packages/snakano/cakephp-redis-cluster-cache)


Redis cluster cache engine for CakePHP 2.x

## Installation

Install the plugin using composer

```
composer require "snakano/cakephp-redis-cluster-cache"
```

## Usage

Load the plugin `app/Config/bootstrap.php` file:

```
CakePlugin::load('RedisClusterCache');
```

And cache configuration:
```
Cache::config('default', array(
    'engine'         => 'RedisClusterCache.RedisCluster',
    'prefix'         => Inflector::slug(APP_DIR) . '_',
    'servers'        => ['127.0.0.1:6379'],
    'password'       => false,
    'timeout'        => 0,
    'read_timeout'   => 0,
    'persistent'     => false,
    'slave_failover' => RedisCluster::FAILOVER_NONE,
));
```
