DO_C_O=g++ -c $(INC_DIR) -Wall
objects=ChessBoard.o FindMoves.o 

all: moves check

moves: AllMoves.cpp FindMoves.cpp $(objects) 
	g++ -Wall AllMoves.cpp $(objects) -o moves

check: CheckMate.cpp FindMoves.cpp $(objects) 
	g++ -Wall CheckMate.cpp $(objects) -o check

ChessBoard.o: ChessBoard.cpp
	$(DO_C_O) ChessBoard.cpp

FindMoves.o: FindMoves.cpp
	$(DO_C_O) FindMoves.cpp
