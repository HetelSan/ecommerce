--
-- MySQL dump 10.13  Distrib 5.7.17, for Win64 (x86_64)
--
-- Host: 127.0.0.1    Database: db_ecommerce
-- ------------------------------------------------------
-- Server version	5.5.5-10.1.19-MariaDB

CREATE DATABASE IF NOT EXISTS db_ecommerce DEFAULT CHARACTER SET utf8 DEFAULT COLLATE utf8_general_ci;

USE db_ecommerce;

--
-- Table structure for table tb_ordersstatus
--
DROP TABLE IF EXISTS tb_ordersstatus;
CREATE TABLE tb_ordersstatus (
  idstatus   INT(11)     NOT NULL AUTO_INCREMENT,
  desstatus  VARCHAR(32) NOT NULL,
  dtregister TIMESTAMP   NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (idstatus)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8 DEFAULT COLLATE utf8_general_ci;

--
-- Dumping data for table tb_ordersstatus
--
LOCK TABLES tb_ordersstatus WRITE;
INSERT INTO tb_ordersstatus VALUES (1,'Em Aberto','2017-03-13 03:00:00'),(2,'Aguardando Pagamento','2017-03-13 03:00:00'),(3,'Pago','2017-03-13 03:00:00'),(4,'Entregue','2017-03-13 03:00:00');
UNLOCK TABLES;

--
-- Table structure for table tb_categories
--
DROP TABLE IF EXISTS tb_categories;
CREATE TABLE tb_categories (
  idcategory  INT(11)     NOT NULL AUTO_INCREMENT,
  descategory VARCHAR(32) NOT NULL,
  dtregister  TIMESTAMP   NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (idcategory)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 DEFAULT COLLATE utf8_general_ci;

--
-- Table structure for table tb_products
--
DROP TABLE IF EXISTS tb_products;
CREATE TABLE tb_products (
  idproduct  INT(11)       NOT NULL AUTO_INCREMENT,
  desproduct VARCHAR(64)   NOT NULL,
  vlprice    DECIMAL(10,2) NOT NULL,
  vlwidth    DECIMAL(10,2) NOT NULL,
  vlheight   DECIMAL(10,2) NOT NULL,
  vllength   DECIMAL(10,2) NOT NULL,
  vlweight   DECIMAL(10,2) NOT NULL,
  desurl     VARCHAR(128)  NOT NULL,
  dtregister TIMESTAMP     NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (idproduct)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 DEFAULT COLLATE utf8_general_ci;

--
-- Dumping data for table tb_products
--
LOCK TABLES tb_products WRITE;
INSERT INTO tb_products VALUES (1,'Smartphone Android 7.0',999.95,75.00,151.00,80.00,167.00,'smartphone-android-7.0','2017-03-13 03:00:00'),(2,'SmartTV LED 4K',3925.99,917.00,596.00,288.00,8600.00,'smarttv-led-4k','2017-03-13 03:00:00'),(3,'Notebook 14\" 4GB 1TB',1949.99,345.00,23.00,30.00,2000.00,'notebook-14-4gb-1tb','2017-03-13 03:00:00');
UNLOCK TABLES;

--
-- Table structure for table tb_persons
--
DROP TABLE IF EXISTS tb_persons;
CREATE TABLE tb_persons (
  idperson   INT(11)      NOT NULL AUTO_INCREMENT,
  desperson  VARCHAR(64)  NOT NULL,
  desemail   VARCHAR(128) DEFAULT NULL,
  nrphone    BIGINT(20)   DEFAULT NULL,
  dtregister TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (idperson)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8 DEFAULT COLLATE utf8_general_ci;

--
-- Dumping data for table tb_persons
--
LOCK TABLES tb_persons WRITE;
INSERT INTO tb_persons VALUES (1,'João Rangel','admin@hcode.com.br',2147483647,'2017-03-01 03:00:00'),(7,'Suporte','suporte@hcode.com.br',1112345678,'2017-03-15 16:10:27');
UNLOCK TABLES;

--
-- Table structure for table tb_users
--
DROP TABLE IF EXISTS tb_users;
CREATE TABLE tb_users (
  iduser      INT(11)      NOT NULL AUTO_INCREMENT,
  idperson    INT(11)      NOT NULL,
  deslogin    VARCHAR(64)  NOT NULL,
  despassword VARCHAR(256) NOT NULL,
  inadmin     TINYINT(4)   NOT NULL DEFAULT '0',
  dtregister  TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (iduser),
  KEY FK_users_persons_idx (idperson),
  CONSTRAINT fk_users_persons FOREIGN KEY (idperson) REFERENCES tb_persons (idperson) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8 DEFAULT COLLATE utf8_general_ci;

--
-- Dumping data for table tb_users
--
LOCK TABLES tb_users WRITE;
INSERT INTO tb_users VALUES (1,1,'admin','$2y$12$YlooCyNvyTji8bPRcrfNfOKnVMmZA9ViM2A3IpFjmrpIbp5ovNmga',1,'2017-03-13 03:00:00'),(7,7,'suporte','$2y$12$HFjgUm/mk1RzTy4ZkJaZBe0Mc/BA2hQyoUckvm.lFa6TesjtNpiMe',1,'2017-03-15 16:10:27');
UNLOCK TABLES;

--
-- Table structure for table tb_addresses
--
DROP TABLE IF EXISTS tb_addresses;
CREATE TABLE tb_addresses (
  idaddress     INT(11)      NOT NULL AUTO_INCREMENT,
  idperson      INT(11)      NOT NULL,
  desaddress    VARCHAR(128) NOT NULL,
  descomplement VARCHAR(32)  DEFAULT NULL,
  descity       VARCHAR(32)  NOT NULL,
  desstate      VARCHAR(32)  NOT NULL,
  descountry    VARCHAR(32)  NOT NULL,
  nrzipcode     INT(11)      NOT NULL,
  dtregister    TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (idaddress),
  KEY fk_addresses_persons_idx (idperson),
  CONSTRAINT fk_addresses_persons FOREIGN KEY (idperson) REFERENCES tb_persons (idperson) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8 DEFAULT COLLATE utf8_general_ci;

--
-- Table structure for table tb_carts
--
DROP TABLE IF EXISTS tb_carts;
CREATE TABLE tb_carts (
  idcart       INT(11)       NOT NULL,
  dessessionid VARCHAR(64)   NOT NULL,
  iduser       INT(11)       DEFAULT NULL,
  idaddress    INT(11)       DEFAULT NULL,
  vlfreight    DECIMAL(10,2) DEFAULT NULL,
  dtregister   TIMESTAMP     NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (idcart),
  KEY FK_carts_users_idx (iduser),
  KEY fk_carts_addresses_idx (idaddress),
  CONSTRAINT fk_carts_addresses FOREIGN KEY (idaddress) REFERENCES tb_addresses (idaddress) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT fk_carts_users FOREIGN KEY (iduser) REFERENCES tb_users (iduser) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8 DEFAULT COLLATE utf8_general_ci;

--
-- Table structure for table tb_cartsproducts
--
DROP TABLE IF EXISTS tb_cartsproducts;
CREATE TABLE tb_cartsproducts (
  idcartproduct INT(11)   NOT NULL AUTO_INCREMENT,
  idcart        INT(11)   NOT NULL,
  idproduct     INT(11)   NOT NULL,
  dtremoved     DATETIME  NOT NULL,
  dtregister    TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (idcartproduct),
  KEY FK_cartsproducts_carts_idx (idcart),
  KEY FK_cartsproducts_products_idx (idproduct),
  CONSTRAINT fk_cartsproducts_carts FOREIGN KEY (idcart) REFERENCES tb_carts (idcart) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT fk_cartsproducts_products FOREIGN KEY (idproduct) REFERENCES tb_products (idproduct) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8 DEFAULT COLLATE utf8_general_ci;

--
-- Table structure for table tb_orders
--
DROP TABLE IF EXISTS tb_orders;
CREATE TABLE tb_orders (
  idorder    INT(11)       NOT NULL AUTO_INCREMENT,
  idcart     INT(11)       NOT NULL,
  iduser     INT(11)       NOT NULL,
  idstatus   INT(11)       NOT NULL,
  vltotal    DECIMAL(10,2) NOT NULL,
  dtregister TIMESTAMP     NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (idorder),
  KEY FK_orders_carts_idx (idcart),
  KEY FK_orders_users_idx (iduser),
  KEY fk_orders_ordersstatus_idx (idstatus),
  CONSTRAINT fk_orders_carts FOREIGN KEY (idcart) REFERENCES tb_carts (idcart) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT fk_orders_ordersstatus FOREIGN KEY (idstatus) REFERENCES tb_ordersstatus (idstatus) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT fk_orders_users FOREIGN KEY (iduser) REFERENCES tb_users (iduser) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8 DEFAULT COLLATE utf8_general_ci;

--
-- Table structure for table tb_productscategories
--
DROP TABLE IF EXISTS tb_productscategories;
CREATE TABLE tb_productscategories (
  idcategory INT(11) NOT NULL,
  idproduct  INT(11) NOT NULL,
  PRIMARY KEY (idcategory,idproduct),
  KEY fk_productscategories_products_idx (idproduct),
  CONSTRAINT fk_productscategories_categories FOREIGN KEY (idcategory) REFERENCES tb_categories (idcategory) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT fk_productscategories_products FOREIGN KEY (idproduct) REFERENCES tb_products (idproduct) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8 DEFAULT COLLATE utf8_general_ci;

--
-- Table structure for table tb_userslogs
--
DROP TABLE IF EXISTS tb_userslogs;
CREATE TABLE tb_userslogs (
  idlog        INT(11)      NOT NULL AUTO_INCREMENT,
  iduser       INT(11)      NOT NULL,
  deslog       VARCHAR(128) NOT NULL,
  desip        VARCHAR(45)  NOT NULL,
  desuseragent VARCHAR(128) NOT NULL,
  dessessionid VARCHAR(64)  NOT NULL,
  desurl       VARCHAR(128) NOT NULL,
  dtregister   TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (idlog),
  KEY fk_userslogs_users_idx (iduser),
  CONSTRAINT fk_userslogs_users FOREIGN KEY (iduser) REFERENCES tb_users (iduser) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8 DEFAULT COLLATE utf8_general_ci;

--
-- Table structure for table tb_userspasswordsrecoveries
--
DROP TABLE IF EXISTS tb_userspasswordsrecoveries;
CREATE TABLE tb_userspasswordsrecoveries (
  idrecovery INT(11)     NOT NULL AUTO_INCREMENT,
  iduser     INT(11)     NOT NULL,
  desip      VARCHAR(45) NOT NULL,
  dtrecovery DATETIME    DEFAULT NULL,
  dtregister TIMESTAMP   NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (idrecovery),
  KEY fk_userspasswordsrecoveries_users_idx (iduser),
  CONSTRAINT fk_userspasswordsrecoveries_users FOREIGN KEY (iduser) REFERENCES tb_users (iduser) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8 DEFAULT COLLATE utf8_general_ci;

--
-- Dumping routines for database 'db_ecommerce'
--
DROP procedure IF EXISTS `sp_userspasswordsrecoveries_create`;
DELIMITER $$
CREATE PROCEDURE `sp_userspasswordsrecoveries_create` (
piduser INT,
pdesip VARCHAR(45)
)
BEGIN

	INSERT INTO tb_userspasswordsrecoveries (iduser, desip)
  VALUES(piduser, pdesip);
    
  SELECT * FROM tb_userspasswordsrecoveries WHERE idrecovery = LAST_INSERT_ID();

END$$
DELIMITER ;

DROP procedure IF EXISTS `sp_usersupdate_save`;
DELIMITER $$
CREATE PROCEDURE `sp_usersupdate_save` (
piduser INT,
pdesperson VARCHAR(64), 
pdeslogin VARCHAR(64), 
pdespassword VARCHAR(256), 
pdesemail VARCHAR(128), 
pnrphone BIGINT, 
pinadmin TINYINT
)
BEGIN
	
  DECLARE vidperson INT;
    
	SELECT idperson INTO vidperson
    FROM tb_users
    WHERE iduser = piduser;
    
  UPDATE tb_persons
     SET desperson = pdesperson,
         desemail = pdesemail,
         nrphone = pnrphone
	 WHERE idperson = vidperson;
    
  UPDATE tb_users
     SET deslogin = pdeslogin,
         despassword = pdespassword,
         inadmin = pinadmin
   WHERE iduser = piduser;
    
  SELECT * FROM tb_users a INNER JOIN tb_persons b USING(idperson) WHERE a.iduser = piduser;

END$$
DELIMITER ;

DROP procedure IF EXISTS `sp_users_delete`;
DELIMITER $$
CREATE PROCEDURE `sp_users_delete` (
piduser INT
)
BEGIN
	
  DECLARE vidperson INT;
    
	SELECT idperson INTO vidperson
    FROM tb_users
    WHERE iduser = piduser;
    
  DELETE FROM tb_users WHERE iduser = piduser;
  
  DELETE FROM tb_persons WHERE idperson = vidperson;

END$$
DELIMITER ;

DROP procedure IF EXISTS `sp_users_save`;
DELIMITER $$
CREATE PROCEDURE `sp_users_save` (
pdesperson VARCHAR(64), 
pdeslogin VARCHAR(64), 
pdespassword VARCHAR(256), 
pdesemail VARCHAR(128), 
pnrphone BIGINT, 
pinadmin TINYINT
)
BEGIN
	
  DECLARE vidperson INT;
    
	INSERT INTO tb_persons (desperson, desemail, nrphone)
  VALUES(pdesperson, pdesemail, pnrphone);
    
  SET vidperson = LAST_INSERT_ID();
    
  INSERT INTO tb_users (idperson, deslogin, despassword, inadmin)
  VALUES(vidperson, pdeslogin, pdespassword, pinadmin);
    
  SELECT * FROM tb_users a INNER JOIN tb_persons b USING(idperson) WHERE a.iduser = LAST_INSERT_ID();

END$$
DELIMITER ;

DELIMITER $$
CREATE PROCEDURE `sp_categories_save` (
pidcategory INT,
pdescategory VARCHAR(64)
)
BEGIN
	
	IF pidcategory > 0 THEN
		
		UPDATE tb_categories
       SET descategory = pdescategory
     WHERE idcategory = pidcategory;
        
  ELSE
		
		INSERT INTO tb_categories (descategory) VALUES(pdescategory);
        
    SET pidcategory = LAST_INSERT_ID();
        
  END IF;
    
  SELECT * FROM tb_categories WHERE idcategory = pidcategory;
    
END$$
DELIMITER ;

-- Dump completed on 2017-04-24 11:50:48
