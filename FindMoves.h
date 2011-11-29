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

#ifndef NN_FINDMOVES_H
#define NN_FINDMOVES_H


#include "ChessBoard.h"
#include <string>
#include <vector>
#include <iostream>

std::string toAlg(const std::string& input);

std::vector<std::string> genList(const std::string& original, bool recurse = true);

bool w_commas(char t);

std::vector<std::string> breakwords(const std::string& input, bool (*whitespc)(char));

bool kingKillable(const std::string& input);

bool leftCastleThroughCheck(const ChessBoard& input);

bool rightCastleThroughCheck(const ChessBoard& input);


#endif
