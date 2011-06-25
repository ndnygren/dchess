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

#include "FindMoves.cpp"
#include <string>
#include <vector>
#include <iostream>

using namespace std;


int main(int argc, char** argv)
{
	int i;
	ChessBoard board;

	vector<string> list;

	if (argc > 1)
		{ list = genList(argv[1]); }
	else
		{ list = genList("AA1121314151617181122232425262728217273747576777871828384858687888000000"); }

	for (i = 0; i < (int)list.size(); i++)
	{
		if (kingKillable(list[i]) == false) { return 0; }
	}

	board.loadString(argv[1]);

	board.changeTurn();

	if (kingKillable(board.toString()))
	{
		cout << "Checkmate\n";
	}
	else
	{
		cout << "Stalemate\n";
	}

	return 0;
}
