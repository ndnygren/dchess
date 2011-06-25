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

#ifndef NN_DCHESS_KING
#define NN_DCHESS_KING

#include "Piece.cpp"
#include <vector>
#include <utility>


class King : public Piece
{
	protected:
	int direction;
	int homerank;
	int typecode;
	std::vector<int> team;
	
	void addBoard(std::vector<std::string> &output, int posx, int posy, std::pair<int,int>& move) const
	{
		ChessBoard temp;
		if ((posy + move.second <= 8) && (posy + move.second >= 1)
			&& (posx + move.first <= 8) && (posx + move.first >= 1)) 
		{
			if ((board.get(posx + move.first, posy + move.second) == -1)
				|| ((direction == 1) && (board.get(x + move.first, y + move.second) > 6)) 
				|| ((direction == -1) && (board.get(x + move.first, y + move.second) < 6)))
			{
				temp = ChessBoard(board);
				temp.set(posx, posy, -1);
				temp.set(posx + move.first, posy + move.second,typecode);
				temp.setLastMove(posx, posy, posx + move.first, posy + move.second);
				temp.changeTurn();
				output.push_back(temp.toString());
			}
		}	
	}

	public:
	std::vector<std::string> generatePreList()
	{
		int i;
		std::vector<std::pair<int,int> > moves;
		std::vector<std::string> output;

		moves.push_back(std::pair<int,int>(1,0));
		moves.push_back(std::pair<int,int>(-1,0));
		moves.push_back(std::pair<int,int>(1,1));
		moves.push_back(std::pair<int,int>(-1,1));
		moves.push_back(std::pair<int,int>(1,-1));
		moves.push_back(std::pair<int,int>(-1,-1));
		moves.push_back(std::pair<int,int>(0,1));
		moves.push_back(std::pair<int,int>(0,-1));

		for (i = 0; i < (int)moves.size(); i++)
		{
			addBoard(output, x, y, moves[1]);
		}

		return output;
	}

	virtual ~King() { }
};


#endif
