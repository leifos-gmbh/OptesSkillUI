<?php

/* Copyright (c) 1998-2013 ILIAS open source, Extended GPL, see docs/LICENSE */

include_once("./Services/Table/classes/class.ilTable2GUI.php");

/**
 * TableGUI class for compentecs
 *
 * @author Alex Killing <alex.killing@gmx.de>
 * @version $Id$
 */
class ilOptesSkillTableGUI extends ilTable2GUI
{
	/**
	 * Constructor
	 */
	function __construct($a_parent_obj, $a_parent_cmd, $a_plugin)
	{
		global $ilCtrl, $lng, $ilAccess, $lng;

		$this->plugin = $a_plugin;
		
		parent::__construct($a_parent_obj, $a_parent_cmd);

		$this->setLimit(9999);

		$this->plugin->includeClass("class.ilOptesUI.php");
		$o = new ilOptesUI();

		$this->skills = $o->getSkills();

		$data = array();
		foreach ($o->getRows() as $r)
		{
			foreach ($o->getCols() as $c)
			{
				$data[] = array("row" => $r, "col" => $c);
			}
		}

		$this->setData($data);

		$this->addColumn($this->plugin->txt("rows")." / ".$this->plugin->txt("cols"));
		$this->addColumn($this->plugin->txt("competences"));
		$this->addColumn($this->lng->txt("actions"));
		
		$this->setFormAction($ilCtrl->getFormAction($a_parent_obj));
		$this->setRowTemplate($this->plugin->getDirectory()."/templates/tpl.skill_row.html", "");
	}
	
	/**
	 * Fill table row
	 */
	protected function fillRow($a_set)
	{
		global $lng, $ilCtrl;

		$ilCtrl->setParameter($this->parent_obj, "row_id", $a_set["row"]["id"]);
		$ilCtrl->setParameter($this->parent_obj, "col_id", $a_set["col"]["id"]);
		$this->tpl->setCurrentBlock("cmd");
		$this->tpl->setVariable("CMD", $this->plugin->txt("set_competence"));
		$this->tpl->setVariable("HREF_CMD", $ilCtrl->getLinkTarget($this->parent_obj, "selectCompetence"));
		$this->tpl->parseCurrentBlock();

		$this->tpl->setVariable("ROW", $a_set["row"]["title"]);
		$this->tpl->setVariable("COL", $a_set["col"]["title"]);

		if (is_array($this->skills[$a_set["row"]["id"]][$a_set["col"]["id"]])
			&& $this->skills[$a_set["row"]["id"]][$a_set["col"]["id"]]["skill_id"] > 0)
		{
			$sk = $this->skills[$a_set["row"]["id"]][$a_set["col"]["id"]];
			include_once("./Services/Skill/classes/class.ilBasicSkill.php");

			$this->tpl->setVariable("SKILL", ilBasicSkill::_lookupTitle($sk["skill_id"], $sk["tref_id"]));

			$this->tpl->setCurrentBlock("cmd");
			$this->tpl->setVariable("CMD", $this->plugin->txt("remove_competence"));
			$this->tpl->setVariable("HREF_CMD", $ilCtrl->getLinkTarget($this->parent_obj, "removeCompetence"));
			$this->tpl->parseCurrentBlock();
		}

	}

}
?>