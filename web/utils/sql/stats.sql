select cast(registration_dt as date) as dt, count(*) as cnt, sum(IF(status='ACTIVE',1,0)) as cnt_active
from user
where registration_dt>=DATE_SUB(NOW(),INTERVAL 14 DAY)
group by dt
having dt<CURDATE()
order by dt desc

select cast(dt as date) as dt2, count(*) as cnt
from balance_log
where id>(select max(id) from balance_log)-5000000
group by dt2
having dt2<CURDATE()
order by dt2 desc

select dt,count(*) as cnt
from user_activity_log
where dt>=DATE_SUB(NOW(),INTERVAL 14 DAY)
group by dt
having dt<CURDATE()
order by dt desc

select count(distinct user_id)
from user_activity_log
where dt>=DATE_SUB(NOW(),INTERVAL 3 DAY)

select sum(online),sum(online_mobile)
from chat_user

-- cleanup devices
update user_device set setting_notification_all=0
where last_seen<DATE_SUB(NOW(),INTERVAL 14 DAY)