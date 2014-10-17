<?php
class DoozyBot	{
	
	private $myMoves;
	private $oppMoves;
	private $first;
	
	private $victoryPairs;
	
	private $winMove;
	
	function DoozyBot($first, $playbook, $playbook2)	{
		//The playbook contains, for each given space on the board, the other two spaces 
		//that need to match to win the game.
		$this->victoryPairs = $playbook;
		$this->victorySpaces = $playbook2;
		$this->reset($first);
	}
	
	/**
	 * The bot strategy is pretty simple;
	 * 1. Win if you can
	 * 2. Block if you can't win
	 * 3. Setup a win next turn if you don't need to block
	 * 4. Flail wildly if that isn't possible
	 * 
	 * Turns 1 and 2 are pretty set-in-stone so those are handled seperately.
	 * 
	 * @param integer 0-8 $lastPlayerMove
	 * @return integer 0-8
	 */
	public function getMove($lastPlayerMove)	{
		if ($lastPlayerMove !== null)	{
			array_push($this->oppMoves, $lastPlayerMove);
		}
		switch(count($this->myMoves))	{
			case 0:
				if ($this->first || $lastPlayerMove !== 4)	{
					$move = 4;
				}
				else {
					$move = 0;
				}
				break;
			case 1:
				$move = $this->attemptBlock();
				if ($move === -1)	{
					$move = $this->setupWin();
				}
				break;
			default:
				$move = $this->doWin();
				if ($move === -1)	{
					$move = $this->attemptBlock();
				}
				if ($move === -1)	{
					$move = $this->setupWin();
				}
				if ($move === -1)	{
					$move = $this->randomMove();
				}
				break;
		}
		
		array_push($this->myMoves, $move);
		return $move;
	}
	
	/**
	 * Tries to block the human player, if possible.
	 * @return integer 0-8
	 */
	private function attemptBlock()	{
		$keys = $this->buildKeys($this->oppMoves);
		$vSpace = $this->getVictorySpace($keys, $this->myMoves);
		if ($vSpace !== -1) {
			$this->checkBlockWinSetup($vSpace);
		}
		return $vSpace;
	}
	
	/**
	 * Check if blocking in the way we've chosen sets us up for a win.
	 * 
	 * @param integer 0-8 $vSpace
	 */
	private function checkBlockWinSetup($vSpace)	{
		$tempArr = $this->myMoves;
		array_push($tempArr, $vSpace);
		$keys = $this->buildKeys($tempArr);
		$newVSpace = $this->getVictorySpace($keys, $this->oppMoves);
		if ($newVSpace !== -1)	{
			$this->winMove = $newVSpace;
		}
	}
	
	/**
	 * Checks if, given a set of keys created from the indexes of two other moves,
	 * there is a third move which wins the game.
	 * @param string $key
	 * @return number
	 */
	private function getVictorySpace($keys, $arr = array())	{
		foreach ($keys as $key)	{
			if (isset($this->victorySpaces[$key]))	{
				if (!in_array($this->victorySpaces[$key], $arr))	{
					return $this->victorySpaces[$key];
				}
			}
		}
		return -1;
	}
	
	/**
	 * Creates every combo of keys possible from the given array for use in getVictorySpace
	 * @param array $arr
	 */
	private function buildKeys($arr)	{
		$count =  count($arr);
		$keys = array();
		for ($i = 0; $i < $count; $i++)	{
			for ($j = 0; $j < $count; $j++)	{
				$key = $arr[$i] . $arr[$j];
				array_push($keys, $key);
			}
		}
		return $keys;
	}
	
	/**
	 * Setup a 2-in-a-row and force a block, or you win
	 * @return unknown|number
	 */
	private function setupWin()	{
		//First check if we lucked into setting up a fork.
		//This is unlikely, but fast to check
		$keys = $this->buildKeys($this->myMoves);
		$vSpace = $this->getVictorySpace($keys, $this->oppMoves);
		if ($vSpace !== -1)	{
			return $vSpace;
		}
		//Otherwise iterate down all our spaces and look for a win we can setup
		foreach($this->myMoves as $move)	{
			if ($move === -1)	{
				echo "huh?";
			}
			$victoryPairs = $this->victoryPairs[$move];
			foreach($victoryPairs as $pair)	{
				foreach($pair as $space)	{
					if (in_array($space, $this->oppMoves))	{
						continue 2;
					}
				}
				$this->winMove = $pair[0];
				return $pair[1];
			}
		}
		
		//If we just can't setup a win, return -1 and do a random move;
		return -1;
	}
	
	/**
	 * Finds a move sequence that wins us the game, and begins executing it.
	 * @return integer 0-8
	 */
	private function findVictoryMove()	{
		$myMove = end($this->myMoves);
		
		
		//Given our first move, find the first pair of adjacent spaces that are not blocked.
		//If no win is possible, return begin forcing a draw.
		
		return $this->randomMove();
	}
	/**
	 * Tries to use a move that wins us the game that we found before.
	 * 
	 * If it can't, it returns a random move, because a draw is guarenteed.
	 * @return integer 0-8
	 */
	private function doWin()	{
		if (!in_array($this->winMove, $this->oppMoves) && $this->winMove >= 0)	{
			return $this->winMove;
		}
		
		//We got blocked!
		$this->winMove = -1;
		return -1;
	}
	
	/**
	 * Only called when a draw is guarenteed, or there's nothing else we can do.
	 * 
	 * rand is not very random, but at the point this is
	 * called, i'm fine with that. We're just trying to end the game.
	 * @return integer 0-8
	 */
	private function randomMove()	{
		//Find the set difference of the array of integers 0-8 and the moves already taken.
		//Select randomly from that set.
		$values = range(0,8);
		$taken = array_merge($this->myMoves, $this->oppMoves);
		$diff = array_values(array_diff($values, $taken));
		$rand = rand(0, count($diff)-1);
		return $diff[$rand];
	}
	
	public function reset($first)	{
		$this->winMove = -1;
		$this->myMoves = array();
		$this->oppMoves = array();
		$this->first = $first;
	}

}