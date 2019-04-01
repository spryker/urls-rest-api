<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Client\Redis;

use Generated\Shared\Transfer\RedisConfigurationTransfer;
use Spryker\Client\Kernel\AbstractClient;
use Spryker\Client\Redis\Client\Adapter\ClientAdapterInterface;

/**
 * @method \Spryker\Client\Redis\RedisFactory getFactory()
 */
class RedisClient extends AbstractClient implements RedisClientInterface
{
    /**
     * {@inheritdoc}
     *
     * @api
     *
     * @param string $connectionKey
     * @param string $key
     *
     * @return string|null
     */
    public function get(string $connectionKey, string $key): ?string
    {
        return $this->getConnection($connectionKey)->get($key);
    }

    /**
     * {@inheritdoc}
     *
     * @api
     *
     * @param string $connectionKey
     * @param string $key
     * @param int $seconds
     * @param string $value
     *
     * @return bool
     */
    public function setex(string $connectionKey, string $key, int $seconds, string $value): bool
    {
        return $this->getConnection($connectionKey)->setex($key, $seconds, $value);
    }

    /**
     * {@inheritdoc}
     *
     * @api
     *
     * @param string $connectionKey
     * @param string $key
     * @param string $value
     * @param string|null $expireResolution
     * @param int|null $expireTTL
     * @param string|null $flag
     *
     * @return bool
     */
    public function set(string $connectionKey, string $key, string $value, ?string $expireResolution = null, ?int $expireTTL = null, ?string $flag = null): bool
    {
        return $this->getConnection($connectionKey)->set($key, $value, $expireResolution, $expireTTL, $flag);
    }

    /**
     * {@inheritdoc}
     *
     * @api
     *
     * @param string $connectionKey
     * @param array $keys
     *
     * @return int
     */
    public function del(string $connectionKey, array $keys): int
    {
        return $this->getConnection($connectionKey)->del($keys);
    }

    /**
     * {@inheritdoc}
     *
     * @api
     *
     * @param string $connectionKey
     * @param string $script
     * @param int $numkeys
     * @param string|null $keyOrArg1
     * @param string|null $keyOrArgN
     *
     * @return bool
     */
    public function eval(string $connectionKey, string $script, int $numkeys, ?string $keyOrArg1 = null, ?string $keyOrArgN = null): bool
    {
        return $this->getConnection($connectionKey)->eval($script, $numkeys, $keyOrArg1, $keyOrArgN);
    }

    /**
     * {@inheritdoc}
     *
     * @api
     *
     * @param string $connectionKey
     *
     * @return void
     */
    public function connect(string $connectionKey): void
    {
        $this->getConnection($connectionKey)->connect();
    }

    /**
     * {@inheritdoc}
     *
     * @api
     *
     * @param string $connectionKey
     *
     * @return void
     */
    public function disconnect(string $connectionKey): void
    {
        $this->getConnection($connectionKey)->disconnect();
    }

    /**
     * {@inheritdoc}
     *
     * @api
     *
     * @param string $connectionKey
     *
     * @return bool
     */
    public function isConnected(string $connectionKey): bool
    {
        return $this->getConnection($connectionKey)->isConnected();
    }

    /**
     * {@inheritdoc}
     *
     * @api
     *
     * @param string $connectionKey
     * @param string[] $keys
     *
     * @return array
     */
    public function mget(string $connectionKey, array $keys): array
    {
        return $this->getConnection($connectionKey)->mget($keys);
    }

    /**
     * {@inheritdoc}
     *
     * @api
     *
     * @param string $connectionKey
     * @param array $dictionary
     *
     * @return bool
     */
    public function mset(string $connectionKey, array $dictionary): bool
    {
        return $this->getConnection($connectionKey)->mset($dictionary);
    }

    /**
     * {@inheritdoc}
     *
     * @api
     *
     * @param string $connectionKey
     * @param string|null $section
     *
     * @return array
     */
    public function info(string $connectionKey, ?string $section = null): array
    {
        return $this->getConnection($connectionKey)->info($section);
    }

    /**
     * {@inheritdoc}
     *
     * @api
     *
     * @param string $connectionKey
     * @param string $pattern
     *
     * @return string[]
     */
    public function keys(string $connectionKey, string $pattern): array
    {
        return $this->getConnection($connectionKey)->keys($pattern);
    }

    /**
     * {@inheritdoc}
     *
     * @api
     *
     * @param string $connectionKey
     * @param \Generated\Shared\Transfer\RedisConfigurationTransfer $configurationTransfer
     *
     * @return void
     */
    public function setupConnection(string $connectionKey, RedisConfigurationTransfer $configurationTransfer): void
    {
        $this->getFactory()->createConnectionProvider()->setupConnection($connectionKey, $configurationTransfer);
    }

    /**
     * @param string $connectionKey
     *
     * @return \Spryker\Client\Redis\Client\Adapter\ClientAdapterInterface
     */
    protected function getConnection(string $connectionKey): ClientAdapterInterface
    {
        return $this->getFactory()->createConnectionProvider()->getClient($connectionKey);
    }
}
