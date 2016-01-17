<?php

include 'FindMoves.php';

/*  dchess - backend processing for chess application
 *  Copyright (C) 2016 Nick Nygren
 *
 *  This program is free software: you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation, either version 3 of the License, or
 *  (at your option) any later version.
 *
 *  This program is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  You should have received a copy of the GNU General Public License
 *  along with this program.  If not, see <http://www.gnu.org/licenses/>. */

function moves($input) {
	if (strlen($input) > 0) { $list = genList($input, false); }
	else { $list = genList("AA1121314151617181122232425262728217273747576777871828384858687888000000",false); }

	foreach ($list as $row)
	{
		if (kingKillable($row) == false) { echo  $row . "\n";  }
	}

	return $list;
}

moves("BB1153008371000061142434435462728217273746555685004874006738000068574153");

?>
