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
    
    # GET DATA ON TABLE tblDisplaySpecs PER displaySpecsId
    function getDiplaySpecs($display_specs_id){
                 
        $sql = "
            select 
                displaySpecsId,displaySpecsDesc,createdBy,dateCreated,stat,specsAmount
            from 
                tblDisplaySpecs
            where
                stat = 'A'
                and displaySpecsId = {$display_specs_id}
            order by
                displaySpecsDesc
        ";                                                               
        return $this->getSqlAssoc($this->execQry($sql));
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
    
    # LIST AVAILABLE RENTABLES
    function listAvailableRentables($display_specs_id,$display_location){
                 
        $sql = "
            SELECT
                a.strCode,a.locId,
                c.displaySpecsDesc,c.specsAmount,
                b.displaySpecsId,b.suffix,b.series,b.createdBy,b.dateCreated,b.status,b.sizeSpecs,
                b.suffix+cast(b.series as nvarchar) as suffixSeries  
            FROM 
                tblDisplayDaDtlStr a
            INNER JOIN
                tblDisplaySpecsSeries b 
                on a.displaySpecsId = b.displaySpecsId 
                and    a.series = b.series  
            INNER JOIN
                tblDisplaySpecs c 
                on a.displaySpecsId = c.displaySpecsId
            WHERE
                b.displaySpecsId = {$display_specs_id}
                and a.locId = {$display_location}
                and a.stsRefNo is null
            GROUP BY
                a.strCode,a.locId,
                c.displaySpecsDesc,c.specsAmount,
                b.displaySpecsId,b.suffix,b.series,b.createdBy,b.dateCreated,b.status,b.sizeSpecs,
                b.suffix+cast(b.series as nvarchar)
        ";    
        return $this->getArrRes($this->execQry($sql));
    }  
}	
?>
