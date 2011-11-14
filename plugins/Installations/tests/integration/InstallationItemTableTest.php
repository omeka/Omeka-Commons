<?php

class InstallationItemTableTest extends Installations_Test_AppTestCase
{
    public function testFindItemForId()
    {
        
        $item = get_db()->getTable('InstallationItem')->findItemForId(1);
        $this->assertEquals('Item', get_class($item));
        
    }
    
    public function testFilters()
    {
        $table = get_db()->getTable('InstallationItem');
        $installationItems = $table->findBy(array(
													'installation_id'=>1,
													'orig_id'=>100
                                                    )
                                                );
        $this->assertEquals(1, count($installationItems));
        
        $installationItems = $table->findBy(array(
													'installation_id'=>1,
													'orig_id'=>101
                                                    )
                                                );
        $this->assertEquals(0, count($installationItems));
    }

}