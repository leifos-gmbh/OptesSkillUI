<?php

/* Copyright (c) 1998-2016 ILIAS open source, Extended GPL, see docs/LICENSE */

include_once("./Services/Chart/classes/class.ilChart.php");

/**
 * Optes changes for chart class
 *
 * @author Alex Killing <alex.killing@gmx.de>
 * @version $Id$
 */
abstract class ilOptesChart extends ilChart
{
	protected $ticks; // [array]
	const TYPE_BUBBLE = 4;

	/**
	 * Get type instance
	 *
	 * @param int $a_type
	 * @param string $a_id
	 * @return ilChart
	 */
	public static function getInstanceByType($a_type, $a_id)
	{
		switch($a_type)
		{
			case self::TYPE_BUBBLE:
				return new ilChartBubble($a_id);

			default:
				return parent::getInstanceByType($a_type, $a_id);
		}
	}

	/**
	 * Set ticks
	 *
	 * @param int|array $a_x
	 * @param int|array $a_y
	 * @param bool $a_labeled
	 */
	public function setTicks($a_x, $a_y, $a_labeled = false)
	{
		$this->ticks = array("x" => $a_x, "y" => $a_y, "labeled" => (bool)$a_labeled);
	}

	/**
	 * Get ticks
	 *
	 * @return array (x, y)
	 */
	public function getTicks()
	{
		return $this->ticks;
	}


}

?>