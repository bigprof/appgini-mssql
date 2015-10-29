<?php
	// check this file's MD5 to make sure it wasn't called before
	$prevMD5=@implode('', @file(dirname(__FILE__).'/setup.md5'));
	$thisMD5=md5(@implode('', @file("./updateDB.php")));
	if($thisMD5==$prevMD5){
		$setupAlreadyRun=true;
	}else{
		// set up tables
		if(!isset($silent)){
			$silent=true;
		}

		// set up tables
		setupTable('customers', "create table if not exists `customers` (   `CustomerID` VARCHAR(5) not null , primary key (`CustomerID`), `CompanyName` VARCHAR(40) , `ContactName` VARCHAR(30) , `ContactTitle` VARCHAR(30) , `Address` VARCHAR(60) , `City` VARCHAR(15) , `Region` VARCHAR(15) , `PostalCode` VARCHAR(10) , `Country` VARCHAR(15) , `Phone` VARCHAR(24) , `Fax` VARCHAR(24) ) CHARSET ascii", $silent);
		setupTable('employees', "create table if not exists `employees` (   `EmployeeID` INT not null auto_increment , primary key (`EmployeeID`), `TitleOfCourtesy` VARCHAR(50) , `Photo` VARCHAR(40) , `LastName` VARCHAR(50) , `FirstName` VARCHAR(10) , `Title` VARCHAR(30) , `BirthDate` DATE , `HireDate` DATE , `Address` VARCHAR(50) , `City` VARCHAR(15) , `Region` VARCHAR(15) , `PostalCode` VARCHAR(10) , `Country` VARCHAR(15) , `HomePhone` VARCHAR(24) , `Extension` VARCHAR(4) , `Notes` TEXT , `ReportsTo` INT ) CHARSET ascii", $silent);
		setupIndexes('employees', array('ReportsTo'));
		setupTable('orders', "create table if not exists `orders` (   `OrderID` INT not null auto_increment , primary key (`OrderID`), `CustomerID` VARCHAR(5) , `EmployeeID` INT , `OrderDate` DATE , `RequiredDate` DATE , `ShippedDate` DATE , `ShipVia` INT(11) , `Freight` FLOAT(10,2) default '0' , `ShipName` VARCHAR(5) , `ShipAddress` VARCHAR(5) , `ShipCity` VARCHAR(5) , `ShipRegion` VARCHAR(5) , `ShipPostalCode` VARCHAR(5) , `ShipCountry` VARCHAR(5) ) CHARSET ascii", $silent);
		setupIndexes('orders', array('CustomerID','EmployeeID','ShipVia'));
		setupTable('order_details', "create table if not exists `order_details` (   `odID` INT unsigned not null auto_increment , primary key (`odID`), `OrderID` INT default '0' , `ProductID` INT default '0' , `Category` INT , `UnitPrice` FLOAT(10,2) default '0' , `Quantity` SMALLINT default '1' , `Discount` FLOAT(10,2) default '0' ) CHARSET ascii", $silent);
		setupIndexes('order_details', array('OrderID','ProductID'));
		setupTable('products', "create table if not exists `products` (   `ProductID` INT not null auto_increment , primary key (`ProductID`), `ProductName` VARCHAR(50) , `SupplierID` INT(11) , `CategoryID` INT , `QuantityPerUnit` VARCHAR(50) , `UnitPrice` FLOAT(10,2) default '0' , `UnitsInStock` SMALLINT default '0' , `UnitsOnOrder` SMALLINT(6) default '0' , `ReorderLevel` SMALLINT default '0' , `Discontinued` TINYINT default '0' ) CHARSET ascii", $silent);
		setupIndexes('products', array('SupplierID','CategoryID'));
		setupTable('categories', "create table if not exists `categories` (   `CategoryID` INT not null auto_increment , primary key (`CategoryID`), `Picture` VARCHAR(40) , `CategoryName` VARCHAR(50) , unique(`CategoryName`), `Description` TEXT ) CHARSET ascii", $silent);
		setupTable('suppliers', "create table if not exists `suppliers` (   `SupplierID` INT(11) not null auto_increment , primary key (`SupplierID`), `CompanyName` VARCHAR(50) , `ContactName` VARCHAR(30) , `ContactTitle` VARCHAR(30) , `Address` VARCHAR(50) , `City` VARCHAR(15) , `Region` VARCHAR(15) , `PostalCode` VARCHAR(10) , `Country` VARCHAR(50) , `Phone` VARCHAR(24) , `Fax` VARCHAR(24) , `HomePage` TEXT ) CHARSET ascii", $silent);
		setupTable('shippers', "create table if not exists `shippers` (   `ShipperID` INT(11) not null auto_increment , primary key (`ShipperID`), `CompanyName` VARCHAR(40) not null , `Phone` VARCHAR(24) ) CHARSET ascii", $silent);


		// save MD5
		if($fp=@fopen(dirname(__FILE__).'/setup.md5', 'w')){
			fwrite($fp, $thisMD5);
			fclose($fp);
		}
	}


	function setupIndexes($tableName, $arrFields){
		if(!is_array($arrFields)){
			return false;
		}

		foreach($arrFields as $fieldName){
			if(!$res=@db_query("SHOW COLUMNS FROM `$tableName` like '$fieldName'")){
				continue;
			}
			if(!$row=@db_fetch_assoc($res)){
				continue;
			}
			if($row['Key']==''){
				@db_query("ALTER TABLE `$tableName` ADD INDEX `$fieldName` (`$fieldName`)");
			}
		}
	}


	function setupTable($tableName, $createSQL='', $silent=true, $arrAlter=''){
		global $Translation;
		ob_start();

		echo '<div style="padding: 5px; border-bottom:solid 1px silver; font-family: verdana, arial; font-size: 10px;">';

		// is there a table rename query?
		if(is_array($arrAlter)){
			$matches=array();
			if(preg_match("/ALTER TABLE `(.*)` RENAME `$tableName`/", $arrAlter[0], $matches)){
				$oldTableName=$matches[1];
			}
		}

		if($res=@db_query("select count(1) from `$tableName`")){ // table already exists
			if($row = @db_fetch_array($res)){
				echo str_replace("<TableName>", $tableName, str_replace("<NumRecords>", $row[0],$Translation["table exists"]));
				if(is_array($arrAlter)){
					echo '<br>';
					foreach($arrAlter as $alter){
						if($alter!=''){
							echo "$alter ... ";
							if(!@db_query($alter)){
								echo '<span class="label label-danger">' . $Translation['failed'] . '</span>';
								echo '<div class="text-danger">' . $Translation['mysql said'] . ' ' . db_error(db_link()) . '</div>';
							}else{
								echo '<span class="label label-success">' . $Translation['ok'] . '</span>';
							}
						}
					}
				}else{
					echo $Translation["table uptodate"];
				}
			}else{
				echo str_replace("<TableName>", $tableName, $Translation["couldnt count"]);
			}
		}else{ // given tableName doesn't exist

			if($oldTableName!=''){ // if we have a table rename query
				if($ro=@db_query("select count(1) from `$oldTableName`")){ // if old table exists, rename it.
					$renameQuery=array_shift($arrAlter); // get and remove rename query

					echo "$renameQuery ... ";
					if(!@db_query($renameQuery)){
						echo '<span class="label label-danger">' . $Translation['failed'] . '</span>';
						echo '<div class="text-danger">' . $Translation['mysql said'] . ' ' . db_error(db_link()) . '</div>';
					}else{
						echo '<span class="label label-success">' . $Translation['ok'] . '</span>';
					}

					if(is_array($arrAlter)) setupTable($tableName, $createSQL, false, $arrAlter); // execute Alter queries on renamed table ...
				}else{ // if old tableName doesn't exist (nor the new one since we're here), then just create the table.
					setupTable($tableName, $createSQL, false); // no Alter queries passed ...
				}
			}else{ // tableName doesn't exist and no rename, so just create the table
				echo str_replace("<TableName>", $tableName, $Translation["creating table"]);
				if(!@db_query($createSQL)){
					echo '<span class="label label-danger">' . $Translation['failed'] . '</span>';
					echo '<div class="text-danger">' . $Translation['mysql said'] . db_error(db_link()) . '</div>';
				}else{
					echo '<span class="label label-success">' . $Translation['ok'] . '</span>';
				}
			}
		}

		echo "</div>";

		$out=ob_get_contents();
		ob_end_clean();
		if(!$silent){
			echo $out;
		}
	}
?>