create database tempovideo;
use tempovideo;

create table Rol(id int primary key, 
omschr varchar(45)
);

create table Medewerker(
id int primary key, 
rolid int, 
achternaam varchar(45), 
wachtwoordid int
);

create table Wachtwoord(
id int primary key, 
wachtwoord varchar(255)
);

create table Klant(
id int auto_increment primary key, 
naam varchar(50), 
adres varchar(50), 
postcode varchar(7), 
woonplaats varchar(25), 
telefoonnummer varchar(10), 
email varchar(45), 
wachtwoordid int,
active bool
);

ALTER TABLE `Medewerker`
ADD FOREIGN KEY (rolid)
REFERENCES Rol(id);

ALTER TABLE `Medewerker`
ADD FOREIGN KEY (wachtwoordid)
REFERENCES Wachtwoord(id);

create table `Order`(
id int primary key, 
klantid int, 
afleverdatum VARCHAR(50),
aflevertijd VARCHAR(50), 
ophaaldatum VARCHAR(50),
ophaaltijd VARCHAR(50), 
bedrag float,
reminder bool, 
openbedrag float,
orderdatum varchar(50)
);

create table Orderregel(
exemplaarid int, 
orderid int, 
primary key(exemplaarid, orderid)
);

ALTER TABLE `Order`
ADD FOREIGN KEY (klantid)
REFERENCES Klant(id);

create table Exemplaar(
id int auto_increment primary key, 
filmid int, 
statusid int, 
aantalVerhuur int,
reservering bool
);

ALTER TABLE `Orderregel`
ADD FOREIGN KEY (exemplaarid)
REFERENCES Exemplaar(id);

create table Film(
id int primary key, 
titel varchar(50), 
acteur varchar(100), 
omschr varchar(200)
);

ALTER TABLE `Exemplaar`
ADD FOREIGN KEY (filmid)
REFERENCES Film(id);

ALTER TABLE Film
ADD genre varchar(50);
ALTER TABLE Film
ADD img varchar(50);

drop table Medewerker;
rename table Klant to Persoon;

ALTER TABLE Persoon
ADD rolid int;

ALTER TABLE `Persoon`
ADD FOREIGN KEY (rolid)
REFERENCES Rol(id);

create table Status(
id int primary key, 
omschr varchar(50)
);

ALTER TABLE `Exemplaar`
ADD FOREIGN KEY (statusid)
REFERENCES Status(id);

INSERT INTO Rol (id, omschr) VALUES (1, "Klant");
INSERT INTO Rol (id, omschr) VALUES (2, "Bezorger");
INSERT INTO Rol (id, omschr) VALUES (3, "baliemedewerker");
INSERT INTO Rol (id, omschr) VALUES (4, "eigenaar");
INSERT INTO Rol (id, omschr) VALUES (5, "Geblokkeerd");

INSERT INTO `Status`(id, omschr) VALUES(1, "Beschikbaar");
INSERT INTO `Status`(id, omschr) VALUES(2, "NIET Beschikbaar");

ALTER TABLE `Order`
ADD `Afhandeling` bool;

ALTER TABLE `Order`
ADD `besteld` bool;

create table Reservering(id int auto_increment primary key, filmid int, persoonid int, datum varchar(50));
alter table Reservering
add foreign key (filmid)
references Film(id);

alter table Reservering
add foreign key (persoonid)
references Persoon(id);

-- SELECT * FROM `Rol`;
-- SELECT * FROM `Wachtwoord`;
SELECT * FROM `Order`;
SELECT * FROM `Orderregel`;
SELECT * FROM `Exemplaar`;
SELECT * FROM `Film`;
SELECT * FROM `Persoon`;
SELECT * FROM `Reservering`;


INSERT INTO Wachtwoord(id, wachtwoord) VALUES (1, '$2y$10$GjFXmwAmtSTX5f7WR3IIpebLaNCCv0ehFZCE1lEttXhcYGgCp9EB.');
INSERT INTO Persoon (naam, adres, postcode, woonplaats, telefoonnummer, email, wachtwoordid, active, rolid) VALUES ('Eigenaar', 'columbuslaan 540', '3526 EP', 'Utrecht', '0302815100', 'eigenaar@jeroengrooten.nl', 1, 1, 4);

INSERT INTO Wachtwoord(id, wachtwoord) VALUES (2, '$2y$10$GjFXmwAmtSTX5f7WR3IIpebLaNCCv0ehFZCE1lEttXhcYGgCp9EB.');
INSERT INTO Persoon (naam, adres, postcode, woonplaats, telefoonnummer, email, wachtwoordid, active, rolid) VALUES ('Baliemedewerker', 'columbuslaan 540', '3526 EP', 'Utrecht', '0302815100', 'balie@jeroengrooten.nl', 2, 1, 3);

INSERT INTO Wachtwoord(id, wachtwoord) VALUES (3, '$2y$10$GjFXmwAmtSTX5f7WR3IIpebLaNCCv0ehFZCE1lEttXhcYGgCp9EB.');
INSERT INTO Persoon (naam, adres, postcode, woonplaats, telefoonnummer, email, wachtwoordid, active, rolid) VALUES ('Bezorger', 'columbuslaan 540', '3526 EP', 'Utrecht', '0302815100', 'bezorger@jeroengrooten.nl', 2, 1, 2);

INSERT INTO Wachtwoord(id, wachtwoord) VALUES (4, '$2y$10$GjFXmwAmtSTX5f7WR3IIpebLaNCCv0ehFZCE1lEttXhcYGgCp9EB.');
INSERT INTO Persoon (naam, adres, postcode, woonplaats, telefoonnummer, email, wachtwoordid, active, rolid) VALUES ('Hans Odijk', 'columbuslaan 540', '3526 EP', 'Utrecht', '0302815100', 'klant@jeroengrooten.nl', 4, 1, 1);
