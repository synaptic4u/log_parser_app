select file, count(file) as cnt 
  from log_dump 
 where file = right(file, length(file)-locate("www-data",file)-17-16)
 group by file
 having count(file) > 1;


select file, count(file) as cnt 
  from log_dump 
 group by file
 having count(file) > 1;



create table audit_file(
    `logid` int(11) auto_increment primary key,
    `logdate` datetime,
    `alias` varchar(255),
    `filename` text,
    `contents` longtext,
    index `logdate`(`logdate`),
    index `alias`(`alias`),
    index `filename`(`filename`)
);

select right(file, length(file)-locate("www-data",file)-17-16) as file from log_dump where dumpid = 476381;


insert into audit_file(`filename`, `logdate`, `alias`, `contents`)
select distinct `file`, `dumpdate`, right(`file`, length(`file`)-locate("www-data",`file`)-17-16), `contents`
  from log_dump
 where file = right(file, length(file)-locate("www-data",file)-17-16)
 limit 1;