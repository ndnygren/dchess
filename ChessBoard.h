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

#ifndef NN_DCHESS_CHESSBOARD_H
#define NN_DCHESS_CHESSBOARD_H

#include<string>

class ChessBoard
{
	protected:
	bool whiteturn;
	int castle;
	int enpassant[2];
	int data[8][8];

	public:
	static const int WHITEPAWN = 0;
	static const int WHITEROOK = 1;
	static const int WHITEKNIGHT= 2;
	static const int WHITEBISHOP= 3;
	static const int WHITEQUEEN= 4;
	static const int WHITEKING= 5;
	static const int BOARDDARK = 6;
	static const int BOARDLIGHT = 7;
	static const int BLACKPAWN = 8;
	static const int BLACKROOK = 9;
	static const int BLACKKNIGHT= 10;
	static const int BLACKBISHOP= 11;
	static const int BLACKQUEEN= 12;
	static const int BLACKKING= 13;

	bool whiteTurn() const;
	int castleData() const;
	int enPassantData(int i) const;
	int get(int x, int y) const;

	void set(int loc, int type);

	void setLastMove(int fx, int fy, int tx, int ty);

	void changeTurn();

	void set(int x, int y, int type);

	void loadString(std::string input);
	int iptob(int x, int y) const;
	std::string btos(int x, int y) const;

	int digit(char input) const;

	std::string btos(int input) const;

	std::string toString() const;
	ChessBoard(const ChessBoard& oldboard);
	ChessBoard();
};


#endif
