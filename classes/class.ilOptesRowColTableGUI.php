<?php

/* Copyright (c) 1998-2013 ILIAS open source, Extended GPL, see docs/LICENSE */

include_once("./Services/Table/classes/class.ilTable2GUI.php");

/**
 * TableGUI class for 
 *
 * @author Alex Killing <alex.killing@gmx.de>
 * @version $Id$
 *
 * @ingroup Services
 */
class ilOptesRowColTableGUI extends ilTable2GUI
{
	/**
	 * Constructor
	 */
	function __construct($a_parent_obj, $a_parent_cmd, $a_plugin, $a_cols = false)
	{
		global $ilCtrl, $lng, $ilAccess, $lng;

		$this->plugin = $a_plugin;
		$this->cols = $a_cols;

		parent::__construct($a_parent_obj, $a_parent_cmd);

		$this->setLimit(9999);

		$this->plugin->includeClass("class.ilOptesUI.php");
		$o = new ilOptesUI();
		if ($this->cols)
		{
			$this->setData($o->getCols());
		}
		else
		{
			$this->setData($o->getRows());
		}

		$this->setTitle("");
		
		$this->addColumn($this->lng->txt(""), "", "1");
		$this->addColumn($this->plugin->txt("nr"));
		$this->addColumn($this->lng->txt("title"));
		
		$this->setFormAction($ilCtrl->getFormAction($a_parent_obj));
		$this->setRowTemplate($this->plugin->getDirectory()."/templates/tpl.col_row.html", "");

		if ($this->cols)
		{
			$this->addMultiCommand("deleteCols", $lng->txt("delete"));
			$this->addCommandButton("saveCols", $lng->txt("save"));
		}
		else
		{
			$this->addMultiCommand("deleteRows", $lng->txt("delete"));
			$this->addCommandButton("saveRows", $lng->txt("save"));
		}
	}
	
	/**
	 * Fill table row
	 */
	protected function fillRow($a_set)
	{
		$this->tpl->setVariable("TITLE", ilUtil::prepareFormOutput($a_set["title"]));
		$this->tpl->setVariable("NR", ilUtil::prepareFormOutput($a_set["nr"]));
		$this->tpl->setVariable("ID", ilUtil::prepareFormOutput($a_set["id"]));
	}

}
?>