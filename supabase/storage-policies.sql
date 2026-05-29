create policy "Allow read quiz-media"
on storage.objects
for select
to anon
using (bucket_id = 'quiz-media');

create policy "Allow upload quiz-media"
on storage.objects
for insert
to anon
with check (bucket_id = 'quiz-media');

create policy "Allow update quiz-media"
on storage.objects
for update
to anon
using (bucket_id = 'quiz-media')
with check (bucket_id = 'quiz-media');

create policy "Allow delete quiz-media"
on storage.objects
for delete
to anon
using (bucket_id = 'quiz-media');

create policy "Allow read reward"
on storage.objects
for select
to anon
using (bucket_id = 'reward');

create policy "Allow upload reward"
on storage.objects
for insert
to anon
with check (bucket_id = 'reward');

create policy "Allow update reward"
on storage.objects
for update
to anon
using (bucket_id = 'reward')
with check (bucket_id = 'reward');

create policy "Allow delete reward"
on storage.objects
for delete
to anon
using (bucket_id = 'reward');
