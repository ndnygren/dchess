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

#include "ChessBoard.h"
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
#include <string>
#include <vector>
#include <iostream>

using namespace std;

string toAlg(string input)
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

int main(int argc, char** argv)
{
	int i,j,k;
	ChessBoard board;
	Piece* piece;
	vector<string> list;

	if (argc > 1)
		{ board.loadString(argv[1]); }
	else
		{ board.loadString("AA1121314151617181122232425262728217273747576777871828384858687888000000"); }

/*		if (args.length > 0) { System.out.println(args[0]); }
		else { System.out.println("AA1121314151617181122232425262728217273747576777871828384858687888000000"); }
		System.out.println(board.toString());*/

	for (i = 1; i < 9; i++)
		for (j = 1; j < 9; j++)
		{
			if (board.whiteTurn())
			{
				if (board.get(i,j) == board.WHITEPAWN)
				{
					piece = new WhitePawn(i, j, board);
					list = piece->generatePreList();
					for (k = 0; k < (int)list.size(); k++)
					{
						cout << "Pawn,";
						cout << toAlg(list[k].substr(68,70)) << ",";
						cout << toAlg(list[k].substr(70,72)) << ",";
						cout << list[k] << "\n";
					}

				}
				else if (board.get(i,j) == board.WHITEKING)
				{
					piece = new WhiteKing(i, j, board);
					list = piece->generatePreList();
					for (k = 0; k < (int)list.size(); k++)
					{
						cout << "King,";
						cout << toAlg(list[k].substr(68,70)) << ",";
						cout << toAlg(list[k].substr(70,72)) << ",";
						cout << list[k] << "\n";
					}

				}
				else if (board.get(i,j) == board.WHITEKNIGHT)
				{
					piece = new WhiteKnight(i, j, board);
					list = piece->generatePreList();
					for (k = 0; k < (int)list.size(); k++)
					{
						cout << "Knight,";
						cout << toAlg(list[k].substr(68,70)) << ",";
						cout << toAlg(list[k].substr(70,72)) << ",";
						cout << list[k] << "\n";
					}

				}
				else if (board.get(i,j) == board.WHITEROOK)
				{
					piece = new WhiteRook(i, j, board);
					list = piece->generatePreList();
					for (k = 0; k < (int)list.size(); k++)
					{
						cout << "Rook,";
						cout << toAlg(list[k].substr(68,70)) << ",";
						cout << toAlg(list[k].substr(70,72)) << ",";
						cout << list[k] << "\n";
					}

				}
				else if (board.get(i,j) == board.WHITEBISHOP)
				{
					piece = new WhiteBishop(i, j, board);
					list = piece->generatePreList();
					for (k = 0; k < (int)list.size(); k++)
					{
						cout << "Bishop,";
						cout << toAlg(list[k].substr(68,70)) << ",";
						cout << toAlg(list[k].substr(70,72)) << ",";
						cout << list[k] << "\n";
					}

				}
				else if (board.get(i,j) == board.WHITEQUEEN)
				{
					piece = new WhiteQueen(i, j, board);
					list = piece->generatePreList();
					for (k = 0; k < (int)list.size(); k++)
					{
						cout << "Queen,";
						cout << toAlg(list[k].substr(68,70)) << ",";
						cout << toAlg(list[k].substr(70,72)) << ",";
						cout << list[k] << "\n";
					}

				}
			}
			else
			{
				if (board.get(i,j) == board.BLACKPAWN)
				{
					piece = new BlackPawn(i, j, board);
					list = piece->generatePreList();
					for (k = 0; k < (int)list.size(); k++)
					{
						cout << "Pawn,";
						cout << toAlg(list[k].substr(68,70)) << ",";
						cout << toAlg(list[k].substr(70,72)) << ",";
						cout << list[k] << "\n";
					}

				}
				else if (board.get(i,j) == board.BLACKKING)
				{
					piece = new BlackKing(i, j, board);
					list = piece->generatePreList();
					for (k = 0; k < (int)list.size(); k++)
					{
						cout << "King,";
						cout << toAlg(list[k].substr(68,70)) << ",";
						cout << toAlg(list[k].substr(70,72)) << ",";
						cout << list[k] << "\n";
					}

				}
				else if (board.get(i,j) == board.BLACKKNIGHT)
				{
					piece = new BlackKnight(i, j, board);
					list = piece->generatePreList();
					for (k = 0; k < (int)list.size(); k++)
					{
						cout << "Knight,";
						cout << toAlg(list[k].substr(68,70)) << ",";
						cout << toAlg(list[k].substr(70,72)) << ",";
						cout << list[k] << "\n";
					}

				}
				else if (board.get(i,j) == board.BLACKROOK)
				{
					piece = new BlackRook(i, j, board);
					list = piece->generatePreList();
					for (k = 0; k < (int)list.size(); k++)
					{
						cout << "Rook,";
						cout << toAlg(list[k].substr(68,70)) << ",";
						cout << toAlg(list[k].substr(70,72)) << ",";
						cout << list[k] << "\n";
					}

				}
				else if (board.get(i,j) == board.BLACKBISHOP)
				{
					piece = new BlackBishop(i, j, board);
					list = piece->generatePreList();
					for (k = 0; k < (int)list.size(); k++)
					{
						cout << "Bishop,";
						cout << toAlg(list[k].substr(68,70)) << ",";
						cout << toAlg(list[k].substr(70,72)) << ",";
						cout << list[k] << "\n";
					}

				}
				else if (board.get(i,j) == board.BLACKQUEEN)
				{
					piece = new BlackRook(i, j, board);
					list = piece->generatePreList();
					for (k = 0; k < (int)list.size(); k++)
					{
						cout << "Queen,";
						cout << toAlg(list[k].substr(68,70)) << ",";
						cout << toAlg(list[k].substr(70,72)) << ",";
						cout << list[k] << "\n";
					}

				}
			}
		}

	return 0;
}
