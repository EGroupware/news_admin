create table webpage_news (
  news_id           serial,
  news_date         int,
  news_subject      varchar(255),
  news_submittedby  varchar(255),
  news_content      text,
  news_status       char(8)
);

insert into webpage_news (news_date,news_subject,news_submittedby,news_content,news_status) values 
                         (date_part('epoch',now()),'Sample news item','1','This is a sample item to show what this program does.',
                         'Active');