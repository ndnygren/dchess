<?php


abstract class ChessAI {
	abstract public function nextMove($input);
}

class RandomAI extends ChessAI {
	public function nextMove($input) {
		$moves = ChessBoard::moves($input);
		$movestring = $moves[rand(0, count($moves) - 1)];
		$movestring = explode(',', $movestring);
		return $movestring[3];
	}
}



?>
