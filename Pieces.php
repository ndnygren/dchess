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


class Piece
{
	protected $direction;
	protected $homerank;
	protected $typecode;
	protected $board;
	public $x;
	public $y;
	public $isWhite;

	public function addBoard($output, $posx, $posy, $move)
	{
		$temp = new ChessBoard();
		if (($posy + $move[1] <= 8) && ($posy + $move[1] >= 1)
			&& ($posx + $move[0] <= 8) && ($posx + $move[0] >= 1))
		{
			if (($this->board.get($posx + $move[0], $posy + $move[1]) == -1)
				|| (($this->direction == 1) && ($this->board.get($posx + $move[0], $posy + $move[1]) > 6)) 
				|| (($this->direction == -1) && ($this->board.get($posx + $move[0], $posy + $move[1]) < 6)))
			{
				$temp->copy($this->board);
				$temp->set($posx, $posy, -1);
				$temp->set($posx + $move[0], $posy + $move[1], $typecode);
				$temp->setLastMove($posx, $posy, $posx + $move[0], $posy + $move[1]);
				$temp->changeTurn();
				$temp->checkKingRookHome();
				$output[] = $temp->toString());
			}
		}
	}

	public searchDir($output, $posx, $posy, $move)
	{
		$tx = 0;
		$ty = 0;
		do
		{
			$tx = $tx + $move[0];
			$ty = $ty + $move[1];
			$diff = array(tx,ty);
			$this->addBoard($output, $posx, $posy, $diff);
		} while (($this->board.get($posx + $tx, $posy + $ty) == -1)
			&& ($posx + $tx > 0)
			&& ($posx + $tx <= 8)
			&& ($posy + $ty > 0)
			&& ($posy + $ty <= 8));
	}

}


?>
