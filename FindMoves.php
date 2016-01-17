<?php

include 'ChessBoard.php';
include 'Pieces.php';

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
	$temp = $temp . substr($input,1,1);
	return $temp;
}

function genList($original, $recurse) {
	$board = new ChessBoard();
	$board->loadString($original);
	$outlist = array();
	//echo var_dump(debug_backtrace()) . "\n";
	for ($i = 1; $i < 9; $i++)
		for ($j = 1; $j < 9; $j++)
		{
			if ($board->whiteTurn())
			{
				if ($board->get($i,$j) == ChessBoard::$WHITEPAWN)
				{
					//$piece = new Pawn($i, $j, $board, true);
				}
				else if ($board->get($i,$j) == ChessBoard::$WHITEKING)
				{
					//$piece = new King($i, $j, $board, true);
					//$piece->setRecurse(recurse);
				}
				else if ($board->get($i,$j) == ChessBoard::$WHITEKNIGHT)
				{
					$piece = new Knight($i, $j, $board, true);
				}
				else if ($board->get($i,$j) == ChessBoard::$WHITEROOK)
				{
					//$piece = new Rook($i, $j, $board, true);
				}
				else if ($board->get($i,$j) == ChessBoard::$WHITEBISHOP)
				{
					//$piece = new Bishop($i, $j, $board, true);
				}
				else if ($board->get($i,$j) == ChessBoard::$WHITEQUEEN)
				{
					//$piece = new Queen($i, $j, $board, true);
				}
				else { $piece = 0; }
			}
			else
			{
				if ($board->get($i,$j) == ChessBoard::$BLACKPAWN)
				{
					//$piece = new Pawn($i, $j, $board, false);
				}
				else if ($board->get($i,$j) == ChessBoard::$BLACKKING)
				{
					//$piece = new King($i, $j, $board, false);
					//$piece->setRecurse(recurse);
				}
				else if ($board->get($i,$j) == ChessBoard::$BLACKKNIGHT)
				{
					//$piece = new Knight($i, $j, $board, false);
				}
				else if ($board->get($i,$j) == ChessBoard::$BLACKROOK)
				{
					//$piece = new Rook($i, $j, $board, false);
				}
				else if ($board->get($i,$j) == ChessBoard::$BLACKBISHOP)
				{
					//$piece = new Bishop($i, $j, $board, false);
				}
				else if ($board->get($i,$j) == ChessBoard::$BLACKQUEEN)
				{
					//$piece = new Queen($i, $j, $board, false);
				}
				else { $piece = 0; }
			}

			if ($piece != 0)
			{
				$list = $piece->generatePreList();
				foreach ($list as $row)
				{
					$temp = $piece->typeString();
					$temp .= toAlg(substr($row,68,2)) . ",";
					$temp .= toAlg(substr($row,70,2)) . ",";
					$temp .= $row;
					$outlist[] = $temp;
				}
				$piece = 0;
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
		$alter.set(4, 1, ChessBoard::$WHITEKING);
	}
	else
	{
		$alter.set(5, 8, -1);
		$alter.set(4, 8, ChessBoard::$BLACKKING);
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
		$alter.set(6, 1, ChessBoard::$WHITEKING);
	}
	else
	{
		$alter.set(5, 8, -1);
		$alter.set(6, 8, ChessBoard::$BLACKKING);
	}
	return kingKillable("K,1,1," + $alter.toString());

}
