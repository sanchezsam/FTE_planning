SELECT
vw_fte_mapping.startdate,
vw_fte_mapping.enddate,
tbl_workpackage.forcasted_fte_total,
round(sum(vw_fte_mapping.forcasted_amount),2) as forcasted,
round((tbl_workpackage.forcasted_fte_total-round(sum(vw_fte_mapping.forcasted_amount),2)),2) as difference,
vw_fte_mapping.workpackage_name
FROM vw_fte_mapping,tbl_workpackage
where 
vw_fte_mapping.workpackage_name=tbl_workpackage.workpackage_name
AND
vw_fte_mapping.startdate=tbl_workpackage.startdate
group by tbl_workpackage.startdate,vw_fte_mapping.workpackage_name

create view vw_over_or_under as SELECT
vw_fte_mapping.startdate,
vw_fte_mapping.enddate,
tbl_workpackage.forcasted_fte_total,
round(sum(vw_fte_mapping.forcasted_amount),2) as forcasted,
round((tbl_workpackage.forcasted_fte_total-round(sum(vw_fte_mapping.forcasted_amount),2)),2) as difference,
vw_fte_mapping.workpackage_name
FROM vw_fte_mapping,tbl_workpackage
where 
vw_fte_mapping.workpackage_name=tbl_workpackage.workpackage_name
AND
vw_fte_mapping.startdate=tbl_workpackage.startdate
group by tbl_workpackage.startdate,vw_fte_mapping.workpackage_name



####TESTing
SELECT
vw_fte_mapping.startdate,
vw_fte_mapping.enddate,
tbl_workpackage.forcasted_fte_total,
round(sum(vw_fte_mapping.forcasted_amount),2) as forcasted,
round((tbl_workpackage.forcasted_fte_total-round(sum(vw_fte_mapping.forcasted_amount),2)),2) as difference,
vw_fte_mapping.workpackage_name
FROM vw_fte_mapping,tbl_workpackage
where 
vw_fte_mapping.workpackage_name=tbl_workpackage.workpackage_name
AND vw_fte_mapping.startdate=tbl_workpackage.startdate
AND tbl_workpackage.workpackage_name like "%JAGL%"
group by tbl_workpackage.startdate,vw_fte_mapping.workpackage_name
