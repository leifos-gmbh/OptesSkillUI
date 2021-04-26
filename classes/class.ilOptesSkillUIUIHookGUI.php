<?php

/* Copyright (c) 1998-2010 ILIAS open source, Extended GPL, see docs/LICENSE */

include_once("./Services/UIComponent/classes/class.ilUIHookPluginGUI.php");

/**
 * User interface hook class
 *
 * @author Alex Killing <alex.killing@gmx.de>
 * @version $Id$
 * @ingroup ServicesUIComponent
 */
class ilOptesSkillUIUIHookGUI extends ilUIHookPluginGUI
{
	protected $involved_courses = array();
	protected $initialized = false;
	protected $trigger_childs = [];

	/**
	 * Init
	 * @param
	 * @return
	 */
	protected function init()
	{
		$this->trigger_childs = [];
		if (!$this->initialized) {

			$o = new ilOptesUI();
			$vtree = new ilVirtualSkillTree();
			$this->trigger_childs = array_map(function ($a) {
				return $a["id"];
			}, $vtree->getChildsOfNode($o->getTriggerSkill().":0"));
			$this->initialized = true;
		}
	}

	/**
	 * Modify HTML output of GUI elements. Modifications modes are:
	 * - ilUIHookPluginGUI::KEEP (No modification)
	 * - ilUIHookPluginGUI::REPLACE (Replace default HTML with your HTML)
	 * - ilUIHookPluginGUI::APPEND (Append your HTML to the default HTML)
	 * - ilUIHookPluginGUI::PREPEND (Prepend your HTML to the default HTML)
	 *
	 * @param string $a_comp component
	 * @param string $a_part string that identifies the part of the UI that is handled
	 * @param string $a_par array of parameters (depend on $a_comp and $a_part)
	 *
	 * @return array array with entries "mode" => modification mode, "html" => your html
	 */
	function getHTML($a_comp, $a_part, $a_par = array())
	{
		global $lng;

		$this->getPluginObject()->includeClass("class.ilOptesUI.php");

		$this->init();

		// do not show the search part of the main menu
		// $a_par["main_menu_gui"]
		if ($a_comp == "Services/Skill" && $a_part == "personal_skill_html")
		{
			$o = new ilOptesUI();

			if ($o->getTriggerSkill() != $a_par["top_skill_id"])
			{
				$skill_id = $a_par["top_skill_id"];
				$tref_id = $a_par["tref_id"];
				if (ilSkillTreeNode::_lookupType($a_par["top_skill_id"]) == "sktr") {
					$skill_id = ilSkillTemplateReference::_lookupTemplateId($a_par["top_skill_id"]);
					$tref_id = $a_par["top_skill_id"];
				}
				if (in_array($tref_id.":".$skill_id,	$this->trigger_childs)) {
					return array("mode" => ilUIHookPluginGUI::REPLACE, "html" => "");
				}
				//return array("mode" => ilUIHookPluginGUI::REPLACE, "html" => "");
				return array("mode" => ilUIHookPluginGUI::KEEP, "html" => "");
			}

				// $a_par["main_menu_gui"] is ilMainMenu object
			// $a_par["main_menu_search_gui"] is ilMainMenuSearchGUI object

			$this->getPluginObject()->includeClass("class.ilOptesChart.php");
			$this->getPluginObject()->includeClass("class.ilChartBubble.php");
			$this->getPluginObject()->includeClass("class.ilChartDataBubble.php");

			$tpl = $this->getPluginObject()->getTemplate("tpl.skill_addon.html");

			include_once("./Services/UIComponent/Panel/classes/class.ilPanelGUI.php");
			$p = ilPanelGUI::getInstance();

			// main
			$tpl->setCurrentBlock("chart");
			$tpl->setVariable("TITLE", $this->getPluginObject()->txt("overview_all_courses"));
			$tpl->setVariable("CHART", $this->getChartHTML(0, $a_par["user_id"]));
			$tpl->parseCurrentBlock();
			$ac = ilUtil::sortArray($this->involved_courses, "crs_title", "asc");
			foreach ($ac as $tst_ref_id => $crs)
			{
				$tpl->setCurrentBlock("chart");
				$tpl->setVariable("TITLE", $lng->txt("obj_crs").": ".$crs["crs_title"]);
				$tpl->setVariable("CHART", $this->getChartHTML($crs["crs_ref_id"], $a_par["user_id"]));
				$tpl->parseCurrentBlock();
			}

			$p->setBody($a_par["personal_skills_gui"]->renderSkillHTML($a_par["top_skill_id"], $a_par["user_id"],
				$a_par["edit"], $a_par["tref_id"]));

			include_once("./Services/Accordion/classes/class.ilAccordionGUI.php");
			$acc = new ilAccordionGUI();
			$acc->setId("optes_skill_acc");
			$acc->addItem($this->getPluginObject()->txt("detail_skill_pres"),
				$p->getHTML());

			return array("mode" => ilUIHookPluginGUI::REPLACE, "html" =>
				$tpl->get()."</br>".
				$acc->getHTML()."</br></br>");
		}
		
		return array("mode" => ilUIHookPluginGUI::KEEP, "html" => "");
	}

	/**
	 * Get chart HTML
	 *
	 * @param
	 * @return
	 */
	function getChartHTML($a_crs_ref_id, $a_user_id)
	{
		$chart = ilOptesChart::getInstanceByType(ilOptesChart::TYPE_BUBBLE, "optes_chart_".$a_crs_ref_id);
		$chart->setPluginObject($this->getPluginObject());
		//$chart = new ilChartBubble("optes_chart_".$a_crs_ref_id, 850, 250);
		$pl = $this->getPluginObject();
		$pl->includeClass("class.ilOptesUI.php");
		$o = new ilOptesUI();
		$rows = $o->getRows();

		// remove rows without data
		foreach ($o->getCols() as $c)
		{
			reset($rows);
			foreach ($rows as $k => $r)
			{
				$val = $o->getCompetenceValueForMatrix($r["id"], $c["id"], $a_user_id, $a_crs_ref_id);
				if ($val !== null)
				{
					$rows[$k]["got_data"] = true;
				}
			}
		}
		$rows = array_filter($rows, function($r) {
			return (isset($r["got_data"]));
		});
		if (count($rows) == 0)
		{
			return '<div class="alert alert-info" role="info">'.
				'<h5 class="ilAccHeadingHidden"><a id="il_message_focus" name="il_message_focus">Fehlermeldung</a></h5>'.
				$this->getPluginObject()->txt("no_data_yet").'</div>';
		}

		// set height
		$height = (40 * count($rows)) + 50;
		$chart->setSize(850, $height);

		// fill char
		$yticks = $xticks = array();
		$cnt = 0;
		foreach ($rows as $r)
		{
			$yticks[count($rows) - ($cnt++)] = $r["title"];
		}
		foreach ($o->getCols() as $c)
		{
			$cd = new ilChartDataBubble();
			$xticks[++$i] = $c["title"];
			reset($rows);
			$cnt = 0;
			foreach ($rows as $r)
			{
				$val = $o->getCompetenceValueForMatrix($r["id"], $c["id"], $a_user_id, $a_crs_ref_id);
				$cd->addPoint($i, count($rows) - ($cnt++), round($val, 2));
			}
			$chart->addData($cd);
		}
		$chart->setTicks($xticks, $yticks, true);
		$chart->setMinMax(0.5, 0.5 + count($o->getCols()), 0, 1 + count($rows));
		$chart_html = $chart->getHTML();
		if ($a_crs_ref_id == 0)
		{
			$this->involved_courses = array();
			$ics = $o->getInvolvedCourses();
			foreach ($ics as $k => $v)
			{
				$this->involved_courses[$v["crs_ref_id"]] = $v;
			}
		}
		return $chart_html;
	}



}
?>
