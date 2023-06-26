CREATE TABLE IF NOT EXISTS `debtam` (
    `id` INT NOT NULL AUTO_INCREMENT , 
    `tax_name` VARCHAR(255) NOT NULL , 
    `org_name` VARCHAR(255) NOT NULL , 
    `inn` VARCHAR(255) NOT NULL , 
    `sum_arrears` VARCHAR(255) NOT NULL , 
    `sum_penalties` VARCHAR(255) NOT NULL , 
    `sum_ticket` VARCHAR(255) NOT NULL , 
    `total_sum_arrears` VARCHAR(255) NOT NULL , 
    `created_date` VARCHAR(255) NOT NULL , 
    PRIMARY KEY (`id`)
) 
ENGINE = InnoDB;