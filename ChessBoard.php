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

	public function addBoard($posx, $posy, $move)
	{
		$output = array();
		$temp = new ChessBoard();
		//echo $posx . "," . $posy . " to " . json_encode($move) . "\n";
		if (($posy + $move[1] <= 8) && ($posy + $move[1] >= 1)
			&& ($posx + $move[0] <= 8) && ($posx + $move[0] >= 1))
		{
			if (($this->board->get($posx + $move[0], $posy + $move[1]) == -1)
				|| (($this->direction == 1) && ($this->board->get($posx + $move[0], $posy + $move[1]) > 6))
				|| (($this->direction == -1) && ($this->board->get($posx + $move[0], $posy + $move[1]) < 6)))
			{
				$temp->copy($this->board);
				$temp->set($posx, $posy, -1);
				$temp->set($posx + $move[0], $posy + $move[1], $this->typecode);
				$temp->setLastMove($posx, $posy, $posx + $move[0], $posy + $move[1]);
				$temp->changeTurn();
				$temp->checkKingRookHome();
				$output[] = $temp->toString();
			}
		}
		return $output;
	}

	public function searchDir($posx, $posy, $move)
	{
		$tx = 0;
		$ty = 0;
		$temp = array();
		do
		{
			$tx = $tx + $move[0];
			$ty = $ty + $move[1];
			$diff = array($tx,$ty);
			$temp = array_merge($temp, $this->addBoard($posx, $posy, $diff));
		} while (($this->board->get($posx + $tx, $posy + $ty) == -1)
			&& ($posx + $tx > 0)
			&& ($posx + $tx <= 8)
			&& ($posy + $ty > 0)
			&& ($posy + $ty <= 8));
		return $temp;
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
			$output = array_merge($output, $this->searchDir($this->x, $this->y, $move));
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

		if (($this->y + $this->direction <= 8) && ($this->y + $this->direction >= 1) && ($this->board->get($this->x, $this->y + $this->direction) == -1))
		{
			$temp->copy($this->board);
			$temp->set($this->x,$this->y,-1);
			$temp->set($this->x,$this->y + $this->direction,$this->typecode);
			$temp->setLastMove($this->x,$this->y,$this->x,$this->y + $this->direction);
			$temp->changeTurn();
			$temp->checkPromotion();
			$output[] = $temp->toString();
		}
		if (($this->y == $this->homerank) && ($this->board->get($this->x, $this->y + $this->direction) == -1) && ($this->board->get($this->x, $this->y + (2 * $this->direction)) == -1))
		{
			$temp->copy($this->board);
			$temp->set($this->x,$this->y,-1);
			$temp->set($this->x,$this->y + 2*$this->direction,$this->typecode);
			$temp->setLastMove($this->x,$this->y,$this->x,$this->y + 2*$this->direction);
			$temp->changeTurn();
			$temp->checkPromotion();
			$output[] = ($temp->toString());
		}
		if (($this->y + $this->direction <= 8) && ($this->y + $this->direction >= 1) && ($this->x + 1 < 9))
		{
			if (($this->direction == 1) && ($this->board->get($this->x + 1, $this->y + $this->direction) > 6))
			{
				$temp->copy($this->board);
				$temp->set($this->x,$this->y,-1);
				$temp->set($this->x + 1, $this->y + $this->direction, $this->typecode);
				$temp->setLastMove($this->x,$this->y,$this->x + 1, $this->y + $this->direction);
				$temp->changeTurn();
				$temp->checkPromotion();
				$output[] = ($temp->toString());
			}
			else if (($this->direction == -1) && ($this->board->get($this->x + 1, $this->y + $this->direction) < 6) && ($this->board->get($this->x + 1, $this->y + $this->direction) >= 0))
			{
				$temp->copy($this->board);
				$temp->set($this->x,$this->y,-1);
				$temp->set($this->x + 1, $this->y + $this->direction, $this->typecode);
				$temp->setLastMove($this->x,$this->y,$this->x + 1, $this->y + $this->direction);
				$temp->changeTurn();
				$temp->checkPromotion();
				$output[] = ($temp->toString());
			}
		}
		if (($this->y + $this->direction <= 8) && ($this->y + $this->direction >= 1) && ($this->x - 1 > 0))
		{
			if (($this->direction == 1) && ($this->board->get($this->x - 1, $this->y + $this->direction) > 6))
			{
				$temp->copy($this->board);
				$temp->set($this->x,$this->y,-1);
				$temp->set($this->x - 1, $this->y + $this->direction, $this->typecode);
				$temp->setLastMove($this->x,$this->y,$this->x - 1, $this->y + $this->direction);
				$temp->changeTurn();
				$temp->checkPromotion();
				$output[] = ($temp->toString());
			}
			else if (($this->direction == -1) && ($this->board->get($this->x - 1, $this->y + $this->direction) < 6) && ($this->board->get($this->x - 1, $this->y + $this->direction) >= 0))
			{
				$temp->copy($this->board);
				$temp->set($this->x,$this->y,-1);
				$temp->set($this->x - 1, $this->y + $this->direction, $this->typecode);
				$temp->setLastMove($this->x,$this->y,x - 1, $this->y + $this->direction);
				$temp->changeTurn();
				$temp->checkPromotion();
				$output[] = ($temp->toString());
			}
		}

		$lastmove = $this->board->getLastMove();
		$lastmoved = $this->board->get($lastmove[1] >> 4, $lastmove[1] & 0x0F);
//		cout << "lastmove:" << $lastmove[0] << "," << $lastmove[1] << "\n";
		if (ChessBoard::iptob($this->x+1,$this->y) == $lastmove[1] || ChessBoard::iptob($this->x-1,$this->y) == $lastmove[1])
		{
			if ($lastmove[0] - $lastmove[1] == 2*$this->direction)
			{
				if (($this->direction == -1 && $lastmoved == ChessBoard::$WHITEPAWN)
					|| ($this->direction == 1 && $lastmoved == ChessBoard::$BLACKPAWN))
				{
					$temp->copy($this->board);
					$temp->set($this->x,$this->y,-1);
					$temp->set($lastmove[1] >> 4, $lastmove[1] & 0x0F,-1);
					$temp->set($lastmove[1] >> 4, $this->y + $this->direction, $this->typecode);
					$temp->setLastMove($this->x,$this->y,$lastmove[1] >> 4, $this->y + $this->direction);
					$temp->changeTurn();
					$output[] = ($temp->toString());
				}
			}
		}
		return $output;
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
}


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
			$output = array_merge($output, $this->addBoard($this->x, $this->y, $move));
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
			$output = array_merge($output, $this->searchDir($this->x, $this->y, $move));
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
			$output = array_merge($output, $this->searchDir($this->x, $this->y, $move));
		}

		return $output;
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


class King extends Piece
{
	public $recurse = false;

	public function setRecurse($input) { $this->recurse = $input; }

	public function generatePreList()
	{
		$temp = new ChessBoard();
		$aboard = new ChessBoard();
		
		$moves = array();
		$output = array();

		$moves[] = (array(1,-1));
		$moves[] = (array(1,0));
		$moves[] = (array(1,1));
		$moves[] = (array(-1,1));
		$moves[] = (array(-1,-1));
		$moves[] = (array(-1,0));
		$moves[] = (array(0,1));
		$moves[] = (array(0,-1));

		foreach ($moves as $move)
		{
			$output = array_merge($output, $this->addBoard($this->x, $this->y, $move));
		}

		//the rest is castling conditions
		if ($this->typecode == ChessBoard::$WHITEKING) 
		{
			//white king castling to the left
			if ($this->x == 5 && $this->y == 1
				&& $this->board->get(4,1) == -1 
				&& $this->board->get(3,1) == -1 
				&& $this->board->get(2,1) == -1 
				&& $this->board->get(1,1) == ChessBoard::$WHITEROOK
				&& !($this->board->castleData() & 64) 
				&& !($this->board->castleData() & 32))
			{
				$aboard->copy($this->board);
				$aboard->changeTurn();
				if ($this->recurse && !ChessBoard::kingKillable("k,1,1," + $aboard->toString()) && !ChessBoard::leftCastleThroughCheck($this->board))
				{
					$temp->copy($this->board);
					$temp->set(1, 1, -1);
					$temp->set(3, 1, ChessBoard::$WHITEKING);
					$temp->set(4, 1, ChessBoard::$WHITEROOK);
					$temp->set(5, 1, -1);
					$temp->setLastMove(5, 1, 3, 1);
					$temp->changeTurn();
					$temp->checkKingRookHome();
					$output[] = ($temp->toString());
				}
			}
			// white king castling to the right
			if ($this->x == 5 && $this->y == 1
				&& $this->board->get(6,1) == -1 
				&& $this->board->get(7,1) == -1 
				&& $this->board->get(8,1) == ChessBoard::$WHITEROOK
				&& !($this->board->castleData() & 64) 
				&& !($this->board->castleData() & 16))
			{
				$aboard->copy($this->board);
				$aboard->changeTurn();
				if ($this->recurse && !ChessBoard::kingKillable("k,1,1," + $aboard->toString()) && !ChessBoard::rightCastleThroughCheck($this->board))
				{
					$temp->copy($this->board);
					$temp->set(8, 1, -1);
					$temp->set(7, 1, ChessBoard::$WHITEKING);
					$temp->set(6, 1, ChessBoard::$WHITEROOK);
					$temp->set(5, 1, -1);
					$temp->setLastMove(5, 1, 7, 1);
					$temp->changeTurn();
					$temp->checkKingRookHome();
					$output[] = ($temp->toString());
				}
			}
		}	
		if ($this->typecode == ChessBoard::$BLACKKING) 
		{
			// black king castling to the left
			if ($this->x == 5 && $this->y == 8
				&& $this->board->get(4,8) == -1 
				&& $this->board->get(3,8) == -1 
				&& $this->board->get(2,8) == -1 
				&& $this->board->get(1,8) == ChessBoard::$BLACKROOK
				&& !($this->board->castleData() & 4) 
				&& !($this->board->castleData() & 2))
			{
				$aboard->copy($this->board);
				$aboard->changeTurn();
				if ($this->recurse && !ChessBoard::kingKillable("k,1,1," + $aboard->toString()) && !ChessBoard::leftCastleThroughCheck($this->board))
				{
					$temp->copy($this->board);
					$temp->set(1, 8, -1);
					$temp->set(3, 8, ChessBoard::$BLACKKING);
					$temp->set(4, 8, ChessBoard::$BLACKROOK);
					$temp->set(5, 8, -1);
					$temp->setLastMove(5, 8, 3, 8);
					$temp->changeTurn();
					$temp->checkKingRookHome();
					$output[] = ($temp->toString());
				}
			}
			// black king castling to the right
			if ($this->x == 5 && $this->y == 8
				&& $this->board->get(6,8) == -1 
				&& $this->board->get(7,8) == -1 
				&& $this->board->get(8,8) == ChessBoard::$BLACKROOK
				&& !($this->board->castleData() & 4) 
				&& !($this->board->castleData() & 1))
			{
				$aboard->copy($this->board);
				$aboard->changeTurn();
				if ($this->recurse && !ChessBoard::kingKillable("k,1,1," + $aboard->toString()) && !ChessBoard::rightCastleThroughCheck($this->board))
				{
					$temp->copy($this->board);
					$temp->set(8, 8, -1);
					$temp->set(7, 8, ChessBoard::$BLACKKING);
					$temp->set(6, 8, ChessBoard::$BLACKROOK);
					$temp->set(5, 8, -1);
					$temp->setLastMove(5, 8, 7, 8);
					$temp->changeTurn();
					$temp->checkKingRookHome();
					$output[] = ($temp->toString());
				}
			}
		}	

		return $output;
	}

	public function typeString() { return "King,"; }

	function __construct($xin, $yin, $board, $white)
	{
		parent::__construct($xin, $yin, $board);
		$this->setColor($white);
		if ($white) {
			$this->typecode = ChessBoard::$WHITEKING;
		}
		else {
			$this->typecode = ChessBoard::$BLACKKING;
		}
	}
}

class ChessBoard {
	protected $whiteturn;
	protected $castle;
	protected $enpassant;
	protected $data;

	public static $WHITEPAWN = 0;
	public static $WHITEROOK = 1;
	public static $WHITEKNIGHT= 2;
	public static $WHITEBISHOP= 3;
	public static $WHITEQUEEN= 4;
	public static $WHITEKING= 5;
	public static $BOARDDARK = 6;
	public static $BOARDLIGHT = 7;
	public static $BLACKPAWN = 8;
	public static $BLACKROOK = 9;
	public static $BLACKKNIGHT= 10;
	public static $BLACKBISHOP= 11;
	public static $BLACKQUEEN= 12;
	public static $BLACKKING= 13;

	public function whiteTurn() { return $this->whiteturn; }
	public function castleData() { return $this->castle; }
	public function enPassantData($i) { return $this->enpassant[$i]; }
	public function getLastMove() { return $this->enpassant; }

	public function checkPromotion()
	{
		$x = 0;

		for ($x = 0; $x <= 8; $x++)
		{
			if ($this->get($x,1) == self::$BLACKPAWN)
				{ $this->set($x, 1, self::$BLACKQUEEN); }
			if ($this->get($x,8) == self::$WHITEPAWN)
				{ $this->set($x, 8, self::$WHITEQUEEN); }
		}
	}

	public function checkKingRookHome()
	{
		if ($this->get(1,1) != self::$WHITEROOK)
			{ $this->castle = $this->castle | 32; }
		if ($this->get(8,1) != self::$WHITEROOK)
			{ $this->castle = $this->castle | 16; }
		if ($this->get(1,8) != self::$BLACKROOK)
			{ $this->castle = $this->castle | 2; }
		if ($this->get(8,8) != self::$BLACKROOK)
			{ $this->castle = $this->castle | 1; }
		if ($this->get(5,1) != self::$WHITEKING)
			{ $this->castle = $this->castle | 64; }
		if ($this->get(5,8) != self::$BLACKKING)
			{ $this->castle = $this->castle | 4; }

	}

	public function get($x, $y)
	{
		if (($x > 0) && ($x <= 8) && ($y > 0) && ($y <= 8))
			{ return $this->data[$x - 1][$y - 1]; }
		else { return -1; }
	}

	public function locset($loc, $type)
	{
		$realloc = (($loc - 16*9) % 8) + (floor(($loc - 16*9) / 8) * 16);
		if ($type == self::$WHITEPAWN && ($loc >> 4) > 8)
		{
			$this->set(($realloc >> 4) + 1, ($realloc & 0x0F) + 1, self::$WHITEQUEEN);
		}
		else if ($type == self::$BLACKPAWN && ($loc >> 4) > 8)
		{
			$this->set(($realloc >> 4) + 1, ($realloc & 0x0F) + 1, self::$BLACKQUEEN);
		}
		else if (($loc >> 4) > 0)
			{ $this->set($loc >> 4, $loc & 0x0F, $type); }
		else
			{ $this->set(-($loc >> 4), $loc & 0x0F, $type); }
	}

	public function setLastMove($fx, $fy, $tx, $ty)
	{
		$this->enpassant[0] = self::iptob($fx,$fy);
		$this->enpassant[1] = self::iptob($tx,$ty);
	}

	public function changeTurn()
		{ $this->whiteturn = !$this->whiteTurn(); }

	public function set($x, $y, $type)
	{
		if (($x > 0) && ($x <= 8) && ($y > 0) && ($y <= 8))
			{ $this->data[$x - 1][$y - 1] = $type; }
	}

	public function loadString($input) {
		$intarray = Array();
		for ($i = 0; $i < strlen($input); $i += 2)
		{
			$intarray[] = (self::digit($input[$i]) << 4)
				     + self::digit($input[$i+1]);
		}
		if ($intarray[0] == 0xAA) { $this->whiteturn = true; }
		else { $this->whiteturn = false; }

		$this->locset($intarray[1], self::$WHITEROOK);
		$this->locset($intarray[2], self::$WHITEKNIGHT);
		$this->locset($intarray[3], self::$WHITEBISHOP);
		$this->locset($intarray[4], self::$WHITEQUEEN);
		$this->locset($intarray[5], self::$WHITEKING);
		$this->locset($intarray[6], self::$WHITEBISHOP);
		$this->locset($intarray[7], self::$WHITEKNIGHT);
		$this->locset($intarray[8], self::$WHITEROOK);

		$this->locset($intarray[9], self::$WHITEPAWN);
		$this->locset($intarray[10], self::$WHITEPAWN);
		$this->locset($intarray[11], self::$WHITEPAWN);
		$this->locset($intarray[12], self::$WHITEPAWN);
		$this->locset($intarray[13], self::$WHITEPAWN);
		$this->locset($intarray[14], self::$WHITEPAWN);
		$this->locset($intarray[15], self::$WHITEPAWN);
		$this->locset($intarray[16], self::$WHITEPAWN);
		$this->locset($intarray[17], self::$BLACKPAWN);
		$this->locset($intarray[18], self::$BLACKPAWN);
		$this->locset($intarray[19], self::$BLACKPAWN);
		$this->locset($intarray[20], self::$BLACKPAWN);
		$this->locset($intarray[21], self::$BLACKPAWN);
		$this->locset($intarray[22], self::$BLACKPAWN);
		$this->locset($intarray[23], self::$BLACKPAWN);
		$this->locset($intarray[24], self::$BLACKPAWN);

		$this->locset($intarray[25], self::$BLACKROOK);
		$this->locset($intarray[26], self::$BLACKKNIGHT);
		$this->locset($intarray[27], self::$BLACKBISHOP);
		$this->locset($intarray[28], self::$BLACKQUEEN);
		$this->locset($intarray[29], self::$BLACKKING);
		$this->locset($intarray[30], self::$BLACKBISHOP);
		$this->locset($intarray[31], self::$BLACKKNIGHT);
		$this->locset($intarray[32], self::$BLACKROOK);

		$this->castle = $intarray[33];
		$this->enpassant[0] = $intarray[34];
		$this->enpassant[1] = $intarray[35];
	}

	public static function qiptob($x, $y)
		{ return ((16*9 + 8*($x-1) + ($y-1)) & 0xFF); }

	public static function iptob($x, $y)
	{
		return ((($x << 4) + $y) & 0xFF); 
	}

	public static function btos2($x, $y)
		{ return self::btos(self::iptob($x,$y)); }

	public static function digit($input)
	{
		if ($input == 'F') { return 15; }
		else if ($input == 'E') { return 14; }
		else if ($input == 'D') { return 13; }
		else if ($input == 'C') { return 12; }
		else if ($input == 'B') { return 11; }
		else if ($input == 'A') { return 10; }
		else if ($input == '9') { return 9; }
		else if ($input == '8') { return 8; }
		else if ($input == '7') { return 7; }
		else if ($input == '6') { return 6; }
		else if ($input == '5') { return 5; }
		else if ($input == '4') { return 4; }
		else if ($input == '3') { return 3; }
		else if ($input == '2') { return 2; }
		else if ($input == '1') { return 1; }

		return 0;
	}

	public static function btos($input)
	{
		if (($input & 0xf0) == 0xf0) { $temp = "F"; }
		else if (($input & 0xf0) == 0xe0) { $temp = "E"; }
		else if (($input & 0xf0) == 0xd0) { $temp = "D"; }
		else if (($input & 0xf0) == 0xc0) { $temp = "C"; }
		else if (($input & 0xf0) == 0xb0) { $temp = "B"; }
		else if (($input & 0xf0) == 0xa0) { $temp = "A"; }
		else if (($input & 0xf0) == 0x90) { $temp = "9"; }
		else if (($input & 0xf0) == 0x80) { $temp = "8"; }
		else if (($input & 0xf0) == 0x70) { $temp = "7"; }
		else if (($input & 0xf0) == 0x60) { $temp = "6"; }
		else if (($input & 0xf0) == 0x50) { $temp = "5"; }
		else if (($input & 0xf0) == 0x40) { $temp = "4"; }
		else if (($input & 0xf0) == 0x30) { $temp = "3"; }
		else if (($input & 0xf0) == 0x20) { $temp = "2"; }
		else if (($input & 0xf0) == 0x10) { $temp = "1"; }
		else { $temp = "0"; }

		if (($input & 0x0f) == 0x0f) { $temp .= "F"; }
		else if (($input & 0x0f) == 0x0e) { $temp .= "E"; }
		else if (($input & 0x0f) == 0x0d) { $temp .= "D"; }
		else if (($input & 0x0f) == 0x0c) { $temp .= "C"; }
		else if (($input & 0x0f) == 0x0b) { $temp .= "B"; }
		else if (($input & 0x0f) == 0x0a) { $temp .= "A"; }
		else if (($input & 0x0f) == 0x09) { $temp .= "9"; }
		else if (($input & 0x0f) == 0x08) { $temp .= "8"; }
		else if (($input & 0x0f) == 0x07) { $temp .= "7"; }
		else if (($input & 0x0f) == 0x06) { $temp .= "6"; }
		else if (($input & 0x0f) == 0x05) { $temp .= "5"; }
		else if (($input & 0x0f) == 0x04) { $temp .= "4"; }
		else if (($input & 0x0f) == 0x03) { $temp .= "3"; }
		else if (($input & 0x0f) == 0x02) { $temp .= "2"; }
		else if (($input & 0x0f) == 0x01) { $temp .= "1"; }
		else { $temp .= "0"; }

		return $temp;
	}

	public function toString()
	{
		$wp = 0;
		$bp = 0;
		$wr = 0;
		$br = 0;
		$wk = 0;
		$bk = 0;
		$wb = 0;
		$bb = 0;
		$wq = 0;
		$bq = 0;
		$parray = array();

		for ($i = 0; $i < 32; $i++)
			{ $parray[] = 0x00; }
			
		if ($this->whiteturn) { $temp = "AA"; }
		else { $temp = "BB"; }

		for ($i = 1; $i < 9; $i++)
		{
			for($j = 1; $j < 9; $j++)
			{
				if ($this->get($i,$j) == self::$WHITEPAWN)
				{
					$parray[8 + $wp] = self::iptob($i,$j);
					$wp++;
				}
				else if ($this->get($i,$j) == self::$BLACKPAWN)
				{
					$parray[16 + $bp] = self::iptob($i,$j);
					$bp++;
				}
				if ($this->get($i,$j) == self::$WHITEROOK)
				{
					$parray[0 + 7 * $wr] = self::iptob($i,$j);
					$wr++;
				}
				else if ($this->get($i,$j) == self::$BLACKROOK)
				{
					$parray[24 + 7 * $br] = self::iptob($i,$j);
					$br++;
				}
				if ($this->get($i,$j) == self::$WHITEKNIGHT)
				{
					$parray[1 + 5 * $wk] = self::iptob($i,$j);
					$wk++;
				}
				else if ($this->get($i,$j) == self::$BLACKKNIGHT)
				{
					$parray[25 + 5 * $bk] = self::iptob($i,$j);
					$bk++;
				}
				if ($this->get($i,$j) == self::$WHITEBISHOP)
				{
					$parray[2 + 3 * $wb] = self::iptob($i,$j);
					$wb++;
				}
				else if ($this->get($i,$j) == self::$BLACKBISHOP)
				{
					$parray[26 + 3 * $bb] = self::iptob($i,$j);
					$bb++;
				}
				if ($this->get($i,$j) == self::$WHITEKING)
					{ $parray[4] = self::iptob($i,$j); }
				else if ($this->get($i,$j) == self::$BLACKKING)
					{ $parray[28] = self::iptob($i,$j); }
				if ($this->get($i,$j) == self::$WHITEQUEEN)
				{
					if ($wq == 0)
					{
						$parray[3] = self::iptob($i,$j); 
						$wq++;
					}
					else
					{
						$parray[8 + $wp] = self::qiptob($i,$j);
						$wp++;
					}
				}
				else if ($this->get($i,$j) == self::$BLACKQUEEN)
				{
					if ($bq == 0)
					{
						$parray[27] = self::iptob($i,$j);
						$bq++;
					}
					else
					{
						$parray[16 + $bp] = self::qiptob($i,$j);
						$bp++;
					}
				}
			}
			
		}

		for ($i = 0; $i < 32; $i++)
			{ $temp .= self::btos($parray[$i]); }

		$temp .= self::btos($this->castle);
		$temp .= self::btos($this->enpassant[0]);
		$temp .= self::btos($this->enpassant[1]);

		return $temp;

	}

	public function copy($oldboard)
	{
		$this->whiteturn = $oldboard->whiteTurn();
		$this->castle = $oldboard->castleData();

		$this->enpassant[0] = $oldboard->enPassantData(0);
		$this->enpassant[1] = $oldboard->enPassantData(1);

		for ($j = 0; $j < 8; $j++)
			for ($i = 0; $i < 8; $i++)
		{
			$this->data[$i][$j] = $oldboard->get($i + 1, $j + 1);
		}
	}

	function __construct()
	{
		$this->whiteturn = true;
		$this->castle = 0;

		$this->enpassant[0] = 0;
		$this->enpassant[1] = 0;

		for ($j = 0; $j < 8; $j++)
			for ($i = 0; $i < 8; $i++)
		{
			$this->data[$i][$j] = -1;
		}
	}

	public static function toAlg($input)
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

	public static function genList($original, $recurse) {
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
						$piece = new Pawn($i, $j, $board, true);
					}
					else if ($board->get($i,$j) == ChessBoard::$WHITEKING)
					{
						$piece = new King($i, $j, $board, true);
						$piece->setRecurse(recurse);
					}
					else if ($board->get($i,$j) == ChessBoard::$WHITEKNIGHT)
					{
						$piece = new Knight($i, $j, $board, true);
					}
					else if ($board->get($i,$j) == ChessBoard::$WHITEROOK)
					{
						$piece = new Rook($i, $j, $board, true);
					}
					else if ($board->get($i,$j) == ChessBoard::$WHITEBISHOP)
					{
						$piece = new Bishop($i, $j, $board, true);
					}
					else if ($board->get($i,$j) == ChessBoard::$WHITEQUEEN)
					{
						$piece = new Queen($i, $j, $board, true);
					}
					else { $piece = 0; }
				}
				else
				{
					if ($board->get($i,$j) == ChessBoard::$BLACKPAWN)
					{
						$piece = new Pawn($i, $j, $board, false);
					}
					else if ($board->get($i,$j) == ChessBoard::$BLACKKING)
					{
						$piece = new King($i, $j, $board, false);
						$piece->setRecurse(recurse);
					}
					else if ($board->get($i,$j) == ChessBoard::$BLACKKNIGHT)
					{
						$piece = new Knight($i, $j, $board, false);
					}
					else if ($board->get($i,$j) == ChessBoard::$BLACKROOK)
					{
						$piece = new Rook($i, $j, $board, false);
					}
					else if ($board->get($i,$j) == ChessBoard::$BLACKBISHOP)
					{
						$piece = new Bishop($i, $j, $board, false);
					}
					else if ($board->get($i,$j) == ChessBoard::$BLACKQUEEN)
					{
						$piece = new Queen($i, $j, $board, false);
					}
					else { $piece = 0; }
				}

				if ($piece != 0)
				{
					$list = $piece->generatePreList();
					foreach ($list as $row)
					{
						$temp = $piece->typeString();
						$temp .= self::toAlg(substr($row,68,2)) . ",";
						$temp .= self::toAlg(substr($row,70,2)) . ",";
						$temp .= $row;
						$outlist[] = $temp;
					}
					$piece = 0;
				}
			}

			return $outlist;
		}

	public static function kingKillable($input) {
		$rec = explode(',', $input);
		$list = self::genList($rec[3], false);

		foreach ($list as $row) {
			$rec = explode(',', $row);
			$board = $rec[3];
			if (substr($board,10,2) == "00" || substr($board,58,2) == "00")
			{
				return true; 
			}
		}
		return false;
	}

	public static function leftCastleThroughCheck($input)
	{
		$alter = new ChessBoard();
		$alter->copy($input);
		$alter->changeTurn();
		if ($input->whiteTurn())
		{
			$alter->set(5, 1, -1);
			$alter->set(4, 1, ChessBoard::$WHITEKING);

		}
		else
		{
			$alter->set(5, 8, -1);
			$alter->set(4, 8, ChessBoard::$BLACKKING);
		}
		return self::kingKillable("k,1,1," + $alter->toString());

	}

	public static function rightCastleThroughCheck($input)
	{
		$alter = new ChessBoard();
		$alter->copy($input);
		$alter->changeTurn();
		if ($input->whiteTurn())
		{
			$alter->set(5, 1, -1);
			$alter->set(6, 1, ChessBoard::$WHITEKING);
		}
		else
		{
			$alter->set(5, 8, -1);
			$alter->set(6, 8, ChessBoard::$BLACKKING);
		}
		return self::kingKillable("K,1,1," + $alter->toString());


	}

	public static function moves($input) {
		if (strlen($input) > 0) { $list = self::genList($input, false); }
		else { $list = self::genList("AA1121314151617181122232425262728217273747576777871828384858687888000000",false); }

		$output = array();
		foreach ($list as $row)
		{
			if (self::kingKillable($row) == false) { $output[] = $row;  }
		}

		return $output;
	}

	public static function check($input)
	{
		if (strlen($input) > 0)
			{ $list = self::genList($input, false); }
		else
			{ $list = self::genList("AA1121314151617181122232425262728217273747576777871828384858687888000000", false); }

		$cnt = 0;
		for ($i = 0; $i < count($list); $i++)
		{
			if (self::kingKillable($list[$i]) == false) 
			{
				$cnt++;

				if ($cnt > 1)
				{
					return "Moves Available\n";
				}
				else
				{
					$rec = explode(',',$list[$i]);
					$oneboard = $rec[3];
				}
			}
		}

		if ($cnt == 1)
		{
			return "One:" . $oneboard . "\n";
		}

		$board = new ChessBoard();
		$board->loadString($input);
		$board->changeTurn();

		$revboard = "Test,00,00," . $board->toString();

		if (self::kingKillable($revboard))
		{
			return "Checkmate\n";
		}
		else
		{
			return "Stalemate\n";
		}
	}
}

?>
