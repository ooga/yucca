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

interface SourceInterface
{
    public function canHandle($field);
    public function isIdentifier($field);
    public function load(array $identifier);
    public function remove(array $identifier);
    public function save($datas, array $identifier=array(), &$affectedRows=null);
    public function saveAfterLoading($datas, array $identifier=array(), &$affectedRows=null);
}
