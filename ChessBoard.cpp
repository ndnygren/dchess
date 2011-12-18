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

#include<string>
#include "ChessBoard.h"

using namespace std;

bool ChessBoard::whiteTurn() const { return whiteturn; }
int ChessBoard::castleData() const { return castle; }
int ChessBoard::enPassantData(int i) const { return enpassant[i]; }

void ChessBoard::checkPromotion()
{
	int x = 0;

	for (x = 0; x <= 8; x++)
	{
		if (get(x,1) == BLACKPAWN)
			{ set(x, 1, BLACKQUEEN); }
		if (get(x,8) == WHITEPAWN)
			{ set(x, 8, WHITEQUEEN); }
	}
}

void ChessBoard::checkKingRookHome()
{
	if (get(1,1) != WHITEROOK)
		{ castle = castle | 32; }
	if (get(8,1) != WHITEROOK)
		{ castle = castle | 16; }
	if (get(1,8) != BLACKROOK)
		{ castle = castle | 2; }
	if (get(8,8) != BLACKROOK)
		{ castle = castle | 1; }
	if (get(5,1) != WHITEKING)
		{ castle = castle | 64; }
	if (get(5,8) != BLACKKING)
		{ castle = castle | 4; }

}

int ChessBoard::get(int x, int y) const
{
	if ((x > 0) && (x <= 8) && (y > 0) && (y <= 8))
		{ return data[x - 1][y - 1]; }
	else { return -1; }
}

void ChessBoard::set(int loc, int type)
{
	int realloc = 0;
	if (type == WHITEPAWN && (loc >> 4) > 8)
	{
		realloc = ((loc - 16*9) % 8) + (((loc - 16*9) / 8) * 16);
		set((realloc >> 4) + 1, (realloc & 0x0F) + 1, WHITEQUEEN);
	}
	else if (type == BLACKPAWN && (loc >> 4) > 8)
	{
		realloc = ((loc - 16*9) % 8) + (((loc - 16*9) / 8) * 16);
		set((realloc >> 4) + 1, (realloc & 0x0F) + 1, BLACKQUEEN);
	}
	else if ((loc >> 4) > 0)
		{ set(loc >> 4, loc & 0x0F, type); }
	else
		{ set(-(loc >> 4), loc & 0x0F, type); }
}

void ChessBoard::setLastMove(int fx, int fy, int tx, int ty)
{
	enpassant[0] = iptob(fx,fy);
	enpassant[1] = iptob(tx,ty);
}

void ChessBoard::changeTurn()
	{ whiteturn = !whiteturn; }

void ChessBoard::set(int x, int y, int type)
{
	if ((x > 0) && (x <= 8) && (y > 0) && (y <= 8))
		{ data[x - 1][y - 1] = type; }
}

void ChessBoard::loadString(const std::string& input)
{
	int i;
	int* intarray = new int[input.length() / 2];

	for (i = 0; i < (int)input.length(); i += 2)
	{
		intarray[i / 2] = (digit(input.at(i)) << 4)
       	                     + digit(input.at(i+1));
	}

	if (intarray[0] == (int)0xAA) { whiteturn = true; }
	else { whiteturn = false; }

	set(intarray[1], WHITEROOK);
	set(intarray[2], WHITEKNIGHT);
	set(intarray[3], WHITEBISHOP);
	set(intarray[4], WHITEQUEEN);
	set(intarray[5], WHITEKING);
	set(intarray[6], WHITEBISHOP);
	set(intarray[7], WHITEKNIGHT);
	set(intarray[8], WHITEROOK);

	set(intarray[9], WHITEPAWN);
	set(intarray[10], WHITEPAWN);
	set(intarray[11], WHITEPAWN);
	set(intarray[12], WHITEPAWN);
	set(intarray[13], WHITEPAWN);
	set(intarray[14], WHITEPAWN);
	set(intarray[15], WHITEPAWN);
	set(intarray[16], WHITEPAWN);
	set(intarray[17], BLACKPAWN);
	set(intarray[18], BLACKPAWN);
	set(intarray[19], BLACKPAWN);
	set(intarray[20], BLACKPAWN);
	set(intarray[21], BLACKPAWN);
	set(intarray[22], BLACKPAWN);
	set(intarray[23], BLACKPAWN);
	set(intarray[24], BLACKPAWN);

	set(intarray[25], BLACKROOK);
	set(intarray[26], BLACKKNIGHT);
	set(intarray[27], BLACKBISHOP);
	set(intarray[28], BLACKQUEEN);
	set(intarray[29], BLACKKING);
	set(intarray[30], BLACKBISHOP);
	set(intarray[31], BLACKKNIGHT);
	set(intarray[32], BLACKROOK);

	castle = intarray[33];
	enpassant[0] = intarray[34];
	enpassant[1] = intarray[35];
	delete [] intarray;
}

int ChessBoard::qiptob(int x, int y) const
	{ return (int)((16*9 + 8*(x-1) + (y-1)) & 0xFF); }

int ChessBoard::iptob(int x, int y) const
	{ return (int)(((x << 4) + y) & 0xFF); }

std::string ChessBoard::btos(int x, int y) const
	{ return btos(iptob(x,y)); }

int ChessBoard::digit(char input) const
{
	std::string temp;
	if (input == 'F') { return 15; }
	else if (input == 'E') { return 14; }
	else if (input == 'D') { return 13; }
	else if (input == 'C') { return 12; }
	else if (input == 'B') { return 11; }
	else if (input == 'A') { return 10; }
	else if (input == '9') { return 9; }
	else if (input == '8') { return 8; }
	else if (input == '7') { return 7; }
	else if (input == '6') { return 6; }
	else if (input == '5') { return 5; }
	else if (input == '4') { return 4; }
	else if (input == '3') { return 3; }
	else if (input == '2') { return 2; }
	else if (input == '1') { return 1; }

	return 0;
}

std::string ChessBoard::btos(int input) const
{
	std::string temp;
	if ((input & 0xf0) == 0xf0) { temp = "F"; }
	else if ((input & 0xf0) == 0xe0) { temp = "E"; }
	else if ((input & 0xf0) == 0xd0) { temp = "D"; }
	else if ((input & 0xf0) == 0xc0) { temp = "C"; }
	else if ((input & 0xf0) == 0xb0) { temp = "B"; }
	else if ((input & 0xf0) == 0xa0) { temp = "A"; }
	else if ((input & 0xf0) == 0x90) { temp = "9"; }
	else if ((input & 0xf0) == 0x80) { temp = "8"; }
	else if ((input & 0xf0) == 0x70) { temp = "7"; }
	else if ((input & 0xf0) == 0x60) { temp = "6"; }
	else if ((input & 0xf0) == 0x50) { temp = "5"; }
	else if ((input & 0xf0) == 0x40) { temp = "4"; }
	else if ((input & 0xf0) == 0x30) { temp = "3"; }
	else if ((input & 0xf0) == 0x20) { temp = "2"; }
	else if ((input & 0xf0) == 0x10) { temp = "1"; }
	else { temp = "0"; }

	if ((input & 0x0f) == 0x0f) { temp = temp + "F"; }
	else if ((input & 0x0f) == 0x0e) { temp = temp + "E"; }
	else if ((input & 0x0f) == 0x0d) { temp = temp + "D"; }
	else if ((input & 0x0f) == 0x0c) { temp = temp + "C"; }
	else if ((input & 0x0f) == 0x0b) { temp = temp + "B"; }
	else if ((input & 0x0f) == 0x0a) { temp = temp + "A"; }
	else if ((input & 0x0f) == 0x09) { temp = temp + "9"; }
	else if ((input & 0x0f) == 0x08) { temp = temp + "8"; }
	else if ((input & 0x0f) == 0x07) { temp = temp + "7"; }
	else if ((input & 0x0f) == 0x06) { temp = temp + "6"; }
	else if ((input & 0x0f) == 0x05) { temp = temp + "5"; }
	else if ((input & 0x0f) == 0x04) { temp = temp + "4"; }
	else if ((input & 0x0f) == 0x03) { temp = temp + "3"; }
	else if ((input & 0x0f) == 0x02) { temp = temp + "2"; }
	else if ((input & 0x0f) == 0x01) { temp = temp + "1"; }
	else { temp = temp + "0"; }

	return temp;
}


std::string ChessBoard::toString() const
{
	std::string temp;
	int i,j;
	int wp = 0;
	int bp = 0;
	int wr = 0;
	int br = 0;
	int wk = 0;
	int bk = 0;
	int wb = 0;
	int bb = 0;
	int wq = 0;
	int bq = 0;
	int parray[32];

	for (i = 0; i < 32; i++)
		{ parray[i] = 0x00; }
		
	if (whiteturn) { temp = "AA"; }
	else { temp = "BB"; }

	for (i = 1; i < 9; i++)
	{
		for(j = 1; j < 9; j++)
		{
			if (get(i,j) == WHITEPAWN)
			{
				parray[8 + wp] = iptob(i,j);
				wp++;
			}
			else if (get(i,j) == BLACKPAWN)
			{
				parray[16 + bp] = iptob(i,j);
				bp++;
			}
			if (get(i,j) == WHITEROOK)
			{
				parray[0 + 7 * wr] = iptob(i,j);
				wr++;
			}
			else if (get(i,j) == BLACKROOK)
			{
				parray[24 + 7 * br] = iptob(i,j);
				br++;
			}
			if (get(i,j) == WHITEKNIGHT)
			{
				parray[1 + 5 * wk] = iptob(i,j);
				wk++;
			}
			else if (get(i,j) == BLACKKNIGHT)
			{
				parray[25 + 5 * bk] = iptob(i,j);
				bk++;
			}
			if (get(i,j) == WHITEBISHOP)
			{
				parray[2 + 3 * wb] = iptob(i,j);
				wb++;
			}
			else if (get(i,j) == BLACKBISHOP)
			{
				parray[26 + 3 * bb] = iptob(i,j);
				bb++;
			}
			if (get(i,j) == WHITEKING)
				{ parray[4] = iptob(i,j); }
			else if (get(i,j) == BLACKKING)
				{ parray[28] = iptob(i,j); }
			if (get(i,j) == WHITEQUEEN)
			{
				if (wq == 0)
				{
					parray[3] = iptob(i,j); 
					wq++;
				}
				else
				{
					parray[8 + wp] = qiptob(i,j);
					wp++;
				}
			}
			else if (get(i,j) == BLACKQUEEN)
			{
				if (bq == 0)
				{
					parray[27] = iptob(i,j);
					bq++;
				}
				else
				{
					parray[16 + bp] = qiptob(i,j);
					bp++;
				}
			}
		}
		
	}

	for (i = 0; i < 32; i++)
		{ temp = temp + btos(parray[i]); }

	temp = temp + btos(castle);
	temp = temp + btos(enpassant[0]);
	temp = temp + btos(enpassant[1]);

	return temp;

}

ChessBoard::ChessBoard(const ChessBoard& oldboard)
{
	int i,j;
	whiteturn = oldboard.whiteTurn();
	castle = oldboard.castleData();

	enpassant[0] = oldboard.enPassantData(0);
	enpassant[1] = oldboard.enPassantData(1);

	for (j = 0; j < 8; j++)
		for (i = 0; i < 8; i++)
	{
		data[i][j] = oldboard.get(i + 1, j + 1);
	}
}

ChessBoard::ChessBoard()
{
	int i,j;
	whiteturn = true;
	castle = 0;

	enpassant[0] = 0;
	enpassant[1] = 0;

	for (j = 0; j < 8; j++)
		for (i = 0; i < 8; i++)
	{
		data[i][j] = -1;
	}
}

