create table A_TipologiaAlimentazione (
  IDtipologiaAlimentazione int not null primary key auto_increment,
  ShortName varchar(16) not null,
  Description varchar(128) null default null,
  Autore varchar(5) null default null,
  Data datetime null default null,
  IDutente int(11) null default null);


