<?php
/**
 * util.php
 * 
 * Holds a few strings/functions I can't really work into a class or need to refer to in multiple places.
 * 
 */

//A few static strings that I need to print or otherwise refer to in a variety of places
define("PLAYER_PIECE", X);
define("BOT_PIECE", O);
define("USAGE_STRING", "");
define("INPUT_INSTRUCTIONS", "");


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
		
		if ($line === 'exit')	{
			return -1;
		}
		
		//check if our line matches our expected pattern
		if (strlen($line) === 2 && preg_match("/[a-c][1-3]/", $line) === 1)	{
			$str = explode("", $line);
			//Fancy math! We're translating matrix positions to a regular array
			//aX is 0-2, bX is 3-5, cX is 6-8.
			return 3*(ord($str[0])-97)+($str[1]-1);
		}
			
		$line = fgets(STDIN);
	}
}

