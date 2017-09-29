<?php
/*
 * This file was delivered to you as part of the Yucca package.
 *
 * (c) Rémi JANOT <r.janot@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Yucca\Component\ConnectionFactory;

/**
 * Class MemcacheFactory
 * @package Yucca\Component\ConnectionFactory
 */
class MemcacheFactory implements ConnectionFactoryInterface
{
    /**
     * @param array $params
     * @return \Memcache
     */
    public function getConnection(array $params)
    {
        $addServerParamsDefault = array(
            'host'=>null,
            'port'=>null,
        );

        $memcache = new \Memcache();
        foreach ($params['options']['servers'] as $server) {
            $addServerParams = array_merge($addServerParamsDefault, $server);
            $memcache->addserver($addServerParams['host'], $addServerParams['port']);
        }

        return $memcache;
    }
}
