SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0;
SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='TRADITIONAL,ALLOW_INVALID_DATES';

DROP SCHEMA IF EXISTS `top10kino` ;
CREATE SCHEMA IF NOT EXISTS `top10kino` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci ;
USE `top10kino` ;

-- -----------------------------------------------------
-- Table `top10kino`.`movies`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `top10kino`.`movies` ;

CREATE TABLE IF NOT EXISTS `top10kino`.`movies` (
  `idmovie` MEDIUMINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(100) NOT NULL,
  `year` YEAR NOT NULL,
  PRIMARY KEY (`idmovie`),
  UNIQUE INDEX `name_year_UNIQUE` (`name` ASC, `year` ASC),
  INDEX `name_idx[5]` (`name`(5) ASC),
  INDEX `year_idx` (`year` ASC))
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `top10kino`.`rating`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `top10kino`.`rating` ;

CREATE TABLE IF NOT EXISTS `top10kino`.`rating` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `idmovie` MEDIUMINT UNSIGNED NOT NULL,
  `position` SMALLINT NOT NULL,
  `rating` FLOAT NOT NULL,
  `votes` MEDIUMINT UNSIGNED NOT NULL,
  `iddates` SMALLINT NOT NULL,
  PRIMARY KEY (`id`),
  INDEX `fk_movies_idmovie_idx` (`idmovie` ASC),
  CONSTRAINT `fk_rating_movies`
    FOREIGN KEY (`idmovie`)
    REFERENCES `top10kino`.`movies` (`idmovie`)
    ON DELETE CASCADE
    ON UPDATE CASCADE)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `top10kino`.`dates`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `top10kino`.`dates` ;

CREATE TABLE IF NOT EXISTS `top10kino`.`dates` (
  `iddates` SMALLINT NOT NULL AUTO_INCREMENT,
  `date` DATE NOT NULL,
  `time` TIME NOT NULL,
  PRIMARY KEY (`iddates`),
  UNIQUE INDEX `date_UNIQUE` (`date` ASC),
  INDEX `date_idx` (`date` ASC))
ENGINE = InnoDB;


SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;
