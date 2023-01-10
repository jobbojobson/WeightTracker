create table t_weight(
	date int primary key,
	kilograms real not null,
	note varchar(200)
);

create table t_user(
	gender varchar(1),
	dob int,
	height int,
	activity_multiplier real,
	deficit int,
	goal_kg real 
);

create view v_weight as 
select
	date,
	kilograms,
	avg(kilograms) over(order by date rows between 6 preceding and current row) as last_week_average,
	(kilograms * 2.2046) as pounds,
	(cast((kilograms * 2.2046) / 14 as int) || ' ' || cast((kilograms * 2.2046) % 14 as int)) as stone,
	note
from t_weight;
	
	



insert into t_user values (
'M',
CAST(strftime('%s', '1970-01-01') as int),
179,
1.375,
1100,
80.0);
