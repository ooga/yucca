<?php
/*
 * This file was delivered to you as part of the Yucca package.
 *
 * (c) Rémi JANOT <r.janot@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Yucca\Component\Source;

use Yucca\Component\ConnectionManager;
use Yucca\Component\SchemaManager;
use Yucca\Component\Source\DataParser\DataParser;

/**
 * @todo : handle data parser
 */
class DatabaseMultipleRow extends SourceAbstract
{

    /**
     * @var string
     */
    protected $tableName;

    /**
     * @var string
     */
    protected $nameField;

    /**
     * @var string
     */
    protected $valueField;

    /**
     * @var string
     */
    protected $mapping;

    /**
     * @var array
     */
    protected $fields;

    /**
     * @var \Yucca\Component\SchemaManager
     */
    protected $schemaManager;

    /**
     * @var \Yucca\Component\ConnectionManager
     */
    protected $connectionManager;

    /**
     * @var \Yucca\Component\Source\DataParser\DataParser
     */
    protected $dataParser;

    /**
     * DatabaseMultipleRow constructor.
     *
     * @param string $sourceName
     * @param array  $configuration
     */
    public function __construct($sourceName, array $configuration = array())
    {
        parent::__construct($sourceName, $configuration);

        if (false === isset($this->configuration['table_name'])) {
            throw new \InvalidArgumentException("Configuration array must contain a 'table_name' key");
        }
        $this->tableName = $this->configuration['table_name'];

        if (false === isset($this->configuration['name_field'])) {
            throw new \InvalidArgumentException("Configuration array must contain a 'name_field' key");
        }
        $this->nameField = $this->configuration['name_field'];

        if (false === isset($this->configuration['value_field'])) {
            throw new \InvalidArgumentException("Configuration array must contain a 'value_field' key");
        }
        $this->valueField = $this->configuration['value_field'];

        if (false === isset($this->configuration['mapping'])) {
            throw new \InvalidArgumentException("Configuration array must contain a 'mapping' key");
        }
        $this->mapping = $this->configuration['mapping'];

        if (false === isset($this->configuration['fields'])) {
            throw new \InvalidArgumentException("Configuration array must contain a 'fields' key");
        }
        $this->fields = $this->configuration['fields'];
    }

    /**
     * @param \Yucca\Component\SchemaManager $schemaManager
     * @return \Yucca\Component\Source\DatabaseMultipleRow
     */
    public function setSchemaManager(SchemaManager $schemaManager)
    {
        $this->schemaManager = $schemaManager;

        return $this;
    }

    /**
     * @param \Yucca\Component\ConnectionManager $connectionManager
     * @return \Yucca\Component\Source\DatabaseMultipleRow
     */
    public function setConnectionManager(ConnectionManager $connectionManager)
    {
        $this->connectionManager = $connectionManager;

        return $this;
    }

    /**
     * @param \Yucca\Component\Source\DataParser\DataParser $dataParser
     * @return \Yucca\Component\Source\DatabaseMultipleRow
     */
    public function setDataParser(DataParser $dataParser)
    {
        $this->dataParser = $dataParser;

        return $this;
    }

    /**
     * @param array $identifier
     * @param bool  $rawData
     * @param mixed $shardingKey
     *
     * @return array
     */
    public function load(array $identifier, $rawData, $shardingKey)
    {
        $mappedIdentifier = $this->mapIdentifier($identifier);
        $datas = $this->schemaManager->fetchIds($this->tableName, $mappedIdentifier, array($this->nameField, $this->valueField), $shardingKey);
        $toReturn = array();
        foreach ($datas as $row) {
            $toReturn[$row[$this->nameField]] = $row[$this->valueField];
        }

        return $toReturn;
    }

    /**
     * @param array $identifier
     * @param null  $shardingKey
     *
     * @return $this
     */
    public function remove(array $identifier, $shardingKey = null)
    {
        $this->schemaManager->remove(
            $this->tableName,
            array_merge(
                $this->mapIdentifier($identifier),
                array($this->nameField=>array_keys($this->fields))
            ),
            $shardingKey
        );

        return $this;
    }

    /**
     * @param array $datas
     * @param array $identifier
     * @param null  $shardingKey
     * @param null  $affectedRows
     *
     * @return array
     * @throws \Exception
     */
    public function save($datas, array $identifier = array(), $shardingKey = null, &$affectedRows = null)
    {
        $datasWithoutIdentifiers=array();
        foreach ($datas as $key => $value) {
            if (isset($this->mapping[$key])) {
                $identifier[$key] = $value;
            } else {
                $datasWithoutIdentifiers[$key]=$value;
            }
        }
        $datas = $datasWithoutIdentifiers;
        //Get new identifiers
        $mappedIdentifier = $this->mapIdentifier($identifier);

        //Extract sharding key from identifier
        $shardingKey = null;
        if (isset($mappedIdentifier['sharding_key'])) {
            $shardingKey = $mappedIdentifier['sharding_key'];
            unset($mappedIdentifier['sharding_key']);
        }

        //Get Connection and table name
        $connection = $this->connectionManager->getConnection(
            $this->schemaManager->getConnectionName($this->tableName, $shardingKey, true),
            true
        );
        $shardingIdentifier = $this->schemaManager->getShardingIdentifier($this->tableName, $shardingKey);
        $tableName = $this->tableName;
        if (isset($shardingIdentifier)) {
            $tableName = sprintf('%1$s_%2$s', $this->tableName, $shardingIdentifier);
        }

        //First, remove all
        if (false === empty($identifier)) {
            $this->remove($identifier);
        }

        //then Insert
        $affectedRows = 0;
        foreach ($datas as $key => $value) {
            if (false === array_key_exists($key, $identifier)) {
                $connection->insert(
                    $tableName,
                    array_merge(
                        $mappedIdentifier,
                        array(
                            $this->nameField=>$key,
                            $this->valueField=>$value,
                        )
                    )
                );
                $affectedRows++;
            }
        }

        return $identifier;
    }

    /**
     * @param array $datas
     * @param array $identifier
     * @param null  $shardingKey
     * @param null  $affectedRows
     *
     * @return int
     */
    public function saveAfterLoading($datas, array $identifier = array(), $shardingKey = null, &$affectedRows = null)
    {
        return $this->save($datas, $identifier, $shardingKey, $affectedRows);
    }

    /**
     * @param array $identifier
     * @return array
     * @throws \RuntimeException
     */
    protected function mapIdentifier(array $identifier)
    {
        $mappedIdentifier = array();
        foreach ($identifier as $key => $value) {
            if (false === isset($this->mapping[$key])) {
                throw new \RuntimeException('Missing field mapping key : '.$key);
            }
            $mappedIdentifier[$this->mapping[$key]] = $value;
        }

        return $mappedIdentifier;
    }
}
