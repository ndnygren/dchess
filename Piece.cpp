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

#ifndef NN_DCHESS_PIECE
#define NN_DCHESS_PIECE

#include "ChessBoard.h"
#include <vector>
#include <string>

class Piece
{
	protected:
	int direction;
	int homerank;
	int typecode;
	ChessBoard board;



	void addBoard(std::vector<std::string> &output, int posx, int posy, std::pair<int,int>& move) const
	{
		ChessBoard temp;
		if ((posy + move.second <= 8) && (posy + move.second >= 1)
			&& (posx + move.first <= 8) && (posx + move.first >= 1)) 
		{
			if ((board.get(posx + move.first, posy + move.second) == -1)
				|| ((direction == 1) && (board.get(posx + move.first, posy + move.second) > 6)) 
				|| ((direction == -1) && (board.get(posx + move.first, posy + move.second) < 6)))
			{
				temp = ChessBoard(board);
				temp.set(posx, posy, -1);
				temp.set(posx + move.first, posy + move.second, typecode);
				temp.setLastMove(posx, posy, posx + move.first, posy + move.second);
				temp.changeTurn();
				temp.checkKingRookHome();
				output.push_back(temp.toString());
			}
		}	
	}

	void searchDir(std::vector<std::string> &output, int posx, int posy, std::pair<int,int>& move) const
	{
		int tx = 0;
		int ty = 0;
		std::pair<int,int> diff(0,0);
		do
		{
			tx = tx + move.first;
			ty = ty + move.second;
			diff = std::pair<int,int>(tx,ty);
			addBoard(output, posx, posy, diff);
		} while ((board.get(posx + tx, posy + ty) == -1)
			&& (posx + tx > 0)
			&& (posx + tx <= 8)
			&& (posy + ty > 0)
			&& (posy + ty <= 8));
	}


	public:
	int x;
	int y;
	bool isWhite;

	virtual std::string typeString() const = 0;
	virtual std::vector<std::string> generatePreList() = 0;

	virtual ~Piece() { }
};

#endif
