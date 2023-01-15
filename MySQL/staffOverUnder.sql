SELECT
vw_team_forcast.staff_name,
round(sum(vw_team_forcast.forcasted_amount),2) as forcasted,
tbl_staff.fte_amount,
round(sum(vw_team_forcast.forcasted_amount),2)-tbl_staff.fte_amount as difference,
vw_team_forcast.team_name,
vw_team_forcast.group_name,
vw_team_forcast.startdate,
vw_team_forcast.enddate
FROM vw_team_forcast,tbl_staff
where
vw_team_forcast.staff_id=tbl_staff.staff_id
group by vw_team_forcast.enddate,vw_team_forcast.staff_name  
ORDER BY `vw_team_forcast`.`staff_name` ASC



create view vw_staff_over_under as SELECT
vw_team_forcast.staff_name,
round(sum(vw_team_forcast.forcasted_amount),2) as forcasted,
tbl_staff.fte_amount,
round(sum(vw_team_forcast.forcasted_amount),2)-tbl_staff.fte_amount as difference,
vw_team_forcast.team_name,
vw_team_forcast.group_name,
vw_team_forcast.startdate,
vw_team_forcast.enddate
FROM vw_team_forcast,tbl_staff
where
vw_team_forcast.staff_id=tbl_staff.staff_id
group by vw_team_forcast.enddate,vw_team_forcast.staff_name



#pull data
SELECT * FROM `vw_staff_over_under`
where group_name="HPC-SYS"
and YEAR(enddate)="2023"
and difference!=0
order by difference
