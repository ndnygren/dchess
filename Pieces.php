<?php

/*  dchess - backend processing for chess application
 *  Copyright (C) 2016 Nick Nygren
 *
 *  This program is free software: $you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation, either version 3 of the License, or
 *  (at $your option) any later version.
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
			if (($this->board->get($posx + $move[0], $posy + $move[1]) == -1)
				|| (($this->direction == 1) && ($this->board->get($posx + $move[0], $posy + $move[1]) > 6))
				|| (($this->direction == -1) && ($this->board->get($posx + $move[0], $posy + $move[1]) < 6)))
			{
				$temp->copy($this->board);
				$temp->set($posx, $posy, -1);
				$temp->set($posx + $move[0], $posy + $move[1], $typecode);
				$temp->setLastMove($posx, $posy, $posx + $move[0], $posy + $move[1]);
				$temp->changeTurn();
				$temp->checkKingRookHome();
				$output[] = $temp->toString();
			}
		}
	}

	public function searchDir($output, $posx, $posy, $move)
	{
		$tx = 0;
		$ty = 0;
		do
		{
			$tx = $tx + $move[0];
			$ty = $ty + $move[1];
			$diff = array(tx,ty);
			$this->addBoard($output, $posx, $posy, $diff);
		} while (($this->board->get($posx + $tx, $posy + $ty) == -1)
			&& ($posx + $tx > 0)
			&& ($posx + $tx <= 8)
			&& ($posy + $ty > 0)
			&& ($posy + $ty <= 8));
	}

	public function setColor($white) {
		if ($white) {
			$this->isWhite = true;
			$this->direction = 1;
			$this->homerank = 2;
		}
		else {
			$this->isWhite = false;
			$this->direction = -1;
			$this->homerank = 7;
		}
	}

	function __construct($xin, $yin, $board) {
		$this->x = $xin;
		$this->y = $yin;
		$this->board = $board;
	}
}

class Rook extends Piece
{
	public function generatePreList()
	{
		$output = array();
		$moves = array();

		$moves[] = (array(1,0));
		$moves[] = (array(-1,0));
		$moves[] = (array(0,1));
		$moves[] = (array(0,-1));

		foreach ($moves as $move)
		{
			$this->searchDir($output, $this->x, $this->y, $move);
		}

		return $output;
	}

	public function typeString() { return "Rook,"; }

	function __construct($xin, $yin, $board, $white)
	{
		parent::__construct($xin, $yin, $board);
		$this->setColor($white);
		if ($white) {
			$this->typecode = ChessBoard::$WHITEROOK;
		}
		else {
			$this->typecode = ChessBoard::$BLACKROOK;
		}
	}
}

class Pawn extends Piece
{
	protected $team;

	public function generatePreList()
	{
		$output = array();
		$temp = new ChessBoard();
		$lastmove;
		$lastmoved;

		if (($y + $this->direction <= 8) && ($y + $this->direction >= 1) && ($this->board->get($x, $y + $this->direction) == -1))
		{
			$temp->copy($this->board);
			$temp->set($x,$y,-1);
			$temp->set($x,$y + $this->direction,$typecode);
			$temp->setLastMove($x,$y,x,$y + $this->direction);
			$temp->changeTurn();
			$temp->checkPromotion();
			$output[] = $temp->toString();
		}
		if (($y == $this->homerank) && ($this->board->get($x, $y + $this->direction) == -1) && ($this->board->get($x, $y + (2 * $this->direction)) == -1))
		{
			$temp = ChessBoard($this->board);
			$temp->set($x,$y,-1);
			$temp->set($x,$y + 2*$this->direction,$typecode);
			$temp->setLastMove($x,$y,x,$y + 2*$this->direction);
			$temp->changeTurn();
			$temp->checkPromotion();
			$output[] = ($temp->toString());
		}
		if (($y + $this->direction <= 8) && ($y + $this->direction >= 1) && ($x + 1 < 9))
		{
			if (($this->direction == 1) && ($this->board->get($x + 1, $y + $this->direction) > 6))
			{
				$temp = ChessBoard($this->board);
				$temp->set($x,$y,-1);
				$temp->set($x + 1, $y + $this->direction, $typecode);
				$temp->setLastMove($x,$y,x + 1, $y + $this->direction);
				$temp->changeTurn();
				$temp->checkPromotion();
				$output[] = ($temp->toString());
			}
			else if (($this->direction == -1) && ($this->board->get($x + 1, $y + $this->direction) < 6) && ($this->board->get($x + 1, $y + $this->direction) >= 0))
			{
				$temp = ChessBoard($this->board);
				$temp->set($x,$y,-1);
				$temp->set($x + 1, $y + $this->direction, $typecode);
				$temp->setLastMove($x,$y,x + 1, $y + $this->direction);
				$temp->changeTurn();
				$temp->checkPromotion();
				$output[] = ($temp->toString());
			}
		}
		if (($y + $this->direction <= 8) && ($y + $this->direction >= 1) && ($x - 1 > 0))
		{
			if (($this->direction == 1) && ($this->board->get($x - 1, $y + $this->direction) > 6))
			{
				$temp = ChessBoard($this->board);
				$temp->set($x,$y,-1);
				$temp->set($x - 1, $y + $this->direction, $typecode);
				$temp->setLastMove($x,$y,x - 1, $y + $this->direction);
				$temp->changeTurn();
				$temp->checkPromotion();
				$output[] = ($temp->toString());
			}
			else if (($this->direction == -1) && ($this->board->get($x - 1, $y + $this->direction) < 6) && ($this->board->get($x - 1, $y + $this->direction) >= 0))
			{
				$temp = ChessBoard($this->board);
				$temp->set($x,$y,-1);
				$temp->set($x - 1, $y + $this->direction, $typecode);
				$temp->setLastMove($x,$y,x - 1, $y + $this->direction);
				$temp->changeTurn();
				$temp->checkPromotion();
				$output[] = ($temp->toString());
			}
		}

		$lastmove = $this->board->getLastMove();
		$lastmoved = $this->board->get($lastmove[1] >> 4, $lastmove[1] & 0x0F);
//		cout << "lastmove:" << $lastmove[0] << "," << $lastmove[1] << "\n";
		if (ChessBoard::iptob($x+1,$y) == $lastmove[1] || ChessBoard::iptob($x-1,$y) == $lastmove[1])
		{
			if ($lastmove[0] - $lastmove[1] == 2*$this->direction)
			{
				if (($this->direction == -1 && $lastmoved == ChessBoard::WHITEPAWN)
					|| ($this->direction == 1 && $lastmoved == ChessBoard::BLACKPAWN))
				{
					$temp->copy($this->board);
					$temp->set($x,$y,-1);
					$temp->set($lastmove[1] >> 4, $lastmove[1] & 0x0F,-1);
					$temp->set($lastmove[1] >> 4, $y + $this->direction, $typecode);
					$temp->setLastMove($x,$y,$lastmove[1] >> 4, $y + $this->direction);
					$temp->changeTurn();
					$output[] = ($temp->toString());
				}
			}
		}

		return output;
	}

	public function typeString() { return "Pawn,"; }

	function __construct($xin, $yin, $board, $white)
	{
		parent::__construct($xin, $yin, $board);
		$this->setColor($white);
		if ($white) {
			$this->typecode = ChessBoard::$WHITEPAWN;
		}
		else {
			$this->typecode = ChessBoard::$BLACKPAWN;
		}
	}
};


class Knight extends Piece
{
	public function generatePreList()
	{
		$moves = array();
		$output = array();

		$moves[] = (array(1,2));
		$moves[] = (array(1,-2));
		$moves[] = (array(2,1));
		$moves[] = (array(2,-1));
		$moves[] = (array(-1,2));
		$moves[] = (array(-1,-2));
		$moves[] = (array(-2,1));
		$moves[] = (array(-2,-1));

		foreach ($moves as $move)
		{
			$this->addBoard($output, $x, $y, $move);
		}

		return $output;
	}

	public function typeString() { return "Knight,"; }

	function __construct($xin, $yin, $board, $white)
	{
		parent::__construct($xin, $yin, $board);
		$this->setColor($white);
		if ($white) {
			$this->typecode = ChessBoard::$WHITEKNIGHT;
		}
		else {
			$this->typecode = ChessBoard::$BLACKKNIGHT;
		}
	}
}


class Queen extends Piece
{
	public function generatePreList()
	{
		$moves = array();
		$output = array();

		$moves[] = (array(1,0));
		$moves[] = (array(-1,0));
		$moves[] = (array(0,1));
		$moves[] = (array(0,-1));
		$moves[] = (array(1,1));
		$moves[] = (array(1,-1));
		$moves[] = (array(-1,1));
		$moves[] = (array(-1,-1));

		foreach ($moves as $move)
		{
			$this->searchDir($output, $this->x, $this->y, $move);
		}

		return $output;
	}

	public function typeString() { return "Queen,"; }

	function __construct($xin, $yin, $board, $white)
	{
		parent::__construct($xin, $yin, $board);
		$this->setColor($white);
		if ($white) {
			$this->typecode = ChessBoard::$WHITEQUEEN;
		}
		else {
			$this->typecode = ChessBoard::$BLACKQUEEN;
		}
	}
}


class Bishop extends Piece
{
	public function generatePreList()
	{
		$moves = array();
		$output = array();

		$moves[] = (array(1,1));
		$moves[] = (array(1,-1));
		$moves[] = (array(-1,1));
		$moves[] = (array(-1,-1));

		foreach ($moves as $move)
		{
			$this->searchDir($output, $this->x, $this->y, $move);
		}

		return output;
	}

	function typeString() { return "Bishop,"; }

	function __construct($xin, $yin, $board, $white)
	{
		parent::__construct($xin, $yin, $board);
		$this->setColor($white);
		if ($white) {
			$this->typecode = ChessBoard::$WHITEBISHOP;
		}
		else {
			$this->typecode = ChessBoard::$BLACKBISHOP;
		}
	}

}



?>

