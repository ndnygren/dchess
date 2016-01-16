<?php

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


function toAlg($input)
{
	if ($input[0]== '1') { $temp = "A"; }
	else if ($input[0] == '2') { $temp = "B"; }
	else if ($input[0] == '3') { $temp = "C"; }
	else if ($input[0] == '4') { $temp = "D"; }
	else if ($input[0] == '5') { $temp = "E"; }
	else if ($input[0] == '6') { $temp = "F"; }
	else if ($input[0] == '7') { $temp = "G"; }
	else if ($input[0] == '8') { $temp = "H"; }
	else { $temp = "0"; }
	$temp = $temp + substr($input,1,1);
	return $temp;
}

function genList($original, $recurse) {
	$list = Array();
	$outlist = Array();
	$temp = "";

	$board = ChessBoard::loadString($original);

	for ($i = 1; $i < 9; $i++)
		for ($j = 1; $j < 9; $j++)
		{
			if ($board.whiteTurn())
			{
				if ($board.get($i,$j) == ChessBoard::WHITEPAWN)
				{
					$piece = new WhitePawn($i, $j, board);
				}
				else if ($board.get($i,$j) == ChessBoard::WHITEKING)
				{
					$piece = new WhiteKing($i, $j, board);
					$piece->setRecurse(recurse);
				}
				else if ($board.get($i,$j) == ChessBoard::WHITEKNIGHT)
				{
					$piece = new WhiteKnight($i, $j, board);
				}
				else if ($board.get($i,$j) == ChessBoard::WHITEROOK)
				{
					$piece = new WhiteRook($i, $j, board);
				}
				else if ($board.get($i,$j) == ChessBoard::WHITEBISHOP)
				{
					$piece = new WhiteBishop($i, $j, board);
				}
				else if ($board.get($i,$j) == ChessBoard::WHITEQUEEN)
				{
					$piece = new WhiteQueen($i, $j, board);
				}
				else { $piece = 0; }
			}
			else
			{
				if ($board.get($i,$j) == ChessBoard::BLACKPAWN)
				{
					$piece = new BlackPawn($i, $j, board);
				}
				else if ($board.get($i,$j) == ChessBoard::BLACKKING)
				{
					$piece = new BlackKing($i, $j, board);
					$piece->setRecurse(recurse);
				}
				else if ($board.get($i,$j) == ChessBoard::BLACKKNIGHT)
				{
					$piece = new BlackKnight($i, $j, board);
				}
				else if ($board.get($i,$j) == ChessBoard::BLACKROOK)
				{
					$piece = new BlackRook($i, $j, board);
				}
				else if ($board.get($i,$j) == ChessBoard::BLACKBISHOP)
				{
					$piece = new BlackBishop($i, $j, board);
				}
				else if ($board.get($i,$j) == ChessBoard::BLACKQUEEN)
				{
					$piece = new BlackQueen($i, $j, board);
				}
				else { $piece = 0; }
			}

			if ($piece != 0)
			{
				$list = $piece->generatePreList();
				foreach ($list as $row)
				{
					$temp = $piece->typeString();
					$temp += toAlg($row.substr(68,70)) + ",";
					$temp += toAlg($row.substr(70,72)) + ",";
					$temp += $row;
					$outlist[] = $temp;
				}
			}
		}

	return $outlist;
}

function kingKillable($input) {
	$rec = explode(',', $input);
	$list = genList($rec[3], false);

	foreach ($list as $row) {
		$rec = explode(',', $row);
		$board = $rec[3];
		if (substr($board,10,2) == "00" || substr($board,58,2) == "00")
		{ return true; }
	}
	return false;
}

function leftCastleThroughCheck($input)
{
	$alter = $input;
	$alter.changeTurn();
	if ($input.whiteTurn())
	{
		$alter.set(5, 1, -1);
		$alter.set(4, 1, ChessBoard::WHITEKING);
	}
	else
	{
		$alter.set(5, 8, -1);
		$alter.set(4, 8, ChessBoard::BLACKKING);
	}
	return kingKillable("k,1,1," + $alter.toString());

}

function rightCastleThroughCheck($input)
{
	$alter = $input;
	$alter.changeTurn();
	if ($input.whiteTurn())
	{
		$alter.set(5, 1, -1);
		$alter.set(6, 1, ChessBoard::WHITEKING);
	}
	else
	{
		$alter.set(5, 8, -1);
		$alter.set(6, 8, ChessBoard::BLACKKING);
	}
	return kingKillable("K,1,1," + $alter.toString());

}
