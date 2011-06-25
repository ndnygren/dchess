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

#ifndef NN_DCHESS_PAWN
#define NN_DCHESS_PAWN

#include "Piece.cpp"
#include <vector>


class Pawn : public Piece
{
	protected:
	int direction;
	int homerank;
	int typecode;
	std::vector<int> team;
	
	public:
	std::vector<std::string> generatePreList()
	{
		std::vector<std::string> output;
		ChessBoard temp;

		if ((y + direction <= 8) && (y + direction >= 1) && (board.get(x, y + direction) == -1))
		{
			temp = ChessBoard(board);
			temp.set(x,y,-1);
			temp.set(x,y + direction,typecode);
			temp.setLastMove(x,y,x,y + direction);
			temp.changeTurn();
			output.push_back(temp.toString());
		}
		if ((y == homerank) && (board.get(x, y + direction) == -1) && (board.get(x, y + (2 * direction)) == -1))
		{
			temp = ChessBoard(board);
			temp.set(x,y,-1);
			temp.set(x,y + 2*direction,typecode);
			temp.setLastMove(x,y,x,y + 2*direction);
			temp.changeTurn();
			output.push_back(temp.toString());
		}
		if ((y + direction <= 8) && (y + direction >= 1) && (x + 1 < 9))
		{
			if ((direction == 1) && (board.get(x + 1, y + direction) > 6))
			{
				temp = ChessBoard(board);
				temp.set(x,y,-1);
				temp.set(x + 1, y + direction, typecode);
				temp.setLastMove(x,y,x + 1, y + direction);
				temp.changeTurn();
				output.push_back(temp.toString());
			}
			else if ((direction == -1) && (board.get(x + 1, y + direction) < 6) && (board.get(x + 1, y + direction) >= 0))
			{
				temp = ChessBoard(board);
				temp.set(x,y,-1);
				temp.set(x + 1, y + direction, typecode);
				temp.setLastMove(x,y,x + 1, y + direction);
				temp.changeTurn();
				output.push_back(temp.toString());
			}
		}
		if ((y + direction <= 8) && (y + direction >= 1) && (x - 1 > 0))
		{
			if ((direction == 1) && (board.get(x - 1, y + direction) > 6))
			{
				temp = ChessBoard(board);
				temp.set(x,y,-1);
				temp.set(x - 1, y + direction, typecode);
				temp.setLastMove(x,y,x - 1, y + direction);
				temp.changeTurn();
				output.push_back(temp.toString());
			}
			else if ((direction == -1) && (board.get(x - 1, y + direction) < 6) && (board.get(x - 1, y + direction) >= 0))
			{
				temp = ChessBoard(board);
				temp.set(x,y,-1);
				temp.set(x - 1, y + direction, typecode);
				temp.setLastMove(x,y,x - 1, y + direction);
				temp.changeTurn();
				output.push_back(temp.toString());
			}
		}
		return output;
	}

	virtual ~Pawn() { }
};


#endif
