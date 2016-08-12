<?php

include_once("./Services/Component/classes/class.ilPluginConfigGUI.php");
 
/**
 * Example configuration user interface class
 *
 * @author Alex Killing <alex.killing@gmx.de>
 * @version $Id$
 *
 */
class ilOptesSkillUIConfigGUI extends ilPluginConfigGUI
{
	/**
	* Handles all commmands, default is "configure"
	*/
	function performCommand($cmd)
	{
		global $tpl;

		$pl = $this->getPluginObject();

		$tpl->setTitle($pl->txt("optes_skill_matrix_configuration"));

		switch ($cmd)
		{
			case "configure":
			case "listCols":
			case "save":
			case "addRow":
			case "addCol":
			case "saveRows":
			case "saveCols":
			case "deleteRows":
			case "deleteCols":
			case "listCompetences":
			case "selectCompetence":
			case "setCompetence":
			case "removeCompetence":
			case "showTrigger":
			case "setTrigger":
			case "selectTrigger":
			case "resetTrigger":
				$this->$cmd();
				break;
		}
	}

	/**
	 * Configure screen
	 */
	function configure($a_cols = false)
	{
		global $tpl, $ilToolbar, $lng, $ilCtrl;

		$pl = $this->getPluginObject();

		$form_field = "row";
		$add_cmd = "addRow";
		$cmd = "configure";
		if ($a_cols)
		{
			$form_field = "col";
			$add_cmd = "addCol";
			$cmd = "listCols";
		}

		// add skill row
		include_once("./Services/Form/classes/class.ilTextInputGUI.php");
		$ti = new ilTextInputGUI("", $form_field);
		$ti->setMaxLength(100);
		$ti->setSize(40);
		$ilToolbar->addInputItem($ti);
		$ilToolbar->addFormButton($lng->txt("add"), $add_cmd);
		$ilToolbar->setFormAction($ilCtrl->getFormAction($this));

		$this->setTabs($form_field."s");

		$pl->includeClass("class.ilOptesRowColTableGUI.php");

		$tab = new ilOptesRowColTableGUI($this, $cmd, $pl, $a_cols);

		$tpl->setContent($tab->getHTML());
	}
	
	/**
	 * List cols
	 */
	function listCols()
	{
		$this->configure(true);
	}
	

	/**
	 * Set tabs
	 *
	 * @param string $a_active active tab
	 */
	function setTabs($a_active)
	{
		global $ilTabs, $ilCtrl;

		$pl = $this->getPluginObject();

		$ilTabs->addTab("rows", $pl->txt("rows"),
			$ilCtrl->getLinkTarget($this, "configure"));

		$ilTabs->addTab("cols", $pl->txt("cols"),
			$ilCtrl->getLinkTarget($this, "listCols"));

		$ilTabs->addTab("competences", $pl->txt("competences"),
			$ilCtrl->getLinkTarget($this, "listCompetences"));

		$ilTabs->addTab("trigger", $pl->txt("trigger"),
			$ilCtrl->getLinkTarget($this, "showTrigger"));

		$ilTabs->activateTab($a_active);
	}


	/**
	 * Add row
	 */
	function addRow()
	{
		global $ilCtrl;

		$pl = $this->getPluginObject();

		$pl->includeClass("class.ilOptesUI.php");
		$o = new ilOptesUI();
		$o->addRow(ilUtil::stripSlashes($_POST["row"]));
		$o->updateRows();

		$ilCtrl->redirect($this, "configure");
	}

	/**
	 * Add col
	 */
	function addCol()
	{
		global $ilCtrl;

		$pl = $this->getPluginObject();

		$pl->includeClass("class.ilOptesUI.php");
		$o = new ilOptesUI();
		$o->addCol(ilUtil::stripSlashes($_POST["col"]));
		$o->updateCols();

		$ilCtrl->redirect($this, "listCols");
	}

	/**
	 * Save Rows
	 */
	function saveRows()
	{
		global $ilCtrl;

		$pl = $this->getPluginObject();

		$rows = array();
		if (is_array($_POST["nr"]))
		{
			foreach ($_POST["nr"] as $k => $v)
			{
				$rows[] = array(
					"id" => ilUtil::stripSlashes($k),
					"nr" => ilUtil::stripSlashes($v),
					"title" => ilUtil::stripSlashes($_POST["title"][$k]));
			}
		}
		$pl->includeClass("class.ilOptesUI.php");
		$o = new ilOptesUI();
		$o->setRows($rows);
		$o->updateRows();

		$ilCtrl->redirect($this, "configure");
	}

	/**
	 * Delete Rows
	 */
	function deleteRows()
	{
		global $ilCtrl;

		$pl = $this->getPluginObject();
		$pl->includeClass("class.ilOptesUI.php");
		$o = new ilOptesUI();

		if (is_array($_POST["id"]))
		{
			foreach ($_POST["id"] as $v)
			{
				$o->removeRow($v);
			}
		}

		$o->updateRows();

		$ilCtrl->redirect($this, "configure");
	}

	/**
	 * Save Cols
	 */
	function saveCols()
	{
		global $ilCtrl;

		$pl = $this->getPluginObject();

		$cols = array();
		if (is_array($_POST["nr"]))
		{
			foreach ($_POST["nr"] as $k => $v)
			{
				$cols[] = array(
					"id" => ilUtil::stripSlashes($k),
					"nr" => ilUtil::stripSlashes($v),
					"title" => ilUtil::stripSlashes($_POST["title"][$k]));
			}
		}
		$pl->includeClass("class.ilOptesUI.php");
		$o = new ilOptesUI();
		$o->setCols($cols);
		$o->updateCols();

		$ilCtrl->redirect($this, "listCols");
	}

	/**
	 * Delete cols
	 */
	function deleteCols()
	{
		global $ilCtrl;

		$pl = $this->getPluginObject();
		$pl->includeClass("class.ilOptesUI.php");
		$o = new ilOptesUI();

		if (is_array($_POST["id"]))
		{
			foreach ($_POST["id"] as $v)
			{
				$o->removeCol($v);
			}
		}

		$o->updateCols();

		$ilCtrl->redirect($this, "listCols");
	}

	/**
	 * List competences
	 */
	function listCompetences()
	{
		global $tpl, $ilToolbar, $lng, $ilCtrl;

		$pl = $this->getPluginObject();

		$this->setTabs("competences");

		$pl->includeClass("class.ilOptesSkillTableGUI.php");

		$tab = new ilOptesSkillTableGUI($this, $cmd, $pl);

		$tpl->setContent($tab->getHTML());
	}


	/**
	 * Select competence
	 */
	function selectCompetence()
	{
		global $ilUser, $tpl, $ilCtrl, $lng, $ilTabs;

		$ilCtrl->saveParameter($this, array("row_id", "col_id"));

		include_once("./Services/Skill/classes/class.ilSkillSelectorGUI.php");
		$sel = new ilSkillSelectorGUI($this, "selectCompetence", $this, "setCompetence");
		if (!$sel->handleCommand())
		{
			$tpl->setContent($sel->getHTML());
		}
	}

	/**
	 * Set competence
	 */
	function setCompetence()
	{
		global $ilCtrl;

		$skill = explode(":", $_GET["selected_skill"]);

		$pl = $this->getPluginObject();
		$pl->includeClass("class.ilOptesUI.php");
		$o = new ilOptesUI();
		$o->setSkill((int) $_GET["row_id"], (int) $_GET["col_id"], (int) $skill[0], (int) $skill[1]);
		$o->updateSkills();

		$ilCtrl->redirect($this, "listCompetences");
	}

	/**
	 * Remove competence
	 */
	function removeCompetence()
	{
		global $ilCtrl;

		$pl = $this->getPluginObject();
		$pl->includeClass("class.ilOptesUI.php");
		$o = new ilOptesUI();
		$o->removeCompetence((int) $_GET["row_id"], (int) $_GET["col_id"]);
		$o->updateSkills();

		$ilCtrl->redirect($this, "listCompetences");
	}

	/**
	 * Show trigger
	 *
	 * @param
	 * @return
	 */
	function showTrigger()
	{
		global $tpl;

		$this->setTabs("trigger");

		$tpl->setContent($this->initTriggerForm()->getHTML());
	}

	/**
	 * Init trigger form.
	 */
	public function initTriggerForm()
	{
		global $lng, $ilCtrl;

		$pl = $this->getPluginObject();

		include_once("Services/Form/classes/class.ilPropertyFormGUI.php");
		$form = new ilPropertyFormGUI();

		$pl->includeClass("class.ilOptesUI.php");
		$o = new ilOptesUI();

		// trigger skill
		$ne = new ilNonEditableValueGUI($pl->txt("trigger_skill"), "");
		if ($o->getTriggerSkill() > 0)
		{
			include_once("./Services/Skill/classes/class.ilSkillTreeNode.php");
			$ne->setValue(ilSkillTreeNode::_lookupTitle($o->getTriggerSkill()));
		}
		$form->addItem($ne);

		$form->addCommandButton("selectTrigger", $lng->txt("select"));
		$form->addCommandButton("resetTrigger", $lng->txt("reset"));

		$form->setTitle($pl->txt("trigger"));
		$form->setFormAction($ilCtrl->getFormAction($this));

		return $form;
	}

	/**
	 * Reset trigger
	 *
	 * @param
	 * @return
	 */
	function resetTrigger()
	{
		global $lng, $ilCtrl;

		$pl = $this->getPluginObject();

		$pl->includeClass("class.ilOptesUI.php");
		$o = new ilOptesUI();
		$o->setTriggerSkill(0);

		ilUtil::sendSuccess($lng->txt("msg_obj_modified"));

		$ilCtrl->redirect($this, "showTrigger");
	}

	/**
	 * Select trigger
	 */
	function selectTrigger()
	{
		global $ilUser, $tpl, $ilCtrl, $lng, $ilTabs;

		include_once("./Services/Skill/classes/class.ilPersonalSkillExplorerGUI.php");
		$sel = new ilPersonalSkillExplorerGUI($this, "selectTrigger", $this, "setTrigger");
		if (!$sel->handleCommand())
		{
			$tpl->setContent($sel->getHTML());
		}
	}

	/**
	 * Set trigger
	 */
	function setTrigger()
	{
		global $ilCtrl;

		$pl = $this->getPluginObject();
		$pl->includeClass("class.ilOptesUI.php");
		$o = new ilOptesUI();
		$o->setTriggerSkill((int) $_GET["obj_id"]);

		$ilCtrl->redirect($this, "showTrigger");
	}

}
?>
