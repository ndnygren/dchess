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

#ifndef NN_DCHESS_ROOK
#define NN_DCHESS_ROOK

#include "Piece.cpp"
#include <vector>
#include <utility>


class Rook : public Piece
{
	
	public:
	std::vector<std::string> generatePreList()
	{
		int i;
		std::vector<std::pair<int,int> > moves;
		std::vector<std::string> output;

		moves.push_back(std::pair<int,int>(1,0));
		moves.push_back(std::pair<int,int>(-1,0));
		moves.push_back(std::pair<int,int>(0,1));
		moves.push_back(std::pair<int,int>(0,-1));

		for (i = 0; i < (int)moves.size(); i++)
		{
			searchDir(output, x, y, moves[i]);
		}

		return output;
	}

	virtual ~Rook() { }
};


#endif
