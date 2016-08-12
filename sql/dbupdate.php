<#1>
<?php
	$fields = array(
		'id' => array(
			'type' => 'integer',
			'length' => 4,
			'notnull' => true
		),
		'nr' => array(
			'type' => 'integer',
			'length' => 4,
			'notnull' => true,
			'default' => 0
		),
		'title' => array(
			'type' => 'text',
			'length' => 100,
			'notnull' => false
		)
	);

	$ilDB->createTable("lf_optes_sm_row", $fields);
	$ilDB->addPrimaryKey("lf_optes_sm_row", array("id"));
?>
<#2>
<?php
	$fields = array(
		'id' => array(
			'type' => 'integer',
			'length' => 4,
			'notnull' => true
		),
		'nr' => array(
			'type' => 'integer',
			'length' => 4,
			'notnull' => true,
			'default' => 0
		),
		'title' => array(
			'type' => 'text',
			'length' => 100,
			'notnull' => false
		)
	);

	$ilDB->createTable("lf_optes_sm_col", $fields);
	$ilDB->addPrimaryKey("lf_optes_sm_col", array("id"));
?>
<#3>
<?php
	$fields = array(
		'col_id' => array(
			'type' => 'integer',
			'length' => 4,
			'notnull' => true
		),
		'row_id' => array(
			'type' => 'integer',
			'length' => 4,
			'notnull' => true
		),
		'skill_id' => array(
			'type' => 'integer',
			'length' => 4,
			'notnull' => true,
			'default' => 0
		),
		'tref_id' => array(
			'type' => 'integer',
			'length' => 4,
			'notnull' => true,
			'default' => 0
		)
	);

	$ilDB->createTable("lf_optes_sm_skill", $fields);
	$ilDB->addPrimaryKey("lf_optes_sm_skill", array("col_id", "row_id"));
?>
<#4>
<?php
	$ilDB->createSequence("lf_optes_sm_row");
	$ilDB->createSequence("lf_optes_sm_col");
?>