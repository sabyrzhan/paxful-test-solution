create table user_log
(
    id          serial constraint user_log_pk primary key,
    path        varchar(255) not null,
    ip          varchar(32) not null,
    create_date timestamp without time zone default current_timestamp not null
);

