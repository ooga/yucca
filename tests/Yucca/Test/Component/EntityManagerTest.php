<?php
/*
 * This file was delivered to you as part of the Yucca package.
 *
 * (c) Rémi JANOT <r.janot@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Yucca\Test\Component;

class EntityManagerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @return mixed
     */
    public function test_construct(){
        //Correct constructor
        new \Yucca\Component\EntityManager();
    }

    /**
     * @return mixed
     */
    public function test_setMappingManager(){
        $mappingManagerMock = $this->getMockBuilder('\Yucca\Component\MappingManager')
                                    ->disableOriginalConstructor()
                                    ->getMock();

        $entityManager = new \Yucca\Component\EntityManager();
        $entityManager->setMappingManager($mappingManagerMock);
    }

    /**
     * @return mixed
     */
    public function test_setSelectorManager(){
        $selectorManagerMock = $this->getMockBuilder('\Yucca\Component\SelectorManager')
                                    ->disableOriginalConstructor()
                                    ->getMock();

        $entityManager = new \Yucca\Component\EntityManager();
        $entityManager->setSelectorManager($selectorManagerMock);
    }

    public function test_load(){
        //Initialize entity manager
        $selectorManagerMock = $this->getMockBuilder('\Yucca\Component\SelectorManager')
            ->disableOriginalConstructor()
            ->getMock();

        $mappingManagerMock = $this->getMockBuilder('\Yucca\Component\MappingManager')
            ->disableOriginalConstructor()
            ->getMock();

        $entityManager = new \Yucca\Component\EntityManager('strange_identifier');
        $entityManager->setMappingManager($mappingManagerMock);
        $entityManager->setSelectorManager($selectorManagerMock);

        //Load with wrong class
        try {
            $entityManager->load('\Datetime',1);
            $this->fail('Should raise an exception');
        } catch(\Exception $exception) {
            $this->assertContains('Entity class \Datetime must inherit from \Yucca\Model\ModelAbstract.', $exception->getMessage());
        }
        //Load with class that does not exists
        try {
            $entityManager->load('\FakeClass',1);
            $this->fail('Should raise an exception');
        } catch(\Exception $exception) {
            $this->assertContains('Entity class \FakeClass not found.', $exception->getMessage());
        }

        //Load without sharding key
        $model = $entityManager->load('\Yucca\Concrete\Model\Base',1);
        $this->assertSame($selectorManagerMock, $model->getYuccaSelectorManager());
        $this->assertSame($mappingManagerMock, $model->getYuccaMappingManager());
        $this->assertSame(array('strange_identifier'=>1), $model->getYuccaIdentifier());
        $this->assertSame($entityManager, $model->getYuccaEntityManager());
        $this->assertInstanceOf('\Yucca\Concrete\Model\Base', $model);

        //Load with sharding key
        $model = $entityManager->load('\Yucca\Concrete\Model\Base',2,4);
        $this->assertSame(array('strange_identifier'=>2, 'sharding_key'=>4), $model->getYuccaIdentifier());
        $this->assertInstanceOf('\Yucca\Concrete\Model\Base', $model);
    }

    public function test_save(){
        //Initialize entity manager
        $selectorManagerMock = $this->getMockBuilder('\Yucca\Component\SelectorManager')
            ->disableOriginalConstructor()
            ->getMock();

        $mappingManagerMock = $this->getMockBuilder('\Yucca\Component\MappingManager')
            ->disableOriginalConstructor()
            ->getMock();

        $entityManager = new \Yucca\Component\EntityManager('strange_identifier');
        $entityManager->setMappingManager($mappingManagerMock);
        $entityManager->setSelectorManager($selectorManagerMock);

        $modelMock = $this->getMock('\Yucca\Model\ModelAbstract');
        $modelMock->expects($this->once())
            ->method('save')
            ->will($this->returnValue(null));
        $modelMock->expects($this->once())
            ->method('setYuccaMappingManager')
            ->will($this->returnValue($modelMock));
        $modelMock->expects($this->once())
            ->method('setYuccaEntityManager')
            ->will($this->returnValue($modelMock));
        $modelMock->expects($this->once())
            ->method('setYuccaSelectorManager')
            ->will($this->returnValue($modelMock));

        $return = $entityManager->save($modelMock);
        $this->assertSame($return, $entityManager);
    }

    public function test_remove(){
        //Initialize entity manager
        $selectorManagerMock = $this->getMockBuilder('\Yucca\Component\SelectorManager')
            ->disableOriginalConstructor()
            ->getMock();

        $mappingManagerMock = $this->getMockBuilder('\Yucca\Component\MappingManager')
            ->disableOriginalConstructor()
            ->getMock();

        $entityManager = new \Yucca\Component\EntityManager('strange_identifier');
        $entityManager->setMappingManager($mappingManagerMock);
        $entityManager->setSelectorManager($selectorManagerMock);

        $modelMock = $this->getMock('\Yucca\Model\ModelAbstract');
        $modelMock->expects($this->once())
            ->method('remove')
            ->will($this->returnValue(null));
        $modelMock->expects($this->once())
            ->method('setYuccaMappingManager')
            ->will($this->returnValue($modelMock));
        $modelMock->expects($this->once())
            ->method('setYuccaEntityManager')
            ->will($this->returnValue($modelMock));
        $modelMock->expects($this->once())
            ->method('setYuccaSelectorManager')
            ->will($this->returnValue($modelMock));

        $return = $entityManager->remove($modelMock);
        $this->assertSame($return, $entityManager);
    }

    public function test_reset(){
        //Initialize entity manager
        $entityManager = new \Yucca\Component\EntityManager('strange_identifier');

        $newIdentifier = array('id'=>5);
        $modelMock = $this->getMock('\Yucca\Model\ModelAbstract');
        $modelMock->expects($this->once())
            ->method('reset')
            ->with($this->equalTo($newIdentifier))
            ->will($this->returnValue(null));

        //Check call only once
        $return = $entityManager->resetModel($modelMock, $newIdentifier);
        $this->assertSame($return, $entityManager);

        //Check that new identifiers are well set
        $model = new \Yucca\Concrete\Model\Base();
        $entityManager->resetModel($model, $newIdentifier);

        $this->assertSame($newIdentifier, $model->getYuccaIdentifier());
    }
}
