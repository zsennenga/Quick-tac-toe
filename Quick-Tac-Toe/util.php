<?php
/**
 * util.php
 * 
 * Holds a few strings/functions I can't really work into a class or need to refer to in multiple places.
 * 
 * At the start of this endeavour this had more purpose than just holding a bunch of defines, but it sorta
 * withered away as I refined the design. I'd rename it but there isn't too much reason.
 * 
 */

//A few static strings that I need to print or otherwise refer to in a variety of places
define("PLAYER_ID", 0);
define("BOT_ID", 1);

define("CLI_CHASTISE", "Please run this from the command line.\nCall using ./Main.php\n");
define("INPUT_INSTRUCTIONS", "Valid moves require a row and a column in the format 'c3', for example. Enter 'exit' to quit.\n");
define("CHASTISE_MESSAGE","There's already a piece in that position. Try again.\n");
define("BOT_MOVE", "Doozy Bot made a move!\n");
define("NEW_GAME", "New Game!\n\n\n");
define("THANKS_MESSAGE", "Thanks for playing!");

define("DRAW_WIN", "A draw! Unsurprising!\n");
define("PLAYER_WIN", "I... don't know how this happened. Player wins?\n");
define("BOT_WIN", "Beep beep boop. Bot win.\n");


/**
 * Takes in user input, and loops until we get something
 * in the form that we expect.
 * 
 * The form we expect is either the string 'exit' or a string that looks like [A-C][1-3]
 * 
 * The second input type refers to a square to do a move on. This is parse to a number 0-8
 * that refers to an index of the square in the gameState object.
 * 
 * Returns an integer in the range -1 to 8
 */
function parseCliInput()	{
	$line = fgets(STDIN);
	
	//Loop FOREVER until we get something we like	
	while(true)	{
		//clean up the string a bit
		$line = trim(strtolower($line));
		
		if ($line === "exit")	{
			return -1;
		}
		
		//check if our line matches our expected pattern
		if (strlen($line) === 2 && preg_match("/[a-c][1-3]/", $line) === 1)	{
			$str = str_split($line);
			//Fancy math! We're translating matrix positions to a regular array
			//aX is 0-2, bX is 3-5, cX is 6-8.
			return 3*(ord($str[0])-97)+($str[1]-1);
		}

		echo INPUT_INSTRUCTIONS;
		$line = fgets(STDIN);
	}
}

