<?php
	$currDir=dirname(__FILE__);
	require("$currDir/incCommon.php");

	// get groupID of anonymous group
	$anonGroupID=sqlValue("select groupID from membership_groups where name='".$adminConfig['anonymousGroup']."'");

	// request to save changes?
	if($_POST['saveChanges']!=''){
		// validate data
		$name=makeSafe($_POST['name']);
		$description=makeSafe($_POST['description']);
		switch($_POST['visitorSignup']){
			case 0:
				$allowSignup=0;
				$needsApproval=1;
				break;
			case 2:
				$allowSignup=1;
				$needsApproval=0;
				break;
			default:
				$allowSignup=1;
				$needsApproval=1;
		}
		###############################
		$customers_insert=checkPermissionVal('customers_insert');
		$customers_view=checkPermissionVal('customers_view');
		$customers_edit=checkPermissionVal('customers_edit');
		$customers_delete=checkPermissionVal('customers_delete');
		###############################
		$employees_insert=checkPermissionVal('employees_insert');
		$employees_view=checkPermissionVal('employees_view');
		$employees_edit=checkPermissionVal('employees_edit');
		$employees_delete=checkPermissionVal('employees_delete');
		###############################
		$orders_insert=checkPermissionVal('orders_insert');
		$orders_view=checkPermissionVal('orders_view');
		$orders_edit=checkPermissionVal('orders_edit');
		$orders_delete=checkPermissionVal('orders_delete');
		###############################
		$order_details_insert=checkPermissionVal('order_details_insert');
		$order_details_view=checkPermissionVal('order_details_view');
		$order_details_edit=checkPermissionVal('order_details_edit');
		$order_details_delete=checkPermissionVal('order_details_delete');
		###############################
		$products_insert=checkPermissionVal('products_insert');
		$products_view=checkPermissionVal('products_view');
		$products_edit=checkPermissionVal('products_edit');
		$products_delete=checkPermissionVal('products_delete');
		###############################
		$categories_insert=checkPermissionVal('categories_insert');
		$categories_view=checkPermissionVal('categories_view');
		$categories_edit=checkPermissionVal('categories_edit');
		$categories_delete=checkPermissionVal('categories_delete');
		###############################
		$suppliers_insert=checkPermissionVal('suppliers_insert');
		$suppliers_view=checkPermissionVal('suppliers_view');
		$suppliers_edit=checkPermissionVal('suppliers_edit');
		$suppliers_delete=checkPermissionVal('suppliers_delete');
		###############################
		$shippers_insert=checkPermissionVal('shippers_insert');
		$shippers_view=checkPermissionVal('shippers_view');
		$shippers_edit=checkPermissionVal('shippers_edit');
		$shippers_delete=checkPermissionVal('shippers_delete');
		###############################

		// new group or old?
		if($_POST['groupID']==''){ // new group
			// make sure group name is unique
			if(sqlValue("select count(1) from membership_groups where name='$name'")){
				echo "<div class=\"alert alert-danger\">Error: Group name already exists. You must choose a unique group name.</div>";
				include("$currDir/incFooter.php");
			}

			// add group
			sql("insert into membership_groups set name='$name', description='$description', allowSignup='$allowSignup', needsApproval='$needsApproval'", $eo);

			// get new groupID
			$groupID=db_insert_id(db_link());

		}else{ // old group
			// validate groupID
			$groupID=intval($_POST['groupID']);

			if($groupID==$anonGroupID){
				$name=$adminConfig['anonymousGroup'];
				$allowSignup=0;
				$needsApproval=0;
			}

			// make sure group name is unique
			if(sqlValue("select count(1) from membership_groups where name='$name' and groupID!='$groupID'")){
				echo "<div class=\"alert alert-danger\">Error: Group name already exists. You must choose a unique group name.</div>";
				include("$currDir/incFooter.php");
			}

			// update group
			sql("update membership_groups set name='$name', description='$description', allowSignup='$allowSignup', needsApproval='$needsApproval' where groupID='$groupID'", $eo);

			// reset then add group permissions
			sql("delete from membership_grouppermissions where groupID='$groupID' and tableName='customers'", $eo);
			sql("delete from membership_grouppermissions where groupID='$groupID' and tableName='employees'", $eo);
			sql("delete from membership_grouppermissions where groupID='$groupID' and tableName='orders'", $eo);
			sql("delete from membership_grouppermissions where groupID='$groupID' and tableName='order_details'", $eo);
			sql("delete from membership_grouppermissions where groupID='$groupID' and tableName='products'", $eo);
			sql("delete from membership_grouppermissions where groupID='$groupID' and tableName='categories'", $eo);
			sql("delete from membership_grouppermissions where groupID='$groupID' and tableName='suppliers'", $eo);
			sql("delete from membership_grouppermissions where groupID='$groupID' and tableName='shippers'", $eo);
		}

		// add group permissions
		if($groupID){
			// table 'customers'
			sql("insert into membership_grouppermissions set groupID='$groupID', tableName='customers', allowInsert='$customers_insert', allowView='$customers_view', allowEdit='$customers_edit', allowDelete='$customers_delete'", $eo);
			// table 'employees'
			sql("insert into membership_grouppermissions set groupID='$groupID', tableName='employees', allowInsert='$employees_insert', allowView='$employees_view', allowEdit='$employees_edit', allowDelete='$employees_delete'", $eo);
			// table 'orders'
			sql("insert into membership_grouppermissions set groupID='$groupID', tableName='orders', allowInsert='$orders_insert', allowView='$orders_view', allowEdit='$orders_edit', allowDelete='$orders_delete'", $eo);
			// table 'order_details'
			sql("insert into membership_grouppermissions set groupID='$groupID', tableName='order_details', allowInsert='$order_details_insert', allowView='$order_details_view', allowEdit='$order_details_edit', allowDelete='$order_details_delete'", $eo);
			// table 'products'
			sql("insert into membership_grouppermissions set groupID='$groupID', tableName='products', allowInsert='$products_insert', allowView='$products_view', allowEdit='$products_edit', allowDelete='$products_delete'", $eo);
			// table 'categories'
			sql("insert into membership_grouppermissions set groupID='$groupID', tableName='categories', allowInsert='$categories_insert', allowView='$categories_view', allowEdit='$categories_edit', allowDelete='$categories_delete'", $eo);
			// table 'suppliers'
			sql("insert into membership_grouppermissions set groupID='$groupID', tableName='suppliers', allowInsert='$suppliers_insert', allowView='$suppliers_view', allowEdit='$suppliers_edit', allowDelete='$suppliers_delete'", $eo);
			// table 'shippers'
			sql("insert into membership_grouppermissions set groupID='$groupID', tableName='shippers', allowInsert='$shippers_insert', allowView='$shippers_view', allowEdit='$shippers_edit', allowDelete='$shippers_delete'", $eo);
		}

		// redirect to group editing page
		redirect("admin/pageEditGroup.php?groupID=$groupID");

	}elseif($_GET['groupID']!=''){
		// we have an edit request for a group
		$groupID=intval($_GET['groupID']);
	}

	include("$currDir/incHeader.php");

	if($groupID!=''){
		// fetch group data to fill in the form below
		$res=sql("select * from membership_groups where groupID='$groupID'", $eo);
		if($row=db_fetch_assoc($res)){
			// get group data
			$name=$row['name'];
			$description=$row['description'];
			$visitorSignup=($row['allowSignup']==1 && $row['needsApproval']==1 ? 1 : ($row['allowSignup']==1 ? 2 : 0));

			// get group permissions for each table
			$res=sql("select * from membership_grouppermissions where groupID='$groupID'", $eo);
			while($row=db_fetch_assoc($res)){
				$tableName=$row['tableName'];
				$vIns=$tableName."_insert";
				$vUpd=$tableName."_edit";
				$vDel=$tableName."_delete";
				$vVue=$tableName."_view";
				$$vIns=$row['allowInsert'];
				$$vUpd=$row['allowEdit'];
				$$vDel=$row['allowDelete'];
				$$vVue=$row['allowView'];
			}
		}else{
			// no such group exists
			echo "<div class=\"alert alert-danger\">Error: Group not found!</div>";
			$groupID=0;
		}
	}
?>
<div class="page-header"><h1><?php echo ($groupID ? "Edit Group '$name'" : "Add New Group"); ?></h1></div>
<?php if($anonGroupID==$groupID){ ?>
	<div class="alert alert-warning">Attention! This is the anonymous group.</div>
<?php } ?>
<input type="checkbox" id="showToolTips" value="1" checked><label for="showToolTips">Show tool tips as mouse moves over options</label>
<form method="post" action="pageEditGroup.php">
	<input type="hidden" name="groupID" value="<?php echo $groupID; ?>">
	<div class="table-responsive"><table class="table table-striped">
		<tr>
			<td align="right" class="tdFormCaption" valign="top">
				<div class="formFieldCaption">Group name</div>
				</td>
			<td align="left" class="tdFormInput">
				<input type="text" name="name" <?php echo ($anonGroupID==$groupID ? "readonly" : ""); ?> value="<?php echo $name; ?>" size="20" class="formTextBox">
				<br>
				<?php if($anonGroupID==$groupID){ ?>
					The name of the anonymous group is read-only here.
				<?php }else{ ?>
					If you name the group '<?php echo $adminConfig['anonymousGroup']; ?>', it will be considered the anonymous group<br>
					that defines the permissions of guest visitors that do not log into the system.
				<?php } ?>
				</td>
			</tr>
		<tr>
			<td align="right" valign="top" class="tdFormCaption">
				<div class="formFieldCaption">Description</div>
				</td>
			<td align="left" class="tdFormInput">
				<textarea name="description" cols="50" rows="5" class="formTextBox"><?php echo $description; ?></textarea>
				</td>
			</tr>
		<?php if($anonGroupID!=$groupID){ ?>
		<tr>
			<td align="right" valign="top" class="tdFormCaption">
				<div class="formFieldCaption">Allow visitors to sign up?</div>
				</td>
			<td align="left" class="tdFormInput">
				<?php
					echo htmlRadioGroup(
						"visitorSignup",
						array(0, 1, 2),
						array(
							"No. Only the admin can add users.",
							"Yes, and the admin must approve them.",
							"Yes, and automatically approve them."
						),
						($groupID ? $visitorSignup : $adminConfig['defaultSignUp'])
					);
				?>
				</td>
			</tr>
		<?php } ?>
		<tr>
			<td colspan="2" align="right" class="tdFormFooter">
				<input type="submit" name="saveChanges" value="Save changes">
				</td>
			</tr>
		<tr>
			<td colspan="2" class="tdFormHeader">
				<table class="table table-striped">
					<tr>
						<td class="tdFormHeader" colspan="5"><h2>Table permissions for this group</h2></td>
						</tr>
					<?php
						// permissions arrays common to the radio groups below
						$arrPermVal=array(0, 1, 2, 3);
						$arrPermText=array("No", "Owner", "Group", "All");
					?>
					<tr>
						<td class="tdHeader"><div class="ColCaption">Table</div></td>
						<td class="tdHeader"><div class="ColCaption">Insert</div></td>
						<td class="tdHeader"><div class="ColCaption">View</div></td>
						<td class="tdHeader"><div class="ColCaption">Edit</div></td>
						<td class="tdHeader"><div class="ColCaption">Delete</div></td>
						</tr>
				<!-- customers table -->
					<tr>
						<td class="tdCaptionCell" valign="top">Customers</td>
						<td class="tdCell" valign="top">
							<input onMouseOver="stm(customers_addTip, toolTipStyle);" onMouseOut="htm();" type="checkbox" name="customers_insert" value="1" <?php echo ($customers_insert ? "checked class=\"highlight\"" : ""); ?>>
							</td>
						<td class="tdCell">
							<?php
								echo htmlRadioGroup("customers_view", $arrPermVal, $arrPermText, $customers_view, "highlight");
							?>
							</td>
						<td class="tdCell">
							<?php
								echo htmlRadioGroup("customers_edit", $arrPermVal, $arrPermText, $customers_edit, "highlight");
							?>
							</td>
						<td class="tdCell">
							<?php
								echo htmlRadioGroup("customers_delete", $arrPermVal, $arrPermText, $customers_delete, "highlight");
							?>
							</td>
						</tr>
				<!-- employees table -->
					<tr>
						<td class="tdCaptionCell" valign="top">Employees</td>
						<td class="tdCell" valign="top">
							<input onMouseOver="stm(employees_addTip, toolTipStyle);" onMouseOut="htm();" type="checkbox" name="employees_insert" value="1" <?php echo ($employees_insert ? "checked class=\"highlight\"" : ""); ?>>
							</td>
						<td class="tdCell">
							<?php
								echo htmlRadioGroup("employees_view", $arrPermVal, $arrPermText, $employees_view, "highlight");
							?>
							</td>
						<td class="tdCell">
							<?php
								echo htmlRadioGroup("employees_edit", $arrPermVal, $arrPermText, $employees_edit, "highlight");
							?>
							</td>
						<td class="tdCell">
							<?php
								echo htmlRadioGroup("employees_delete", $arrPermVal, $arrPermText, $employees_delete, "highlight");
							?>
							</td>
						</tr>
				<!-- orders table -->
					<tr>
						<td class="tdCaptionCell" valign="top">Orders</td>
						<td class="tdCell" valign="top">
							<input onMouseOver="stm(orders_addTip, toolTipStyle);" onMouseOut="htm();" type="checkbox" name="orders_insert" value="1" <?php echo ($orders_insert ? "checked class=\"highlight\"" : ""); ?>>
							</td>
						<td class="tdCell">
							<?php
								echo htmlRadioGroup("orders_view", $arrPermVal, $arrPermText, $orders_view, "highlight");
							?>
							</td>
						<td class="tdCell">
							<?php
								echo htmlRadioGroup("orders_edit", $arrPermVal, $arrPermText, $orders_edit, "highlight");
							?>
							</td>
						<td class="tdCell">
							<?php
								echo htmlRadioGroup("orders_delete", $arrPermVal, $arrPermText, $orders_delete, "highlight");
							?>
							</td>
						</tr>
				<!-- order_details table -->
					<tr>
						<td class="tdCaptionCell" valign="top">Order Items</td>
						<td class="tdCell" valign="top">
							<input onMouseOver="stm(order_details_addTip, toolTipStyle);" onMouseOut="htm();" type="checkbox" name="order_details_insert" value="1" <?php echo ($order_details_insert ? "checked class=\"highlight\"" : ""); ?>>
							</td>
						<td class="tdCell">
							<?php
								echo htmlRadioGroup("order_details_view", $arrPermVal, $arrPermText, $order_details_view, "highlight");
							?>
							</td>
						<td class="tdCell">
							<?php
								echo htmlRadioGroup("order_details_edit", $arrPermVal, $arrPermText, $order_details_edit, "highlight");
							?>
							</td>
						<td class="tdCell">
							<?php
								echo htmlRadioGroup("order_details_delete", $arrPermVal, $arrPermText, $order_details_delete, "highlight");
							?>
							</td>
						</tr>
				<!-- products table -->
					<tr>
						<td class="tdCaptionCell" valign="top">Products</td>
						<td class="tdCell" valign="top">
							<input onMouseOver="stm(products_addTip, toolTipStyle);" onMouseOut="htm();" type="checkbox" name="products_insert" value="1" <?php echo ($products_insert ? "checked class=\"highlight\"" : ""); ?>>
							</td>
						<td class="tdCell">
							<?php
								echo htmlRadioGroup("products_view", $arrPermVal, $arrPermText, $products_view, "highlight");
							?>
							</td>
						<td class="tdCell">
							<?php
								echo htmlRadioGroup("products_edit", $arrPermVal, $arrPermText, $products_edit, "highlight");
							?>
							</td>
						<td class="tdCell">
							<?php
								echo htmlRadioGroup("products_delete", $arrPermVal, $arrPermText, $products_delete, "highlight");
							?>
							</td>
						</tr>
				<!-- categories table -->
					<tr>
						<td class="tdCaptionCell" valign="top">Product Categories</td>
						<td class="tdCell" valign="top">
							<input onMouseOver="stm(categories_addTip, toolTipStyle);" onMouseOut="htm();" type="checkbox" name="categories_insert" value="1" <?php echo ($categories_insert ? "checked class=\"highlight\"" : ""); ?>>
							</td>
						<td class="tdCell">
							<?php
								echo htmlRadioGroup("categories_view", $arrPermVal, $arrPermText, $categories_view, "highlight");
							?>
							</td>
						<td class="tdCell">
							<?php
								echo htmlRadioGroup("categories_edit", $arrPermVal, $arrPermText, $categories_edit, "highlight");
							?>
							</td>
						<td class="tdCell">
							<?php
								echo htmlRadioGroup("categories_delete", $arrPermVal, $arrPermText, $categories_delete, "highlight");
							?>
							</td>
						</tr>
				<!-- suppliers table -->
					<tr>
						<td class="tdCaptionCell" valign="top">Suppliers</td>
						<td class="tdCell" valign="top">
							<input onMouseOver="stm(suppliers_addTip, toolTipStyle);" onMouseOut="htm();" type="checkbox" name="suppliers_insert" value="1" <?php echo ($suppliers_insert ? "checked class=\"highlight\"" : ""); ?>>
							</td>
						<td class="tdCell">
							<?php
								echo htmlRadioGroup("suppliers_view", $arrPermVal, $arrPermText, $suppliers_view, "highlight");
							?>
							</td>
						<td class="tdCell">
							<?php
								echo htmlRadioGroup("suppliers_edit", $arrPermVal, $arrPermText, $suppliers_edit, "highlight");
							?>
							</td>
						<td class="tdCell">
							<?php
								echo htmlRadioGroup("suppliers_delete", $arrPermVal, $arrPermText, $suppliers_delete, "highlight");
							?>
							</td>
						</tr>
				<!-- shippers table -->
					<tr>
						<td class="tdCaptionCell" valign="top">Shippers</td>
						<td class="tdCell" valign="top">
							<input onMouseOver="stm(shippers_addTip, toolTipStyle);" onMouseOut="htm();" type="checkbox" name="shippers_insert" value="1" <?php echo ($shippers_insert ? "checked class=\"highlight\"" : ""); ?>>
							</td>
						<td class="tdCell">
							<?php
								echo htmlRadioGroup("shippers_view", $arrPermVal, $arrPermText, $shippers_view, "highlight");
							?>
							</td>
						<td class="tdCell">
							<?php
								echo htmlRadioGroup("shippers_edit", $arrPermVal, $arrPermText, $shippers_edit, "highlight");
							?>
							</td>
						<td class="tdCell">
							<?php
								echo htmlRadioGroup("shippers_delete", $arrPermVal, $arrPermText, $shippers_delete, "highlight");
							?>
							</td>
						</tr>
					</table>
				</td>
			</tr>
		<tr>
			<td colspan="2" align="right" class="tdFormFooter">
				<input type="submit" name="saveChanges" value="Save changes">
				</td>
			</tr>
		</table></div>
</form>

	<script>
		$j(function(){
			var highlight_selections = function(){
				$j('input[type=radio]:checked').next().addClass('text-primary');
				$j('input[type=radio]:not(:checked)').next().removeClass('text-primary');
			}

			$j('input[type=radio]').change(function(){ highlight_selections(); });
			highlight_selections();
		});
	</script>


<?php
	include("$currDir/incFooter.php");
?>