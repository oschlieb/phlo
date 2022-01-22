<?php

/**
 * EditModel
 */
class EditModel
{
    /**
     * Constructor, expects a Database connection
     * @param Database $db The Database object
     */
    public function __construct(Database $db)
    {
        $this->db = $db;
    }
    
    /**
     * Set required asset files
     */     
    public function setAssets()
    {
    	$assets = array('js' => 'edit', 'css' => 'edit');
        return $assets;
    }

    /**
     * Set meta data
     */     
    public function setMeta()
    {
    	$meta = array('title' => 'Edit', 'description' => 'Edit your Werkphlo.');
        return $meta;
    }
    
    /**
     * Set breadcrumbs
     */     
    public function setBreadcrumbs()
    {
    	$crumbs = array('link' => 'Account', 'name' => 'Edit');
        return $crumbs;
    }    

    /**
     * Get Werkphlo overview information
     */     
    public function getPhlo($id)
    {		
		$sql = "SELECT name, type, style FROM `ph` WHERE phid = :phid AND customer = :user_id";		
		$query = $this->db->prepare($sql);
    	$query->execute(array(':phid' => $id, ':user_id' => $_SESSION['user_id']));			
        $count = 0;
        $count = $query->rowCount();
        if($count > 0) {
        	return $query->fetchAll();
        } else {
        	$_SESSION["feedback_negative"][] = FEEDBACK_GET_ITEM_FOR_EDIT_ERROR;
        	return false;
        }
    }

    /**
     * Get child information
     */     
    public function getChild($type,$id)
    {		
    	// id, phid, parent, title, field, option, response, weighting
    	$sql = "SELECT * FROM `ph_" . $type . "_child` WHERE phid = :phid";
        $query = $this->db->prepare($sql);
        $query->execute(array(':phid' => $id));
        return $query->fetchAll();
    }

    /**
     * Get parent information
     */     
    public function getParent($type,$id)
    {
    	// id, phid, title    	
    	/*
    	$sql = "SELECT * FROM phlo_" . $type . "_parent 
    			INNER JOIN phlo_" . $type . "_child ON 
    			phlo_" . $type . "_parent.pid = phlo_" . $type . "_child.pid WHERE 
    			phlo_" . $type . "_parent.pid=" . $id;
    	*/
    	$sql = "SELECT * FROM `ph_" . $type . "_parent` WHERE phid = :phid";
    	$query = $this->db->prepare($sql);
        $query->execute(array(':phid' => $id));
        return $query->fetchAll();
	}
	
    /**
     * Get order information
     */     
    public function getOrder($type,$id)
    {		
    	// id, pid, order, type
    	$sql = "SELECT * FROM `ph_" . $type . "_order` WHERE `phid` = " . $id . " ORDER BY `order` ASC";
    	$query = $this->db->prepare($sql);
        $query->execute(array(':phid' => $id, ':user_id' => $_SESSION['user_id']));
        return $query->fetchAll();
	}

    /**
     * Get joined child and parent information
     */
    public function getParentAndChild($type,$id)
    {
    	$child = "ph_" . $type . "_child";
    	$parent = "ph_" . $type . "_parent";    	
    	$sql = "SELECT $child.*, $parent.title FROM $child INNER JOIN $parent ON $child.phid = $parent.phid";
    	$query = $this->db->prepare($sql);
        $query->execute();
        return $query->fetchAll();        
    }
              
}
