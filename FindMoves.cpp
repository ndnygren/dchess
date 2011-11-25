/*  dchess - backend processing for chess application
 *  Copyright (C) 2011 Nick Nygren
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

#include "WhitePawn.cpp"
#include "BlackPawn.cpp"
#include "WhiteKing.cpp"
#include "BlackKing.cpp"
#include "WhiteKnight.cpp"
#include "BlackKnight.cpp"
#include "WhiteRook.cpp"
#include "BlackRook.cpp"
#include "WhiteBishop.cpp"
#include "BlackBishop.cpp"
#include "WhiteQueen.cpp"
#include "BlackQueen.cpp"
#include "FindMoves.h"


using namespace std;

string toAlg(const string& input)
{
	string temp;
	if (input.at(0) == '1') { temp = "A"; }
	else if (input.at(0) == '2') { temp = "B"; }
	else if (input.at(0) == '3') { temp = "C"; }
	else if (input.at(0) == '4') { temp = "D"; }
	else if (input.at(0) == '5') { temp = "E"; }
	else if (input.at(0) == '6') { temp = "F"; }
	else if (input.at(0) == '7') { temp = "G"; }
	else if (input.at(0) == '8') { temp = "H"; }
	else { temp = "0"; }
	temp = temp + input.substr(1,1);
	return temp;
}

vector<string> genList(const string& original, bool recurse)
{
	int i,j,k;
	ChessBoard board;
	Piece* piece;
	vector<string> list;
	vector<string> outlist;
	string temp;

	board.loadString(original);

	for (i = 1; i < 9; i++)
		for (j = 1; j < 9; j++)
		{
			if (board.whiteTurn())
			{
				if (board.get(i,j) == board.WHITEPAWN)
				{
					piece = new WhitePawn(i, j, board);
				}
				else if (board.get(i,j) == board.WHITEKING)
				{
					piece = new WhiteKing(i, j, board);
					((King*)piece)->setRecurse(recurse);
				}
				else if (board.get(i,j) == board.WHITEKNIGHT)
				{
					piece = new WhiteKnight(i, j, board);
				}
				else if (board.get(i,j) == board.WHITEROOK)
				{
					piece = new WhiteRook(i, j, board);
				}
				else if (board.get(i,j) == board.WHITEBISHOP)
				{
					piece = new WhiteBishop(i, j, board);
				}
				else if (board.get(i,j) == board.WHITEQUEEN)
				{
					piece = new WhiteQueen(i, j, board);
				}
				else { piece = 0; }
			}
			else
			{
				if (board.get(i,j) == board.BLACKPAWN)
				{
					piece = new BlackPawn(i, j, board);
				}
				else if (board.get(i,j) == board.BLACKKING)
				{
					piece = new BlackKing(i, j, board);
					((King*)piece)->setRecurse(recurse);
				}
				else if (board.get(i,j) == board.BLACKKNIGHT)
				{
					piece = new BlackKnight(i, j, board);
				}
				else if (board.get(i,j) == board.BLACKROOK)
				{
					piece = new BlackRook(i, j, board);
				}
				else if (board.get(i,j) == board.BLACKBISHOP)
				{
					piece = new BlackBishop(i, j, board);
				}
				else if (board.get(i,j) == board.BLACKQUEEN)
				{
					piece = new BlackQueen(i, j, board);
				}
				else { piece = 0; }
			}

			if (piece != 0)
			{
				list = piece->generatePreList();
				for (k = 0; k < (int)list.size(); k++)
				{
					temp = piece->typeString();
					temp += toAlg(list[k].substr(68,70)) + ",";
					temp += toAlg(list[k].substr(70,72)) + ",";
					temp += list[k];
					outlist.push_back(temp);
				}
				delete piece;
			}
		}

	return outlist;

}

bool w_commas(char t)
{
	switch(t)
	{
		case ',':	return true;
	}	
	return false;
}

vector<string> breakwords(const string& input, bool (*whitespc)(char))
{
	int wordstart = -1;
	int count = 0;
	unsigned int i = 0;
	vector<string> v_words;

	for (i = 0; i < input.length(); i++)
	{
		if ((*whitespc)(input[i]))
		{
			if (wordstart != -1)
			{
					v_words.push_back(input.substr(
						wordstart, i - wordstart));
				wordstart = -1;
				count++;
			}
		}
		else if (wordstart == -1) { wordstart = i; }
		
	}
	if (wordstart != -1)
	{
			v_words.push_back(input.substr(wordstart, 
				input.length() - wordstart));
		wordstart = -1;
		count++;
		
	}
	return v_words;
}

bool kingKillable(const string& input)
{
	int i;
	vector<string> list = genList(breakwords(input, w_commas)[3], false);
	string board;


	for (i = 0; i < (int)list.size(); i++)
	{
		board = breakwords(list[i], w_commas)[3];
		if (board.substr(10,2) == "00" || board.substr(58,2) == "00")
		{ return true; }
	}
	return false;
}

bool leftCastleThroughCheck(const ChessBoard& input)
{
	ChessBoard alter = input;
	if (input.whiteTurn())
	{
		alter.set(5, 1, -1);
		alter.set(4, 1, ChessBoard::WHITEKING);
		return kingKillable("k,1,1," + alter.toString());
	}
	else
	{
		alter.set(5, 8, -1);
		alter.set(4, 8, ChessBoard::WHITEKING);
		return kingKillable("k,1,1," + alter.toString());
	}

}

bool rightCastleThroughCheck(const ChessBoard& input)
{
	ChessBoard alter = input;
	if (input.whiteTurn())
	{
		alter.set(5, 1, -1);
		alter.set(6, 1, ChessBoard::WHITEKING);
		return kingKillable("K,1,1," + alter.toString());
	}
	else
	{
		alter.set(5, 8, -1);
		alter.set(6, 8, ChessBoard::WHITEKING);
		return kingKillable("K,1,1," + alter.toString());
	}

}
