create table if not exists users (
    id int primary key auto_increment,
    name varchar(255) not null,
    surname varchar(255) not null,
    nickname varchar(255) not null unique,
    password varchar(255) not null
);

create table if not exists subjects (
    id int primary key auto_increment,
    name varchar(255) not null,
    id_user int not null,
    foreign key (id_user) references users(id) 
);

create table if not exists topics(
    id int primary key auto_increment,
    title varchar(255) default '',
    id_subject int not null,
    foreign key (id_subject) references subjects(id)
);

create table if not exists task(
    id int primary key auto_increment,
    title varchar(255) not null,
    id_topics int not null,
    status ENUM("to complete","complete","suspended") not null,
    created_at timestamp default CURRENT_TIMESTAMP,
    completed_at timestamp,
    foreign key (id_topics) references topics(id)
);

create table if not exists study_sessions(
    id int primary key auto_increment,
    start_session timestamp default CURRENT_TIMESTAMP,
    finished_session timestamp,
    description text default '',
    id_user int not null,
    foreign key (id_user) references users(id)
);

