<?php
	$currDir=dirname(__FILE__);
	require("$currDir/incCommon.php");
	include("$currDir/incHeader.php");

	/* application schema as created in AppGini */
	$schema = array(   
		'customers' => array(   
			'CustomerID' => array('appgini' => 'VARCHAR(5) not null primary key '),
			'CompanyName' => array('appgini' => 'VARCHAR(40) '),
			'ContactName' => array('appgini' => 'VARCHAR(30) '),
			'ContactTitle' => array('appgini' => 'VARCHAR(30) '),
			'Address' => array('appgini' => 'VARCHAR(60) '),
			'City' => array('appgini' => 'VARCHAR(15) '),
			'Region' => array('appgini' => 'VARCHAR(15) '),
			'PostalCode' => array('appgini' => 'VARCHAR(10) '),
			'Country' => array('appgini' => 'VARCHAR(15) '),
			'Phone' => array('appgini' => 'VARCHAR(24) '),
			'Fax' => array('appgini' => 'VARCHAR(24) ')
		),
		'employees' => array(   
			'EmployeeID' => array('appgini' => 'INT not null primary key auto_increment '),
			'TitleOfCourtesy' => array('appgini' => 'VARCHAR(50) '),
			'Photo' => array('appgini' => 'VARCHAR(40) '),
			'LastName' => array('appgini' => 'VARCHAR(50) '),
			'FirstName' => array('appgini' => 'VARCHAR(10) '),
			'Title' => array('appgini' => 'VARCHAR(30) '),
			'BirthDate' => array('appgini' => 'DATE '),
			'HireDate' => array('appgini' => 'DATE '),
			'Address' => array('appgini' => 'VARCHAR(50) '),
			'City' => array('appgini' => 'VARCHAR(15) '),
			'Region' => array('appgini' => 'VARCHAR(15) '),
			'PostalCode' => array('appgini' => 'VARCHAR(10) '),
			'Country' => array('appgini' => 'VARCHAR(15) '),
			'HomePhone' => array('appgini' => 'VARCHAR(24) '),
			'Extension' => array('appgini' => 'VARCHAR(4) '),
			'Notes' => array('appgini' => 'TEXT '),
			'ReportsTo' => array('appgini' => 'INT ')
		),
		'orders' => array(   
			'OrderID' => array('appgini' => 'INT not null primary key auto_increment '),
			'CustomerID' => array('appgini' => 'VARCHAR(5) '),
			'EmployeeID' => array('appgini' => 'INT '),
			'OrderDate' => array('appgini' => 'DATE '),
			'RequiredDate' => array('appgini' => 'DATE '),
			'ShippedDate' => array('appgini' => 'DATE '),
			'ShipVia' => array('appgini' => 'INT(11) '),
			'Freight' => array('appgini' => 'FLOAT(10,2) default \'0\' '),
			'ShipName' => array('appgini' => 'VARCHAR(5) '),
			'ShipAddress' => array('appgini' => 'VARCHAR(5) '),
			'ShipCity' => array('appgini' => 'VARCHAR(5) '),
			'ShipRegion' => array('appgini' => 'VARCHAR(5) '),
			'ShipPostalCode' => array('appgini' => 'VARCHAR(5) '),
			'ShipCountry' => array('appgini' => 'VARCHAR(5) ')
		),
		'order_details' => array(   
			'odID' => array('appgini' => 'INT unsigned not null primary key auto_increment '),
			'OrderID' => array('appgini' => 'INT default \'0\' '),
			'ProductID' => array('appgini' => 'INT default \'0\' '),
			'Category' => array('appgini' => 'INT '),
			'UnitPrice' => array('appgini' => 'FLOAT(10,2) default \'0\' '),
			'Quantity' => array('appgini' => 'SMALLINT default \'1\' '),
			'Discount' => array('appgini' => 'FLOAT(10,2) default \'0\' ')
		),
		'products' => array(   
			'ProductID' => array('appgini' => 'INT not null primary key auto_increment '),
			'ProductName' => array('appgini' => 'VARCHAR(50) '),
			'SupplierID' => array('appgini' => 'INT(11) '),
			'CategoryID' => array('appgini' => 'INT '),
			'QuantityPerUnit' => array('appgini' => 'VARCHAR(50) '),
			'UnitPrice' => array('appgini' => 'FLOAT(10,2) default \'0\' '),
			'UnitsInStock' => array('appgini' => 'SMALLINT default \'0\' '),
			'UnitsOnOrder' => array('appgini' => 'SMALLINT(6) default \'0\' '),
			'ReorderLevel' => array('appgini' => 'SMALLINT default \'0\' '),
			'Discontinued' => array('appgini' => 'TINYINT default \'0\' ')
		),
		'categories' => array(   
			'CategoryID' => array('appgini' => 'INT not null primary key auto_increment '),
			'Picture' => array('appgini' => 'VARCHAR(40) '),
			'CategoryName' => array('appgini' => 'VARCHAR(50) unique '),
			'Description' => array('appgini' => 'TEXT ')
		),
		'suppliers' => array(   
			'SupplierID' => array('appgini' => 'INT(11) not null primary key auto_increment '),
			'CompanyName' => array('appgini' => 'VARCHAR(50) '),
			'ContactName' => array('appgini' => 'VARCHAR(30) '),
			'ContactTitle' => array('appgini' => 'VARCHAR(30) '),
			'Address' => array('appgini' => 'VARCHAR(50) '),
			'City' => array('appgini' => 'VARCHAR(15) '),
			'Region' => array('appgini' => 'VARCHAR(15) '),
			'PostalCode' => array('appgini' => 'VARCHAR(10) '),
			'Country' => array('appgini' => 'VARCHAR(50) '),
			'Phone' => array('appgini' => 'VARCHAR(24) '),
			'Fax' => array('appgini' => 'VARCHAR(24) '),
			'HomePage' => array('appgini' => 'TEXT ')
		),
		'shippers' => array(   
			'ShipperID' => array('appgini' => 'INT(11) not null primary key auto_increment '),
			'CompanyName' => array('appgini' => 'VARCHAR(40) not null '),
			'Phone' => array('appgini' => 'VARCHAR(24) ')
		)
	);

	$table_captions = getTableList();

	/* function for preparing field definition for comparison */
	function prepare_def($def){
		$def = trim($def);
		$def = strtolower($def);

		/* ignore length for int data types */
		$def = preg_replace('/int\w*\([0-9]+\)/', 'int', $def);

		/* make sure there is always a space before mysql words */
		$def = preg_replace('/(\S)(unsigned|not null|binary|zerofill|auto_increment|default)/', '$1 $2', $def);

		/* treat 0.000.. same as 0 */
		$def = preg_replace('/([0-9])*\.0+/', '$1', $def);

		/* treat unsigned zerofill same as zerofill */
		$def = str_ireplace('unsigned zerofill', 'zerofill', $def);

		/* ignore zero-padding for date data types */
		$def = preg_replace("/date\s*default\s*'([0-9]{4})-0?([1-9])-0?([1-9])'/i", "date default '$1-$2-$3'", $def);

		return $def;
	}

	/* process requested fixes */
	$fix_table = (isset($_GET['t']) ? $_GET['t'] : false);
	$fix_field = (isset($_GET['f']) ? $_GET['f'] : false);

	if($fix_table && $fix_field && isset($schema[$fix_table][$fix_field])){
		$field_added = $field_updated = false;

		// field exists?
		$res = sql("show columns from `{$fix_table}` like '{$fix_field}'", $eo);
		if($row = db_fetch_assoc($res)){
			// modify field
			$qry = "alter table `{$fix_table}` modify `{$fix_field}` {$schema[$fix_table][$fix_field]['appgini']}";
			sql($qry, $eo);
			$field_updated = true;
		}else{
			// create field
			$qry = "alter table `{$fix_table}` add column `{$fix_field}` {$schema[$fix_table][$fix_field]['appgini']}";
			sql($qry, $eo);
			$field_added = true;
		}
	}

	foreach($table_captions as $tn => $tc){
		$eo['silentErrors'] = true;
		$res = sql("show columns from `{$tn}`", $eo);
		if($res){
			while($row = db_fetch_assoc($res)){
				if(!isset($schema[$tn][$row['Field']]['appgini'])) continue;
				$field_description = strtoupper(str_replace(' ', '', $row['Type']));
				$field_description = str_ireplace('unsigned', ' unsigned', $field_description);
				$field_description = str_ireplace('zerofill', ' zerofill', $field_description);
				$field_description = str_ireplace('binary', ' binary', $field_description);
				$field_description .= ($row['Null'] == 'NO' ? ' not null' : '');
				$field_description .= ($row['Key'] == 'PRI' ? ' primary key' : '');
				$field_description .= ($row['Key'] == 'UNI' ? ' unique' : '');
				$field_description .= ($row['Default'] != '' ? " default '" . makeSafe($row['Default']) . "'" : '');
				$field_description .= ($row['Extra'] == 'auto_increment' ? ' auto_increment' : '');

				$schema[$tn][$row['Field']]['db'] = '';
				if(isset($schema[$tn][$row['Field']])){
					$schema[$tn][$row['Field']]['db'] = $field_description;
				}
			}
		}
	}
?>

<?php if($field_added || $field_updated){ ?>
	<div class="alert alert-info alert-dismissable">
		<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
		<i class="glyphicon glyphicon-info-sign"></i>
		An attempt to <?php echo ($field_added ? 'create' : 'update'); ?> the field <i><?php echo $fix_field; ?></i> in <i><?php echo $fix_table; ?></i> table
		was made by executing this query:
		<pre><?php echo $qry; ?></pre>
		Results are shown below.
	</div>
<?php } ?>

<div class="page-header"><h1>
	View/Rebuild fields
	<button type="button" class="btn btn-default" id="show_deviations_only"><i class="glyphicon glyphicon-eye-close"></i> Show deviations only</button>
	<button type="button" class="btn btn-default hidden" id="show_all_fields"><i class="glyphicon glyphicon-eye-open"></i> Show all fields</button>
</h1></div>

<p class="lead">This page compares the tables and fields structure/schema as designed in AppGini to the actual database structure and allows you to fix any deviations.</p>

<div class="alert summary"></div>
<table class="table table-responsive table-hover table-striped">
	<thead><tr>
		<th></th>
		<th>Field</th>
		<th>AppGini definition</th>
		<th>Current definition in the database</th>
		<th></th>
	</tr></thead>

	<tbody>
	<?php foreach($schema as $tn => $fields){ ?>
		<tr class="text-info"><td colspan="5"><h4 data-placement="left" data-toggle="tooltip" title="<?php echo $tn; ?> table"><i class="glyphicon glyphicon-th-list"></i> <?php echo $table_captions[$tn]; ?></h4></td></tr>
		<?php foreach($fields as $fn => $fd){ ?>
			<?php $diff = ((prepare_def($fd['appgini']) == prepare_def($fd['db'])) ? false : true); ?>
			<?php $no_db = ($fd['db'] ? false : true); ?>
			<tr class="<?php echo ($diff ? 'highlight' : 'field_ok'); ?>">
				<td><i class="glyphicon glyphicon-<?php echo ($diff ? 'remove text-danger' : 'ok text-success'); ?>"></i></td>
				<td><?php echo $fn; ?></td>
				<td class="<?php echo ($diff ? 'bold text-success' : ''); ?>"><?php echo $fd['appgini']; ?></td>
				<td class="<?php echo ($diff ? 'bold text-danger' : ''); ?>"><?php echo thisOr($fd['db'], "Doesn't exist!"); ?></td>
				<td>
					<?php if($diff && $no_db){ ?>
						<a href="pageRebuildFields.php?t=<?php echo $tn; ?>&f=<?php echo $fn; ?>" class="btn btn-success btn-xs btn_create" data-toggle="tooltip" data-placement="top" title="Create the field by running an ADD COLUMN query."><i class="glyphicon glyphicon-plus"></i> Create it</a>
					<?php }elseif($diff){ ?>
						<a href="pageRebuildFields.php?t=<?php echo $tn; ?>&f=<?php echo $fn; ?>" class="btn btn-warning btn-xs btn_update" data-toggle="tooltip" title="Fix the field by running an ALTER COLUMN query so that its definition becomes the same as that in AppGini."><i class="glyphicon glyphicon-cog"></i> Fix it</a>
					<?php } ?>
				</td>
			</tr>
		<?php } ?>
	<?php } ?>
	</tbody>
</table>
<div class="alert summary"></div>

<style>
	.bold{ font-weight: bold; }
	.highlight, .highlight td{ background-color: #FFFFE0 !important; }
	[data-toggle="tooltip"]{ display: block !important; }
</style>

<script>
	jQuery(function(){
		jQuery('[data-toggle="tooltip"]').tooltip();

		jQuery('#show_deviations_only').click(function(){
			jQuery(this).addClass('hidden');
			jQuery('#show_all_fields').removeClass('hidden');
			jQuery('.field_ok').hide();
		});

		jQuery('#show_all_fields').click(function(){
			jQuery(this).addClass('hidden');
			jQuery('#show_deviations_only').removeClass('hidden');
			jQuery('.field_ok').show();
		});

		jQuery('.btn_update').click(function(){
			return confirm("DANGER!! In some cases, this might lead to data loss, truncation, or corruption. It might be a better idea sometimes to update the field in AppGini to match that in the database. Would you still like to continue?");
		});

		var count_updates = jQuery('.btn_update').length;
		var count_creates = jQuery('.btn_create').length;
		if(!count_creates && !count_updates){
			jQuery('.summary').addClass('alert-success').html('No deviations found. All fields OK!');
		}else{
			jQuery('.summary')
				.addClass('alert-warning')
				.html(
					'Found ' + count_creates + ' non-existing fields that need to be created.<br>' +
					'Found ' + count_updates + ' deviating fields that might need to be updated.'
				);
		}
	});
</script>

<?php
	include("$currDir/incFooter.php");
?>
