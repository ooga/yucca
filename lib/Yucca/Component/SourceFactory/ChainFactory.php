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

use \Yucca\Component\Source\Chain;
use Yucca\Component\Source\DataParser\DataParser;

/**
 * Class ChainFactory
 * @package Yucca\Component\SourceFactory
 */
class ChainFactory implements SourceFactoryInterface
{
    /**
     * @var \Yucca\Component\Source\DataParser\DataParser
     */
    protected $dataParser;

    /**
     * @param \Yucca\Component\Source\DataParser\DataParser $dataParser
     */
    public function __construct(DataParser $dataParser)
    {
        $this->dataParser = $dataParser;
    }
    /**
     * Build source
     * @param string $sourceName
     * @param array  $params
     * @param array  $sources
     * @return \Yucca\Component\Source\Chain
     */
    public function getSource($sourceName, array $params = array(), array $sources = array())
    {
        $toReturn = new Chain($sourceName, $params, $sources);
        $toReturn->setDataParser($this->dataParser);

        return $toReturn;
    }
}
