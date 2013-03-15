<?php

class Sites_SiteItemType extends Omeka_Record_AbstractRecord
{
    public $id;
    public $orig_id;
    public $site_id;
    public $item_type_id;


    /**
     * Each site will need to duplicate the 'core' Omeka ItemTypes for their
     * records in the Commons. Usually, this will be irrelevant overhead, because
     * most sites don't work much with Item Types. But when it happens, we'll have to address it
     *
     * This also means avoiding ItemTypeTable::findByName(), and helpers that use it.
     * Commons gotta do what the Commons gotta do
     *
     * This method is a convenience to do that duplication.
     *
     * Creates a new record for the Item Type in ItemTypeTable, and creates the SiteItemType
     * to work with it.
     *
     * @param int $orig_id The original id in the site. maps to core Omeka item types
     * @param string $new_name If the item type has been edited, the new name
     * @param string $new_desc If the item type has been edited, the new description
     */

    public function cloneCoreType($orig_id, $new_name = false, $new_desc = false)
    {
        $type = get_db()->getTable('ItemType')->find($orig_id);
        $newType = new ItemType();
        if($new_name) {
            $newType->name = $new_name;
        } else {
            $newType->name = $type->name;
        }

        if($new_desc) {
            $newType->description = $new_desc;
        } else {
            $newType->description = $type->description;
        }
        $newType->save();

        $this->orig_id = $orig_id;
        $this->item_type_id = $newType->id;
        $this->save();
    }

}