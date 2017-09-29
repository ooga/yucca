<?php
/*
 * This file was delivered to you as part of the Yucca package.
 *
 * (c) Rémi JANOT <r.janot@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Yucca\Component\SourceFactory;

use \Yucca\Component\Source\FakeMemcache;

/**
 * Class FakeMemcacheFactory
 * @package Yucca\Component\SourceFactory
 */
class FakeMemcacheFactory implements SourceFactoryInterface
{
    /**
     * Build source
     * @param string $sourceName
     * @param array  $params
     * @return \Yucca\Component\Source\Memcache
     */
    public function getSource($sourceName, array $params = array())
    {
        $toReturn = new FakeMemcache($sourceName, $params);

        return $toReturn;
    }
}
