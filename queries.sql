Top users by email:

select u.email, count(v.id) from candidates c join votes v on c.id = v.candidate_id join users u on u.fb_id=c.fb_id where c.fb_id is not null  group by v.candidate_id having count(v.id)>30 order by count(v.id) desc;

