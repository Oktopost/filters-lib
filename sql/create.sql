CREATE TABLE `Filters` ( 
    `Id` CHAR(35) NOT NULL , 
    `Created` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP , 
    `Touched` TIMESTAMP on update CURRENT_TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP , 
    `Hash` CHAR(6) NOT NULL , 
    `Payload` TEXT NOT NULL , 
    `Metadata` TEXT NULL DEFAULT NULL , 
    
    PRIMARY KEY (`Id`),
    KEY `k_Created` (`Created`),
    KEY `k_Touched` (`Touched`),
    KEY `k_Hash` (`Hash`)
)
ENGINE = InnoDB 
CHARSET=utf8;