import csv
with open('FTEplanningFY23.csv', 'rb') as f:
    reader = csv.reader(f)
    count=0
    header_array=[]
    all_dict={}
    header_values=[]
    header_dict={}
    for row in reader:
        if count==0:
           header_array=row
        elif count==1:
           header_values=row
        else:
           i=0
           fte_dict={}
           for column in row:
               znumber=row[0] 
               if column:
                   fte_dict[header_array[i]]=column 
               i+=1
           all_dict[znumber]=fte_dict
        count+=1
#Inserts for workpackage
i=0
startdate='2022-10-01'
enddate='2023-10-01'
for wp in header_array:
    if i>1:
        #JAGS SYS0 1.5
        forcasted=header_values[i]
        if forcasted=="":
           forcasted=0
        insert="""INSERT INTO tbl_workpackage (wp_id, workpackage_name, startdate, enddate, manager_id, forcasted_fte_total) 
                    VALUES (NULL, '%s', '%s','%s','%s','%s');"""%(wp,startdate,enddate,7,forcasted)
        #print wp,header_values[i]
        print insert
        
    i+=1

print "<br>"
print "<br>"
print "<br>"
print "<br>"
output_str=""
for znumber,wp_dict in all_dict.iteritems():
    code_dict={}
    for key,value in wp_dict.iteritems():
        if key=="Code":
            name_list=value.split(",")
            name="%s %s"%(name_list[1],name_list[0])
            
        else:    
            if key!="":
                code_dict[key]=value  
    #print znumber,name
    insert_staff="""INSERT INTO `tbl_staff` (`staff_id`, `znumber`, `staff_name`, `team_id`, `startdate`, `enddate`)
                     VALUES (NULL, '%s','%s', '1', '%s', NULL);"""%(znumber,name,startdate)
    #print insert_staff
    output_str+="%s,%s"%(znumber,name)
    for wp,percent in code_dict.iteritems(): 
        output_str+=",%s,%s"%(wp,percent)
    print output_str
    output_str=""
