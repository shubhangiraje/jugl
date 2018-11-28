update user set network_size=1,network_levels=1

update user u
join (
select parent_id as n_user_id,sum(network_size-IF(status='ACTIVE',0,1)) as n_size,max(network_levels) as n_levels,SUM(IF(status in ('ACTIVE','BLOCKED','DELETED'),1,0)) as referrals
from user
group by parent_id) as t
on (u.id=t.n_user_id)
set network_size=coalesce(n_size,0)+1,network_levels=coalesce(n_levels,0)+1,invitations=coalesce(referrals,0);


-- update invitations
update user set invitations=0

update user u
join (
select parent_id as n_user_id,count(*) as referrals
from user
where status in ('ACTIVE','BLOCKED')
group by parent_id) as t
on (u.id=t.n_user_id)
set invitations=coalesce(referrals,0);
