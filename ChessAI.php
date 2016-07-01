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

class WeightAI extends ChessAI {
	public function convertToBool($input) {
		$output = Array();

		for ($i = 0; $i < strlen($input); $i+=2) {
			if ($input[$i] == "0" && $input[$i+1] == "0") {
				$output[] = false;
			} else {
				$output[] = true;
			}

		}

		return $output;
	}

	public function weigh($input) {
		$barr = $this->convertToBool($input);
		$w_score = 0;
		$b_score = 0;

		//[1], self::$WHITEROOK);
		$w_score += $barr[1] ? 5 : 0;
		//[2], self::$WHITEKNIGHT);
		$w_score += $barr[2] ? 2.5 : 0;
		//[3], self::$WHITEBISHOP);
		$w_score += $barr[3] ? 2.5 : 0;
		//[4], self::$WHITEQUEEN);
		$w_score += $barr[4] ? 10 : 0;
		//[5], self::$WHITEKING);
		$w_score += $barr[5] ? 10 : 0;
		//[6], self::$WHITEBISHOP);
		$w_score += $barr[6] ? 2.5 : 0;
		//[7], self::$WHITEKNIGHT);
		$w_score += $barr[7] ? 2.5 : 0;
		//[8], self::$WHITEROOK);
		$w_score += $barr[8] ? 5 : 0;

		for ($i = 9; $i <= 16; $i++) {
			$w_score += $barr[$i] ? 1 : 0;
		}
		for ($i = 17; $i <= 24; $i++) {
			$b_score += $barr[$i] ? 1 : 0;
		}

		//[25], self::$BLACKROOK);
		$b_score += $barr[25] ? 5 : 0;
		//[26], self::$BLACKKNIGHT);
		$b_score += $barr[26] ? 2.5 : 0;
		//[27], self::$BLACKBISHOP);
		$b_score += $barr[27] ? 2.5 : 0;
		//[28], self::$BLACKQUEEN);
		$b_score += $barr[28] ? 10 : 0;
		//[29], self::$BLACKKING);
		$b_score += $barr[29] ? 10 : 0;
		//[30], self::$BLACKBISHOP);
		$b_score += $barr[30] ? 2.5 : 0;
		//[31], self::$BLACKKNIGHT);
		$b_score += $barr[31] ? 2.5 : 0;
		//[32], self::$BLACKROOK);
		$b_score += $barr[32] ? 5 : 0;
		return $w_score - $b_score;
	}

	public function nextMove($input) {
		$moves = ChessBoard::moves($input);
		$bestscore = -1000000;
		$best = Array();
		$mult = substr($input,0,2) == "BB" ? -1 : 1;

		foreach ($moves as $movestring) {
			$movestring = explode(',', $movestring);
			$movestring = $movestring[3];
			$score = $mult * $this->weigh($movestring);
			if ($score > $bestscore) {
				$bestscore = $score;
				$best = [$movestring];
			} else if ($score == $bestscore) {
				$best[] = $movestring;
			}
		}
		return $best[rand(0, count($best) - 1)];
	}
}

/*
$cnt = 0;

$p1 = new RandomAI();
$p2 = new WeightAI();
$start = "AA1121314151617181122232425262728217273747576777871828384858687888000000";
$current = $start;
$status = "Mov";
while (substr($status,0,3) == "One" || substr($status,0,3) == "Mov") {
	$current = $p1->nextMove($current);
	$status = ChessBoard::check($current);
	$cnt++;
	if (substr($status,0,3) == "One" || substr($status,0,3) == "Mov") {
		$current = $p2->nextMove($current);
		$status = ChessBoard::check($current);
		$cnt++;
	}
	echo "Count: " . $cnt . ", " . $p2->weigh($current) . "\n";
	if ($cnt > 200) { $status = "Stalemate"; }
}
echo $status . "\n";
 */
?>
