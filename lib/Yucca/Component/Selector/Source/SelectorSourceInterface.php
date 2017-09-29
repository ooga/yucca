<?php
/*
 * This file was delivered to you as part of the Yucca package.
 *
 * (c) Rémi JANOT <r.janot@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Yucca\Component\Selector\Source;

/**
 * Interface SelectorSourceInterface
 * @package Yucca\Component\Selector\Source
 */
interface SelectorSourceInterface
{
    const ID_FIELD = 'idField';
    const SHARDING_KEY_FIELD = 'shardingKeyField';
    const SHARDING_KEY = 'sharding_key';
    const TABLE = 'table';
    const RESULT = 'result';
    const RESULT_COUNT = 'count';
    const RESULT_IDENTIFIERS = 'identifiers';
    const CONNECTION_NAME = 'connection_name';
    const SELECTOR_NAME = 'selector_name';
    const LIMIT = 'limit';
    const OFFSET = 'offset';
    const GROUPBY = 'groupBy';
    const ORDERBY = 'orderBy';
    const FORCE_FROM_MASTER = 'force_from_master';
    const ELASTIC_SEARCHABLE = 'elastic_searchable';
    const ELASTIC_QUERY = 'elastic_query';
    const FACETS = 'facets';

    /**
     * @param array $criterias
     * @param array $options
     *
     * @return mixed
     */
    public function loadIds(array $criterias, array $options = array());

    /**
     * @param array $ids
     * @param array $criterias
     * @param array $options
     *
     * @return mixed
     */
    public function saveIds($ids, array $criterias, array $options = array());

    /**
     * @param array $options
     *
     * @return mixed
     */
    public function invalidateGlobal(array $options = array());
}
