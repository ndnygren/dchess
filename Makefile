DO_C_O=g++ -c $(INC_DIR) -Wall
objects=ChessBoard.o 

all: test

test: FindMoves.cpp $(objects) 
	g++ -Wall FindMoves.cpp $(objects) -o moves

ChessBoard.o: ChessBoard.cpp
	$(DO_C_O) ChessBoard.cpp

