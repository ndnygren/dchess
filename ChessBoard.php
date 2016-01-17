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
	public function getLastMove() { return $this-> enpassant; }

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
		$realloc = 0;
		if ($type == self::$WHITEPAWN && ($loc >> 4) > 8)
		{
			$realloc = (($loc - 16*9) % 8) + ((($loc - 16*9) / 8) * 16);
			$this->set(($realloc >> 4) + 1, ($realloc & 0x0F) + 1, self::$WHITEQUEEN);
		}
		else if ($type == self::$BLACKPAWN && ($loc >> 4) > 8)
		{
			$realloc = (($loc - 16*9) % 8) + ((($loc - 16*9) / 8) * 16);
			$this->set(($realloc >> 4) + 1, ($realloc & 0x0F) + 1, self::$BLACKQUEEN);
		}
		else if (($loc >> 4) > 0)
			{ $this->set($loc >> 4, $loc & 0x0F, $type); }
		else
			{ $this->set(-($loc >> 4), $loc & 0x0F, $type); }
	}

	public function setLastMove($fx, $fy, $tx, $ty)
	{
		$this->enpassant[0] = self::iptob(fx,fy);
		$this->enpassant[1] = self::iptob(tx,ty);
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
		echo "input: " . $input . "\n";
		echo "intarray: " . json_encode($intarray) . "\n";
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
		echo json_encode($this->data) . "\n";
	}

	public static function qiptob($x, $y)
		{ return ((16*9 + 8*($x-1) + ($y-1)) & 0xFF); }

	public static function iptob($x, $y)
		{ return ((($x << 4) + $y) & 0xFF); }

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

		if (($input & 0x0f) == 0x0f) { $temp = $temp + "F"; }
		else if (($input & 0x0f) == 0x0e) { $temp = $temp + "E"; }
		else if (($input & 0x0f) == 0x0d) { $temp = $temp + "D"; }
		else if (($input & 0x0f) == 0x0c) { $temp = $temp + "C"; }
		else if (($input & 0x0f) == 0x0b) { $temp = $temp + "B"; }
		else if (($input & 0x0f) == 0x0a) { $temp = $temp + "A"; }
		else if (($input & 0x0f) == 0x09) { $temp = $temp + "9"; }
		else if (($input & 0x0f) == 0x08) { $temp = $temp + "8"; }
		else if (($input & 0x0f) == 0x07) { $temp = $temp + "7"; }
		else if (($input & 0x0f) == 0x06) { $temp = $temp + "6"; }
		else if (($input & 0x0f) == 0x05) { $temp = $temp + "5"; }
		else if (($input & 0x0f) == 0x04) { $temp = $temp + "4"; }
		else if (($input & 0x0f) == 0x03) { $temp = $temp + "3"; }
		else if (($input & 0x0f) == 0x02) { $temp = $temp + "2"; }
		else if (($input & 0x0f) == 0x01) { $temp = $temp + "1"; }
		else { $temp = $temp + "0"; }

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
		$parray = array(0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0);

		for ($i = 0; $i < 32; $i++)
			{ $parray[$i] = 0x00; }
			
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
			{ $temp = $temp + self::btos($parray[$i]); }

		$temp = $temp + self::btos($this->castle);
		$temp = $temp + self::btos($this->enpassant[0]);
		$temp = $temp + self::btos($this->enpassant[1]);

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
}

?>
