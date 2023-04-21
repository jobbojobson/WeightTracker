--SQLite 3 schema

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

create table t_image(
	date int primary key,
	image blob not null,
	mime varchar(100) not null,
	foreign key (date) references t_weight(date) on delete cascade
);

create view v_weight as 
select
	w.date,
	w.kilograms,
	avg(w.kilograms) over(order by w.date rows between 6 preceding and current row) as last_week_average,
	(w.kilograms * 2.2046) as pounds,
	(cast((w.kilograms * 2.2046) / 14 as int) || ' ' || cast((w.kilograms * 2.2046) % 14 as int)) as stone,
	w.note,
	case when i.date is not null then 1 else 0 end as image_exists
from t_weight w
left join t_image i on w.date = i.date;

insert into t_user values (
'M',
CAST(strftime('%s', '1970-01-01') as int),
179,
1.375,
1100,
80.0);
