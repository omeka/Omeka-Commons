<?php


class CollectionTableTest extends Installations_Test_AppTestCase
{
    public function testFindBy()
    {
        $db = get_db();
        $collection = $db->getTable('InstallationCollection')->findBy(array('installation_id'=>1, 'orig_id'=>200));
        $this->assertTrue(count($collection) > 0);
    }
    
}