SET FOREIGN_KEY_CHECKS = 0;

TRUNCATE `BackgroundCheckAPI`.states;

SET FOREIGN_KEY_CHECKS = 1;

INSERT INTO `BackgroundCheckAPI`.states 
SELECT StateId AS id,
Abbrv AS code,
Name AS title,
ExtraFee AS extra_cost,
MVRCost AS mvr_cost
FROM `eyeforsecurity_dev`.State