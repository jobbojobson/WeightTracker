--database creation script for MariaDB 10. to be run as the root user

create database if not exists WeightTracker;

use WeightTracker;


create table t_weight(
	date date primary key,
	kilograms decimal(4,1) unsigned not null,
	note varchar(200)
);

create table t_user(
	gender binary(1),
	dob date,
	height int,
	activity_multiplier float,
	deficit int,
	goal_kg decimal(4,1) unsigned
);

create table t_image(
	date date primary key,
	image blob not null,
	mime varchar(100) not null,
	foreign key (date) references t_weight(date) on delete cascade
);

create or replace view v_weight as 
select
	w.date,
	w.kilograms,
	avg(w.kilograms) over(order by w.date rows between 6 preceding and current row) as last_week_average,
	(w.kilograms * 2.2046) as pounds,
	concat(cast(floor((w.kilograms * 2.2046) / 14) as char), ' ', cast(floor((w.kilograms * 2.2046) % 14) as char)) as stone,
	w.note,
	case when i.date is not null then 1 else 0 end as image_exists
from t_weight w
left join t_image i on w.date = i.date;

insert into t_user values (
'M',
'1970-01-01',
179,
1.375,
1100,
80.0);

create user if not exists WeightTracker identified by 'WeightTracker' password expire never;
grant select, insert, update, delete on t_weight to WeightTracker;
grant select, insert, update, delete on t_user to WeightTracker;
grant select, insert, update, delete on t_image to WeightTracker;
grant select on v_weight to WeightTracker;
