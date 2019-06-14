<?php

/* Copyright (c) 1998-2014 ILIAS open source, Extended GPL, see docs/LICENSE */

/**
 *  
 *
 * @author Alex Killing <alex.killing@gmx.de>
 * @version $Id$
 */
class ilOptesUI
{
	protected $rows = array();
	protected $cols = array();
	protected $skills = array();
	protected $involved_courses = array();

	/**
	 * Constructor
	 */
	function __construct()
	{
		$this->read();
	}

	/**
	 * Read
	 */
	function read()
	{
		global $ilDB;

		$this->rows = array();
		$this->cols = array();
		$this->competences = array();

		$set = $ilDB->query("SELECT * FROM lf_optes_sm_row ORDER BY nr");
		while ($rec = $ilDB->fetchAssoc($set))
		{
			$this->rows[$rec["id"]] = $rec;
		}
		$set = $ilDB->query("SELECT * FROM lf_optes_sm_col ORDER BY nr");
		while ($rec = $ilDB->fetchAssoc($set))
		{
			$this->cols[] = $rec;
		}
		$set = $ilDB->query("SELECT * FROM lf_optes_sm_skill");
		while ($rec = $ilDB->fetchAssoc($set))
		{
			$this->skills[$rec["row_id"]][$rec["col_id"]] = $rec;
		}
	}

	/**
	 * Update rows
	 */
	function updateRows()
	{
		global $ilDB;

		$old_row_ids = array();
		$set = $ilDB->query("SELECT id FROM lf_optes_sm_row "
			);
		while ($rec = $ilDB->fetchAssoc($set))
		{
			$old_row_ids[$rec["id"]] = false;
		}

		$this->rows = ilUtil::sortArray($this->rows, "nr", "asc", true);

		$nr = 10;
		foreach ($this->rows as $r)
		{
			if ($r["id"] == "")
			{
				$r["id"] = $ilDB->nextId("lf_optes_sm_row");
			}
			else
			{
				$old_row_ids[$r["id"]] = true;
			}
			$r["nr"] = $nr;

			$ilDB->replace("lf_optes_sm_row",
				array("id" => array("integer", $r["id"])),
				array(
					"nr" => array("integer", $r["nr"]),
					"title" => array("text", $r["title"])
				)
			);
			$nr+=10;
		}

		// delete all rows, that are not in the row array anymore
		foreach ($old_row_ids as $k => $v)
		{
			if (!$v)
			{
				$ilDB->manipulate("DELETE FROM lf_optes_sm_row WHERE ".
					" id = ".$ilDB->quote($k, "integer")
					);
			}
		}
	}

	/**
	 * Update cols
	 */
	function updateCols()
	{
		global $ilDB;

		$old_col_ids = array();
		$set = $ilDB->query("SELECT id FROM lf_optes_sm_col "
		);
		while ($rec = $ilDB->fetchAssoc($set))
		{
			$old_col_ids[$rec["id"]] = false;
		}

		$this->cols = ilUtil::sortArray($this->cols, "nr", "asc", true);

		$nr = 10;
		foreach ($this->cols as $r)
		{
			if ($r["id"] == "")
			{
				$r["id"] = $ilDB->nextId("lf_optes_sm_col");
			}
			else
			{
				$old_col_ids[$r["id"]] = true;
			}
			$r["nr"] = $nr;

			$ilDB->replace("lf_optes_sm_col",
				array("id" => array("integer", $r["id"])),
				array(
					"nr" => array("integer", $r["nr"]),
					"title" => array("text", $r["title"])
				)
			);
			$nr+=10;
		}

		// delete all cols, that are not in the col array anymore
		foreach ($old_col_ids as $k => $v)
		{
			if (!$v)
			{
				$ilDB->manipulate("DELETE FROM lf_optes_sm_col WHERE ".
					" id = ".$ilDB->quote($k, "integer")
				);
			}
		}
	}


	/**
	 * Get rows
	 *
	 * @return array rows
	 */
	function getRows()
	{
		return $this->rows;
	}

	/**
	 * Set rows
	 *
	 * @param array $a_rows rows
	 */
	function setRows($a_rows)
	{
		$this->rows = $a_rows;
	}

	/**
	 * Get cols
	 *
	 * @return array cols
	 */
	function getCols()
	{
		return $this->cols;
	}

	/**
	 * Set cols
	 *
	 * @param array $a_cols cols
	 */
	function setCols($a_cols)
	{
		$this->cols = $a_cols;
	}


	/**
	 * Add row
	 *
	 * @param string $a_title title
	 */
	function addRow($a_title)
	{
		$this->rows[] = array("nr" => $this->getNextRowNr(), "title" => $a_title);
	}

	/**
	 * Remove row
	 *
	 * @param int $a_id row id
	 */
	function removeRow($a_id)
	{
		foreach($this->rows as $k => $v)
		{
			if ($v["id"] == $a_id)
			{
				unset($this->rows[$k]);
			}
		}
	}


	/**
	 * Add col
	 *
	 * @param string $a_title title
	 */
	function addCol($a_title)
	{
		$this->cols[] = array("nr" => $this->getNextColNr(), "title" => $a_title);
	}

	/**
	 * Remove col
	 *
	 * @param int $a_id col id
	 */
	function removeCol($a_id)
	{
		foreach($this->cols as $k => $v)
		{
			if ($v["id"] == $a_id)
			{
				unset($this->cols[$k]);
			}
		}
	}

	/**
	 * Get next row nr
	 *
	 * @return int next row nr
	 */
	function getNextRowNr()
	{
		$max = 0;
		foreach ($this->rows as $r)
		{
			$max = max($max, $r["nr"]);
		}
		return $max + 10;
	}

	/**
	 * Get next col nr
	 *
	 * @return int next col nr
	 */
	function getNextColNr()
	{
		$max = 0;
		foreach ($this->cols as $r)
		{
			$max = max($max, $r["nr"]);
		}
		return $max + 10;
	}

	/**
	 * Get skills
	 *
	 * @return array skills
	 */
	function getSkills()
	{
		return $this->skills;
	}

	/**
	 * Set skill
	 *
	 * @param
	 */
	function setSkill($a_row_id, $a_col_id, $a_skill_id, $a_tref_id)
	{
		$this->skills[$a_row_id][$a_col_id] = array("row_id" => $a_row_id,
			"col_id" => $a_col_id, "skill_id" => $a_skill_id, "tref_id" => $a_tref_id);
	}

	/**
	 * Update skills
	 *
	 * @param
	 * @return
	 */
	function updateSkills()
	{
		global $ilDB;

		foreach ($this->getCols() as $c)
		{
			foreach ($this->getRows() as $r)
			{
				if ($this->skills[$r["id"]][$c["id"]]["skill_id"] > 0)
				{
					$ilDB->replace("lf_optes_sm_skill",
						array("row_id" => array("integer", $r["id"]),
							"col_id" => array("integer", $c["id"])),
						array(
							"skill_id" => array("integer", $this->skills[$r["id"]][$c["id"]]["skill_id"]),
							"tref_id" => array("integer", $this->skills[$r["id"]][$c["id"]]["tref_id"])
						)
					);
				}
				else
				{
					$ilDB->manipulate("DELETE FROM lf_optes_sm_skill WHERE ".
						" row_id = ".$ilDB->quote($r["id"], "integer").
						" AND col_id = ".$ilDB->quote($c["id"], "integer")
						);
				}
			}
		}
	}

	/**
	 * Remove competence
	 *
	 * @param
	 */
	function removeCompetence($a_row_id, $a_col_id)
	{
		$this->skills[$a_row_id][$a_col_id]["skill_id"] = 0;
	}

	/**
	 * Clear involved courses
	 *
	 * @param
	 * @return
	 */
	function clearInvolvedCourses()
	{
		$this->involved_courses = array();
	}
	
	/**
	 * Get involved courses
	 *
	 * @param
	 * @return
	 */
	function getInvolvedCourses()
	{
		return $this->involved_courses;
	}
	
	
	/**
	 * Get competence value for matrix
	 *
	 * @param
	 * @return
	 */
	function getCompetenceValueForMatrix($a_row_id, $a_col_id, $a_user_id, $a_crs_ref_id = 0)
	{
		global $tree;

		include_once("./Services/Skill/classes/class.ilSkillTreeNodeFactory.php");
		$s = $this->skills[$a_row_id][$a_col_id];
		if ($s["skill_id"] > 0)
		{
			$skill = ilSkillTreeNodeFactory::getInstance((int) $s["skill_id"]);
			$lev_ents = $skill->getAllLevelEntriesOfUser((int) $s["tref_id"], $a_user_id);
			$cnt_objs = 0;
			$level_cnt = 0;
			$level_val = array();
			foreach ($skill->getLevelData() as $l)
			{
				$level_val[$l["id"]] = ++$level_cnt;
			}
			$val_sum = 0;
			foreach ($lev_ents as $e)
			{
				if ($e["trigger_obj_type"] == "tst")
				{
					// get course
					if (!isset($this->involved_courses[$e["trigger_ref_id"]]))
					{
						foreach ($tree->getPathFull($e["trigger_ref_id"]) as $p)
						{
							if ($p["type"] == "crs")
							{
								$obj_id = ilObject::_lookupObjId($p["ref_id"]);
								$this->involved_courses[$e["trigger_ref_id"]] =
									array(
										"crs_ref_id" => $p["ref_id"],
										"crs_obj_id" => $obj_id,
										"crs_title" => ilObject::_lookupTitle($obj_id)
									);
							}
						}
					}

					if ($a_crs_ref_id == 0 || $a_crs_ref_id == $this->involved_courses[$e["trigger_ref_id"]]["crs_ref_id"])
					{
						// calculate sum and number of objects
						$cnt_objs++;
						$val_sum += $level_val[$e["level_id"]];
					}

				}
			}
			if ($level_cnt == 0 || $cnt_objs == 0)
			{
				return null;
			}
			return $val_sum / ($level_cnt * $cnt_objs);
		}
		//exit;
		return null;
	}

	/**
	 * Set trigger skill
	 *
	 * @param int $a_val trigger skill id
	 */
	function setTriggerSkill($a_val)
	{
		global $ilSetting;

		$ilSetting->set("optes_trigger_skill", (int) $a_val);
	}

	/**
	 * Get trigger skill
	 *
	 * @return int trigger skill id
	 */
	function getTriggerSkill()
	{
		global $ilSetting;

		return (int) $ilSetting->get("optes_trigger_skill");
	}
}

?>