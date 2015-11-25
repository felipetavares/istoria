# Database for istoria

#drop database istoria;

# create DB
create database istoria;

use istoria;

# create tables

# users
create table user (
	id int not null auto_increment,
	name varchar(255) not null,
	email varchar(255) not null,
	pass varchar(255) not null,
	primary key(id)
);

# infos
create table info (
	id int not null auto_increment,
	name varchar(255) not null,
	content varchar(16384) not null,
	primary key(id)
);

# relationships between infos
create table info_info (
	id_a int not null,
	id_b int not null,

	primary key(id_a, id_b),
	foreign key(id_a) references info(id),
	foreign key(id_b) references info(id)
);

# edits
create table edit (
	id int not null auto_increment,

	user int not null,
	info int not null,
	name varchar(255) not null,
	content varchar(16384) not null,
	published tinyint(1) not null,

	primary key(id),
	foreign key(user) references user(id),
	foreign key(info) references info(id)
);

# votes
create table votes (
	user int not null,
	edit int not null,
	primary key(user, edit),
	foreign key(user) references user(id),
	foreign key(edit) references edit(id)
);

# relationships between infos, for each edit
create table edit_info (
	edit int not null,
	info int not null,

	primary key(edit, info),
	foreign key(edit) references edit(id),
	foreign key(info) references info(id)
);

# notifications
create table notifications (
	id int not null auto_increment,
	user int not null,
	content varchar(255) not null,
	viewed tinyint(1) not null,
	primary key(id),
	foreign key(user) references user(id)
);
