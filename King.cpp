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
	
	public:
	virtual std::vector<std::string> generatePreList()
	{
		ChessBoard temp;
		int i;
		std::vector<std::pair<int,int> > moves;
		std::vector<std::string> output;

		moves.push_back(std::pair<int,int>(1,-1));
		moves.push_back(std::pair<int,int>(1,0));
		moves.push_back(std::pair<int,int>(1,1));
		moves.push_back(std::pair<int,int>(-1,1));
		moves.push_back(std::pair<int,int>(-1,-1));
		moves.push_back(std::pair<int,int>(-1,0));
		moves.push_back(std::pair<int,int>(0,1));
		moves.push_back(std::pair<int,int>(0,-1));

		for (i = 0; i < (int)moves.size(); i++)
		{
			addBoard(output, x, y, moves[i]);
		}

		if (typecode == ChessBoard::WHITEKING) 
		{
			if (x == 5 && y == 1
				&& board.get(4,1) == -1 
				&& board.get(3,1) == -1 
				&& board.get(2,1) == -1 
				&& board.get(1,1) == ChessBoard::WHITEROOK
				&& !(board.castleData() & 64) 
				&& !(board.castleData() & 32))
			{
				temp = ChessBoard(board);
				temp.set(1, 1, -1);
				temp.set(3, 1, ChessBoard::WHITEKING);
				temp.set(4, 1, ChessBoard::WHITEROOK);
				temp.set(5, 1, -1);
				temp.setLastMove(5, 1, 3, 1);
				temp.changeTurn();
				temp.checkKingRookHome();
				output.push_back(temp.toString());
			}
			if (x == 5 && y == 1
				&& board.get(6,1) == -1 
				&& board.get(7,1) == -1 
				&& board.get(8,1) == ChessBoard::WHITEROOK
				&& !(board.castleData() & 64) 
				&& !(board.castleData() & 16))
			{
				temp = ChessBoard(board);
				temp.set(8, 1, -1);
				temp.set(7, 1, ChessBoard::WHITEKING);
				temp.set(6, 1, ChessBoard::WHITEROOK);
				temp.set(5, 1, -1);
				temp.setLastMove(5, 1, 7, 1);
				temp.changeTurn();
				temp.checkKingRookHome();
				output.push_back(temp.toString());
			}
		}	
		if (typecode == ChessBoard::BLACKKING) 
		{
			if (x == 5 && y == 8
				&& board.get(4,8) == -1 
				&& board.get(3,8) == -1 
				&& board.get(2,8) == -1 
				&& board.get(1,8) == ChessBoard::BLACKROOK
				&& !(board.castleData() & 4) 
				&& !(board.castleData() & 2))
			{
				temp = ChessBoard(board);
				temp.set(1, 8, -1);
				temp.set(3, 8, ChessBoard::BLACKKING);
				temp.set(4, 8, ChessBoard::BLACKROOK);
				temp.set(5, 8, -1);
				temp.setLastMove(5, 8, 3, 8);
				temp.changeTurn();
				temp.checkKingRookHome();
				output.push_back(temp.toString());
			}
			if (x == 5 && y == 8
				&& board.get(6,8) == -1 
				&& board.get(7,8) == -1 
				&& board.get(8,8) == ChessBoard::BLACKROOK
				&& !(board.castleData() & 4) 
				&& !(board.castleData() & 1))
			{
				temp = ChessBoard(board);
				temp.set(8, 8, -1);
				temp.set(7, 8, ChessBoard::BLACKKING);
				temp.set(6, 8, ChessBoard::BLACKROOK);
				temp.set(5, 8, -1);
				temp.setLastMove(5, 8, 7, 8);
				temp.changeTurn();
				temp.checkKingRookHome();
				output.push_back(temp.toString());
			}
		}	

		return output;
	}

	virtual std::string typeString() const { return "King,"; }

	virtual ~King() { }
};


#endif
