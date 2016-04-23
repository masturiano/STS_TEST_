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
                b.suffix+cast(b.series as nvarchar) as suffixSeries,
                cast(b.displaySpecsId as nvarchar)+cast(b.series as nvarchar)+cast(a.strCode as nvarchar) as displaySpecsIdSeriesStore,
                d.compCode  
            FROM 
                tblDisplayDaDtlStr a
            INNER JOIN
                tblDisplaySpecsSeries b 
                on a.displaySpecsId = b.displaySpecsId 
                and    a.series = b.series  
            INNER JOIN
                tblDisplaySpecs c 
                on a.displaySpecsId = c.displaySpecsId
            INNER JOIN
                tblBranches d
                on a.strCode = d.strCode 
            WHERE
                b.displaySpecsId = {$display_specs_id}
                and a.locId = {$display_location}
                and a.stsRefNo is null
            GROUP BY
                a.strCode,a.locId,
                c.displaySpecsDesc,c.specsAmount,
                b.displaySpecsId,b.suffix,b.series,b.createdBy,b.dateCreated,b.status,b.sizeSpecs,
                b.suffix+cast(b.series as nvarchar),
                cast(b.displaySpecsId as nvarchar)+cast(b.series as nvarchar)+cast(a.strCode as nvarchar),
                d.compCode  
        ";    
        return $this->getArrRes($this->execQry($sql));
    }  
    
    # NASA DA OBJ NA    
    function getEwt($dept,$class,$subClass){
        $sql = "
            SELECT 
                ewtMultiplier 
            FROM 
                tblStsHierarchy 
            WHERE 
                (stsDept = '$dept') 
                AND (stsCls = '$class') 
                AND (stsSubCls = '$subClass')
        ";    
        $arr = $this->getSqlAssoc($this->execQry($sql));
        return $arr['ewtMultiplier'];
    }
    function getVat(){
        $sql = "
            SELECT 
                top 1 vatMultiplier 
            FROM 
                tblVat";    
        $arr = $this->getSqlAssoc($this->execQry($sql));
        return $arr['vatMultiplier'];
    }
    # NASA DA OBJ NA    
    function addStsDtl($arr){
        
        $idSeriesStore = $arr['txtCounterId'];
        $thePostIdArray = explode(',', $idSeriesStore);
        
        $counter = $arr['txtCounter'];
        
        $numberOfUnits = 1;
        $contractNumber = 'NULL';
        $stsNo = 'NULL';
        
        for($a=0;$a<=$counter;$a++){
            // $thePostIdArray[$a];
            // $arr['txtDisplaySpecsIdSeriesStore_'.$thePostIdArray[$a]]
            // $arr['txtGrossAmount_'.$thePostIdArray[$a]]
            // $arr['txtCounter']
            // $arr['txtPerMonth_'.$thePostIdArray[$a]]      
            $sql = "
                INSERT INTO
                    tblStsDaDetail
                    (stsRefno,compCode,strCode,displayType,brand,location,daSize,dispSpecs,noUnits,daRemarks,contractNo,stsNo,perUnitAmt,stsAmt,stsVatAmt,stsEwtAmt)
                VALUES
                    ({$arr['txtReferenceNo']},'{$arr['txtCompCode_'.$thePostIdArray[$a]]}','{$arr['txtStoreCode_'.$thePostIdArray[$a]]}','{$arr['txtDispTyp']}','{$arr['txtBrand']}',
                    '{$arr['cmb_location']}','{$arr['txtSizeSpecs_'.$thePostIdArray[$a]]}','{$arr['cmb_specs']}',{$numberOfUnits},'{$arr['txtRem']}',{$contractNumber},{$stsNo},
                    '{$arr['txtDisplayFee_'.$thePostIdArray[$a]]}','{$arr['txtDisplayFee_'.$thePostIdArray[$a]]}','{$arr['txtVatAmount_'.$thePostIdArray[$a]]}',
                    '{$arr['txtEwtAmount_'.$thePostIdArray[$a]]}')
            "; 
            $this->execQry($sql); 
        }   
    }
}	
?>
