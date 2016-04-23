<?
$now = date('Y-m-d H:i:s');
ini_set("date.timezone","Asia/Manila");
class daObj2 extends commonObj {
    
    # LIST OF DISPLAY TYPE
    function getDisplayType(){
        $sql = "SELECT * FROM tblDisplayType";    
        return $this->getArrRes($this->execQry($sql));    
    }
    
    # LIST OF RENTABLE
    function listDiplaySpecs(){
                 
        $sql = "
            select 
                displaySpecsId,displaySpecsDesc,createdBy,dateCreated,stat,specsAmount
            from 
                tblDisplaySpecs
            where
                stat = 'A'
            order by
                displaySpecsDesc
        ";    
        return $this->getArrRes($this->execQry($sql));
    }    

    # LIST OF LOCATION
    function getLocation($display_specs_id){
        $sql = "
            SELECT 
                locId,locDescription,locStatus,dateAdded,addedBy,displaySpecsId,
                locDescription+'-'+cast(locId as nvarchar) as description
            FROM 
                tblDisplayLocation
            WHERE
                locStatus = 'A'
                and displaySpecsId = {$display_specs_id}
            ORDER BY
                displaySpecsId,locId
        ";    
        return $this->getArrRes($this->execQry($sql));
    }  
}	
?>
