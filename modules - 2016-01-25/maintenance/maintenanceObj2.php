<?
$now = date('Y-m-d H:i:s');
ini_set("date.timezone","Asia/Manila");

class maintenanceObj2 extends commonObj {
    
    ##########( STORE RENTABLE )##########
    
    # GET SERVER CURRENT DATE
    function getServerCurrentDateTime()
    {
        $sql = "
            select GETDATE() as currentDateTime
        ";
        return $this->getSqlAssoc($this->execQry($sql));
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
    
    # LIST OF DISPLACE SPECS SERIES
    function listDiplaySpecsSeries($storeCode,$post_display_specs_id,$post_display_group,$post_display_series){
        
        # FILTER STORE CODE
        $branch = "a.strCode = {$storeCode}";
           
        # FILTER SPECS ID
        if($post_display_specs_id == 0){
            $specs_id = "";    
        }
        else{
            $specs_id = "and b.displaySpecsId = '{$post_display_specs_id}'";        
        }
        
        # FILTER GROUP ID
        if($post_display_group == 0){
            $group_id = "";    
        }
        else{
            $group_id = "and a.grpCode = '{$post_display_group}'";        
        }
        
        # FILTER GROUP ID
        if($post_display_series == ""){
            $series = "";    
        }
        else{
            $series = "and b.suffix+cast(b.series as nvarchar) like '%{$post_display_series}%'";        
        }
        
        $sql = "
            select
                a.strCode,a.locId,
                c.displaySpecsDesc,c.specsAmount,
                b.displaySpecsId,b.suffix,b.series,b.createdBy,b.dateCreated,b.status,b.sizeSpecs,
                b.suffix+cast(b.series as nvarchar) as suffixSeries  
            from 
                tblDisplayDaDtlStr a
            inner join 
                    tblDisplaySpecsSeries b 
                    on a.displaySpecsId = b.displaySpecsId 
                        and    a.series = b.series  
            inner join
                    tblDisplaySpecs c 
                    on a.displaySpecsId = c.displaySpecsId 
            where
                {$branch}
                {$specs_id}
                {$group_id}
                {$series}
            order by
                c.displaySpecsDesc,b.series asc
        ";    
        return $this->getArrRes($this->execQry($sql));
    }
    
    # LIST OF DEPARTMENT GROUP
    function getGrpList(){
        $sql = "
            SELECT     
                minCode, CAST(minCode AS nvarchar) + ' - ' + deptDesc AS description
            FROM         
                tblDepartment
            WHERE     
                (deptStat = 'A') 
                AND mdsgGrpTag = 'Y'";    
        return $this->getArrRes($this->execQry($sql));
    }
    
    # GET SELECTED DEPARTMENT GROUP
    function getSelectedGrpList($storeCode,$display_specs_id_series){
        
        # FILTER STORE CODE
        $branch = "a.strCode = {$storeCode}";
        
        $sql = "
            select 
                a.grpCode,CAST(b.minCode AS nvarchar) + ' - ' + b.deptDesc AS description
            from 
                tblDisplayDaDtlStr a 
            left join 
                tblDepartment b on b.minCode = a.grpCode
            where 
                {$branch}
                and cast(displaySpecsId as nvarchar)+cast(series as nvarchar) = {$display_specs_id_series}
        ";    
        return $this->getSqlAssoc($this->execQry($sql));
    }
    
    # GET LOCATION
    
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
    
    # GET SELECTED LOCATION
    function getSelectedLocation($display_specs_id_locid){
        $sql = "
            SELECT 
                locId,locDescription,locStatus,dateAdded,addedBy,displaySpecsId,
                locDescription+'-'+cast(locId as nvarchar) as description
            FROM 
                tblDisplayLocation
            WHERE
                cast(displaySpecsId as nvarchar)+cast(locId as nvarchar) = {$display_specs_id_locid}
        ";    
        return $this->getSqlAssoc($this->execQry($sql));
    }
    
    # GET SUPPLIER
    function findSupplier(){
        $sql = "
            SELECT 
                sql_mmpgtlib..APSUPP.ASNUM as suppCode, 
                sql_mmpgtlib..APSUPP.ASNAME as suppName, 
                CAST(sql_mmpgtlib..APSUPP.ASNUM  as nvarchar)+' - '+sql_mmpgtlib..APSUPP.ASNAME as suppCodeName
            FROM 
                sql_mmpgtlib..APSUPP 
            WHERE 
                sql_mmpgtlib..APSUPP.ASNAME not like '%NTBU%'
                and sql_mmpgtlib.dbo.APSUPP.ASTYPE = 1
            ORDER BY 
                sql_mmpgtlib.dbo.APSUPP.ASNUM
        ";
        return $this->getArrRes($this->execQry($sql));
    }
    
    # GET SELECTED SUPPLIER
    function findSelectedSupplier($storeCode,$display_specs_id_series){
        
        # FILTER STORE CODE
        $branch = "a.strCode = {$storeCode}";
        
        $sql = "
            select 
                a.suppCode,CAST(b.asnum AS nvarchar) + ' - ' + b.ASNAME AS description 
            from 
                tblDisplayDaDtlStr a
            left join 
                sql_mmpgtlib.dbo.APSUPP b on b.ASNUM = a.suppCode
            where
                {$branch} 
                and cast(displaySpecsId as nvarchar)+cast(series as nvarchar) = {$display_specs_id_series}
        ";
        return $this->getSqlAssoc($this->execQry($sql));
    }
    
    # DISPLAY AVAILABILITY
    function displayAvailability($display_specs_id,$series){
        
        $filter_usable = "usableTag = 'Y'";
        
        $sql = "
            select 
                availabilityTag,
                case when availabilityTag = 'Y' then 'YES' else 'NO' end as availabilityTagName
            from 
                tblDisplayDaDtlStr 
            where 
                {$filter_usable}
                and cast(displaySpecsId as nvarchar)+cast(series as nvarchar) = '{$display_specs_id}{$series}'
        ";    
        return $this->getSqlAssoc($this->execQry($sql));
    }
    
    # UPDATE RENTABLES
    
    function updateRentables($store_code,$display_specs_id_series,$group,$availability,$locId,$supplier){
        
        $current_date = $this->getServerCurrentDateTime();
        $current_date = date('Y-m-d h:i:s',strtotime($current_date['currentDateTime']));
               
        $sql = "
            update 
                tblDisplayDaDtlStr 
                set 
                    grpCode = '{$group}',
                    availabilityTag = '{$availability}',
                    locId = '{$locId}', 
                    taggedBy = '{$_SESSION['sts-userId']}',
                    taggedDate = '{$current_date}',
                    suppCode = '{$supplier}'
            from 
                tblDisplayDaDtlStr 
            where 
                strCode = {$store_code}
                and cast(displaySpecsId as nvarchar)+cast(series as nvarchar) = '{$display_specs_id_series}'
        ";   
        return $this->execQry($sql);
    }
    
    # GET BRANCHES
    function getBranches($user_name){
        $sql = "
            SELECT 
                strCode, brnDesc, cast(strCode as nvarchar)+' - '+brnDesc as strCodeName 
            FROM 
                pg_pf..tblbranches 
            where 
                strCode = (select strCode from tblUsers where userName = '$user_name')
            order by strCode
        ";    
        return $this->getSqlAssoc($this->execQry($sql));
    }
    
    # CHECK STORE AVAILABILITY
    function checkStoreAvailability($store_code,$display_specs_id){
        $sql = "
            select 
                * 
            from 
                tblDisplayDaDtlStr
            where 
                strCode = {$store_code}
            and 
                cast(displaySpecsId as nvarchar)+cast(series as nvarchar) = {$display_specs_id}
        ";    
        return $this->getSqlAssoc($this->execQry($sql));
    }
    
    function checkAddSeries($store_code,$display_specs_id){
        $sql = "
            select 
                top 1 *
            from 
                tblDisplaySpecsSeries
            where 
                cast(displaySpecsId as nvarchar)+cast(series as nvarchar) not in (
                    select 
                        cast(displaySpecsId as nvarchar)+cast(series as nvarchar) 
                    from 
                        tblDisplayDaDtlStr
                    where 
                        strCode = $store_code
                        and displaySpecsId = $display_specs_id
                )
            and displaySpecsId = $display_specs_id
            order by series asc
        ";
        return  $this->getRecCount($this->execQry($sql));    
    }
    
    function addSeries($store_code,$display_specs_id){
        $sql = "
            insert into 
                tblDisplayDaDtlStr(strCode,displaySpecsId,series,usableTag,availabilityTag,suffix)
            select 
                top 1 $store_code as strCode,displaySpecsId,series,'Y' as usableTag,'N' as availabilityTag,suffix 
            from 
                tblDisplaySpecsSeries
            where 
                cast(displaySpecsId as nvarchar)+cast(series as nvarchar) not in (
                    select 
                        cast(displaySpecsId as nvarchar)+cast(series as nvarchar) 
                    from 
                        tblDisplayDaDtlStr
                    where 
                        strCode = $store_code
                        and displaySpecsId = $display_specs_id
                )
            and displaySpecsId = $display_specs_id
            order by series asc
        ";
        $this->execQry($sql);    
    }
    
    function checkMinusSeries($store_code,$display_specs_id){
        $sql = "
            select 
                top 1 * 
            from 
                tblDisplayDaDtlStr
            where 
                strCode = $store_code
                and displaySpecsId = $display_specs_id
            order by 
                series desc
        ";
        return $this->getSqlAssoc($this->execQry($sql));
    } 
    
    function minusSeries($store_code,$display_specs_id){
        $sql = "
            delete
            from 
                tblDisplayDaDtlStr
            where 
                cast(displaySpecsId as nvarchar)+cast(series as nvarchar) in (
                    select 
                        top 1 cast(displaySpecsId as nvarchar)+cast(series as nvarchar) 
                    from 
                        tblDisplayDaDtlStr
                    where 
                        strCode = $store_code
                        and displaySpecsId = $display_specs_id
                    order by 
                        series desc
                )
                and strCode = $store_code
                and displaySpecsId = $display_specs_id
        ";
        $this->execQry($sql);    
    }
    
    ##########( DISPLAY SERIES )##########
    
    # LIST OF DISPLACE SPECS SERIES NUMBER
    function listDiplaySpecsSeriesNumber($post_display_specs_id){

        # FILTER SPECS ID
        if($post_display_specs_id == 0){
            $specs_id = "";    
        }
        else{
            $specs_id = "where a.displaySpecsId = '{$post_display_specs_id}'";        
        }
        
        $sql = "
            SELECT
                b.displaySpecsDesc,a.suffix,a.displaySpecsId,a.series,a.sizeSpecs,d.sizeSpecsDesc,a.dateCreated,c.fullName
            FROM 
                tblDisplaySpecsSeries a
            LEFT JOIN 
                tblDisplaySpecs b on a.displaySpecsId = b.displaySpecsId
            left join 
                tblUsers c on c.userId =  a.createdBy
            left join 
                tblSizeSpecs d on d.sizeSpecsId = a.sizeSpecs
            {$specs_id}
            order by
                b.displaySpecsDesc,a.series
        ";    
        return $this->getArrRes($this->execQry($sql));
    }
    
    # LIST OF RENTABLE SIZE SPECS
    function listDiplaySizeSpecs(){
                 
        $sql = "
            SELECT sizeSpecsId,sizeSpecsDesc FROM tblSizeSpecs order by sizeSpecsId
        ";    
        return $this->getArrRes($this->execQry($sql));
    }  
    
    function addMainSeries($display_specs_id,$display_size_specs_id,$user_id){
        $sql = "
            insert into tblDisplaySpecsSeries
            (displaySpecsId,series,createdBy,dateCreated,status,sizeSpecs,suffix)
            values({$display_specs_id},RIGHT('00000'+CAST((select top 1 series from tblDisplaySpecsSeries where displaySpecsId = {$display_specs_id}  order by series desc)+1 AS VARCHAR(5)),5),{$user_id},GETDATE(),'A',{$display_size_specs_id},(select suffix from tblDisplaySpecs where displaySpecsId = {$display_specs_id}))
        ";
        $this->execQry($sql);    
    }  
    
    function minusMainSeries($display_specs_id){
        $sql = "
            delete from tblDisplaySpecsSeries where series in (
                select top 1 series from tblDisplaySpecsSeries
                where displaySpecsId = {$display_specs_id} 
                order by series desc
            )
            and displaySpecsId = {$display_specs_id} 
        ";
        $this->execQry($sql);    
    }
    
    function findSupplier_for_autocomplete($terms){
        $sql = "SELECT TOP 10 sql_mmpgtlib..APSUPP.ASNUM as suppCode, sql_mmpgtlib..APSUPP.ASNAME as suppName, sql_mmpgtlib..APADDR.AACONT as contactPerson 
        FROM sql_mmpgtlib..APSUPP 
        LEFT  JOIN sql_mmpgtlib..APADDR on sql_mmpgtlib..APSUPP.ASNUM  = sql_mmpgtlib..APADDR.AANUM
        WHERE (sql_mmpgtlib..APSUPP.ASNUM like '%$terms%') or (sql_mmpgtlib..APSUPP.ASNAME like '%$terms%')     
                AND  sql_mmpgtlib..APSUPP.ASNAME not like '%NTBU%'";
        return $this->getArrRes($this->execQry($sql));
    }   
    
}
?>