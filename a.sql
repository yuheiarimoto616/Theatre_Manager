drop table Customer CASCADE constraints;
drop table Review CASCADE constraints;
drop table Theatre CASCADE constraints;
drop table Room CASCADE constraints;
drop table Movie CASCADE constraints;
drop table Shows CASCADE constraints;
drop table MovieSchedule CASCADE constraints;
drop table Ticket CASCADE constraints;
drop table ConcessionStand CASCADE constraints;
drop table Foodstuff CASCADE constraints;
drop table Offers CASCADE constraints;
drop table Eats CASCADE constraints;

CREATE TABLE Customer (
	ID 		VARCHAR(20) PRIMARY KEY,
	name   		VARCHAR(30),
	email   		VARCHAR(40)  UNIQUE NOT NULL,
	phone	 	VARCHAR(10),
	dateOfBirth 	DATE,
UNIQUE  (name, phone, dateOfBirth)
);
grant select on Customer to public;

CREATE TABLE Movie (
	ID		VARCHAR(20) PRIMARY KEY,
	duration	INTEGER NOT NULL,
	rating		VARCHAR(10),
	name 		VARCHAR(50) NOT NULL
);
grant select on Movie to public;

CREATE TABLE Review (
ID		VARCHAR(20) PRIMARY KEY,
star		FLOAT,
commentText      	VARCHAR(500),
customerID   	VARCHAR(20)  NOT NULL,
movieID        	VARCHAR(20)  NOT NULL,
FOREIGN KEY (customerID) REFERENCES Customer(ID),
FOREIGN KEY (movieID) REFERENCES Movie(ID)
);
grant select on Review to public;

CREATE TABLE Theatre (
	address 	VARCHAR(50) PRIMARY KEY,
	phone		VARCHAR(20) UNIQUE
);

grant select on Theatre to public;

CREATE TABLE Room (
	roomNum 	INTEGER,
	address	VARCHAR(50) NOT NULL,
	capacity	INTEGER,
	PRIMARY KEY (roomNum, address),
	FOREIGN KEY (address) REFERENCES Theatre ON DELETE CASCADE);

grant select on Room to public;

CREATE TABLE Shows (
	address 	VARCHAR(50),
	roomNum	INTEGER,	
	movieID	VARCHAR(20),
	PRIMARY KEY (address, roomNum, movieID),
	FOREIGN KEY (roomNum, address) REFERENCES Room(roomNum, address) ON DELETE CASCADE,
	FOREIGN KEY (movieID) REFERENCES Movie(ID)
);
grant select on Shows to public;

CREATE TABLE MovieSchedule (
	time 		TIMESTAMP,
	roomNum 	INTEGER,
	address	VARCHAR(50),
	movieID 	VARCHAR(20) NOT NULL,
	PRIMARY KEY (time, roomNum, address),
	FOREIGN KEY (address, roomNum, movieID) REFERENCES Shows(address, roomNum, movieID)
	ON DELETE CASCADE
);
grant select on MovieSchedule to public;

CREATE TABLE Ticket (
	ID 		VARCHAR(20) 	PRIMARY KEY,
	type		VARCHAR(20),
	seatNum 	VARCHAR(4),
	time		TIMESTAMP NOT NULL,
	customerID	VARCHAR(20) NOT NULL,
	price		FLOAT NOT NULL,
	address 	VARCHAR(50) NOT NULL,
	roomNum	INTEGER NOT NULL,
	UNIQUE (time, roomNum, address, seatNum),
	FOREIGN KEY (customerID) REFERENCES Customer(ID),
	FOREIGN KEY (time, roomNum, address) REFERENCES MovieSchedule(time, roomNum, address) ON DELETE CASCADE
);
grant select on Ticket to public;

CREATE TABLE ConcessionStand (
	ID 		VARCHAR(20) PRIMARY KEY,
	name   		VARCHAR(20),
	address           	VARCHAR(50) NOT NULL,
	FOREIGN KEY (address) REFERENCES Theatre(address) ON DELETE CASCADE
);
grant select on ConcessionStand to public;

CREATE TABLE Foodstuff (
	ID 		VARCHAR(20) PRIMARY KEY,
	name   		VARCHAR(20) NOT NULL,
	price           	FLOAT NOT NULL
);
grant select on Foodstuff to public;

CREATE TABLE Offers (
	foodStuffID 		VARCHAR(20),
	concessionStandID   	VARCHAR(20),
	PRIMARY KEY (foodStuffID, concessionStandID),
	FOREIGN KEY (foodStuffID) REFERENCES Foodstuff(ID),
	FOREIGN KEY (concessionStandID) REFERENCES ConcessionStand(ID) ON DELETE CASCADE
);
grant select on Offers to public;

CREATE TABLE Eats (
	customerID 		VARCHAR(20),
	concessionStandID   	VARCHAR(20),
	foodStuffID		VARCHAR(20),
	PRIMARY KEY (customerID, concessionStandID, foodStuffID),
	FOREIGN KEY (customerID) REFERENCES Customer(ID),
	FOREIGN KEY (foodStuffID, concessionStandID) REFERENCES Offers(foodStuffID, concessionStandID) ON DELETE CASCADE
);
grant select on Eats to public;
 
-- grant select on authors to public;
 
INSERT INTO Customer(ID, name, email, phone, dateOfBirth) 
VALUES ('1', 'Yuhei Arimoto', 'yuhei61627@icloud.com', '6043652065', DATE '2002-12-20');
INSERT INTO Customer(ID, name, email, phone, dateOfBirth) 
VALUES ('2', 'Sean Quan', 'seanpquan@gmail.com', '6044961125', DATE '2002-12-31');
INSERT INTO Customer(ID, name, email, phone, dateOfBirth) 
VALUES ('3', 'Tanmay Goyal', 'sancriuse75@gmail.com', '2083891111', DATE '2003-01-20');
INSERT INTO Customer(ID, name, email, phone, dateOfBirth) 
VALUES ('4', 'Jack Aaaa', 'jack1234@icloud.com', '6041655235', DATE '2000-10-05');
INSERT INTO Customer(ID, name, email, phone, dateOfBirth) 
VALUES ('5', 'Sam Bcde', 'samsam@gmail.com', '1234561234', DATE '2000-01-05');

INSERT INTO  Movie(ID, duration, rating, name) 
VALUES ('10', 148 , 'PG13' , 'Spider-Man: No Way Home');
INSERT INTO  Movie(ID, duration, rating, name) 
VALUES ('53', 121 , 'PG13' , 'Spider-Man');
INSERT INTO  Movie(ID, duration, rating, name) 
VALUES ('2345', 95 , '18A' , 'Cocaine Bear');
INSERT INTO  Movie(ID, duration, rating, name) 
VALUES ('157982', 126 , 'G' , 'The Karate Kid');
INSERT INTO  Movie(ID, duration, rating, name) 
VALUES ('113', 125 , 'PG13' , 'Ant-Man and the Wasp: Quantumania');

INSERT INTO Review(ID, star, commentText, customerID, movieID)
VALUES ('1', 4.0, 'Great movie', '1', '10');
INSERT INTO Review(ID, star, commentText, customerID, movieID)
VALUES ('2', 4.5, 'Awesome plot', '2', '10');
INSERT INTO Review(ID, star, commentText, customerID, movieID)
VALUES ('3', 1.0, 'Just boring', '5', '2345');
INSERT INTO Review(ID, star, commentText, customerID, movieID)
VALUES ('4', 3.2, 'Super Mid movie, not bad', '3', '157982');
INSERT INTO Review(ID, star, commentText, customerID, movieID)
VALUES ('10', 4.1, 'Pretty good. Great actors', '4', '113');

INSERT INTO Theatre(address, phone) 
VALUES ('1234 West Mall, Vancouver, BC', '3251112682');
INSERT INTO Theatre(address, phone) 
VALUES ('5959 Student Union Bouldvard, Vancouver, BC', '6041112222');
INSERT INTO Theatre(address, phone) 
VALUES ('2929 Main Mall, Victoria, BC', '6049871234');
INSERT INTO Theatre(address, phone) 
VALUES ('104 58 Ave SE, Calgary, AB', '4032555501');
INSERT INTO Theatre(address, phone) 
VALUES ('452 SW Marine Dr, Vancouver, BC', '6046300414');

INSERT INTO Room(roomNum, address, capacity) 
VALUES (1, '1234 West Mall, Vancouver, BC', 100);
INSERT INTO Room(roomNum, address, capacity) 
VALUES (2, '1234 West Mall, Vancouver, BC', 150);
INSERT INTO Room(roomNum, address, capacity) 
VALUES (1, '452 SW Marine Dr, Vancouver, BC', 85);
INSERT INTO Room(roomNum, address, capacity) 
VALUES (4, '452 SW Marine Dr, Vancouver, BC', 50);
INSERT INTO Room(roomNum, address, capacity) 
VALUES (5, '452 SW Marine Dr, Vancouver, BC', 200);

INSERT INTO  Shows(address, roomNum, movieID) 
VALUES ('1234 West Mall, Vancouver, BC', 1, '10');
INSERT INTO  Shows(address, roomNum, movieID) 
VALUES ('1234 West Mall, Vancouver, BC', 1, '2345');
INSERT INTO  Shows(address, roomNum, movieID) 
VALUES ('452 SW Marine Dr, Vancouver, BC', 4, '10');
INSERT INTO  Shows(address, roomNum, movieID) 
VALUES ('452 SW Marine Dr, Vancouver, BC', 4, '157982');
INSERT INTO  Shows(address, roomNum, movieID) 
VALUES ('452 SW Marine Dr, Vancouver, BC', 4, '113');

INSERT INTO  MovieSchedule(time, roomNum, address, movieID) 
VALUES (to_timestamp('2023/04/01 09:00', 'YYYY/MM/DD HH24 MI'), 4, '452 SW Marine Dr, Vancouver, BC', '10');
INSERT INTO  MovieSchedule(time, roomNum, address, movieID) 
VALUES (to_timestamp('2023/04/01 15:00', 'YYYY/MM/DD HH24 MI'), 4, '452 SW Marine Dr, Vancouver, BC', '10');
INSERT INTO  MovieSchedule(time, roomNum, address, movieID) 
VALUES (to_timestamp('2023/04/01 13:00', 'YYYY/MM/DD HH24 MI'), 4, '452 SW Marine Dr, Vancouver, BC', '10');
INSERT INTO  MovieSchedule(time, roomNum, address, movieID) 
VALUES (to_timestamp('2023/04/01 10:00', 'YYYY/MM/DD HH24 MI'), 4, '452 SW Marine Dr, Vancouver, BC', '157982');
INSERT INTO  MovieSchedule(time, roomNum, address, movieID) 
VALUES (to_timestamp('2023/04/01 09:00', 'YYYY/MM/DD HH24 MI'), 1, '1234 West Mall, Vancouver, BC', '10');

INSERT INTO  Ticket(ID, type, seatNum, time, customerID, price, address, roomNum) 
VALUES ('1452', '3D', 'D15', to_timestamp('2023/04/01 09:00', 'YYYY/MM/DD HH24 MI'), '1', 19.0, '452 SW Marine Dr, Vancouver, BC', 4);
INSERT INTO  Ticket(ID, type, seatNum, time, customerID, price, address, roomNum) 
VALUES ('1453', '3D Luxury', 'A05', to_timestamp('2023/04/01 09:00', 'YYYY/MM/DD HH24 MI'), '2', 20.0, '1234 West Mall, Vancouver, BC', 1);
INSERT INTO  Ticket(ID, type, seatNum, time, customerID, price, address, roomNum) 
VALUES ('1500', '4DX', 'C05', to_timestamp('2023/04/01 10:00', 'YYYY/MM/DD HH24 MI'), '4', 25.0, '452 SW Marine Dr, Vancouver, BC', 4);
INSERT INTO  Ticket(ID, type, seatNum, time, customerID, price, address, roomNum) 
VALUES ('173', 'Normal', 'C05', to_timestamp('2023/04/01 13:00', 'YYYY/MM/DD HH24 MI'), '1', 17.0, '452 SW Marine Dr, Vancouver, BC', 4);
INSERT INTO  Ticket(ID, type, seatNum, time, customerID, price, address, roomNum) 
VALUES ('782', 'Normal', 'D21', to_timestamp('2023/04/01 15:00', 'YYYY/MM/DD HH24 MI'), '5', 17.0, '452 SW Marine Dr, Vancouver, BC', 4);

INSERT INTO  ConcessionStand(ID, name, address) 
VALUES ('12', 'McDonald', '452 SW Marine Dr, Vancouver, BC');
INSERT INTO  ConcessionStand(ID, name, address) 
VALUES ('115', 'StarBucks', '452 SW Marine Dr, Vancouver, BC');
INSERT INTO  ConcessionStand(ID, name, address) 
VALUES ('1', 'Subway', '1234 West Mall, Vancouver, BC');
INSERT INTO  ConcessionStand(ID, name, address) 
VALUES ('1234', 'StarBucks', '1234 West Mall, Vancouver, BC');
INSERT INTO  ConcessionStand(ID, name, address) 
VALUES ('602', 'Tim Hortons', '104 58 Ave SE, Calgary, AB');

INSERT INTO  Foodstuff(ID, name, price) 
VALUES ('100', 'BigMac Meal', 12.0);
INSERT INTO  Foodstuff(ID, name, price) 
VALUES ('101', 'Fries', 3.5);
INSERT INTO  Foodstuff(ID, name, price) 
VALUES ('2001', 'Iced Latte', 5.5);
INSERT INTO  Foodstuff(ID, name, price) 
VALUES ('57', 'Wedges', 4.5);
INSERT INTO  Foodstuff(ID, name, price) 
VALUES ('3', 'Root Beer', 2.5);

INSERT INTO  Offers(foodStuffID, concessionStandID) 
VALUES ('100', '12');
INSERT INTO  Offers(foodStuffID, concessionStandID) 
VALUES ('2001', '1234');
INSERT INTO  Offers(foodStuffID, concessionStandID) 
VALUES ('2001', '115');
INSERT INTO  Offers(foodStuffID, concessionStandID) 
VALUES ('3', '1');
INSERT INTO  Offers(foodStuffID, concessionStandID) 
VALUES ('57', '602');

INSERT INTO  Eats(customerID, concessionStandID,  foodStuffID) 
VALUES ('1', '12', '100');
INSERT INTO  Eats(customerID, concessionStandID,  foodStuffID) 
VALUES ('2', '115', '2001');
INSERT INTO  Eats(customerID, concessionStandID,  foodStuffID) 
VALUES ('2', '12', '100');
INSERT INTO  Eats(customerID, concessionStandID,  foodStuffID) 
VALUES ('3', '602', '57');
INSERT INTO  Eats(customerID, concessionStandID,  foodStuffID) 
VALUES ('4', '1234', '2001');
