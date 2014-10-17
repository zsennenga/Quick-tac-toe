Quick-tac-toe
=============

Usage: ./Main.php

This is a program that allows you to play tic-tac-toe vs a "perfect" bot. It will never lose, though often draw.

Since the problem space of tic-tac-toe is so small, the AI makes use of a "playbook" which allows it to quickly figure out which spaces it needs to move to in order to block and so on. While calculating these adjacencies at runtime isn't particularly computationally expensive, it simplifies a number of sections of the code. 

The code for the game itself is found in Main.php, DoozyBot.php, GameState.php and util.php. DataBuilder and RandomTest are scripts I wrote during this to speed up some tasks.

Turns and 1 and 2 the AI has a rather set-in-stone strategy. After that, the AI's strategy is as follows:

1. Try to win (if there's an open 2-in-a-row)
2. Block the opponent
3. Set up a win for yourself
4. Choose a random open square

To test the AI I used RandomTest, a quick montecarlo-esque simulator for the AI. I ran it against batches of 100,000 random games to verify it never lost. This is of course in addition to some games I considered edge cases. It had a 100% not-loss rate over a number of 100k game batches, so I'm fairly confidant in it's not-lossitude.

While there are certainly optimizations to be made, I tended to choose clarity over speed here because greater optimization wasn't really necessary given how quickly the AI makes it's moves.

I went with an object oriented design here. A few functions and constants sit in util.php, however the bulk of the logic is in the DoozyBot and GameState classes. I did this for a number of reasons. I favor object oriented designs in general for the ease of unit testing and refactoring, however, that wasn't super relevant here. 
My initial plan was to quickly prototype a few different AIs with different strategies and play them against each other, which an OO setup would have made really easy... however the solution ended up being pretty straightforward, so that became unnecessary. 

Despite those facts I still stand by the design as developing the AI without direct access to the gameState data (board state etc) forced me to keep better track of the moves as they happened and overall lead to a better search algorithm for possible moves.

I think that's enough words about a tic-tac-toe bot.
